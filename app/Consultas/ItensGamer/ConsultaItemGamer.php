<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Consultas\ItensGamer;

use Exception; // Importa a classe Exception para tratamento de erros.
use Laminas\Db\Sql\Select; // Importa a classe Select do Laminas DB para construir consultas SQL.
use Laminas\Db\Sql\Where; // Importa a classe Where do Laminas DB para construir cláusulas WHERE.
use Vios\Juridico\App\DAO\DAO; // Objeto de Acesso a Dados, para interagir com o banco.
use Vios\Juridico\App\Entities\ItensGamer\PesquisaItensGamerEntity; // Entidade para os filtros de pesquisa.
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Constantes para nomes de campos e tabelas.
use Vios\Juridico\App\Entities\ItensGamer\ItemGamerEntity; // Entidade que representa um Item Gamer.
use Vios\Juridico\App\Entities\ItensGamer\TagEntity; // Entidade para Tags.
use Vios\Juridico\App\Util\Integer; // Utilitário para garantir que valores sejam inteiros.

/**
 * ConsultaItensGamer
 *
 * Esta classe é responsável por realizar consultas de Itens Gamer no banco de dados.
 * Ela traduz os filtros da entidade de pesquisa em consultas SQL.
 */
final class ConsultaItensGamer
{
    public const TABELA = 'itens_gamer'; // Nome da tabela principal de itens.

    private $dao; // Instância do DAO para acesso ao banco.

    /**
     * Construtor da classe de consulta.
     * @param DAO $dao Recebe uma instância do DAO para poder realizar operações no banco.
     */
    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Busca e retorna uma lista de ItemGamerEntity com base nos filtros da pesquisa.
     * @param PesquisaItensGamerEntity $pesquisa Objeto com os critérios de pesquisa.
     * @return ItemGamerEntity[] Um array de objetos ItemGamerEntity.
     * @throws Exception Em caso de erro na consulta ou mapeamento.
     */
    public function getItensGamer(PesquisaItensGamerEntity $pesquisa): array
    {
        // Define uma função anônima (callable) para mapear cada linha do resultado do banco para um objeto ItemGamerEntity.
        $callable = function (array $ItensGamer): ItemGamerEntity {
            // Formata a data de cadastro para um objeto DateTime, se existir.
            $dataCadastro = !empty($ItensGamer[ItensGamerCamposConstants::DATA_CADASTRO])
                ? new \DateTime($ItensGamer[ItensGamerCamposConstants::DATA_CADASTRO])
                : null;

            // Garante que o ID é um inteiro.
            $id = Integer::int($ItensGamer[ItensGamerCamposConstants::ID] ?? 0);

            // Cria e popula a entidade ItemGamerEntity com os dados da linha do banco.
            return (new ItemGamerEntity())
                ->setId($id)
                ->setNome((string)$ItensGamer[ItensGamerCamposConstants::NOME] ?? '')
                ->setDescricao((string)$ItensGamer[ItensGamerCamposConstants::DESCRICAO] ?? '')
                ->setTipo((string)$ItensGamer[ItensGamerCamposConstants::TIPO] ?? '')
                // Pega as tags associadas ao item usando o ID.
                ->setTags(... $this->getTagsDoItem($id))
                ->setCorEmblema((string)$ItensGamer[ItensGamerCamposConstants::COR_EMBLEMA] ?? '')
                ->setQuantidade((integer)$ItensGamer[ItensGamerCamposConstants::QUANTIDADE] ?? 0)
                ->setPrecoVenda((float)$ItensGamer[ItensGamerCamposConstants::PRECO_VENDA] ?? 0.0)
                ->setItemAtivo((bool)$ItensGamer[ItensGamerCamposConstants::ITEM_ATIVO] ?? false)
                ->setDataCadastro($dataCadastro);
        };
        // Executa a busca principal e mapeia os resultados para entidades ItemGamerEntity.
        return array_map($callable, $this->buscaItensGamer($pesquisa));
    }

    /**
     * Busca um único Item Gamer pelo seu ID.
     * @param int $ItensGamerId O ID do item a ser buscado.
     * @return ItemGamerEntity O objeto ItemGamerEntity correspondente, ou uma nova entidade vazia se não encontrado.
     * @throws Exception Em caso de erro na consulta.
     */
    public function getById(int $ItensGamerId): ItemGamerEntity
    {
        $pesquisa = new PesquisaItensGamerEntity();
        $pesquisa->setId($ItensGamerId); // Define o ID como filtro na entidade de pesquisa.
        // Se o ID for 0 ou menor, retorna um array vazio; caso contrário, busca os itens.
        $resultado = $ItensGamerId <= 0 ? [] : $this->getItensGamer($pesquisa);
        // Pega o primeiro resultado (se houver).
        $entidade = $resultado[0] ?? null;

        // Retorna a entidade encontrada ou uma nova entidade vazia.
        return $entidade instanceof ItemGamerEntity ? $entidade : new ItemGamerEntity();
    }

    /**
     * Busca e retorna as Tags associadas a um Item Gamer específico.
     * @param int $itensGamerId O ID do Item Gamer.
     * @return TagEntity[] Um array de objetos TagEntity.
     * @throws Exception Em caso de erro na consulta.
     */
    public function getTagsDoItem(int $itensGamerId): array
    {
        // Se o ID do item for inválido, retorna um array vazio de tags.
        if ($itensGamerId <= 0) {
            return [];
        }
        // Constrói a consulta para buscar tags relacionadas a um item.
        $select = $this->dao->getSelectBase('item_gamer_tags', 'igt') // Tabela pivot de relacionamento.
            ->columns([]) // Não seleciona colunas da tabela pivot.
            // Faz um JOIN com a tabela 'tags' para pegar o ID e o nome da tag.
            ->join(['t' => 'tags'], 'igt.tag_id = t.id', [ItensGamerCamposConstants::ID, ItensGamerCamposConstants::NOME])
            ->where(['igt.item_gamer_id' => $itensGamerId]) // Filtra pelo ID do item.
            ->order('t.nome ASC'); // Ordena por nome da tag.

        // Executa a consulta.
        $resultado = $this->dao->executaSelect($select);

        // Mapeia as linhas do resultado para objetos TagEntity.
        return array_map(function (array $tag) {
            return (new TagEntity())
                ->setId((int)$tag[ItensGamerCamposConstants::ID])
                ->setNome((string)$tag[ItensGamerCamposConstants::NOME]);
        }, $resultado);
    }

    /**
     * Busca e retorna todas as Tags disponíveis no sistema.
     * @return array Um array associativo com ID da tag como chave e nome da tag como valor.
     * @throws Exception Em caso de erro na consulta.
     */
    public function getTagsDisponiveis(): array
    {
        // Constrói a consulta para buscar todas as tags.
        $select = $this->dao->getSelectBase('tags', 't')
            ->columns([ItensGamerCamposConstants::ID, ItensGamerCamposConstants::NOME])
            ->order('t.nome ASC'); // Ordena por nome.
        // Executa a consulta.
        $resultado = $this->dao->executaSelect($select);
        // Mapeia o resultado para um array associativo (id => nome).
        return array_column($resultado, 'nome', 'id');
    }

    /**
     * Realiza a busca principal de Itens Gamer com base nos filtros da entidade de pesquisa.
     * @param PesquisaItensGamerEntity $pesquisa Objeto com os critérios de pesquisa.
     * @return array Um array de resultados brutos do banco de dados.
     * @throws Exception Em caso de erro na consulta.
     */
    public function buscaItensGamer(PesquisaItensGamerEntity $pesquisa): array
    {
        // Inicia o construtor de query para a tabela 'itens_gamer'.
        $select = $this->dao->getSelectBase(ItensGamerCamposConstants::TABELA, 'ig');

        // Adiciona o filtro por ID do item, se especificado.
        if ($pesquisa->getId() > 0) {
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::ID, $pesquisa->getId());
        }

        // Adiciona o filtro por Nome ou Descrição (busca geral).
        $buscaNome = trim($pesquisa->getNome() ?? '');
        if (!empty($buscaNome)) {
            // Usa uma condição OR para buscar tanto no nome quanto na descrição.
            $select->where(function ($where) use ($buscaNome) {
                $where->like('ig.' . ItensGamerCamposConstants::NOME, "%{$buscaNome}%")
                      ->or->like('ig.' . ItensGamerCamposConstants::DESCRICAO, "%{$buscaNome}%");
            });
        }

        // Adiciona o filtro por Tipo do Item (multi-seleção).
        $buscaTipo = $pesquisa->getTipo(); // Retorna array ou null.
        if (!empty($buscaTipo) && is_array($buscaTipo)) {
            // Usa 'IN' para buscar por múltiplos tipos selecionados.
            $select->where->in('ig.' . ItensGamerCamposConstants::TIPO, $buscaTipo);
        }

        // Adiciona o filtro por Item Ativo.
        if (!is_null($pesquisa->isItemAtivo())) {
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::ITEM_ATIVO, $pesquisa->isItemAtivo() ? 1 : 0);
        }

        // Adiciona o filtro por Tags (multi-seleção).
        $tags = $pesquisa->getTags(); // Retorna array de IDs de Tag.
        if (!empty($tags)) {
            // Cria um sub-select para verificar a existência de tags relacionadas.
            $subSelect = new Select('item_gamer_tags'); // Tabela pivot de relacionamento.
            $subSelect->where('item_gamer_tags.item_gamer_id = ig.id'); // Condição de JOIN com o item principal.
            $subSelect->where->in('tag_id', $tags); // Filtra por IDs de tags selecionadas.

            // Usa a cláusula 'EXISTS' para incluir itens que possuem as tags selecionadas.
            $select->where->expression('EXISTS (?)', [$subSelect]);
        }

        // Adiciona o filtro por Cor do Emblema (campo de texto).
        $cor = trim($pesquisa->getCorEmblema() ?? '');
        if (!empty($cor)) {
            // Se a cor não começar com '#', adiciona para padronizar (ex: "preto" vira "#preto").
            if (strpos($cor, '#') !== 0) {
                $cor = '#' . ltrim($cor, '#');
            }
            // Filtra pela cor exata (case-insensitive com strtolower).
            $select->where->equalTo('ig.' . ItensGamerCamposConstants::COR_EMBLEMA, strtolower($cor));
        }

        // Adiciona o filtro por Preço Mínimo.
        if ($pesquisa->getPrecoMinimo()) {
            $select->where->greaterThanOrEqualTo(
                'ig.' . ItensGamerCamposConstants::PRECO_VENDA,
                $pesquisa->getPrecoMinimo()
            );
        }

        // Adiciona o filtro por Preço Máximo.
        if ($pesquisa->getPrecoMaximo()) {
            $select->where->lessThanOrEqualTo(
                'ig.' . ItensGamerCamposConstants::PRECO_VENDA,
                $pesquisa->getPrecoMaximo()
            );
        }
        // Executa a consulta e retorna os resultados brutos.
        return $this->dao->executaSelect($select);
    }
}