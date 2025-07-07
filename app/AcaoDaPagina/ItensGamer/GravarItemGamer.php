<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

// Importa classes necessárias para a ação de gravar itens.
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Constantes para nomes de campos e tabelas.
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\DAO\DAO; // Objeto de Acesso a Dados.
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity; // Entidade Item Gamer.
use Vios\Juridico\App\Entities\ItensGamer\TagEntity; // Entidade Tag.
use Vios\Juridico\App\Excecoes\AvisoParaOUsuarioException; // Para lançar avisos ao usuário.
use Vios\Juridico\App\Formatadores\TextoFormatter; // Para formatar textos.
use Vios\Juridico\App\Routes\UrlRedir; // Para redirecionamento.
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposFactory as CamposFactory; // Fábrica de campos de histórico.
use Vios\Juridico\App\Servicos\Historicos\ItensGamer\ItemGamerHistorico; // Serviço de histórico de Itens Gamer.
use Vios\Juridico\App\Servicos\Historicos\ListaHistorico; // Serviço para listar histórico.
use Vios\Juridico\ContainerVios; // Contêiner de Injeção de Dependência.

/**
 * GravarItemGamer
 *
 * Esta classe é responsável por executar a lógica de salvar um Item Gamer no banco de dados,
 * seja para um novo cadastro ou para a edição de um item existente.
 * Também gerencia a persistência das tags associadas e o registro de histórico.
 */
final class GravarItemGamer
{
    private $consultor; // Serviço de consulta de itens.
    private $dao;       // Objeto de acesso a dados.
    private $historico; // Serviço de histórico de itens.

    /**
     * Construtor da classe GravarItemGamer.
     * Recebe as dependências necessárias para realizar as operações.
     *
     * @param DAO $dao Usado para interagir com o banco de dados.
     * @param ConsultaItensGamer $consultor Usado para consultar itens (ex: pegar dados antigos para histórico).
     */
    public function __construct(
        DAO $dao,
        ConsultaItensGamer $consultor
    ) {
        $this->dao = $dao;
        $this->consultor = $consultor;
        
        // Pega as dependências para o serviço de histórico e o inicializa.
        $camposFactory = ContainerVios::fetch(CamposFactory::class);
        $listaHistorico = ContainerVios::fetch(ListaHistorico::class);
        $this->historico = new ItemGamerHistorico($dao, $listaHistorico, $camposFactory);
    }

    /**
     * Executa a ação de salvar (inserir ou atualizar) um Item Gamer.
     *
     * @param ItemGamerEntity $entity A entidade Item Gamer com os dados a serem salvos.
     * @return UrlRedir Objeto para redirecionamento após a operação.
     * @throws AvisoParaOUsuarioException Se o nome do item for vazio.
     * @throws \Exception Em caso de outros erros.
     */
    public function executa(ItemGamerEntity $entity): UrlRedir
    {
        // Validação básica: o nome do item é obrigatório.
        if (empty($entity->getNome())) {
            throw new AvisoParaOUsuarioException('Obrigatório por nome no item');
        }
        
        // Prepara um array com os dados do item para serem salvos no banco.
        $dados = [
            ItensGamerCamposConstants::NOME => $entity->getNome(),
            ItensGamerCamposConstants::DESCRICAO => TextoFormatter::corrigeHtmlEntity($entity->getDescricao()),
            ItensGamerCamposConstants::TIPO => $entity->getTipo(),
            ItensGamerCamposConstants::COR_EMBLEMA => $entity->getCorEmblema(),
            ItensGamerCamposConstants::QUANTIDADE => $entity->getQuantidade(),
            ItensGamerCamposConstants::PRECO_VENDA => $entity->getPrecoVenda(),
            ItensGamerCamposConstants::ITEM_ATIVO => $entity->isItemAtivo() ? 1 : 0,
        ];

        $itemId = $entity->getId(); // Pega o ID do item da entidade.
        $dadosAntigos = []; // Inicializa array para guardar dados antigos (para histórico).

        // Se o item já existe (ID > 0), busca seus dados atuais para o histórico.
        if ($itemId > 0) {
            $entidadeAntiga = $this->consultor->getById($itemId);
            $dadosAntigos = $entidadeAntiga->toArray();
        }

        // --- Lógica de Inserção ou Atualização do Item Principal ---
        if ($itemId > 0) {
            // Se o item já existe, atualiza seus dados no banco.
            $this->dao->atualiza(
                ItensGamerCamposConstants::TABELA, // Tabela 'itens_gamer'.
                $dados, // Dados a serem atualizados.
                [ItensGamerCamposConstants::ID => $itemId] // Condição WHERE para atualização.
            );
        } else {
            // Se é um novo item, insere no banco e pega o ID gerado.
            $itemId = $this->dao->insere($dados, ItensGamerCamposConstants::TABELA, true);
            // Registra a criação do item no histórico.
            $this->historico->registroHistoricoCriacao($itemId);
        }

        // --- Persistência das Tags Associadas ---
        // Mapeia as entidades Tag para um array de IDs.
        $tagIds = array_map(function (TagEntity $tag) {
            return $tag->getId();
        }, $entity->getTags());
        // Persiste o relacionamento entre o item e suas tags.
        $this->persisteTags($itemId, ...$tagIds);

        // --- Registro de Histórico de Alterações ---
        // Resolve e persiste as alterações do item no histórico.
        $this->historico->resolveHistorico($itemId, $dadosAntigos, $entity->toArray());
        
        // Redireciona para a página principal com uma mensagem de sucesso.
        return new UrlRedir('?pag=sys/itens_gamer/itens_gamer.php', 'Item salvo com sucesso');
    }

    /**
     * Persiste as tags associadas a um Item Gamer.
     * Remove tags antigas e insere as novas.
     *
     * @param int $itemId O ID do Item Gamer.
     * @param int ...$tagIds Uma lista de IDs de tags a serem associadas.
     * @throws AvisoParaOUsuarioException Em caso de erro.
     */
    private function persisteTags(int $itemId, int ...$tagIds)
    {
        // Primeiro, deleta todas as tags existentes para este item na tabela de relacionamento.
        $this->dao->deleta('item_gamer_tags', ['item_gamer_id' => $itemId]);
        
        // Garante que os IDs das tags sejam únicos e inteiros.
        $tagIdsUnicas = array_unique(array_map('intval', $tagIds));

        // Se não há tags para inserir, encerra a função.
        if (empty($tagIdsUnicas)) {
            return;
        }

        // Define uma função anônima para formatar os dados de inserção.
        $callable = function ($tagId) use ($itemId) {
            // Retorna um array de dados para inserção na tabela de relacionamento.
            return ($tagId <= 0 || $itemId <= 0) ? [] : [
                'item_gamer_id' => $itemId,
                'tag_id' => $tagId,
            ];
        };

        // Mapeia os IDs únicos para o formato de inserção e filtra entradas vazias.
        $insert = array_values(array_filter(array_map($callable, $tagIdsUnicas)));

        // Se houver dados para inserir, realiza a inserção em massa.
        if (!empty($insert)) {
            $this->dao->insere($insert, 'item_gamer_tags'); // Tabela de relacionamento.
        }
    }
}