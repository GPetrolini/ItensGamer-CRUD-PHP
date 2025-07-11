<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Constants\Campos\ItensGamer;

/**
 * ItensGamerCamposConstants
 *
 * Esta classe armazena todas as constantes relacionadas aos campos e tabelas
 * dos Itens Gamer. Isso ajuda a centralizar informações e evitar "magic strings"
 * espalhadas pelo código, facilitando a manutenção.
 */
final class ItensGamerCamposConstants
{
    // Nome da tabela principal de itens gamer no banco de dados.
    public const TABELA = 'itens_gamer';

    // --- Constantes para o campo ID ---
    public const ID = 'id';         // Nome da coluna no banco de dados.
    public const ID_NAME = 'form[id]'; // Atributo 'name' usado no formulário HTML.
    public const ID_LABEL = 'ID';   // Rótulo exibido para o usuário.

    // --- Constantes para o campo Nome ---
    public const NOME = 'nome';
    public const NOME_NAME = 'form[nome]';
    public const NOME_LABEL = 'Nome do item';

    // --- Constantes para o campo Descrição ---
    public const DESCRICAO = 'descricao';
    public const DESCRICAO_NAME = 'form[descricao]';
    public const DESCRICAO_LABEL = 'Descrição Detalhada';

    // --- Constantes para o campo Tipo do Item ---
    public const TIPO = 'tipo';
    public const TIPO_NAME = 'form[tipo]';
    public const TIPO_LABEL = 'Tipo do item';
    // Opções disponíveis para o campo 'Tipo do item' (usado em selects).
    public const TIPO_OPCOES = [
        'Teclado' => 'Teclado',
        'Mouse' => 'Mouse',
        'Mousepad' => 'Mousepad',
        'Joystick/Controle' => 'Joystick/Controle',
        'Volante' => 'Volante Gamer',
        'Microfone' => 'Microfone',
        'Webcam' => 'Webcam',
        'Monitor' => 'Monitor',
        'Headset/Fone' => 'Headset/Fone de Ouvido',
        'Caixa de Som' => 'Caixa de Som',
        'Projetor' => 'Projetor',
        'Processador' => 'Processador (CPU)',
        'Placa de Video' => 'Placa de Vídeo (GPU)',
        'Placa-Mae' => 'Placa-Mãe',
        'Memoria RAM' => 'Memória RAM',
        'Armazenamento SSD/HD' => 'Armazenamento (SSD/HD)',
        'Fonte de Alimentacao' => 'Fonte de Alimentação (PSU)',
        'Gabinete' => 'Gabinete',
        'Cooler/Refrigeracao' => 'Cooler/Refrigeração',
        'Notebook Gamer' => 'Notebook Gamer',
        'PC Gamer Montado' => 'PC Gamer Montado',
        'Console' => 'Console (Playstation, Xbox, etc)',
        'Cadeira Gamer' => 'Cadeira Gamer',
        'Mesa Gamer' => 'Mesa Gamer',
        'Cabo/Adaptador' => 'Cabo/Adaptador',
        'Software' => 'Software (Jogos, Antivírus, etc)',
        'Outro' => 'Outro',
    ];

    // --- Constantes para o campo Tags ---
    public const TAGS = 'tags';
    public const TAGS_NAME = 'form[tags][]'; // Nome para multi-seleção (array).
    public const TAGS_LABEL = 'Tags';
    // As opções para TAGS_OPCOES seriam definidas aqui, se existissem.

    // --- Constantes para o campo Cor do Emblema ---
    public const COR_EMBLEMA = 'cor_emblema';
    public const COR_EMBLEMA_NAME = 'form[cor_emblema]';
    public const COR_EMBLEMA_LABEL = 'Cor do item';

    // --- Constantes para o campo Quantidade ---
    public const QUANTIDADE = 'quantidade';
    public const QUANTIDADE_NAME = 'form[quantidade]';
    public const QUANTIDADE_LABEL = 'Quantidade em estoque';

    // --- Constantes para o campo Preço de Venda ---
    public const PRECO_VENDA = 'preco_venda';
    public const PRECO_VENDA_NAME = 'form[preco_venda]';
    public const PRECO_VENDA_LABEL = 'Preco de venda (R$)';

    // --- Constantes para o campo Item Ativo ---
    public const ITEM_ATIVO = 'item_ativo';
    public const ITEM_ATIVO_NAME = 'form[item_ativo]';
    public const ITEM_ATIVO_LABEL = 'Item ativo?';

    // --- Constantes para o campo Data de Cadastro ---
    public const DATA_CADASTRO = 'data_cadastrp';
    public const DATA_CADASTRO_LABEL = 'Data de cadastro';
}