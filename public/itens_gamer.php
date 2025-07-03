<?php

declare(strict_types=1);


use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\DeletaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioPesquisaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\GravarItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\ListaItensGamer;
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

// define a ação

$act = $_REQUEST['act'] ?? '';
if (($_POST['Gravar'] ?? '') === 'Gravar') {
    $form = $_POST['form'] ?? []; // pega os dados do formulário
    /** @var GravarItemGamer $gravador */
    $gravador = ContainerVios::fetch(GravarItemGamer::class);
    $tagIds = $form['tags'] ?? [];
    if (!is_array($tagIds)) {
        $tagIds = [];
    }
    $tagEntities = array_map(function ($tagId) {
        return(new TagEntity())->setId((int)$tagId);
    }, $tagIds);
    $precoVendaStr = $form['preco_venda'] ?? '0,00';
    $precoFormatado = str_replace(',', '.', $precoVendaStr);
    $precoFormatado = str_replace('.', '.', $precoFormatado);
    $precoFinal = (float)$precoFormatado;

    $item = (new ItemGamerEntity())
        ->setId(Integer::int($form['id'] ?? 0))
        ->setNome((string)$form['nome'] ?? '')
        ->setDescricao((string)$form['descricao'] ?? '')
        ->setTipo((string)$form['tipo'] ?? '')
        ->setTags(...$tagEntities)
        ->setCorEmblema((string)$form['cor_emblema'] ?? '#00000')
        ->setQuantidade(Integer::int($form['quantidade'] ?? 0))
        ->setPrecoVenda((float)$form['preco_venda'] ?? 0.0)
        ->setItemAtivo(($form['item_ativo'] ?? 0) === '1');

    $gravador->executa($item)->toastRedirect();
} elseif ($act === 'insert' || $act === 'edit') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    $itemGamerId = Integer::int($_REQUEST['id'] ?? 0);
    $formulario = new FormularioItemGamer($consulta, $itemGamerId);
    echo $formulario;
} elseif ($act === 'view') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    $itemGamerId = Integer::int($_REQUEST['id'] ?? 0);
    $formulario = new FormularioItemGamer($consulta, $itemGamerId);
    $formulario->desabilitarEdicao();
    echo $formulario;
} elseif ($act === 'del') {
    /** @var DeletaItensGamer $deletador */
    $deletador = ContainerVios::fetch(DeletaItensGamer::class);

    $itemId = Integer::int($_REQUEST['id'] ?? 0);
    $deletador->executa($itemId)->toastRedirect();
} elseif ($act === 'history') {
    /** @var ItemGamerHistorico $historicoService */
    $dao = ContainerVios::fetch(DAO::class);
    $listaHistorico = ContainerVios::fetch(ListaHistorico::class);
    $camposFactory = ContainerVios::fetch(CamposFactory::class);
    $historicoService = ContainerVios::fetch(ItemGamerHistorico::class);
    $itemId = Integer::int($_REQUEST['id'] ?? 0);
    echo $historicoService->listaHistorico($itemId);
} elseif ($act === 'visualizar_dois') {
    $itemId = Integer::int($_REQUEST['id'] ?? 0);
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    $item = $consulta->getById($itemId);
} else {
    $pesquisaDados = $_REQUEST['pesq'] ?? [];
    $busca = $pesquisaDados['busca'] ?? null;
    $ativo = $pesquisaDados['comboarr'] ?? 'todos';
    $tipoSelecionado = $pesquisaDados['comboarr_multi'] ?? [];
    $tags = $pesquisaDados['comboarr_tag'] ?? [];
    $cor = $pesquisaDados['cor_emblema'] ?? null;
    $precoMin = (float)($pesquisaDados['valor_minimo'] ?? 0.0);
    $precoMax = (float)($pesquisaDados['valor_maximo'] ?? 0.0);

    $pesquisaEntity = (new PesquisaItensGamerEntity())
        ->setNome($busca)
        ->setTags($tags)
        ->setCorEmblema($cor)
        ->setPrecoMinimo($precoMin > 0 ? $precoMin : null)
        ->setPrecoMaximo($precoMax > 0 ? $precoMax : null)
        ->setItemAtivo($ativo === 'todos' ? null : ($ativo === '1'))
        ->setTipo($tipoSelecionado);

    $formularioPesquisa = new FormularioPesquisaItensGamer();
    echo $formularioPesquisa->render($pesquisaEntity);
    echo '<hr>';

    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    $lista = new ListaItensGamer($consulta);
    echo $lista->render($pesquisaEntity);
}
