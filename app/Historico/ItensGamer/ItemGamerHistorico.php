<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Servicos\Historicos\ItensGamer;

// Importa classes necessárias para o funcionamento do histórico.
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Constantes para nomes de campos e tabelas.
use Vios\Juridico\App\DAO\DAO;
use Vios\Juridico\App\Excecoes\IdNaoInformadoException;
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCampos;
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposEntidadeRelacionalUtil; // Utilitário para histórico de relacionamentos.
use Vios\Juridico\App\Servicos\Historicos\HistoricoResolver; // Classe base que lida com a lógica principal do histórico.
use Vios\Juridico\App\Servicos\Historicos\HistoricoVO;     // Objeto que representa um registro de histórico.
use Vios\Juridico\App\Servicos\Historicos\HistoricoEntidadesRelacionais;
use Vios\Juridico\App\Servicos\Historicos\ListaHistorico; // Classe que renderiza a lista de histórico.
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposFactory as CamposFactory; // Fábrica para criar tipos de campos de histórico.

/**
 * ItemGamerHistorico
 *
 * Esta classe é responsável por gerenciar o histórico de alterações dos Itens Gamer.
 * Ela estende HistoricoResolver, que cuida da lógica genérica de detecção e persistência.
 */
final class ItemGamerHistorico extends HistoricoResolver
{
    private $listaHistorico; // Usado para exibir a lista de histórico.
    private const TABELA_HISTORICO = 'itens_gamer_historico'; // Nome da tabela no banco de dados para o histórico dos itens.
    private const FK_TABELA_HISTORICO = 'item_gamer_id';     // Chave estrangeira que liga o histórico ao item principal.
    private $camposFactory; // A fábrica que nos ajuda a criar os diferentes tipos de "comparadores" de campo.

    /**
     * Construtor da classe de histórico.
     * Recebe as "ferramentas" necessárias para funcionar.
     *
     * @param DAO $dao Usado para interagir com o banco de dados.
     * @param ListaHistorico $listaHistorico Usado para renderizar a interface da lista de histórico.
     * @param CamposFactory $camposFactory Usado para criar os objetos que definem como cada campo será rastreado.
     */
    public function __construct(DAO $dao, ListaHistorico $listaHistorico, CamposFactory $camposFactory)
    {
        $this->listaHistorico = $listaHistorico;
        $this->camposFactory = $camposFactory;
        $entidadeRelacionais = new HistoricoEntidadesRelacionais($dao); // Para lidar com histórico de entidades relacionadas (ex: N:M).
        
        // Chama o construtor da classe pai (HistoricoResolver)
        // Passa o DAO, o serviço de relações e os campos que queremos observar.
        parent::__construct($dao, $entidadeRelacionais, ...$this->getCampos());
    }

    /**
     * Prepara e retorna o HTML com a lista do histórico de um item específico.
     *
     * @param int $itemGamerId O ID do Item Gamer para o qual queremos ver o histórico.
     * @return string O HTML da tabela de histórico.
     */
    public function listaHistorico(int $itemGamerId): string
    {
        // Delega a renderização da tabela de histórico para a classe ListaHistorico.
        return $this->listaHistorico->render(self::TABELA_HISTORICO, self::FK_TABELA_HISTORICO, $itemGamerId);
    }

    /**
     * Registra um evento de criação de um novo Item Gamer no histórico.
     *
     * @param int $id O ID do Item Gamer recém-criado.
     * @throws IdNaoInformadoException Se o ID for inválido.
     */
    public function registroHistoricoCriacao(int $id): void
    {
        // Se o ID for 0 ou menor, não há item válido para registrar.
        if ($id <= 0) {
            return;
        }
        
        // Cria um objeto HistoricoVO para este registro.
        $instancia = $this->instanciaHistoricoVO($id);
        
        // Adiciona um texto fixo para o evento de criação.
        $instancia->addTexto('Registro do item criado no sistema');
        
        // Pede à classe pai para persistir este registro de histórico no banco de dados.
        parent::persisteHistorico($instancia);
    }

    /**
     * Cria e retorna uma nova instância de HistoricoVO para um determinado item.
     *
     * @param int $id O ID do Item Gamer ao qual o histórico se refere.
     * @return HistoricoVO O objeto de valor do histórico.
     * @throws IdNaoInformadoException Se o ID for inválido.
     */
    protected function instanciaHistoricoVO(int $id): HistoricoVO
    {
        // Retorna um novo HistoricoVO, configurado com o ID do item, a FK e o nome da tabela.
        return new HistoricoVO($id, self::FK_TABELA_HISTORICO, self::TABELA_HISTORICO);
    }

    /**
     * Retorna o nome da tabela onde os registros de histórico serão persistidos.
     *
     * @return string O nome da tabela de histórico.
     */
    protected function tabelaPersistenciaHistorico(): string
    {
        return self::TABELA_HISTORICO;
    }

    /**
     * Define quais chaves (colunas) devem ser ignoradas ao rastrear alterações.
     * Usa constantes para referenciar os nomes das colunas.
     *
     * @return string[] Um array com os nomes das chaves a serem ignoradas.
     */
    protected function chavesIgnoradas(): array
    {
        return [ItensGamerCamposConstants::ID, ItensGamerCamposConstants::DATA_CADASTRO];
    }

    /**
     * Define e retorna quais campos do Item Gamer esta classe de histórico deve observar.
     * Usa a CamposFactory para criar diferentes tipos de "comparadores" de campo.
     *
     * @return HistoricoCampos[] Um array de objetos HistoricoCampos.
     */
    protected function getCampos(): array
    {
        // Cria um comparador para campos de comparação direta (strings, números).
        $comparacaoDireta = $this->camposFactory->instanciaHistoricoCamposComparacaoDireta();
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::NOME,        // Nome da coluna no DB.
            ItensGamerCamposConstants::NOME_LABEL   // Label amigável para exibição.
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::DESCRICAO,
            ItensGamerCamposConstants::DESCRICAO_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::TIPO,
            ItensGamerCamposConstants::TIPO_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::COR_EMBLEMA,
            ItensGamerCamposConstants::COR_EMBLEMA_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::QUANTIDADE,
            ItensGamerCamposConstants::QUANTIDADE_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::PRECO_VENDA,
            ItensGamerCamposConstants::PRECO_VENDA_LABEL
        );

        // Cria um comparador para campos booleanos (Sim/Não, 0/1).
        $booleanos = $this->camposFactory->instanciaHistoricoCamposBooleanos();
        $booleanos->registraCampo(
            ItensGamerCamposConstants::ITEM_ATIVO,
            ItensGamerCamposConstants::ITEM_ATIVO_LABEL
        );

        // Cria um comparador para campos que representam relacionamentos múltiplos (como Tags).
        $comparacaoMultipla = $this->camposFactory->instanciaHistoricoCamposEntidadesRelacionamentoMultiplo();
        // Define como rastrear as Tags, usando o utilitário para entidades relacionais.
        $comparacaoMultipla->registraCampo(
            ItensGamerCamposConstants::TAGS,        // Nome da coluna no DB.
            HistoricoCamposEntidadeRelacionalUtil::instanciaEntidadeRelacionalTag(ItensGamerCamposConstants::TAGS_LABEL) // Label amigável.
        );
        
        // Retorna todos os comparadores de campo que devem ser usados para este histórico.
        return [
            $comparacaoDireta,
            $booleanos,
            $comparacaoMultipla
        ];
    }
}