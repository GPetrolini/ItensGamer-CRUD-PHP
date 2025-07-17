<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Consultas\ItensGamer;

use Laminas\Db\Sql\Select;
use Vios\Juridico\App\DAO\DAO;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\Formatadores\DataFormatter;
use Vios\Juridico\App\Util\Decimal;
use Vios\Juridico\App\Util\Integer;

final class ConsultaItensGamer
{
    public const TABELA = 'itens_gamer';

    public const TABELA_TAGS = 'item_gamer_tags';

    private $dao;

    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }

    public function getById(int $ItensGamerId): ItemGamerEntity
    {
        $pesquisa = (new PesquisaItensGamerEntity())->setId($ItensGamerId);
        $resultado = $ItensGamerId <= 0 ? [] : $this->getItensGamer($pesquisa);
        $entidade = $resultado[0] ?? null;

        return $entidade instanceof ItemGamerEntity ? $entidade : new ItemGamerEntity();
    }

    /** @return ItemGamerEntity[] */
    public function getItensGamer(PesquisaItensGamerEntity $pesquisa): array
    {
        $callable = function (array $ItensGamer): ItemGamerEntity {
            $id = Integer::int($ItensGamer[ItensGamerCamposConstants::ID] ?? 0);

            return (new ItemGamerEntity())
                ->setId($id)
                ->setNome((string) $ItensGamer[ItensGamerCamposConstants::NOME] ?? '')
                ->setDescricao((string) $ItensGamer[ItensGamerCamposConstants::DESCRICAO] ?? '')
                ->setTipo((string) $ItensGamer[ItensGamerCamposConstants::TIPO] ?? '')
                ->setTags(...$this->getTagsDoItem($id))
                ->setCorEmblema((string) $ItensGamer[ItensGamerCamposConstants::COR_EMBLEMA] ?? '')
                ->setQuantidade(Integer::int($ItensGamer[ItensGamerCamposConstants::QUANTIDADE] ?? 0))
                ->setPrecoVenda(Decimal::decimal($ItensGamer[ItensGamerCamposConstants::PRECO_VENDA] ?? 0))
                ->setIsItemAtivo(!empty($ItensGamer[ItensGamerCamposConstants::ITEM_ATIVO] ?? false))
                ->setDataCadastro(
                    DataFormatter::getCarbon((string) $ItensGamer[ItensGamerCamposConstants::DATA_CADASTRO] ?? '')
                );
        };

        return array_map($callable, $this->buscaItensGamer($pesquisa));
    }

    public function getTagsDisponiveis(): array
    {
        $select = $this->dao->getSelectBase('tags', 't')
            ->columns([ItensGamerCamposConstants::ID, ItensGamerCamposConstants::NOME])
            ->order('t.nome ASC');

        return array_column($this->dao->executaSelect($select), 'nome', 'id');
    }

    /**  @return TagEntity[] */
    private function getTagsDoItem(int $itensGamerId): array
    {
        if ($itensGamerId <= 0) {
            return [];
        }

        $callable = function (array $tag): TagEntity {
            return (new TagEntity())
                ->setId(Integer::int($tag[ItensGamerCamposConstants::ID] ?? 0))
                ->setNome((string) $tag[ItensGamerCamposConstants::NOME] ?? '');
        };

        $select = $this->dao->getSelectBase(self::TABELA_TAGS, 'igt')
            ->columns([])
            ->join(['t' => 'tags'], 'igt.tag_id = t.id', [
                ItensGamerCamposConstants::ID,
                ItensGamerCamposConstants::NOME
            ])
            ->where(['igt.item_gamer_id' => $itensGamerId])
            ->order('t.nome ASC');

        return array_map($callable, $this->dao->executaSelect($select));
    }

    private function buscaItensGamer(PesquisaItensGamerEntity $pesquisa): array
    {
        $select = $this->dao->getSelectBase(ItensGamerCamposConstants::TABELA, 'ig');
        if ($pesquisa->getId() > 0) {
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::ID, $pesquisa->getId());
        }

        $buscaNome = trim($pesquisa->getNome());
        if (!empty($buscaNome)) {
            $select->where->like('ig.' . ItensGamerCamposConstants::NOME, "%{$buscaNome}%");
        }

        $buscaTipo = $pesquisa->getTipo()->getAdded();
        if (!empty($buscaTipo)) {
            $select->where->in('ig.' . ItensGamerCamposConstants::TIPO, $buscaTipo);
        }

        if (!is_null($pesquisa->isItemAtivo())) {
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::ITEM_ATIVO, $pesquisa->isItemAtivo() ? 1 : 0);
        }

        $tags = $pesquisa->getTags()->getAdded();
        if (!empty($tags)) {
            $select->where->expression('EXISTS (?)', call_user_func(function () use ($tags): Select {
                return $this->dao->getSelectBase(self::TABELA_TAGS, self::TABELA_TAGS)
                    ->columns(['tag_id'])
                    ->where([
                        self::TABELA_TAGS . '.item_gamer_id = ig.id',
                        self::TABELA_TAGS . 'tag_id' => $tags,
                    ]);
            }));
        }

        $cor = trim($pesquisa->getCorEmblema());
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
