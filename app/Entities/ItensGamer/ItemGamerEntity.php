<?php
declare(strict_types=1);


namespace Vios\Juridico\App\Entities\ItensGamer;

use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;

final class ItemGamerEntity
{
    /**
     * @var null
     */
    private $cor_emblema;
    /**
     * @var null
     */
    private $id;
    /**
     * @var null
     */
    private $nome;
    /**
     * @var null
     */
    private $descricao;
    /**
     * @var null
     */
    private $tipo;
    /**
     * @var array
     */
    private $tags;
    /**
     * @var null
     */
    private $quantidade;
    /**
     * @var null
     */
    private $preco_venda;
    /**
     * @var null
     */
    private $item_ativo;
    /**
     * @var null
     */
    private $data_cadastro;

    public function __construct()
    {
        $this->id = null;
        $this->nome = null;
        $this->descricao = null;
        $this->tipo = null;
        $this->tags = [];
        $this->cor_emblema = null;
        $this->quantidade = null;
        $this->preco_venda = null;
        $this->item_ativo = null;
        $this->data_cadastro = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id):self
    {
        $this->id = $id;
        return $this;
    }
    public function getNome(): ?string
    {
        return $this->nome;
    }
    public function setNome(?string $nome):self
    {
        $this->nome = $nome;
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
    public function getTipo(): ?string
    {
        return $this->tipo;
    }
    public function setTipo(?string $tipo):self
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * @return TagEntity[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }
    /** @param TagEntity $tags ... $tags */
    public function setTags(TagEntity ...$tags)
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
    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }
    public function setQuantidade(?int $quantidade):self
    {
        $this->quantidade = $quantidade;
        return $this;
    }
    public function getPrecoVenda(): ?float
    {
        return $this->preco_venda;
    }
    public function setPrecoVenda(?float $preco_venda):self
    {
        $this->preco_venda = $preco_venda;
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
    public function getDataCadastro(): ?\DateTime
    {
        return $this->data_cadastro;
    }
    public function setDataCadastro(?\DateTime $data_cadastro):self
    {
        $this->data_cadastro = $data_cadastro;
        return $this;
    }
    public function toArray(): array
    {
        $callable = function (TagEntity $entity) {
            return $entity->getId();
        };

        return [
            ItensGamerCamposConstants::ID => $this->getId(),
            ItensGamerCamposConstants::NOME => $this->getNome(),
            ItensGamerCamposConstants::DESCRICAO => $this->getDescricao(),
            ItensGamerCamposConstants::TIPO => $this->getTipo(),
            ItensGamerCamposConstants::TAGS => array_map($callable, $this->getTags()),
            ItensGamerCamposConstants::COR_EMBLEMA => $this->getCorEmblema(),
            ItensGamerCamposConstants::QUANTIDADE => $this->getQuantidade(),
            ItensGamerCamposConstants::PRECO_VENDA => $this->getPrecoVenda(),
            ItensGamerCamposConstants::ITEM_ATIVO => $this->isItemAtivo(),
            ItensGamerCamposConstants::DATA_CADASTRO => $this->getDataCadastro() ? $this->getDataCadastro()->format('d/m/Y') : null,
        ];
    }
}
