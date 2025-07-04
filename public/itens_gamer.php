<?php

declare(strict_types=1);

// Importa classes essenciais para o CRUD de Itens Gamer.
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

// Define a ação a ser executada com base na requisição (GET/POST).
$act = $_REQUEST['act'] ?? '';
$itemId = Integer::int($_REQUEST['id'] ?? 0); // ID do item para operações específicas.

// --- Contorno para problemas de roteamento (se 'act' e 'id' não vêm em $_REQUEST) ---
// Sobrescreve $act e $itemId usando $_GET, que geralmente é mais confiável para parâmetros de URL.
if (isset($_GET['act'])) {
    $act = $_GET['act'];
}
if (isset($_GET['id'])) {
    $itemId = Integer::int($_GET['id']);
}
// --- Fim Contorno ---


// --- Lógica para Gravar (Cadastrar ou Editar) Item ---
if (($_POST['Gravar'] ?? '') === 'Gravar') {
    $form = $_POST['form'] ?? []; // Dados do formulário submetido.
    /** @var GravarItemGamer $gravador */
    $gravador = ContainerVios::fetch(GravarItemGamer::class);

    // Processa IDs de tags para entidades TagEntity.
    $tagIds = $form['tags'] ?? [];
    $tagEntities = array_map(function ($tagId) {
        return (new TagEntity())->setId((int)$tagId);
    }, is_array($tagIds) ? $tagIds : []); // Garante que $tagIds é um array antes de mapear.

    // Trata o campo de preço para float (substitui vírgula por ponto).
    $precoVendaStr = $form['preco_venda'] ?? '0,00';
    $precoFormatado = str_replace(',', '.', $precoVendaStr);
    $precoFinal = (float)$precoFormatado; // Preço final formatado.

    // Popula a entidade ItemGamerEntity com os dados do formulário.
    $item = (new ItemGamerEntity())
        ->setId(Integer::int($form['id'] ?? 0))
        ->setNome((string)$form['nome'] ?? '')
        // Atenção: 'desabilitarEdicaodescricao' pode ser um erro de digitação, deveria ser 'descricao'.
        ->setDescricao((string)$form['desabilitarEdicaodescricao'] ?? '')
        ->setTipo((string)$form['tipo'] ?? '')
        ->setTags(...$tagEntities) // Passa as entidades Tag.
        ->setCorEmblema((string)$form['cor_emblema'] ?? '#00000') // Cor do item (pode ser texto ou hex).
        ->setQuantidade(Integer::int($form['quantidade'] ?? 0))
        // Usar $precoFinal calculado, em vez do valor bruto do form.
        ->setPrecoVenda($precoFinal)
        ->setItemAtivo(($form['item_ativo'] ?? 0) === '1'); // Converte para booleano.

    $gravador->executa($item)->toastRedirect(); // Salva item e redireciona.

// --- Lógica para Exibir Formulário de Inserção ou Edição ---
} elseif ($act === 'insert' || $act === 'edit') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    // $itemId já vem do contorno inicial.
    /** @var FormularioItemGamer $formulario */
    $formulario = ContainerVios::buildWithoutArg(FormularioItemGamer::class, ['itemGamerId' => $itemId]);
    echo $formulario->__toString();

// --- Lógica para Exibir Formulário em Modo de Visualização ---
} elseif ($act === 'view') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    // $itemId já vem do contorno inicial.
    /** @var FormularioItemGamer $formulario */
    $formulario = ContainerVios::BuildWithoutArg(FormularioItemGamer::class, ['itemGamerId' => $itemId]);
    echo $formulario->__toString();

// --- Lógica para Deletar Item ---
} elseif ($act === 'del') {
    /** @var DeletaItensGamer $deletador */
    $deletador = ContainerVios::fetch(DeletaItensGamer::class);
    // $itemId já vem do contorno inicial.
    $deletador->executa($itemId)->toastRedirect();

// --- Lógica para Exibir Histórico de Item ---
} elseif ($act === 'history') {
    // Instanciação de dependências para o serviço de histórico.
    $dao = ContainerVios::fetch(DAO::class);
    $listaHistorico = ContainerVios::fetch(ListaHistorico::class);
    $camposFactory = ContainerVios::fetch(CamposFactory::class);
    /** @var ItemGamerHistorico $historicoService */
    $historicoService = new ItemGamerHistorico($dao, $listaHistorico, $camposFactory);

    // $itemId já vem do contorno inicial.
    echo $historicoService->listaHistorico($itemId);

// --- Lógica para a Ação "Visualizar Dois" ---
} elseif ($act === 'visualizar_dois') {
    // $itemId já vem do contorno inicial.
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    $item = $consulta->getById($itemId);

    // Exibição simples dos detalhes para teste da ação.
    echo "<h1>Detalhes do Item (Visualizar Dois)</h1>";
    echo "<p>ID: {$item->getId()}</p>";
    echo "<p>Nome: {$item->getNome()}</p>";
    echo "<p>Descrição: {$item->getDescricao()}</p>";
    echo "<p>Este é o modo 'Visualizar Dois'. O item ativo é: " . ($item->isItemAtivo() ? 'Sim' : 'Não') . "</p>";
    // Botão para voltar à lista principal.
    echo '<a href="?pag=sys/itens_gamer/itens_gamer.php" class="btn btn-primary">Voltar à Lista</a>';
    exit(); // Interrompe a execução para mostrar apenas esta tela.

// --- Lógica Padrão: Exibir Formulário de Pesquisa e Lista ---
} else {
    // Pega dados de pesquisa da requisição.
    $pesquisaDados = $_REQUEST['pesq'] ?? [];
    
    // Leitura dos valores dos campos de pesquisa do formulário.
    $busca = $pesquisaDados['busca'] ?? null;
    $id = $pesquisaDados['buscaId'] ?? 0;
    $ativo = $pesquisaDados['item_ativo'] ?? 'todos'; // Valor do select "Item ativo?"
    $cor = $pesquisaDados['cor_emblema'] ?? null;     // Valor do campo "Cor do Item" (texto)
    $precoMin = (float)($pesquisaDados['valor_minimo'] ?? 0.0);
    $precoMax = (float)($pesquisaDados['valor_maximo'] ?? 0.0);

    // Valores para multi-selects (dependem do nome configurado em FormularioPesquisaItensGamer).
    // 'comboarr_multi' geralmente para o Tipo do Item.
    $tipoSelecionado = $pesquisaDados['comboarr_multi'] ?? [];
    // 'comboarr_tag' para o campo de Tags que foi criado.
    $tagsSelecionadas = $pesquisaDados['comboarr_tag'] ?? [];

    // Cria a entidade de pesquisa e popula com os valores lidos.
    $pesquisaEntity = (new PesquisaItensGamerEntity())
        ->setNome($busca)
        ->setId(Integer::int($pesquisaDados['id'] ?? 0))
        ->setTags($tagsSelecionadas)
        ->setCorEmblema($cor)
        ->setPrecoMinimo($precoMin > 0 ? $precoMin : null)
        ->setPrecoMaximo($precoMax > 0 ? $precoMax : null)
        ->setItemAtivo($ativo === 'todos' ? null : ($ativo === '1'))
        ->setTipo($tipoSelecionado);

    // Renderiza e exibe o formulário de pesquisa e a lista de itens.
    $formularioPesquisa = new FormularioPesquisaItensGamer();
    $lista = ContainerVios::fetch(ListaItensGamer::class);
    echo $formularioPesquisa->render($pesquisaEntity) . '<hr>' . $lista->render($pesquisaEntity);
}