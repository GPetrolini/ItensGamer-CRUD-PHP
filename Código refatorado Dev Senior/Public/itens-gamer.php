<?php

declare(strict_types=1);

use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\DeletaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioPesquisaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\GravarItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\ListaItensGamer;
use Vios\Juridico\App\Constants\Acoes;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Constants\Filtros;
use Vios\Juridico\App\Cores;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\Filtros\ArrayFiltro;
use Vios\Juridico\App\Servicos\Historicos\ItensGamer\ItemGamerHistorico;
use Vios\Juridico\App\Util\Decimal;
use Vios\Juridico\App\Util\Integer;
use Vios\Juridico\ContainerVios;

$act = $_REQUEST['act'] ?? '';
if (($_POST[Acoes::GRAVAR] ?? '') === Acoes::GRAVAR) {
    $form = $_POST['form'] ?? [];
    call_user_func(function () use ($form): void {
        $callable = function (int $tagId): TagEntity {
            return (new TagEntity())->setId($tagId);
        };

        $item = (new ItemGamerEntity())
            ->setId(Integer::int($form[ItensGamerCamposConstants::ID] ?? 0))
            ->setNome((string) $form[ItensGamerCamposConstants::NOME] ?? '')
            ->setDescricao((string) $form[ItensGamerCamposConstants::DESCRICAO] ?? '')
            ->setTipo((string) $form[ItensGamerCamposConstants::TIPO] ?? '')
            ->setTags(...array_map($callable, ArrayFiltro::getIds($form[ItensGamerCamposConstants::TAGS] ?? [])))
            ->setCorEmblema((string) $form[ItensGamerCamposConstants::COR_EMBLEMA] ?? Cores::AZUL_VIOS)
            ->setQuantidade(Integer::int($form[ItensGamerCamposConstants::QUANTIDADE] ?? 0))
            ->setPrecoVenda(Decimal::decimal($form[ItensGamerCamposConstants::PRECO_VENDA] ?? 0))
            ->setItemAtivo(!empty($form[ItensGamerCamposConstants::ITEM_ATIVO] ?? 0))
        ;

        /** @var GravarItemGamer $gravador */
        $gravador = ContainerVios::fetch(GravarItemGamer::class);
        $gravador->executa($item)->toastRedirect();
    });
} elseif ($act === Acoes::DELETAR) {
    /** @var DeletaItensGamer $deletador */
    $deletador = ContainerVios::fetch(DeletaItensGamer::class);
    $deletador->executa(Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0))->toastRedirect();
} elseif (in_array($act, [Acoes::INSERIR, Acoes::EDITAR], true)) {
    /** @var FormularioItemGamer $formulario */
    $formulario = ContainerVios::buildWithoutArg(FormularioItemGamer::class, [
        'itemGamerId' => Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0),
    ]);

    echo $formulario->__toString();
} elseif ($act === 'view') {
    /** @var FormularioItemGamer $formulario */
    $formulario = ContainerVios::buildWithoutArg(FormularioItemGamer::class, [
        'itemGamerId' => Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0),
    ]);

    echo $formulario->desabilitarEdicao()->__toString();
} elseif ($act === Acoes::HISTORICO) {
    /** @var ItemGamerHistorico $historico */
    $historico = ContainerVios::fetch(ItemGamerHistorico::class);

    echo $historico->listaHistorico(Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0));
} else {
    echo call_user_func(function (): string {
        $pesq = $_REQUEST['pesq'] ?? [];
        $ativo = ($pesq['comboarr'] ?? '') ?: Filtros::TODOS;

        $pesquisa = (new PesquisaItensGamerEntity())
            ->setNome((string) $pesq['busca'] ?? '')
            ->setId(Integer::int($pesq[ItensGamerCamposConstants::ID] ?? 0))
            ->setTags(...ArrayFiltro::getIds($pesq['comboarr_tag'] ?? []))
            ->setCorEmblema((string) $pesq[ItensGamerCamposConstants::COR_EMBLEMA] ?? '')
            ->setPrecoMinimo(Decimal::decimal($pesq['valor_minimo'] ?? 0))
            ->setPrecoMaximo(Decimal::decimal($pesq['valor_maximo'] ?? 0))
            ->setItemAtivo($ativo === Filtros::TODOS ? null : !empty($ativo))
            ->setTipo(...($pesq['comboarr_multi'] ?? []));

        /** @var FormularioPesquisaItensGamer $formularioPesquisa */
        $formularioPesquisa = ContainerVios::fetch(FormularioPesquisaItensGamer::class);

        /** @var ListaItensGamer $lista */
        $lista = ContainerVios::fetch(ListaItensGamer::class);

        return $formularioPesquisa->render($pesquisa) . $lista->render($pesquisa);
    });
}
