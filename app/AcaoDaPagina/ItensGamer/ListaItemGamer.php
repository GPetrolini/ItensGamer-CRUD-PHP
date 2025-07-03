<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\Cores;
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity;
use Vios\Juridico\App\Entities\ItensGamer\TagEntity;
use Vios\Juridico\App\Excecoes\AvisoParaOUsuarioException;
use Vios\Juridico\App\View\ElementosVisuais\EtiquetaCircular;
use Vios\Juridico\App\View\OpcoesGeraTabela;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;

final class ListaItensGamer
{
    private $consulta;

    private const CAMINHO_PAGINA = 'sys/itens_gamer/itens_gamer.php';


    public function __construct(ConsultaItensGamer $consulta)
    {
        $this->consulta = $consulta;
    }

    /**
     * @throws \Throwable
     */
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
            ItensGamerCamposConstants::QUANTIDADE => ItensGamerCamposConstants::QUANTIDADE_LABEL ,
            'preco_formatado' => ItensGamerCamposConstants::PRECO_VENDA_LABEL,
            'ativo_formatado' => ItensGamerCamposConstants::ITEM_ATIVO_LABEL,
            'visualizar_dois_botao' => 'Visualizar Dois'
        ];
    }

    public function opcoes(): array
    {
        $opcoes = [];
        $opcoes = OpcoesGeraTabela::addTitulo($opcoes, 'Itens Gamers Cadastrados');
        $opcoes = OpcoesGeraTabela::addBotaoInserir($opcoes, self::CAMINHO_PAGINA);
        $opcoes = OpcoesGeraTabela::addLink($opcoes, 'view', ['id'], self::CAMINHO_PAGINA, 'Visualizar');
        $opcoes = OpcoesGeraTabela::addLink(
            $opcoes,
            'history',
            ['id'],
            self::CAMINHO_PAGINA,
            'Visualizar histórico',
            ['act' => 'history']
        );
        $opcoes = OpcoesGeraTabela::addLink($opcoes, 'edit', ['id'], self::CAMINHO_PAGINA);
        $opcoes = OpcoesGeraTabela::addLink($opcoes, 'del', ['id'], self::CAMINHO_PAGINA);
        return $opcoes;
    }

    /**
     * @throws AvisoParaOUsuarioException
     * @throws \Exception
     */
    private function result(PesquisaItensGamerEntity $pesquisa): array
    {
        $itensDoBanco = $this->consulta->getItensGamer($pesquisa);
        /**
         * @throws AvisoParaOUsuarioException
         */
        $callable = function (ItemGamerEntity $entity) {
            $tagEntities = $entity->getTags();
            $tagNomes = array_map(function (TagEntity $tag) {
                return $tag->getNome();
            }, $tagEntities);
            $urlVisualizardois = '';
            if ($entity->isItemAtivo()) {
                $urlVisualizardois = '?' . http_build_query([
                        'pag' => self::CAMINHO_PAGINA,
                        'act' => 'visualizar_dois',
                        'id' => $entity->getId()
                    ]);
            }

            $cor = htmlspecialchars($entity->getCorEmblema() ?? '#ccc');

            return [
                'id' => $entity->getId(),
                'nome' => $entity->getNome(),
                'tipo' => $entity->getTipo(),
                'quantidade' => $entity->getQuantidade(),
                'descricao_curta' => mb_substr($entity->getDescricao() ?? '', 0, 40) . '...',
                'tags_formatadas' => implode(', ', $tagNomes),
                'preco_formatado' => 'R$' . number_format($entity->getPrecoVenda() ?? 0, 2, ',', '.'),
                'cor_display' => (new EtiquetaCircular(
                    $cor,
                    Cores::darken($cor, 20),
                    'vj-icon fa-steam-square',
                    "Cor:{$cor}"
                ))->__toString(),
                'ativo_formatado' => $entity->isItemAtivo() ? 'Sim' : 'Não',
                'visualizar_dois_botao' => '<a href="' . $urlVisualizardois .
                    '" class="btn btn-warning btn-sm">Visualizar dois</a>',
            ];
        };
        return array_map($callable, $itensDoBanco);
    }
}
