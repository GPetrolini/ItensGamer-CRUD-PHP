<?php
declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\FormularioPesquisa\FormularioPesquisa;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\ContainerVios;

final class FormularioPesquisaItensGamer
{
    public function render(PesquisaItensGamerEntity $pesquisa): string
    {
        $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
        $tagsDisponiveis = $consulta->getTagsDisponiveis();
        return (new FormularioPesquisa())
           ->busca(
               $pesquisa->getNome() ?? ''
           )

            ->comboArrayMultiplo(
                ItensGamerCamposConstants::TIPO_OPCOES,
                ItensGamerCamposConstants::TIPO_LABEL,
                $pesquisa->getTipo() ?? []
            )
            ->comboArray(
                ['todos' => 'Todos', 'Não', 'Sim'],
                ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
                is_null($pesquisa->isItemAtivo()) ? 'todos' : ($pesquisa->isItemAtivo() ? '1' : '0'),
                'item_ativo'
            )
            ->campoTextGenerico(
                ItensGamerCamposConstants::COR_EMBLEMA_LABEL,
                'cor_emblema',
                $pesquisa->getCorEmblema() ?? ''
            )
            ->faixaValor(
                $pesquisa->getPrecoMinimo() ?? 0.0,
                $pesquisa->getPrecoMaximo() ?? 0.0
            )
            ->comboArrayTag(
                $tagsDisponiveis,
                ItensGamerCamposConstants::TAGS_LABEL,
                $pesquisa->getTags() ?? []
            )
            ->__toString();
    }
}
