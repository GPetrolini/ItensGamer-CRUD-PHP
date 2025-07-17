<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

use Carbon\Carbon;
use Vios\Juridico\App\Collections\StringCollections;
use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;

final class ItemGamerEntity
{
    private $corEmblema;

    private $dataCadastro;

    private $descricao;

    private $id;

    private $isItemAtivo;

    private $nome;

    private $precoVenda;

    private $quantidade;

    private $tags;

    private $tipo;

    public function __construct()
    {
        $this->corEmblema = '';
        $this->dataCadastro = null;
        $this->descricao = '';
        $this->id = 0;
        $this->isItemAtivo = false;
        $this->nome = '';
        $this->precoVenda = 0;
        $this->quantidade = 0;
        $this->tags = [];
        $this->tipo = '';
    }

    public function getCorEmblema(): string
    {
        return $this->corEmblema;
    }

    public function getDataCadastro(): ?Carbon
    {
        return $this->dataCadastro;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isItemAtivo(): ?bool
    {
        return $this->isItemAtivo;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPrecoVenda(): float
    {
        return $this->precoVenda;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    /** @return TagEntity[]  */
    public function getTags(): array
    {
        return $this->getTags();
    }

    public function getTipo(): StringCollections
    {
        return (new StringCollections())->added(...$this->tipo);
    }

    public function setCorEmblema(string $corEmblema): self
    {
        $this->corEmblema = $corEmblema;
        return $this;
    }

    public function setDataCadastro(?Carbon $dataCadastro): self
    {
        $this->dataCadastro = $dataCadastro;
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

    public function setIsItemAtivo(bool $isItemAtivo): self
    {
        $this->isItemAtivo = $isItemAtivo;
        return $this;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function setPrecoVenda(float $precoVenda): self
    {
        $this->precoVenda = $precoVenda;
        return $this;
    }

    public function setQuantidade(int $quantidade): self
    {
        $this->quantidade = $quantidade;
        return $this;
    }

    public function setTags(TagEntity ...$tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function toArray(): array
    {
        $callable = function (TagEntity $entity) {
            return $entity->getId();
        };

        return [
            ItensGamerCamposConstants::COR_EMBLEMA => $this->getCorEmblema(),
            ItensGamerCamposConstants::DATA_CADASTRO => !is_null($this->getDataCadastro())
                ? $this->getDataCadastro()->format('d/m/Y')
                : '00/00/0000',
            ItensGamerCamposConstants::DESCRICAO => $this->getDescricao(),
            ItensGamerCamposConstants::ID => $this->getId(),
            ItensGamerCamposConstants::ITEM_ATIVO => $this->isItemAtivo(),
            ItensGamerCamposConstants::NOME => $this->getNome(),
            ItensGamerCamposConstants::PRECO_VENDA => $this->getPrecoVenda(),
            ItensGamerCamposConstants::QUANTIDADE => $this->getQuantidade(),
            ItensGamerCamposConstants::TAGS => array_map($callable, $this->getTags()),
            ItensGamerCamposConstants::TIPO => $this->getTipo()->getAdded(),
        ];
    }
}
