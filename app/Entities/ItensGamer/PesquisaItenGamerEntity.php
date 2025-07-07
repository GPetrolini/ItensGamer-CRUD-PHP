<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

/**
 * PesquisaItensGamerEntity
 *
 * Esta entidade é usada para encapsular e transportar os dados dos filtros
 * de pesquisa do formulário para a camada de consulta (DAO).
 * Ela define quais campos podem ser usados para buscar itens.
 */
final class PesquisaItensGamerEntity
{
    // Propriedades que representam os campos do formulário de pesquisa.
    /** @var int|null */
    private $id; // Filtro por ID do item.

    /** @var string|null */
    private $nome; // Filtro por nome do item (busca geral).

    /** @var string|array|null */ // Pode ser string (seleção simples) ou array (multi-seleção).
    private $tipo; // Filtro por tipo do item.

    /** @var bool|null */
    private $item_ativo; // Filtro para itens ativos ou inativos.

    /** @var array|null */
    private $tags = []; // Filtro por tags (multi-seleção).

    /** @var string|null */
    private $cor_emblema; // Filtro por cor do item (campo de texto).

    /** @var float|null */
    private $preco_minimo; // Filtro por preço mínimo.

    /** @var float|null */
    private $preco_maximo; // Filtro por preço máximo.

    /** @var string|null */
    private $descricao; // Filtro por descrição do item (usado na busca geral).

    /** @var int|null */
    private $quantidade; // Filtro por quantidade em estoque.

    /**
     * Construtor da entidade de pesquisa.
     * Inicializa todas as propriedades com valores padrão (geralmente nulo ou vazio),
     * garantindo um estado conhecido.
     */
    public function __construct()
    {
        $this->id = null;
        $this->nome = null;
        $this->tipo = null;
        $this->item_ativo = null;
        $this->tags = []; // Tags iniciam como array vazio.
        $this->cor_emblema = null;
        $this->preco_minimo = null;
        $this->preco_maximo = null;
        $this->descricao = null;
        $this->quantidade = null;
    }

    // --- Métodos Getters e Setters para cada propriedade ---

    /**
     * Retorna o ID de pesquisa.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Define o ID de pesquisa.
     * @param int|null $id
     * @return self Retorna a própria instância para encadeamento de chamadas (fluent interface).
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Retorna o nome para pesquisa.
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Define o nome para pesquisa.
     * @param string|null $nome
     * @return self
     */
    public function setNome(?string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Retorna o(s) tipo(s) selecionado(s) para pesquisa.
     * @return array|null Retorna um array de strings ou null.
     */
    public function getTipo(): ?array
    {
        // Garante que o retorno seja sempre um array (ou null), mesmo se uma string única foi setada.
        if (!is_null($this->tipo) && !is_array($this->tipo)) {
            return [$this->tipo];
        }
        return $this->tipo;
    }

    /**
     * Define o(s) tipo(s) para pesquisa.
     * @param array|string|null $tipo Pode receber uma string, um array de strings ou null.
     * @return self
     */
    public function setTipo($tipo): self
    {
        // Se for uma string (seleção simples), converte para um array com um item.
        if (is_string($tipo) && !empty($tipo)) {
            $this->tipo = [$tipo];
        }
        // Se já for um array ou null, atribui diretamente.
        elseif (is_array($tipo) || is_null($tipo)) {
            $this->tipo = $tipo;
        }
        // Para qualquer outro tipo inesperado, define como null.
        else {
            $this->tipo = null;
        }
        return $this;
    }

    /**
     * Retorna se o item ativo está selecionado para pesquisa.
     * @return bool|null
     */
    public function isItemAtivo(): ?bool
    {
        return $this->item_ativo;
    }

    /**
     * Define se o item ativo está selecionado para pesquisa.
     * @param bool|null $item_ativo
     * @return self
     */
    public function setItemAtivo(?bool $item_ativo): self
    {
        $this->item_ativo = $item_ativo;
        return $this;
    }

    /**
     * Retorna as tags selecionadas para pesquisa.
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Define as tags para pesquisa.
     * @param array $tags
     * @return self
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Retorna a cor do emblema para pesquisa.
     * @return string|null
     */
    public function getCorEmblema(): ?string
    {
        return $this->cor_emblema;
    }

    /**
     * Define a cor do emblema para pesquisa.
     * @param string|null $cor_emblema
     * @return self
     */
    public function setCorEmblema(?string $cor_emblema): self
    {
        $this->cor_emblema = $cor_emblema;
        return $this;
    }

    /**
     * Retorna o preço mínimo para pesquisa.
     * @return float|null
     */
    public function getPrecoMinimo(): ?float
    {
        return $this->preco_minimo;
    }

    /**
     * Define o preço mínimo para pesquisa.
     * @param float|null $preco_minimo
     * @return self
     */
    public function setPrecoMinimo(?float $preco_minimo): self
    {
        $this->preco_minimo = $preco_minimo;
        return $this;
    }

    /**
     * Retorna o preço máximo para pesquisa.
     * @return float|null
     */
    public function getPrecoMaximo(): ?float
    {
        return $this->preco_maximo;
    }

    /**
     * Define o preço máximo para pesquisa.
     * @param float|null $preco_maximo
     * @return self
     */
    public function setPrecoMaximo(?float $preco_maximo): self
    {
        $this->preco_maximo = $preco_maximo;
        return $this;
    }

    /**
     * Retorna a descrição para pesquisa.
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Define a descrição para pesquisa.
     * @param string|null $descricao
     * @return self
     */
    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Retorna a quantidade para pesquisa.
     * @return int|null
     */
    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }

    /**
     * Define a quantidade para pesquisa.
     * @param int|null $quantidade
     * @return self
     */
    public function setQuantidade(?int $quantidade): self
    {
        $this->quantidade = $quantidade;
        return $this;
    }
}