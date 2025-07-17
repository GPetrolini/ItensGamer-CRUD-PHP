<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

use Vios\Juridico\App\Collections\IntIdsCollections;
use Vios\Juridico\App\Collections\StringCollections;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;

final class PesquisaItensGamerEntity
{
    private $corEmblema;

    private $descricao;

    private $id;

    private $itemAtivo;

    private $nome;

    private $precoMaximo;

    private $precoMinimo;

    private $quantidade;

    private $tags;

    private $tipo;

    public function __construct()
    {
        $this->corEmblema = '';
        $this->descricao = '';
        $this->id = 0;
        $this->itemAtivo = false;
        $this->nome = '';
        $this->precoMaximo = 0;
        $this->precoMinimo = 0;
        $this->quantidade = 0;
        $this->tags = [];
        $this->tipo = '';
    }

    public function getCorEmblema(): string
    {
        return $this->corEmblema;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPrecoMaximo(): float
    {
        return $this->precoMaximo;
    }

    public function getPrecoMinimo(): float
    {
        return $this->precoMinimo;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function getTags(): IntIdsCollections
    {
        return (new IntIdsCollections())->added(...$this->tags);
    }

    public function getTipo(): StringCollections
    {
        return (new StringCollections())->added(...$this->tipo);
    }

    public function isItemAtivo(): ?bool
    {
        return $this->itemAtivo;
    }

    public function setCorEmblema(string $corEmblema): self
    {
        $this->corEmblema = $corEmblema;

        return $this;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setItemAtivo(?bool $itemAtivo): self
    {
        $this->itemAtivo = $itemAtivo;

        return $this;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function setPrecoMinimo(float $precoMinimo): self
    {
        $this->precoMinimo = $precoMinimo;

        return $this;
    }

    public function setPrecoMaximo(float $precoMaximo): self
    {
        $this->precoMaximo = $precoMaximo;

        return $this;
    }

    public function setTags(int ...$tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function setTipo(string ...$tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function __toArray(): array
    {
        return [
            ItensGamerCamposConstants::COR_EMBLEMA => $this->getCorEmblema(),
            ItensGamerCamposConstants::DESCRICAO => $this->getDescricao(),
            ItensGamerCamposConstants::ID => $this->getId(),
            ItensGamerCamposConstants::ITEM_ATIVO => $this->isItemAtivo(),
            ItensGamerCamposConstants::NOME => $this->getNome(),
            'preco_maximo' => $this->getPrecoMaximo(),
            'preco_minimo' => $this->getPrecoMinimo(),
            ItensGamerCamposConstants::QUANTIDADE => $this->getQuantidade(),
            ItensGamerCamposConstants::TAGS => $this->getTags()->getAdded(),
            ItensGamerCamposConstants::TIPO => $this->getTipo()->getAdded(),
        ];
    }
}
