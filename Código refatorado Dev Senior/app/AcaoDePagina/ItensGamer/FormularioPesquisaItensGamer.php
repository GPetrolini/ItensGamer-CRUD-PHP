<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\FormularioPesquisa\FormularioPesquisa;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;

final class FormularioPesquisaItensGamer
{
    private $consultor;

    public function __construct(ConsultaItensGamer $consultor)
    {
        $this->consultor = $consultor;
    }

    public function render(PesquisaItensGamerEntity $pesquisa): string
    {
        return (new FormularioPesquisa())
            ->busca($pesquisa->getNome())
            ->buscaId($pesquisa->getId())
            ->comboArrayMultiplo(
                ItensGamerCamposConstants::TIPO_OPCOES,
                ItensGamerCamposConstants::TIPO_LABEL,
                $pesquisa->getTipo()->getAdded()
            )
            ->comboArray(
                ['todos' => 'Todos', 'Não', 'Sim'],
                ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
                is_null($pesquisa->isItemAtivo()) ? 'todos' : ($pesquisa->isItemAtivo() ? '1' : '0'),
                ItensGamerCamposConstants::ITEM_ATIVO
            )
            ->campoTextGenerico(
                ItensGamerCamposConstants::COR_EMBLEMA_LABEL,
                ItensGamerCamposConstants::COR_EMBLEMA,
                $pesquisa->getCorEmblema(),
                FormularioItemGamer::avisoCor()
            )
            ->faixaValor($pesquisa->getPrecoMinimo(), $pesquisa->getPrecoMaximo())
            ->comboArrayTag(
                $this->consultor->getTagsDisponiveis(),
                ItensGamerCamposConstants::TAGS_LABEL,
                $pesquisa->getTags()->getAdded()
            )
            ->__toString();
    }
}
