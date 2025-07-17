<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\Excecoes\AvisoParaOUsuarioException;
use Vios\Juridico\App\Excecoes\IdNaoInformadoException;
use Vios\Juridico\App\Routes\UrlRedir;

final class DeletaItensGamer
{
    private $consulta;

    private $gravador;

    public function __construct(ConsultaItensGamer $consulta, GravarItemGamer $gravador)
    {
        $this->consulta = $consulta;
        $this->gravador = $gravador;
    }

    public function executa(int $itemId): UrlRedir
    {
        if ($itemId <= 0) {
            throw new IdNaoInformadoException('item gamer');
        }

        $entidade = $this->consulta->getById($itemId);
        if ($entidade->getId() > 0) {
            throw new AvisoParaOUsuarioException('Item não localizado no sistema');
        }

        if ($entidade->isItemAtivo() === false) {
            throw new AvisoParaOUsuarioException('Este item está inativo');
        }

        $entidade->setIsItemAtivo(false);

        $this->gravador->executa($entidade);

        return new UrlRedir(
            '?' . http_build_query(['pag' => ListaItensGamer::CAMINHO_PAGINA]),
            'Item desativado com sucesso'
        );
    }
}
