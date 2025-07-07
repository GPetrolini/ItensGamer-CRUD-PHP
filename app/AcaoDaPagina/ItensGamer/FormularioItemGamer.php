<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

// Importa classes necessárias para construir o formulário.
use Vios\Juridico\App\Campos\CampoInteiro;
use Vios\Juridico\App\Campos\CampoNumerico;
use Vios\Juridico\App\Campos\Tipos\CampoCheckbox;
use Vios\Juridico\App\Campos\Tipos\CampoComboArr;
use Vios\Juridico\App\Campos\Tipos\CampoHidden;
use Vios\Juridico\App\Campos\Tipos\CampoTextArea;
use Vios\Juridico\App\Campos\Tipos\CampoTextoUmaLinha;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Constantes para nomes de campos.
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer; // Para buscar dados de itens e tags.
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity; // Entidade Item Gamer.
use Vios\Juridico\App\Entities\ItensGamer\TagEntity; // Entidade Tag.
use Vios\Juridico\App\View\Formulario; // Classe base para formulários.

/**
 * FormularioItemGamer
 *
 * Esta classe é responsável por definir e criar o formulário de cadastro e edição
 * de um Item Gamer. Ela utiliza componentes de campo para construir o HTML.
 */
final class FormularioItemGamer extends Formulario
{
    /**
     * @var ConsultaItensGamer Objeto de acesso a dados (DAO) para consultas.
     */
    private $consulta;
    /**
     * @var array Armazena a lista de objetos Campo que compõem o formulário.
     */
    private $listaDeCampos;

    /**
     * Construtor do formulário.
     * Recebe o serviço de consulta e o ID do item a ser editado (se for edição).
     * @param ConsultaItensGamer $consulta Serviço para buscar dados do item.
     * @param int $itemGamerId O ID do item a ser editado (0 para novo cadastro).
     * @throws \Exception Em caso de erro na criação dos campos.
     */
    public function __construct(ConsultaItensGamer $consulta, int $itemGamerId)
    {
        $this->consulta = $consulta;
        // Cria a lista de campos do formulário.
        $this->listaDeCampos = $this->campos($itemGamerId);
        // Chama o construtor da classe pai (Formulario) passando o título e os campos.
        parent::__construct('Cadastro de Item Gamer', $this->listaDeCampos);
    }

    /**
     * Desabilita todos os campos do formulário e remove o botão de submit.
     * Usado para modo de visualização (apenas leitura).
     * @return FormularioItemGamer Retorna a própria instância para encadeamento.
     * @throws \Exception Em caso de erro ao adicionar dados.
     */
    public function desabilitarEdicao(): FormularioItemGamer
    {
        // Percorre cada campo do formulário.
        foreach ($this->listaDeCampos as $campo) {
            // Se o campo tiver o método 'adicionaDados', desabilita-o.
            if (method_exists($campo, 'adicionaDados')) {
                $campo->adicionaDados(['disabled' => true]);
            }
        }
        // Remove o botão de submit do formulário.
        $this->addOpcoes(['submit' => 'N']);
        return $this;
    }

    /**
     * Método que define e cria os campos do formulário.
     * Ele retorna um array de objetos, onde cada objeto é um campo do formulário.
     * @param int $itemGamerId O ID do item (para preencher campos em edição).
     * @return array Um array de objetos Campo.
     * @throws \Exception Em caso de erro na consulta de tags.
     */
    private function campos($itemGamerId): array
    {
        // Se $itemGamerId > 0, busca a entidade existente; caso contrário, cria uma nova.
        $entidade = ($itemGamerId > 0)
            ? $this->consulta->getById($itemGamerId)
            : new ItemGamerEntity();

        // Mapeia as entidades Tag do item para um array de seus IDs.
        $tagsDoItemIds = array_map(
            function (TagEntity $tag) {
                return $tag->getId();
            },
            $entidade->getTags()
        );
        // Busca todas as tags disponíveis para popular o campo de seleção de tags.
        $tagsDisponiveis = $this->consulta->getTagsDisponiveis();
        // Formata o preço de venda para exibição no padrão brasileiro (vírgula como decimal).
        $precoFormatado = number_format(
            $entidade->getPrecoVenda() ?? 0.0,
            2, // 2 casas decimais.
            ',', // Separador decimal.
            '.' // Separador de milhares.
        );
        
        // Retorna o array de objetos Campo que representam o formulário.
        return [
            // Campo oculto para o ID do item.
            new CampoHidden(
                ItensGamerCamposConstants::ID_NAME,
                $entidade->getId() ?? 0
            ),
            // Campo de texto para o Nome do item.
            (new CampoTextoUmaLinha(
                ItensGamerCamposConstants::NOME_NAME,         // Atributo 'name' do HTML.
                ItensGamerCamposConstants::NOME_LABEL,        // O 'label' que o usuário vê.
                $entidade->getNome() ?? ''                   // O valor inicial pego pela entidade.
            ))
                ->required()                                      // Campo obrigatório.
                -> help('Digite o nome principal do produto')    // Texto de ajuda.
            ,
            // Campo de área de texto para a Descrição Detalhada.
            (new CampoTextArea(
                ItensGamerCamposConstants::DESCRICAO_NAME,
                ItensGamerCamposConstants::DESCRICAO_LABEL,
                $entidade->getDescricao() ?? ''
            ))
                ->help('Descreva o item')
            ,
            // Campo de seleção (combobox) para o Tipo do item.
            (new CampoComboArr(
                ItensGamerCamposConstants::TIPO_NAME,
                ItensGamerCamposConstants::TIPO_LABEL,
                $entidade->getTipo() ?? '',
                ItensGamerCamposConstants::TIPO_OPCOES
            ))->required(), // Campo obrigatório.
            // Campo de seleção múltipla (combobox) para as Tags.
            (new CampoComboArr(
                ItensGamerCamposConstants::TAGS_NAME,
                ItensGamerCamposConstants::TAGS_LABEL,
                $tagsDoItemIds, // IDs das tags já associadas ao item.
                $tagsDisponiveis // Todas as tags disponíveis para seleção.
            ))->campoComboMultiplo(), // Configura como campo de seleção múltipla.
            // Campo de texto para a Cor do item.
            (new CampoTextoUmaLinha(
                ItensGamerCamposConstants::COR_EMBLEMA_NAME,
                ItensGamerCamposConstants::COR_EMBLEMA_LABEL,
                $entidade->getCorEmblema() ?? ''
            ))
                ->help('Digite a cor do item') // Texto de ajuda.
            ,
            // Campo de número inteiro para a Quantidade em estoque.
            (new CampoInteiro(
                ItensGamerCamposConstants::QUANTIDADE_NAME,
                ItensGamerCamposConstants::QUANTIDADE_LABEL,
                $entidade->getQuantidade() ?? 0
            ))
            ,
            // Campo numérico para o Preço de Venda.
            (new CampoNumerico(
                ItensGamerCamposConstants::PRECO_VENDA_NAME,
                ItensGamerCamposConstants::PRECO_VENDA_LABEL,
                $precoFormatado // Valor formatado para exibição.
            ))
            ,
            // Campo de checkbox para "Item ativo?".
            (new CampoCheckbox(
                ItensGamerCamposConstants::ITEM_ATIVO_NAME,
                ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
                $entidade->isItemAtivo() ?? false
            ))
        ];
    }
}