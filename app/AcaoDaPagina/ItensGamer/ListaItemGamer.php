<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

// Importa classes necessárias para a listagem e exibição da tabela.
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\Cores; // Para manipulação de cores (escurecer, etc.).
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity; // Entidade que representa um Item Gamer.
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity; // Entidade para os filtros de pesquisa.
use Vios\Juridico\App\Entities\ItensGamer\TagEntity; // Entidade para Tags.
use Vios\Juridico\App\Excecoes\AvisoParaOUsuarioException; // Para lançar avisos ao usuário.
use Vios\Juridico\App\View\ElementosVisuais\EtiquetaCircular; // Componente visual para exibir cores.
use Vios\Juridico\App\View\OpcoesGeraTabela; // Classe para configurar opções da tabela gerada.
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Constantes para nomes de campos.

/**
 * ListaItensGamer
 *
 * Esta classe é responsável por preparar os dados e as configurações
 * para a exibição da tabela de Itens Gamer. Ela define as colunas,
 * formata os dados para exibição e configura as ações disponíveis.
 */
final class ListaItensGamer
{
    private $consulta; // Instância do serviço de consulta para buscar os itens.

    // Caminho da página principal do CRUD de Itens Gamer, usado para links.
    private const CAMINHO_PAGINA = 'sys/itens_gamer/itens_gamer.php';

    /**
     * Construtor da classe de listagem.
     * @param ConsultaItensGamer $consulta Recebe uma instância do serviço de consulta de itens.
     */
    public function __construct(ConsultaItensGamer $consulta)
    {
        $this->consulta = $consulta;
    }

    /**
     * Renderiza a tabela de itens gamer.
     * @param PesquisaItensGamerEntity $pesquisa Objeto com os filtros de pesquisa aplicados.
     * @return string O HTML da tabela gerada.
     * @throws \Throwable Em caso de erros durante a renderização.
     */
    public function render(PesquisaItensGamerEntity $pesquisa): string
    {
        // Delega a geração da tabela para a função global 'geratabela_wrapper'.
        // Passa os dados formatados, as definições das colunas e as opções da tabela.
        return geratabela_wrapper($this->result($pesquisa), $this->campos(), $this->opcoes());
    }

    /**
     * Define as colunas que serão exibidas na tabela e seus respectivos cabeçalhos.
     * @return array Um array associativo onde a chave é o nome da coluna de dados e o valor é o cabeçalho.
     */
    public function campos(): array
    {
        return [
            // Coluna para exibir a etiqueta colorida da cor do item.
            'cor_display' => 'Etiqueta',
            ItensGamerCamposConstants::ID => ItensGamerCamposConstants::ID_LABEL,
            ItensGamerCamposConstants::NOME => ItensGamerCamposConstants::NOME_LABEL,
            'descricao_curta' => ItensGamerCamposConstants::DESCRICAO_LABEL,
            ItensGamerCamposConstants::TIPO => ItensGamerCamposConstants::TIPO_LABEL,
            'tags_formatadas' => ItensGamerCamposConstants::TAGS_LABEL,
            ItensGamerCamposConstants::QUANTIDADE => ItensGamerCamposConstants::QUANTIDADE_LABEL,
            // Coluna para exibir o preço formatado.
            'preco_formatado' => ItensGamerCamposConstants::PRECO_VENDA_LABEL,
            ItensGamerCamposConstants::ITEM_ATIVO => ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
        ];
    }

    /**
     * Define as opções gerais da tabela, como título e botões de ação.
     * @return array Um array de opções para a função geratabela_wrapper.
     */
    public function opcoes(): array
    {
        $opcoes = [];
        // Adiciona o título da tabela.
        $opcoes = OpcoesGeraTabela::addTitulo($opcoes, 'Itens Gamers Cadastrados');
        // Adiciona o botão de inserir novo item.
        $opcoes = OpcoesGeraTabela::addBotaoInserir($opcoes, self::CAMINHO_PAGINA);
        
        // Adiciona link para visualizar item (modo leitura).
        $opcoes = OpcoesGeraTabela::addLink(
            $opcoes,
            'vis', // Tipo de link 'visualizar'.
            [ItensGamerCamposConstants::ID, 'act' => 'view'], // Parâmetros na URL (ID e ação 'view').
            self::CAMINHO_PAGINA, // Página de destino.
            'Visualizar' // Título do botão.
        );
        // Adiciona link para visualizar histórico.
        $opcoes = OpcoesGeraTabela::addLink(
            $opcoes,
            'history', // Tipo de link 'histórico'.
            [ItensGamerCamposConstants::ID], // Parâmetros na URL (apenas ID).
            self::CAMINHO_PAGINA, // Página de destino.
            'Visualizar histórico', // Título do botão.
            ['act' => 'history'] // Parâmetro 'act' explícito para o controlador.
        );
        // Adiciona link para editar item.
        $opcoes = OpcoesGeraTabela::addLink($opcoes, 'edit', [ItensGamerCamposConstants::ID], self::CAMINHO_PAGINA);
        // Adiciona link para deletar item.
        $opcoes = OpcoesGeraTabela::addLink($opcoes, 'del', [ItensGamerCamposConstants::ID], self::CAMINHO_PAGINA);
        
        // Adiciona link para "Visualizar diferente" (com estilo laranja).
        // Este é o botão "Visualizar Dois" que discutimos.
        $opcoes = OpcoesGeraTabela::addLink(
            $opcoes,
            'vis', // Tipo de link 'visualizar'.
            [ItensGamerCamposConstants::ID, 'act' => 'visualizar_dois'], // Parâmetros na URL (ID e ação 'visualizar_dois').
            self::CAMINHO_PAGINA, // Página de destino.
            'Visualizar diferente', // Título do botão.
            ['btnClass' => 'btn-warning'] // Classe CSS para deixar o botão laranja.
        );
        return $opcoes;
    }

    /**
     * Prepara e formata os dados dos itens para serem exibidos na tabela.
     * @param PesquisaItensGamerEntity $pesquisa Objeto com os filtros de pesquisa.
     * @return array Um array de itens, onde cada item é um array associativo com os dados formatados.
     * @throws AvisoParaOUsuarioException Em caso de avisos durante o processamento.
     * @throws \Exception Em caso de erros gerais.
     */
    private function result(PesquisaItensGamerEntity $pesquisa): array
    {
        // Busca os itens do banco de dados com base nos filtros.
        $itensDoBanco = $this->consulta->getItensGamer($pesquisa);
        
        // Define uma função anônima (callable) para formatar cada ItemGamerEntity.
        $callable = function (ItemGamerEntity $entity) {
            // Pega as entidades de Tags do item.
            $tagEntities = $entity->getTags();
            // Mapeia as entidades Tag para um array contendo apenas os nomes das tags.
            $tagNomes = array_map(function (TagEntity $tag) {
                return $tag->getNome();
            }, $tagEntities);

            // Pega a cor do emblema do item, com fallback para '#ccc'.
            // htmlspecialchars é usado para evitar problemas com caracteres especiais no HTML.
            $cor = htmlspecialchars($entity->getCorEmblema() ?? '#ccc');

            // Retorna um array associativo com os dados do item formatados para a tabela.
            return [
                ItensGamerCamposConstants::ID => $entity->getId(),
                ItensGamerCamposConstants::NOME => $entity->getNome(),
                ItensGamerCamposConstants::TIPO => $entity->getTipo(),
                ItensGamerCamposConstants::QUANTIDADE => $entity->getQuantidade(),
                // Limita a descrição a 40 caracteres e adiciona '...'
                'descricao_curta' => mb_substr($entity->getDescricao() ?? '', 0, 40) . '...',
                // Concatena os nomes das tags com vírgula e espaço.
                'tags_formatadas' => implode(', ', $tagNomes),
                // Formata o preço de venda para o padrão brasileiro (R$ X.XXX,XX).
                'preco_formatado' => 'R$' . number_format($entity->getPrecoVenda() ?? 0, 2, ',', '.'),
                // Cria e renderiza uma etiqueta circular para a cor do item.
                'cor_display' => (new EtiquetaCircular(
                    $cor, // Cor principal da etiqueta.
                    Cores::darken($cor, 20), // Cor mais escura para o contorno/sombra.
                    'vj-icon fa-steam-square', // Ícone a ser exibido dentro da etiqueta.
                    "Cor:{$cor}" // Texto que aparece no tooltip (ajuda).
                ))->__toString(),
                // Converte o booleano 'item_ativo' para 'Sim' ou 'Não'.
                'ativo_formatado' => $entity->isItemAtivo() ? 'Sim' : 'Não',
            ];
        };
        // Aplica a função de formatação a cada item retornado do banco de dados.
        return array_map($callable, $itensDoBanco);
    }
}