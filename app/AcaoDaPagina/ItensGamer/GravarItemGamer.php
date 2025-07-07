<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\DAO\DAO;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\Excecoes\AvisoParaOUsuarioException;
use Vios\Juridico\App\Formatadores\TextoFormatter;
use Vios\Juridico\App\Routes\UrlRedir;
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposFactory as CamposFactory;
use Vios\Juridico\App\Servicos\Historicos\ItensGamer\ItemGamerHistorico;
use Vios\Juridico\App\Servicos\Historicos\ListaHistorico;
use Vios\Juridico\ContainerVios;

final class GravarItemGamer
{

    private $consultor;

    private $dao;

    private $historico;

    public function __construct(
        DAO $dao,
        ConsultaItensGamer $consultor
    ) {
        $this->dao = $dao;
        $this->consultor = $consultor;
        $camposFactory = ContainerVios::fetch(CamposFactory::class);
        $listaHistorico = ContainerVios::fetch(ListaHistorico::class);
        $this->historico = new ItemGamerHistorico($dao, $listaHistorico, $camposFactory);
    }


    /**
     * @throws AvisoParaOUsuarioException
     * @throws \Exception
     */
    public function executa(ItemGamerEntity $entity):UrlRedir
    {
        if (empty($entity->getNome())) {
            throw new AvisoParaOUsuarioException('Obrigatório por nome no item');
        }
            $dados = [
                ItensGamerCamposConstants::NOME => $entity->getNome(),
                ItensGamerCamposConstants::DESCRICAO => TextoFormatter::corrigeHtmlEntity($entity->getDescricao()),
                ItensGamerCamposConstants::TIPO => $entity->getTipo(),
                ItensGamerCamposConstants::COR_EMBLEMA => $entity->getCorEmblema(),
                ItensGamerCamposConstants::QUANTIDADE => $entity->getQuantidade(),
                ItensGamerCamposConstants::PRECO_VENDA => $entity->getPrecoVenda(),
                ItensGamerCamposConstants::ITEM_ATIVO => $entity->isItemAtivo() ? 1 : 0,
            ];

            $itemId = $entity->getId();
            $dadosAntigos = [];
            if ($itemId > 0) {
                $entidadeAntiga = $this->consultor->getById($itemId);
                $dadosAntigos = $entidadeAntiga->toArray();
            }

            if ($itemId > 0) {
                $this->dao->atualiza(
                    ItensGamerCamposConstants::TABELA,
                    $dados,
                    [ItensGamerCamposConstants::ID => $itemId]
                );
            } else {
                $itemId = $this->dao->insere($dados, ItensGamerCamposConstants::TABELA, true);
                $this->historico->registroHistoricoCriacao($itemId);
            }

            $tagIds = array_map(function (TagEntity $tag) {
                return $tag->getId();
            }, $entity->getTags());

            $this->persisteTags($itemId, ...$tagIds);

            $this->historico->resolveHistorico($itemId, $dadosAntigos, $entity->toArray());
        return new UrlRedir('?pag=sys/itens_gamer/itens_gamer.php', 'Item salvo com sucesso');
    }

    /**
     * @throws AvisoParaOUsuarioException
     */
    private function persisteTags($itemId, ...$tagIds)
    {
        $this->dao->deleta('item_gamer_tags', ['item_gamer_id' => $itemId]);
        $tagIdsUnicas = array_unique(array_map('intval', $tagIds));

        if (empty($tagIdsUnicas)) {
            return;
        }

        $callable = function ($tagId) use ($itemId) {
            return ($tagId <= 0 || $itemId <= 0) ? [] : [
                'item_gamer_id' => $itemId,
                'tag_id' => $tagId,
            ];
        };

        $insert = array_values(array_filter(array_map($callable, $tagIdsUnicas)));

        if (!empty($insert)) {
            $this->dao->insere($insert, 'item_gamer_tags');
        }
    }
}
