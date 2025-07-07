<?php
declare(strict_types=1);

namespace Vios\Juridico\App\Servicos\Historicos\ItensGamer;

use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\DAO\DAO;
use Vios\Juridico\App\Excecoes\IdNaoInformadoException;
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCampos;
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposEntidadeRelacionalUtil;
use Vios\Juridico\App\Servicos\Historicos\HistoricoResolver;
use Vios\Juridico\App\Servicos\Historicos\HistoricoVO;
use Vios\Juridico\App\Servicos\Historicos\HistoricoEntidadesRelacionais;
use Vios\Juridico\App\Servicos\Historicos\ListaHistorico;
use Vios\Juridico\App\Servicos\Historicos\HistoricoCampos\HistoricoCamposFactory as CamposFactory;

final class ItemGamerHistorico extends HistoricoResolver
{
    private $listaHistorico;
    private const TABELA_HISTORICO = 'itens_gamer_historico';
    private const FK_TABELA_HISTORICO = 'item_gamer_id';
    private $camposFactory;

    public function __construct(DAO $dao, ListaHistorico $listaHistorico, CamposFactory $camposFactory)
    {
        $this->listaHistorico = $listaHistorico;
        $this->camposFactory = $camposFactory;
        $entidadeRelacionais = new HistoricoEntidadesRelacionais($dao);
        parent::__construct($dao, $entidadeRelacionais, ...$this->getCampos());
    }

    public function listaHistorico(int $itemGamerId): string
    {
        return $this->listaHistorico->render(self::TABELA_HISTORICO, self::FK_TABELA_HISTORICO, $itemGamerId);
    }

    /**
     * @throws IdNaoInformadoException
     */
    public function registroHistoricoCriacao(int $id): void
    {
        if ($id <= 0) {
            return;
        }
        $instancia = $this->instanciaHistoricoVO($id);
        $instancia->addTexto('Registro do item criado no sistema');
        parent::persisteHistorico($instancia);
    }

    /**
     * @throws IdNaoInformadoException
     */
    protected function instanciaHistoricoVO(int $id): HistoricoVO
    {
        return new  HistoricoVO($id, self::FK_TABELA_HISTORICO, self::TABELA_HISTORICO);
    }
    protected function tabelaPersistenciaHistorico(): string
    {
        return self::TABELA_HISTORICO;
    }

    protected function chavesIgnoradas(): array
    {

        return [ItensGamerCamposConstants::ID, ItensGamerCamposConstants::DATA_CADASTRO];
    }

    /**
     * Define quais campos do nosso ItemGamer queremos que o histórico observe.
     * @return HistoricoCampos[]
     */
    protected function getCampos(): array
    {
        $comparacaoDireta = $this->camposFactory->instanciaHistoricoCamposComparacaoDireta();
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::NOME,
            ItensGamerCamposConstants::NOME_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::DESCRICAO,
            ItensGamerCamposConstants::DESCRICAO_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::TIPO,
            ItensGamerCamposConstants::TIPO_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::COR_EMBLEMA,
            ItensGamerCamposConstants::COR_EMBLEMA_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::QUANTIDADE,
            ItensGamerCamposConstants::QUANTIDADE_LABEL
        );
        $comparacaoDireta->registraCampo(
            ItensGamerCamposConstants::PRECO_VENDA,
            ItensGamerCamposConstants::PRECO_VENDA_LABEL
        );


        $booleanos = $this->camposFactory->instanciaHistoricoCamposBooleanos();
        $booleanos->registraCampo(
            ItensGamerCamposConstants::ITEM_ATIVO,
            ItensGamerCamposConstants::ITEM_ATIVO_LABEL
        );

        $comparacaoMultipla = $this->camposFactory->instanciaHistoricoCamposEntidadesRelacionamentoMultiplo();
        $comparacaoMultipla->registraCampo(
            ItensGamerCamposConstants::TAGS,
            HistoricoCamposEntidadeRelacionalUtil::instanciaEntidadeRelacionalTag(ItensGamerCamposConstants::TAGS_LABEL)
        );
        return [
            $comparacaoDireta,
            $booleanos,
            $comparacaoMultipla
        ];
    }
}
