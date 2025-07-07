<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants; // Importa constantes para nomes de campos.

/**
 * ItemGamerEntity
 *
 * Esta entidade representa um Item Gamer no sistema.
 * Ela encapsula os dados de um item, como nome, descrição, tipo, etc.
 */
final class ItemGamerEntity
{
    // Propriedades que representam os atributos de um Item Gamer.
    /**
     * @var string|null O código da cor do emblema (ex: '#RRGGBB' ou nome da cor).
     */
    private $cor_emblema;
    /**
     * @var int|null O ID único do item no banco de dados.
     */
    private $id;
    /**
     * @var string|null O nome do item.
     */
    private $nome;
    /**
     * @var string|null A descrição detalhada do item.
     */
    private $descricao;
    /**
     * @var string|null O tipo do item (ex: 'Periferico', 'Console').
     */
    private $tipo;
    /**
     * @var TagEntity[] Um array de entidades Tag associadas a este item.
     */
    private $tags;
    /**
     * @var int|null A quantidade de itens em estoque.
     */
    private $quantidade;
    /**
     * @var float|null O preço de venda do item.
     */
    private $preco_venda;
    /**
     * @var bool|null Indica se o item está ativo (true) ou inativo (false).
     */
    private $item_ativo;
    /**
     * @var \DateTime|null A data e hora de cadastro do item.
     */
    private $data_cadastro;

    /**
     * Construtor da entidade ItemGamerEntity.
     * Inicializa todas as propriedades com valores padrão (geralmente nulo ou vazio),
     * garantindo que o objeto esteja sempre em um estado conhecido.
     */
    public function __construct()
    {
        $this->id = null;
        $this->nome = null;
        $this->descricao = null;
        $this->tipo = null;
        $this->tags = []; // Tags iniciam como array vazio.
        $this->cor_emblema = null;
        $this->quantidade = null;
        $this->preco_venda = null;
        $this->item_ativo = null;
        $this->data_cadastro = null;
    }

    // --- Métodos Getters e Setters para cada propriedade ---

    /**
     * Retorna o ID do item.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Define o ID do item.
     * @param int|null $id
     * @return self Retorna a própria instância para encadeamento de chamadas.
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Retorna o nome do item.
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Define o nome do item.
     * @param string|null $nome
     * @return self
     */
    public function setNome(?string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Retorna a descrição do item.
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Define a descrição do item.
     * @param string|null $descricao
     * @return self
     */
    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Retorna o tipo do item.
     * @return string|null
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * Define o tipo do item.
     * @param string|null $tipo
     * @return self
     */
    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Retorna as tags associadas ao item.
     * @return TagEntity[] Um array de objetos TagEntity.
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Define as tags associadas ao item.
     * Usa o operador de desempacotamento (...) para aceitar múltiplas TagEntity.
     * @param TagEntity ...$tags Uma lista de objetos TagEntity.
     * @return self
     */
    public function setTags(TagEntity ...$tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Retorna o código da cor do emblema do item.
     * @return string|null
     */
    public function getCorEmblema(): ?string
    {
        return $this->cor_emblema;
    }

    /**
     * Define o código da cor do emblema do item.
     * @param string|null $cor_emblema
     * @return self
     */
    public function setCorEmblema(?string $cor_emblema): self
    {
        $this->cor_emblema = $cor_emblema;
        return $this;
    }

    /**
     * Retorna a quantidade de itens em estoque.
     * @return int|null
     */
    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }

    /**
     * Define a quantidade de itens em estoque.
     * @param int|null $quantidade
     * @return self
     */
    public function setQuantidade(?int $quantidade): self
    {
        $this->quantidade = $quantidade;
        return $this;
    }

    /**
     * Retorna o preço de venda do item.
     * @return float|null
     */
    public function getPrecoVenda(): ?float
    {
        return $this->preco_venda;
    }

    /**
     * Define o preço de venda do item.
     * @param float|null $preco_venda
     * @return self
     */
    public function setPrecoVenda(?float $preco_venda): self
    {
        $this->preco_venda = $preco_venda;
        return $this;
    }

    /**
     * Retorna se o item está ativo.
     * @return bool|null
     */
    public function isItemAtivo(): ?bool
    {
        return $this->item_ativo;
    }

    /**
     * Define se o item está ativo.
     * @param bool|null $item_ativo
     * @return self
     */
    public function setItemAtivo(?bool $item_ativo): self
    {
        $this->item_ativo = $item_ativo;
        return $this;
    }

    /**
     * Retorna a data de cadastro do item.
     * @return \DateTime|null
     */
    public function getDataCadastro(): ?\DateTime
    {
        return $this->data_cadastro;
    }

    /**
     * Define a data de cadastro do item.
     * @param \DateTime|null $data_cadastro
     * @return self
     */
    public function setDataCadastro(?\DateTime $data_cadastro): self
    {
        $this->data_cadastro = $data_cadastro;
        return $this;
    }

    /**
     * Converte a entidade Item Gamer em um array associativo.
     * Útil para passar dados para o DAO ou para exibição.
     * @return array
     */
    public function toArray(): array
    {
        // Função anônima para mapear entidades Tag para seus IDs.
        $callable = function (TagEntity $entity) {
            return $entity->getId();
        };

        return [
            ItensGamerCamposConstants::ID => $this->getId(),
            ItensGamerCamposConstants::NOME => $this->getNome(),
            ItensGamerCamposConstants::DESCRICAO => $this->getDescricao(),
            ItensGamerCamposConstants::TIPO => $this->getTipo(),
            // Mapeia as entidades Tag para um array de IDs.
            ItensGamerCamposConstants::TAGS => array_map($callable, $this->getTags()),
            ItensGamerCamposConstants::COR_EMBLEMA => $this->getCorEmblema(),
            ItensGamerCamposConstants::QUANTIDADE => $this->getQuantidade(),
            ItensGamerCamposConstants::PRECO_VENDA => $this->getPrecoVenda(),
            ItensGamerCamposConstants::ITEM_ATIVO => $this->isItemAtivo(),
            // Formata a data de cadastro para string, se existir.
            ItensGamerCamposConstants::DATA_CADASTRO => $this->getDataCadastro() ? $this->getDataCadastro()->format('d/m/Y') : null,
        ];
    }
}