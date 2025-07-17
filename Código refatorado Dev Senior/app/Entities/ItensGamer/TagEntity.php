<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

final class TagEntity
{
    public $id;

    public $nome;

    public function __construct()
    {
        $this->id = 0;
        $this->nome = '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setNome($nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function __toArray(): array
    {
        return ['id' => $this->getId(), 'nome' => $this->getNome()];
    }
}
