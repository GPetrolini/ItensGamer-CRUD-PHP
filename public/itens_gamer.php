<?php

declare(strict_types=1);

// Importa as classes necessárias para o funcionamento do CRUD de Itens Gamer.
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\DeletaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\FormularioPesquisaItensGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\GravarItemGamer;
use Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer\ListaItensGamer;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Constantes para nomes de campos
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\DAO\DAO;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity; // Entidade para Tags
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposFactory as CamposFactory;
use Vios\Juridico\App\Servicos\Historicos\ItensGamer\ItemGamerHistorico;
use Vios\Juridico\App\Servicos\Historicos\ListaHistorico;
use Vios\Juridico\App\Util\Integer;
use Vios\Juridico\ContainerVios;

// Define a ação a ser executada (ex: 'insert', 'edit', 'del').
$act = $_REQUEST['act'] ?? '';

// --- Lógica para Gravar (Cadastrar ou Editar) Item ---
// Verifica se o formulário foi submetido com o botão 'Gravar'.
if (($_POST['Gravar'] ?? '') === 'Gravar') {
    $form = $_POST['form'] ?? []; // Pega os dados do formulário.

    /** @var GravarItemGamer $gravador */
    // Pega uma instância do gravador de itens.
    $gravador = ContainerVios::fetch(GravarItemGamer::class);

    // Processa os IDs das tags do formulário.
    $tagIds = $form[ItensGamerCamposConstants::TAGS] ?? [];
    // Garante que $tagIds é um array antes de mapear.
    if (!is_array($tagIds)) {
        $tagIds = [];
    }
    // Mapeia os IDs das tags para objetos TagEntity.
    $tagEntities = array_map(function ($tagId) {
        return (new TagEntity())->setId((int)$tagId);
    }, $tagIds);

    // Processa o Preço de Venda (tratamento de vírgula/ponto para float).
    $precoVendaStr = $form[ItensGamerCamposConstants::PRECO_VENDA] ?? '0,00';
    // Substitui vírgulas por pontos.
    $precoFormatado = str_replace(',', '.', $precoVendaStr);
    // Esta linha é redundante, já que o objetivo era substituir ',' por '.', e não '.' por '.'.
    $precoFormatado = str_replace('.', '.', $precoFormatado);
    // Converte a string final para float.
    $precoFinal = (float)$precoFormatado;

    // Cria uma entidade ItemGamerEntity e popula com os dados do formulário.
    $item = (new ItemGamerEntity())
        ->setId(Integer::int($form[ItensGamerCamposConstants::ID] ?? 0))
        ->setNome((string)$form[ItensGamerCamposConstants::NOME] ?? '')
        // Pega a descrição do formulário.
        ->setDescricao((string)$form[ItensGamerCamposConstants::DESCRICAO] ?? '')
        ->setTipo((string)$form[ItensGamerCamposConstants::TIPO] ?? '')
        ->setTags(...$tagEntities) // Passa as entidades Tag.
        ->setCorEmblema((string)$form[ItensGamerCamposConstants::COR_EMBLEMA] ?? '#00000')
        ->setQuantidade(Integer::int($form[ItensGamerCamposConstants::QUANTIDADE] ?? 0))
        // Pega o preço de venda diretamente do form. Nota: o $precoFinal foi calculado, mas não é usado aqui.
        ->setPrecoVenda((float)$form[ItensGamerCamposConstants::PRECO_VENDA] ?? 0.0)
        ->setItemAtivo(($form[ItensGamerCamposConstants::ITEM_ATIVO] ?? 0) === '1');

    // Executa a ação de gravar e redireciona com uma mensagem.
    $gravador->executa($item)->toastRedirect();

// --- Lógica para Exibir Formulário de Inserção ou Edição ---
// Se a ação é 'insert' (novo cadastro) ou 'edit' (edição de um item existente).
} elseif ($act === 'insert' || $act === 'edit') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    // Pega o ID do item da requisição (0 para novo, ID para edição).
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    
    /** @var FormularioItemGamer $formulario */
    // Constrói o formulário de cadastro/edição.
    $formulario = ContainerVios::buildWithoutArg(FormularioItemGamer::class, ['itemGamerId' => $itemGamerId]);
    echo $formulario->__toString();

// --- Lógica para Exibir Formulário em Modo de Visualização ---
// Se a ação é 'view'.
} elseif ($act === 'view') {
    $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
    // Pega o ID do item da requisição.
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    
    /** @var FormularioItemGamer $formulario */
    // Constrói o formulário para visualização.
    $formulario = ContainerVios::BuildWithoutArg(FormularioItemGamer::class, ['itemGamerId' => $itemGamerId]);
    // Esta linha tenta desabilitar a edição do formulário.
    $formulario->desabilitarEdicao();
    echo $formulario->__toString();

// --- Lógica para Deletar Item ---
// Se a ação é 'del'.
} elseif ($act === 'del') {
    /** @var DeletaItensGamer $deletador */
    // Pega uma instância do deletador de itens.
    $deletador = ContainerVios::fetch(DeletaItensGamer::class);
    // Pega o ID do item a ser deletado.
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    // Executa a ação de deletar e redireciona.
    $deletador->executa($itemGamerId)->toastRedirect();

// --- Lógica para Exibir Histórico de Item ---
// Se a ação é 'history'.
} elseif ($act === 'history') {
    // Pega as dependências para o serviço de histórico.
    $dao = ContainerVios::fetch(DAO::class);
    $listaHistorico = ContainerVios::fetch(ListaHistorico::class);
    $camposFactory = ContainerVios::fetch(CamposFactory::class);
    /** @var ItemGamerHistorico $historicoService */
    // Cria uma instância do serviço de histórico de Itens Gamer.
    $historicoService = new ItemGamerHistorico($dao, $listaHistorico, $camposFactory); // Instancia o serviço de histórico.
    // Pega o ID do item para o qual o histórico será exibido.
    $itemGamerId = Integer::int($_REQUEST[ItensGamerCamposConstants::ID] ?? 0);
    // Exibe a lista de histórico.
    echo $historicoService->listaHistorico($itemGamerId);

// --- Lógica Padrão: Exibir Formulário de Pesquisa e Lista de Itens ---
// Se nenhuma ação específica foi solicitada, exibe a tela principal de pesquisa e listagem.
} else {
    // Pega dados de pesquisa do formulário.
    $pesquisaDados = $_REQUEST['pesq'] ?? [];
    
    // Leitura dos valores dos campos de pesquisa.
    $busca = $pesquisaDados['busca'] ?? null;
    $id = $pesquisaDados['buscaId'] ?? 0; // Se houver um campo de busca por ID
    $ativo = $pesquisaDados['comboarr'] ?? 'todos'; // Valor do select "Item ativo?"
    $tipoSelecionado = $pesquisaDados['comboarr_multi'] ?? []; // Tipo do item (multi-select)
    $tags = $pesquisaDados['comboarr_tag'] ?? []; // Tags (multi-select)
    // Pega o valor do campo "Cor do Item" (campo de texto).
    $cor = $pesquisaDados[ItensGamerCamposConstants::COR_EMBLEMA] ?? null; 
    $precoMin = (float)($pesquisaDados['valor_minimo'] ?? 0.0);
    $precoMax = (float)($pesquisaDados['valor_maximo'] ?? 0.0);

    // Cria a entidade de pesquisa e popula com os valores dos filtros.
    $pesquisaEntity = (new PesquisaItensGamerEntity())
        ->setNome($busca)
        ->setId(Integer::int($pesquisaDados[ItensGamerCamposConstants::ID] ?? 0)) // Se o ID for um filtro separado
        ->setTags($tags)
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