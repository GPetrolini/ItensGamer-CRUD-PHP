<?php
declare(strict_types=1);

namespace Vios\Juridico\App\Consultas\ItensGamer;

use Exception;
use Laminas\Db\Sql\Select;
use Vios\Juridico\App\DAO\DAO;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\Util\Integer;


final class ConsultaItensGamer
{
    public const TABELA = 'itens_gamer';

    private $dao;

    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }

    /** @return ItemGamerEntity[]
     * @throws Exception
     */
    public function getItensGamer(PesquisaItensGamerEntity $pesquisa): array
    {
        $callable = function (array $ItensGamer):
        ItemGamerEntity {

            $dataCadastro = !empty($ItensGamer[ItensGamerCamposConstants::DATA_CADASTRO])
                ? new \DateTime($ItensGamer[ItensGamerCamposConstants::DATA_CADASTRO])
                : null;

            $id = Integer::int($ItensGamer[ItensGamerCamposConstants::ID] ?? 0);

            return (new ItemGamerEntity())
                ->setId($id)
                ->setNome((string)$ItensGamer[ItensGamerCamposConstants::NOME] ?? '')
                ->setDescricao((string)$ItensGamer[ItensGamerCamposConstants::DESCRICAO] ?? '')
                ->setTipo((string)$ItensGamer[ItensGamerCamposConstants::TIPO] ?? '')
                ->setTags(... $this->getTagsDoItem($id))
                ->setCorEmblema((string)$ItensGamer[ItensGamerCamposConstants::COR_EMBLEMA] ?? '')
                //->setCorEtiqueta((string)$ItensGamer[ItensGamerCamposConstants::TIPO_COR_ETIQUETA] ?? '')
                ->setQuantidade((integer)$ItensGamer[ItensGamerCamposConstants::QUANTIDADE] ?? 0)
                ->setPrecoVenda((float)$ItensGamer[ItensGamerCamposConstants::PRECO_VENDA] ?? 0.0)
                ->setItemAtivo((bool)$ItensGamer[ItensGamerCamposConstants::ITEM_ATIVO] ?? false)
                ->setDataCadastro($dataCadastro);
        };
        return array_map($callable, $this->buscaItensGamer($pesquisa));
    }

    /**
     * @throws Exception
     */
    public function getById(int $ItensGamerId): ItemGamerEntity
    {
        $pesquisa = new  PesquisaItensGamerEntity();
        $pesquisa->setId($ItensGamerId);
        // se o ID for 0, o resultado será um array vazio
        // se for > 0, chama o getItensGamer com o filtro de pesquisa
        $resultado = $ItensGamerId <= 0 ? [] : $this->getItensGamer($pesquisa);
        $entidade = $resultado[0] ?? null;

        return $entidade instanceof ItemGamerEntity ? $entidade : new ItemGamerEntity();
    }

    /**
     * @throws Exception
     */
    public function getTagsDoItem($itensGamerId): array
    {
        if ($itensGamerId <= 0) {
            return [];
        }
        $select = $this->dao->getSelectBase('item_gamer_tags', 'igt')
            ->columns([])
            ->join(['t' => 'tags'], 'igt.tag_id = t.id', ['id', 'nome'])
            ->where(['igt.item_gamer_id' => $itensGamerId])
            ->order('t.nome ASC');

        $resultado = $this->dao->executaSelect($select);

        return array_map(function (array $tag) {
            return (new TagEntity())
                ->setId((int)$tag['id'])
                ->setNome((string)$tag['nome']);
        }, $resultado);
    }

    /**
     * @throws Exception
     */
    public function getTagsDisponiveis(): array
    {
        $select = $this->dao->getSelectBase('tags', 't')
            ->columns(['id','nome'])
            ->order('t.nome ASC');
        $resultado = $this->dao->executaSelect($select);
        return array_column($resultado, 'nome', 'id');
    }

    /**
     * @throws Exception
     */
    public function buscaItensGamer(PesquisaItensGamerEntity $pesquisa): array
    {
        // inicia o query builder para a tabela "itens_gamer

        $select = $this->dao->getSelectBase(ItensGamerCamposConstants::TABELA, 'ig');

        // adiciona o filtro de ID
        if ($pesquisa->getId() > 0) {
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::ID, $pesquisa->getId());
        }

        // Adiciona o filtro pro nome se preenchido
        $buscaNome = trim($pesquisa->getNome() ?? '');
        if (!empty($buscaNome)) {
            // 'Like' permite buscar por parte do nome
            $select->where->like('ig.' . ItensGamerCamposConstants::NOME, "%{$buscaNome}%");
        }
        $buscaTipo = $pesquisa->getTipo() ?? '';

        if (!empty($buscaTipo) && is_array($buscaTipo)) {
            $select->where->in('ig.' . ItensGamerCamposConstants::TIPO, $buscaTipo);
        }
        if (!is_null($pesquisa->isItemAtivo())) {
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::ITEM_ATIVO, $pesquisa->isItemAtivo() ? 1 : 0);
        }
        $tags = $pesquisa->getTags();
        if (!empty($tags)) {
            $subSelect = new Select('item_gamer_tags');
            $subSelect->where('item_gamer_tags.item_gamer_id = ig.id');
            $subSelect->where-> in('tag_id', $tags);

            $select->where->expression('EXISTS (?)', [$subSelect]);
        }
        $cor = trim($pesquisa->getCorEmblema() ?? '');
        if (!empty($cor)) {
            if (strpos($cor, '#') !== 0) {
                $cor = '#' . ltrim($cor, '#');
            }
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::COR_EMBLEMA, strtolower($cor));
        }
        if ($pesquisa->getPrecoMinimo()) {
            $select->where->greaterThanOrEqualTo(
                'ig.' . ItensGamerCamposConstants::PRECO_VENDA,
                $pesquisa->getPrecoMinimo()
            );
        }
        if ($pesquisa->getPrecoMaximo()) {
            $select->where->lessThanOrEqualTo(
                'ig.' . ItensGamerCamposConstants::PRECO_VENDA,
                $pesquisa->getPrecoMaximo()
            );
        }

        return $this->dao->executaSelect($select);
    }
}
