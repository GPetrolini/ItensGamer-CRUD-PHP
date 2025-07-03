<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Campos\CampoInteiro;
use Vios\Juridico\App\Campos\CampoNumerico;
use Vios\Juridico\App\Campos\Tipos\CampoCheckbox;
use Vios\Juridico\App\Campos\Tipos\CampoComboArr;
use Vios\Juridico\App\Campos\Tipos\CampoHidden;
use Vios\Juridico\App\Campos\Tipos\CampoTextArea;
use Vios\Juridico\App\Campos\Tipos\CampoTextoUmaLinha;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\View\Formulario;

final class FormularioItemGamer extends Formulario
{
    /**
     * @var ConsultaItensGamer objeto de acesso a dados (DAO = Data Access Object)
     */
    private $consulta;
    /**
     * @var array
     */
    private $listaDeCampos;

    /**
     * O construtor nada mais é que o ponto de partida
     * Ele recebe as ferramentas que precisa (DAO) e a info do item que precisa ser editado
     * @throws \Exception
     */
    public function __construct(ConsultaItensGamer $consulta, int $itemGamerId)
    {
        // Guarda o dao para usar depos
        $this->consulta = $consulta;
        $this->listaDeCampos = $this->campos($itemGamerId);
        // chama o construtor da classe "mae" (Formulário)
        parent::__construct('Cadastro de Item Gamer', $this->listaDeCampos);
    }

    /**
     * @throws \Exception
     */
    public function desabilitarEdicao(): FormularioItemGamer
    {
        foreach ($this->listaDeCampos as $campo) {
            if (method_exists($campo, 'adicionaDados')) {
                $campo->adicionaDados(['disabled' => true]);
            }
        }
        $this->removeHtmlArea();
        $this->addOpcoes(['submit' => 'N']);
        return $this;
    }

    /**
     * Metodo que cria o formulário
     * ele retorna um array de objeto, onde cada objeto é um campo do formulário
     * @throws \Exception
     */
    private function campos($itemGamerId): array
    {
        $entidade = ($itemGamerId > 0)
            ? $this->consulta->getById($itemGamerId)
            : new ItemGamerEntity();

        $tagsDoItemIds = array_map(
            function (TagEntity $tag) {
                return $tag->getId();
            },
            $entidade->getTags()
        );
        $tagsDisponiveis = $this->consulta->getTagsDisponiveis();
        $precoFormatado = number_format(
            $entidade->getPrecoVenda() ?? 0.0,
            2,
            ',',
            '.,'
        );
        return [
            new CampoHidden(
                ItensGamerCamposConstants::ID_NAME,
                $entidade->getId() ?? 0
            ),
            (new CampoTextoUmaLinha(
                ItensGamerCamposConstants::NOME_NAME,         // atributo 'name' do HTMl
                ItensGamerCamposConstants::NOME_LABEL,        // o 'label' que o usuário ve
                $entidade->getNome() ?? ''                   // o valor inicial pego pela entidade
            ))
                ->required()                                      // metodo fluente para dizer que o campo é obrigatório
                -> help('Digite o nome principal do produto')    // Adiciona um texto de ajuda
            ,
            (new CampoTextArea(
                ItensGamerCamposConstants::DESCRICAO_NAME,
                ItensGamerCamposConstants::DESCRICAO_LABEL,
                $entidade->getDescricao() ?? ''
            ))
                ->help('Descreva o item')
            ,
            (new CampoComboArr(
                ItensGamerCamposConstants::TIPO_NAME,
                ItensGamerCamposConstants::TIPO_LABEL,
                $entidade->getTipo() ?? '',
                ItensGamerCamposConstants::TIPO_OPCOES
            ))->required(),
            (new CampoComboArr(
                ItensGamerCamposConstants::TAGS_NAME . 'tags[]',
                ItensGamerCamposConstants::TAGS_LABEL,
                $tagsDoItemIds,
                $tagsDisponiveis
            ))->campoComboMultiplo(),
            (new CampoTextoUmaLinha(
                ItensGamerCamposConstants::COR_EMBLEMA_NAME,
                ItensGamerCamposConstants::COR_EMBLEMA_LABEL,
                $entidade->getCorEmblema() ?? ''
            ))
                ->help('Digite a cor do item')
            ,
            (new CampoInteiro(
                ItensGamerCamposConstants::QUANTIDADE_NAME,
                ItensGamerCamposConstants::QUANTIDADE_LABEL,
                $entidade->getQuantidade() ?? 0
            ))
            ,
            (new CampoNumerico(
                ItensGamerCamposConstants::PRECO_VENDA_NAME,
                ItensGamerCamposConstants::PRECO_VENDA_LABEL,
                $precoFormatado
            ))
            ,
            (new CampoCheckbox(
                ItensGamerCamposConstants::ITEM_ATIVO_NAME,
                ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
                $entidade->isItemAtivo() ?? false
            ))
        ];
    }
}
