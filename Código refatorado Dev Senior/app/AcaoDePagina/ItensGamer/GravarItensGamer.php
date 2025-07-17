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
use Vios\Juridico\App\Servicos\Historicos\ItensGamer\ItemGamerHistorico;

final class GravarItemGamer
{
    private $consultor;

    private $dao;

    private $historico;

    public function __construct(
        DAO $dao,
        ConsultaItensGamer $consultor,
        ItemGamerHistorico $historico
    ) {
        $this->dao = $dao;
        $this->consultor = $consultor;
        $this->historico = $historico;
    }

    public function executa(ItemGamerEntity $entity): UrlRedir
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
        $entidadeAntiga = $this->consultor->getById($itemId);

        if ($itemId > 0) {
            $this->dao->atualiza(ItensGamerCamposConstants::TABELA, $dados, [ItensGamerCamposConstants::ID => $itemId]);
            $this->historico->resolveHistorico($itemId, $entidadeAntiga->toArray(), $entity->toArray());
        } else {
            $itemId = $this->dao->insere($dados, ItensGamerCamposConstants::TABELA, true);
            $this->historico->registroHistoricoCriacao($itemId);
        }

        $this->persisteTags($itemId, ...$entity->getTags());

        return new UrlRedir(
            '?' . http_build_query(['pag' => ListaItensGamer::CAMINHO_PAGINA]),
            'Item salvo com sucesso'
        );
    }

    private function persisteTags(int $itemId, TagEntity ...$tagIds): void
    {
        $callable = function (TagEntity $entid) use ($itemId): array {
            return $entid->getId() <= 0 || $itemId <= 0 ? [] : [
                'item_gamer_id' => $itemId,
                'tag_id' => $entid->getId(),
            ];
        };

        $this->dao->deleta(ConsultaItensGamer::TABELA_TAGS, ['item_gamer_id' => $itemId]);

        $insert = array_values(array_filter(array_map($callable, $tagIds)));
        if (!empty($insert)) {
            $this->dao->insere($insert, ConsultaItensGamer::TABELA_TAGS);
        }
    }
}
