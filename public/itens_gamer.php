<?php

declare(strict_types=1);


use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\DeletaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioPesquisaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\GravarItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\ListaItensGamer;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\DAO\DAO;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposFactory as CamposFactory;
use Vios\Juridico\App\Servicos\Historicos\ItensGamer\ItemGamerHistorico;
use Vios\Juridico\App\Servicos\Historicos\ListaHistorico;
use Vios\Juridico\App\Util\Integer;
use Vios\Juridico\ContainerVios;

$act = $_REQUEST['act'] ?? '';
if (($_POST['Gravar'] ?? '') === 'Gravar') {
    $form = $_POST['form'] ?? [];
    /** @var GravarItemGamer $gravador */
    $gravador = ContainerVios::fetch(GravarItemGamer::class);
    $tagIds = $form[ItensGamerCamposConstants::TAGS] ?? [];
    if (!is_array($tagIds)) {
        $tagIds = [];
    }
    $tagEntities = array_map(function ($tagId) {
        return(new TagEntity())->setId((int)$tagId);
    }, $tagIds);
    $precoVendaStr = $form[ItensGamerCamposConstants::PRECO_VENDA] ?? '0,00';
    $precoFormatado = str_replace(',', '.', $precoVendaStr);
    $precoFormatado = str_replace('.', '.', $precoFormatado);
    $precoFinal = (float)$precoFormatado;

    $item = (new ItemGamerEntity())
        ->setId(Integer::int($form[ItensGamerCamposConstants::ID] ?? 0))
        ->setNome((string)$form[ItensGamerCamposConstants::NOME] ?? '')
        ->setDescricao((string)$form[ItensGamerCamposConstants::DESCRICAO] ?? '')
        ->setTipo((string)$form[ItensGamerCamposConstants::TIPO] ?? '')
        ->setTags(...$tagEntities)
        ->setCorEmblema((string)$form[ItensGamerCamposConstants::COR_EMBLEMA] ?? '#00000')
        ->setQuantidade(Integer::int($form[ItensGamerCamposConstants::QUANTIDADE] ?? 0))
        ->setPrecoVenda((float)$form[ItensGamerCamposConstants::PRECO_VENDA] ?? 0.0)
        ->setItemAtivo(($form[ItensGamerCamposConstants::ITEM_ATIVO] ?? 0) === '1');

    $gravador->executa($item)->toastRedirect();
} elseif ($act === 'insert' || $act === 'edit') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    /** @var FormularioItemGamer $formulario */
    $formulario = ContainerVios::buildWithoutArg(FormularioItemGamer::class, ['itemGamerId' => $itemGamerId]);
    echo $formulario->__toString();

} elseif ($act === 'view') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    /** @var FormularioItemGamer $formulario */
    $formulario = ContainerVios::BuildWithoutArg(FormularioItemGamer::class, ['itemGamerId' => $itemGamerId]);
    $formulario->desabilitarEdicao();
    echo $formulario->__toString();

} elseif ($act === 'del') {
    /** @var DeletaItensGamer $deletador */
    $deletador = ContainerVios::fetch(DeletaItensGamer::class);
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    $deletador->executa($itemGamerId)->toastRedirect();

} elseif ($act === 'history') {
    /** @var ItemGamerHistorico $historicoService */
    $dao = ContainerVios::fetch(DAO::class);
    $listaHistorico = ContainerVios::fetch(ListaHistorico::class);
    $camposFactory = ContainerVios::fetch(CamposFactory::class);
    $historicoService = ContainerVios::fetch(ItemGamerHistorico::class);
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    echo $historicoService->listaHistorico($itemGamerId);

} else {
    $pesquisaDados = $_REQUEST['pesq'] ?? [];
    $busca = $pesquisaDados['busca'] ?? null;
    $id = $pesquisaDados['buscaId'] ?? 0;
    $ativo = $pesquisaDados['comboarr'] ?? 'todos';
    $tipoSelecionado = $pesquisaDados['comboarr_multi'] ?? [];
    $tags = $pesquisaDados['comboarr_tag'] ?? [];
    $cor = $pesquisaDados[ItensGamerCamposConstants::COR_EMBLEMA] ?? null;
    $precoMin = (float)($pesquisaDados['valor_minimo'] ?? 0.0);
    $precoMax = (float)($pesquisaDados['valor_maximo'] ?? 0.0);

    $pesquisaEntity = (new PesquisaItensGamerEntity())
        ->setNome($busca)
        ->setId(Integer::int($pesquisaDados[ItensGamerCamposConstants::ID] ?? 0))
        ->setTags($tags)
        ->setCorEmblema($cor)
        ->setPrecoMinimo($precoMin > 0 ? $precoMin : null)
        ->setPrecoMaximo($precoMax > 0 ? $precoMax : null)
        ->setItemAtivo($ativo === 'todos' ? null : ($ativo === '1'))
        ->setTipo($tipoSelecionado);

    $formularioPesquisa = new FormularioPesquisaItensGamer();


    $lista = ContainerVios::fetch(ListaItensGamer::class);
    echo $formularioPesquisa->render($pesquisaEntity) . '<hr>' . $lista->render($pesquisaEntity);
}
