<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Entities\ItensGamer;

/**
 * TagEntity
 *
 * Representa uma entidade de Tag no sistema.
 * É um objeto de valor simples que encapsula os dados de uma tag,
 * como seu ID e nome.
 */
final class TagEntity
{
    // Propriedades da entidade Tag.
    // 'id' pode ser nulo se a tag ainda não foi salva no banco de dados.
    public $id = null;
    // 'nome' da tag, também pode ser nulo inicialmente.
    public $nome = null;

    /**
     * Retorna o ID da tag.
     * @return mixed O ID da tag (pode ser int ou null).
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Define o ID da tag.
     * @param mixed $id O ID a ser definido.
     * @return TagEntity Retorna a própria instância da entidade para encadeamento de chamadas.
     */
    public function setId($id): TagEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Retorna o nome da tag.
     * @return mixed O nome da tag (pode ser string ou null).
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Define o nome da tag.
     * @param mixed $nome O nome a ser definido.
     * @return TagEntity Retorna a própria instância da entidade para encadeamento de chamadas.
     */
    public function setNome($nome): TagEntity
    {
        $this->nome = $nome;
        return $this;
    }
}