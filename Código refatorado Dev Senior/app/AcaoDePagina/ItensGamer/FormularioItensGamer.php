<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Campos\Campo;
use Vios\Juridico\App\Campos\CampoInteiro;
use Vios\Juridico\App\Campos\CampoNumerico;
use Vios\Juridico\App\Campos\Tipos\CampoCheckbox;
use Vios\Juridico\App\Campos\Tipos\CampoComboArr;
use Vios\Juridico\App\Campos\Tipos\CampoCor;
use Vios\Juridico\App\Campos\Tipos\CampoHidden;
use Vios\Juridico\App\Campos\Tipos\CampoTextArea;
use Vios\Juridico\App\Campos\Tipos\CampoTextoUmaLinha;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\Formatadores\Monetario;
use Vios\Juridico\App\View\ElementosVisuais\HtmlTag\Br;
use Vios\Juridico\App\View\Formulario;

final class FormularioItemGamer extends Formulario
{
    private $consulta;

    private $listaDeCampos;

    public function __construct(ConsultaItensGamer $consulta, int $itemGamerId)
    {
        $this->consulta = $consulta;
        $this->listaDeCampos = $this->campos($itemGamerId);

        parent::__construct('Cadastro de Item Gamer', $this->listaDeCampos);
    }

    public function desabilitarEdicao(): self
    {
        foreach ($this->listaDeCampos as $campo) {
            if ($campo instanceof Campo) {
                $campo->adicionaDados(['disabled' => true]);
            }
        }

        $this->addOpcoes(['submit' => 'N']);

        return $this;
    }

    private function campos(int $itemGamerId): array
    {
        $callableTagsItem = function (TagEntity $tag): int {
            return $tag->getId();
        };

        $entidade = $this->consulta->getById($itemGamerId);

        return [
            new CampoHidden(ItensGamerCamposConstants::ID_NAME, $entidade->getId() ?? 0),
            (new CampoTextoUmaLinha(
                ItensGamerCamposConstants::NOME_NAME,
                ItensGamerCamposConstants::NOME_LABEL,
                $entidade->getNome() ?? ''
            ))
                ->required()
                -> help('Digite o nome principal do produto')
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
                $entidade->getTipo(),
                ItensGamerCamposConstants::TIPO_OPCOES
            ))
                ->required()
            ,
            (new CampoComboArr(
                ItensGamerCamposConstants::TAGS_NAME,
                ItensGamerCamposConstants::TAGS_LABEL,
                array_map($callableTagsItem, $entidade->getTags()),
                $this->consulta->getTagsDisponiveis()
            ))
                ->campoComboMultiplo()
            ,
            (new CampoCor(
                ItensGamerCamposConstants::COR_EMBLEMA_NAME,
                ItensGamerCamposConstants::COR_EMBLEMA_LABEL,
                $entidade->getCorEmblema()
            ))
                ->help(self::avisoCor())
            ,
            new CampoInteiro(
                ItensGamerCamposConstants::QUANTIDADE_NAME,
                ItensGamerCamposConstants::QUANTIDADE_LABEL,
                $entidade->getQuantidade()
            ),
            new CampoNumerico(
                ItensGamerCamposConstants::PRECO_VENDA_NAME,
                ItensGamerCamposConstants::PRECO_VENDA_LABEL,
                Monetario::valorParaMoeda($entidade->getPrecoVenda(), '')
            ),
            new CampoCheckbox(
                ItensGamerCamposConstants::ITEM_ATIVO_NAME,
                ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
                $entidade->isItemAtivo() ?? true
            ),
        ];
    }

    public static function avisoCor(): string
    {
        return implode((new Br())->__toString(), [
            '#0750f9 = Teclado',
            '#7ffb02 = Mouse',
            '#972ad2 = Monitor',
            '#fa0bae = Fone',
            '#4656a4 = Cadeira',
        ]);
    }
}
