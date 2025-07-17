<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Servicos\Historicos\ItensGamer;

use Vios\Juridico\App\Constants\Campos\ItensGamer\ItensGamerCamposConstants;
use Vios\Juridico\App\DAO\DAO;
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

    public function __construct(
        CamposFactory $camposFactory,
        DAO $dao,
        HistoricoEntidadesRelacionais $entidadeRelacionais,
        ListaHistorico $listaHistorico
    ) {
        $this->listaHistorico = $listaHistorico;

        parent::__construct($dao, $entidadeRelacionais, ...[
            $this->getCamposComparacaoBoleanos($camposFactory),
            $this->getCamposComparacaoDireta($camposFactory),
            $this->getCamposComparacaoRelacionamentoMultiplo($camposFactory),
        ]);
    }

    public function listaHistorico(int $itemGamerId): string
    {
        return $this->listaHistorico->render(self::TABELA_HISTORICO, self::FK_TABELA_HISTORICO, $itemGamerId);
    }

    public function registroHistoricoCriacao(int $id): void
    {
        if ($id <= 0) {
            return;
        }
        $instancia = $this->instanciaHistoricoVO($id);
        $instancia->addTexto('Registro do item criado no sistema');
        parent::persisteHistorico($instancia);
    }

    protected function instanciaHistoricoVO(int $id): HistoricoVO
    {
        return new HistoricoVO($id, self::FK_TABELA_HISTORICO, self::TABELA_HISTORICO);
    }

    protected function tabelaPersistenciaHistorico(): string
    {
        return self::TABELA_HISTORICO;
    }

    protected function chavesIgnoradas(): array
    {
        return [ItensGamerCamposConstants::ID, ItensGamerCamposConstants::DATA_CADASTRO];
    }

    private function getCamposComparacaoBoleanos(CamposFactory $historicoCamposFactory): HistoricoCampos
    {
        $campos = [
            ItensGamerCamposConstants::ITEM_ATIVO => ItensGamerCamposConstants::ITEM_ATIVO_LABEL
        ];

        $instancia = $historicoCamposFactory->instanciaHistoricoCamposBooleanos();
        foreach ($campos as $name => $label) {
            $instancia->registraCampo($name, $label);
        }

        return $instancia;
    }

    private function getCamposComparacaoDireta(CamposFactory $historicoCamposFactory): HistoricoCampos
    {
        $campos = [
            ItensGamerCamposConstants::NOME => ItensGamerCamposConstants::NOME_LABEL,
            ItensGamerCamposConstants::DESCRICAO => ItensGamerCamposConstants::DESCRICAO_LABEL,
            ItensGamerCamposConstants::TIPO => ItensGamerCamposConstants::TIPO_LABEL,
            ItensGamerCamposConstants::COR_EMBLEMA => ItensGamerCamposConstants::COR_EMBLEMA_LABEL,
            ItensGamerCamposConstants::QUANTIDADE => ItensGamerCamposConstants::QUANTIDADE_LABEL,
            ItensGamerCamposConstants::PRECO_VENDA => ItensGamerCamposConstants::PRECO_VENDA_LABEL,
        ];

        $instancia = $historicoCamposFactory->instanciaHistoricoCamposComparacaoDireta();
        foreach ($campos as $name => $label) {
            $instancia->registraCampo($name, $label);
        }

        return $instancia;
    }

    private function getCamposComparacaoRelacionamentoMultiplo(CamposFactory $historicoCamposFactory): HistoricoCampos
    {
        $campos = [
            ItensGamerCamposConstants::TAGS => HistoricoCamposEntidadeRelacionalUtil::instanciaEntidadeRelacionalTag(
                ItensGamerCamposConstants::TAGS_LABEL
            ),
        ];

        $instancia = $historicoCamposFactory->instanciaHistoricoCamposEntidadesRelacionamentoMultiplo();
        foreach ($campos as $name => $label) {
            $instancia->registraCampo($name, $label);
        }

        return $instancia;
    }
}
