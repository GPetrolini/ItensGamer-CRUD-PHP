<?php

declare(strict_types=1);

namespace Vios\Juridico\App\AcaoDePagina\Sys\ItensGamer;

// Importa classes necessárias para a ação de deletar itens.
use Vios\Juridico\App\Consultas\ItensGamer\ConsultaItensGamer; // Para consultar o item antes de deletar.
use Vios\Juridico\App\Excecoes\AvisoParaOUsuarioException; // Para lançar avisos ao usuário.
use Vios\Juridico\App\Routes\UrlRedir; // Para redirecionamento após a operação.
use Vios\Juridico\App\Util\Integer; // Utilitário para garantir que valores sejam inteiros.

/**
 * DeletaItensGamer
 *
 * Esta classe é responsável por "deletar" (desativar) um Item Gamer no sistema.
 * Ela implementa uma "soft delete", marcando o item como inativo, em vez de removê-lo fisicamente.
 */
final class DeletaItensGamer
{
    // Caminho da página principal do CRUD de Itens Gamer, usado para redirecionamento.
    private const CAMINHO_PAGINA = '?pag=sys/itens_gamer/itens_gamer.php';
    private $consulta; // Serviço de consulta para buscar o item.
    private $gravador; // Serviço de gravação para atualizar o status do item.

    /**
     * Construtor da classe DeletaItensGamer.
     * @param ConsultaItensGamer $consulta Serviço para buscar o item.
     * @param GravarItemGamer $gravador Serviço para gravar (neste caso, desativar) o item.
     */
    public function __construct(ConsultaItensGamer $consulta, GravarItemGamer $gravador)
    {
        $this->consulta = $consulta;
        $this->gravador = $gravador;
    }

    /**
     * Executa a ação de desativar (soft delete) um Item Gamer.
     *
     * @param int $itemId O ID do item a ser desativado.
     * @return UrlRedir Objeto para redirecionamento após a operação.
     * @throws AvisoParaOUsuarioException Se o item não for encontrado ou já estiver inativo.
     */
    public function executa(int $itemId): UrlRedir
    {
        // Garante que o ID é um inteiro.
        $itemId = Integer::int($itemId);
        
        // Verifica se o ID foi informado.
        if ($itemId <= 0) {
            return new UrlRedir(self::CAMINHO_PAGINA, 'ID do item não informado');
        }
        
        // Busca o item pelo ID.
        $entidade = $this->consulta->getById($itemId);
        
        // Se o item não for encontrado, lança uma exceção.
        if (!$entidade->getId()) {
            throw new AvisoParaOUsuarioException('Item não localizado no sistema');
        }
        
        // Se o item já estiver inativo, lança uma exceção.
        if ($entidade->isItemAtivo() === false) {
            throw new AvisoParaOUsuarioException('Este item já está inativo');
        }
        
        // Marca o item como inativo (false).
        $entidade->setItemAtivo(false);
        
        // Usa o serviço de gravação para persistir a mudança de status.
        $this->gravador->executa($entidade);
        
        // Redireciona para a página principal com uma mensagem de sucesso.
        return new UrlRedir(self::CAMINHO_PAGINA, 'Item desativado com sucesso');
    }
}