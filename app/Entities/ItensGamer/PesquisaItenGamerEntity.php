<?php
declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

final class PesquisaItensGamerEntity
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $nome;

    /** @var string|null */
    private $tipo;

    /** @var bool|array|null */
    private $item_ativo;

    /** @var array|null */
    private $tags = [];
    /** @var string|null */
    private $cor_emblema;
    /** @var float|null */

    private $preco_minimo;
    /** @var float|null */
    private $preco_maximo;
    /** @var @var string|null */
    private $descricao;
    /** @var @var int|null */
    private $quantidade;

    public function __construct()
    {
        $this->id = null;
        $this->nome = null;
        $this->tipo = null;
        $this->item_ativo = null;
        $this->tags = [];
        $this->cor_emblema = null;
        $this->preco_minimo = null;
        $this->preco_maximo = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self // 'self' quer dizer que retorna o próprio objeto
    {
        $this->id = $id;
        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getTipo(): ?array
    {
        if (!is_null($this->tipo) && !is_array($this->tipo)) {
            return [$this->tipo];
        }
        return $this->tipo;
    }

    public function setTipo($tipo): self
    {
        if (!is_array($tipo) && !is_null($tipo)) {
            if (is_string($tipo) && !empty($tipo)) {
                $this->tipo = [$tipo];
            } else {
                $this->tipo = null;
            }
        } else {
            $this->tipo = $tipo;
        }
        return $this;
    }
    public function isItemAtivo(): ?bool
    {
        return $this->item_ativo;
    }
    public function setItemAtivo(?bool $item_ativo):self
    {
        $this->item_ativo = $item_ativo;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags):self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getCorEmblema(): ?string
    {
        return $this->cor_emblema;
    }
    public function setCorEmblema(?string $cor_emblema):self
    {
        $this->cor_emblema = $cor_emblema;
        return $this;
    }
    public function getPrecoMinimo(): ?float
    {
        return $this->preco_minimo;
    }
    public function setPrecoMinimo(?float $preco_minimo):self
    {
        $this->preco_minimo = $preco_minimo;
        return $this;
    }
    public function getPrecoMaximo(): ?float
    {
        return $this->preco_maximo;
    }
    public function setPrecoMaximo(?float $preco_maximo):self
    {
        $this->preco_maximo = $preco_maximo;
        return $this;
    }
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }
    public function setDescricao(?string $descricao):self
    {
        $this->descricao = $descricao;
        return $this;
    }
    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }
    public function setQuantidade(?int $quantidade):self
    {
        $this->quantidade = $quantidade;
        return $this;
    }
}