<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\Cores;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\View\ElementosVisuais\EtiquetaCircular;
use Vios\Juridico\App\View\OpcoesGeraTabela;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;

final class ListaItensGamer
{
    private $consulta;

    public const CAMINHO_PAGINA = 'sys/itens_gamer/itens_gamer.php';

    public function __construct(ConsultaItensGamer $consulta)
    {
        $this->consulta = $consulta;
    }

    public function render(PesquisaItensGamerEntity $pesquisa): string
    {
        return geratabela_wrapper($this->result($pesquisa), $this->campos(), $this->opcoes());
    }

    public function campos(): array
    {
        return [
            'cor_display' => 'Etiqueta',
            ItensGamerCamposConstants::ID => ItensGamerCamposConstants::ID_LABEL,
            ItensGamerCamposConstants::NOME => ItensGamerCamposConstants::NOME_LABEL,
            'descricao_curta' => ItensGamerCamposConstants::DESCRICAO_LABEL,
            ItensGamerCamposConstants::TIPO => ItensGamerCamposConstants::TIPO_LABEL,
            'tags_formatadas' => ItensGamerCamposConstants::TAGS_LABEL,
            ItensGamerCamposConstants::QUANTIDADE => ItensGamerCamposConstants::QUANTIDADE_LABEL,
            'preco_formatado' => ItensGamerCamposConstants::PRECO_VENDA_LABEL,
            'ativo' => ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
        ];
    }

    public function opcoes(): array
    {
        $opcoes = [];
        $opcoes = OpcoesGeraTabela::addTitulo($opcoes, 'Itens Gamers Cadastrados');
        $opcoes = OpcoesGeraTabela::addBotaoInserir($opcoes, self::CAMINHO_PAGINA);
        $opcoes = OpcoesGeraTabela::converteBooleanoParaString($opcoes, ['ativo']);
        $opcoes = OpcoesGeraTabela::addLink(
            $opcoes,
            'vis',
            [ItensGamerCamposConstants::ID, 'act' => 'view'],
            self::CAMINHO_PAGINA,
            'Visualizar'
        );
        $opcoes = OpcoesGeraTabela::addLink(
            $opcoes,
            'history',
            [ItensGamerCamposConstants::ID],
            self::CAMINHO_PAGINA,
            'Visualizar histórico',
            ['act' => 'history']
        );
        $opcoes = OpcoesGeraTabela::addLink($opcoes, 'edit', [ItensGamerCamposConstants::ID], self::CAMINHO_PAGINA);
        $opcoes = OpcoesGeraTabela::addLink($opcoes, 'del', [ItensGamerCamposConstants::ID], self::CAMINHO_PAGINA);
        $opcoes = OpcoesGeraTabela::addLink(
            $opcoes,
            'vis',
            [ItensGamerCamposConstants::ID, 'act' => 'view'],
            self::CAMINHO_PAGINA,
            'Visualizar diferente',
            ['btnClass' => 'btn-warning']
        );

        return $opcoes;
    }

    private function result(PesquisaItensGamerEntity $pesquisa): array
    {
        $itensDoBanco = $this->consulta->getItensGamer($pesquisa);
        $callableNomeTags = function (TagEntity $tag): string {
            return $tag->getNome();
        };

        $callable = function (ItemGamerEntity $entity) use ($callableNomeTags): array {
            $cor = htmlspecialchars($entity->getCorEmblema() ?? '#ccc');

            return [
                ItensGamerCamposConstants::ID => $entity->getId(),
                ItensGamerCamposConstants::NOME => $entity->getNome(),
                ItensGamerCamposConstants::TIPO => $entity->getTipo(),
                ItensGamerCamposConstants::QUANTIDADE => $entity->getQuantidade(),
                'descricao_curta' => mb_substr($entity->getDescricao() ?? '', 0, 40) . '...',
                'tags_formatadas' => implode(', ', array_map($callableNomeTags, $entity->getTags())),
                'preco_formatado' => 'R$' . number_format($entity->getPrecoVenda() ?? 0, 2, ',', '.'),
                'cor_display' => (new EtiquetaCircular(
                    $cor,
                    Cores::darken($cor, 20),
                    'vj-icon fa-steam-square',
                    "Cor:{$cor}"
                ))->__toString(),
                'ativo' => $entity->isItemAtivo(),
            ];
        };
        return array_map($callable, $itensDoBanco);
    }
}
