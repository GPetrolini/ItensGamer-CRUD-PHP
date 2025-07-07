<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

// Importa classes necessárias para renderizar o formulário de pesquisa.
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Constantes para nomes de campos.
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer; // Para buscar tags disponíveis.
use Vios\Juridico\App\FormularioPesquisa\FormularioPesquisa; // Classe base para formulários de pesquisa.
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity; // Entidade que guarda os filtros de pesquisa.
use Vios\Juridico\ContainerVios; // Contêiner de Injeção de Dependência.

/**
 * FormularioPesquisaItensGamer
 *
 * Esta classe é responsável por definir e renderizar o formulário de pesquisa
 * para os Itens Gamer. Ela utiliza a classe base FormularioPesquisa para
 * construir os campos visuais.
 */
final class FormularioPesquisaItensGamer
{
    /**
     * Renderiza o formulário de pesquisa com base nos dados da entidade de pesquisa.
     * @param PesquisaItensGamerEntity $pesquisa A entidade com os valores dos filtros atuais.
     * @return string O HTML do formulário de pesquisa.
     */
    public function render(PesquisaItensGamerEntity $pesquisa): string
    {
        // Pega uma instância do serviço de consulta de itens.
        $consulta = ContainerVios::fetch(ConsultaItensGamer::class);
        // Busca todas as tags disponíveis para popular o campo de tags.
        $tagsDisponiveis = $consulta->getTagsDisponiveis();

        // Constrói o formulário de pesquisa usando a classe base FormularioPesquisa.
        return (new FormularioPesquisa())
           // Campo de busca geral por nome ou descrição.
           ->busca(
               $pesquisa->getNome() ?? ''
           )
            // Campo de busca por ID do item.
            ->buscaId(
                $pesquisa->getId() ?? 0
            )
            // Campo de seleção múltipla para o Tipo do Item.
            ->comboArrayMultiplo(
                ItensGamerCamposConstants::TIPO_OPCOES,      // Opções disponíveis.
                ItensGamerCamposConstants::TIPO_LABEL,       // Rótulo do campo.
                $pesquisa->getTipo() ?? []                   // Valor(es) selecionado(s) atualmente.
            )
            // Campo de seleção simples para "Item ativo?".
            // As opções são 'Todos', 'Não', 'Sim'.
            ->comboArray(
                ['todos' => 'Todos', '0' => 'Não', '1' => 'Sim'], // Opções para o select.
                ItensGamerCamposConstants::ITEM_ATIVO_LABEL,     // Rótulo do campo.
                is_null($pesquisa->isItemAtivo()) ? 'todos' : ($pesquisa->isItemAtivo() ? '1' : '0'), // Valor selecionado.
                ItensGamerCamposConstants::ITEM_ATIVO            // Nome do campo no HTML.
            )
            // Campo de texto genérico para "Cor do Item".
            ->campoTextGenerico(
                ItensGamerCamposConstants::COR_EMBLEMA_LABEL, // Rótulo do campo.
                ItensGamerCamposConstants::COR_EMBLEMA,       // Nome do campo no HTML.
                $pesquisa->getCorEmblema() ?? ''              // Valor atual.
            )
            // Campo para faixa de valor (preço mínimo e máximo).
            ->faixaValor(
                $pesquisa->getPrecoMinimo() ?? 0.0,
                $pesquisa->getPrecoMaximo() ?? 0.0
            )
            // Campo de seleção múltipla para Tags.
            ->comboArrayTag(
                $tagsDisponiveis,                             // Opções de tags disponíveis.
                ItensGamerCamposConstants::TAGS_LABEL,        // Rótulo do campo.
                $pesquisa->getTags() ?? []                    // Valor(es) selecionado(s) atualmente.
            )
            // Converte o objeto FormularioPesquisa para string (renderiza o HTML).
            ->__toString();
    }
}