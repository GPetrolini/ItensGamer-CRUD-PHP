<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

final class TagEntity
{
    public $id = null;

    public $nome = null;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id): TagEntity
    {
        $this->id = $id;
        return $this;
    }
    public function getNome()
    {
        return $this->nome;
    }
    public function setNome($nome): TagEntity
    {
        $this->nome = $nome;
        return $this;
    }
}
