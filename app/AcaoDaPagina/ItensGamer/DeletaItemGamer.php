<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer;
use Vios\Juridico\App\Excecoes\AvisoParaOUsuarioException;
use Vios\Juridico\App\Routes\UrlRedir;
use Vios\Juridico\App\Util\Integer;

final class DeletaItensGamer
{
    private const CAMINHO_PAGINA = '?pag=sys/itens_gamer/itens_gamer.php';
    private $consulta;
    private $gravador;

    public function __construct(ConsultaItensGamer  $consulta, GravarItemGamer  $gravador)
    {
        $this->consulta = $consulta;
        $this->gravador = $gravador;
    }

    /**
     * @throws AvisoParaOUsuarioException
     */
    public function executa(int $itemId): UrlRedir
    {
        $itemId = Integer::int($itemId);
        if ($itemId <= 0) {
            return new UrlRedir(self::CAMINHO_PAGINA, 'ID do item não informado');
        }
        $entidade = $this->consulta->getById($itemId);
        if (!$entidade->getId()) {
            throw new AvisoParaOUsuarioException('Item não localizado no sistema');
        }
        if ($entidade->isItemAtivo() === false) {
            throw new AvisoParaOUsuarioException('Este item está inativo');
        }
        $entidade->setItemAtivo(false);
        $this->gravador->executa($entidade);
        return new UrlRedir(self::CAMINHO_PAGINA, 'Item desativado com sucesso');
    }
}
