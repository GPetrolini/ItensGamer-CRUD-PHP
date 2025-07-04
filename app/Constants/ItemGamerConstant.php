<?php

declare(strict_types=1);

namespace Vios\Juridico\App\Constants\Campos\ItensGamer;

final class ItensGamerCamposConstants
{
    public const TABELA = 'itens_gamer';

    public const ID = 'id';

    public const ID_NAME = 'form[id]';

    public const ID_LABEL = 'ID';

    public const NOME = 'nome';

    public const NOME_NAME = 'form[nome]';

    public const NOME_LABEL = 'Nome do item';

    public const DESCRICAO = 'descricao';

    public const DESCRICAO_NAME = 'form[descricao]';

    public const DESCRICAO_LABEL = 'Descrição Detalhada';

    public const TIPO = 'tipo';

    public const TIPO_NAME = 'form[tipo]';

    public const TIPO_LABEL = 'Tipo do item';

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
    public const TAGS = 'tags';

    public const TAGS_NAME = 'form[tags][]';

    public const TAGS_LABEL = 'Tags';

    public const COR_EMBLEMA = 'cor_emblema';

    public const COR_EMBLEMA_NAME = 'form[cor_emblema]';

    public const COR_EMBLEMA_LABEL = 'Cor do item';

    public const QUANTIDADE = 'quantidade';

    public const QUANTIDADE_NAME = 'form[quantidade]';

    public const QUANTIDADE_LABEL = 'Quantidade em estoque';

    public const PRECO_VENDA = 'preco_venda';

    public const PRECO_VENDA_NAME = 'form[preco_venda]';

    public const PRECO_VENDA_LABEL = 'Preco de venda (R$)';

    public const ITEM_ATIVO = 'item_ativo';

    public const ITEM_ATIVO_NAME = 'form[item_ativo]';
    
    public const ITEM_ATIVO_LABEL = 'Item ativo?';

    public const DATA_CADASTRO = 'data_cadastrp';

    public const DATA_CADASTRO_LABEL = 'Data de cadastro';
}
