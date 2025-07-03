CRUD de Estoque Gamer em PHP
Um sistema para gerenciar um estoque de itens gamer, construído com PHP 7.2 e seguindo padrões de arquitetura modernos. Este projeto serve como um diário de bordo do processo de desenvolvimento e aprendizado.

Fase 1: A Entidade de Dados (ItemGamerEntity)
Objetivo da Fase
O objetivo desta primeira fase foi construir a fundação do nosso sistema. Criamos a classe ItemGamerEntity, que serve como um "molde" para representar os dados de um item de estoque em nosso código PHP, garantindo que a aplicação manipule os dados de forma segura e estruturada.

Arquivos Criados
app/Entities/ItensGamer/ItemGamerEntity.php

Conceitos Abordados
Entidade: Uma classe simples cuja única responsabilidade é carregar e transportar dados.

Encapsulamento: Uso de propriedades private para proteger os dados.

Getters e Setters: Métodos públicos para uma interface controlada.

Tipagem no PHP 7.2: Uso de PHPDoc (@var tipo|null) para documentar os tipos.

Interface Fluente: Retornar $this (: self) nos setters para encadear chamadas.

Padrões de Nomenclatura: camelCase e o prefixo is para booleanos.

Método toArray(): Para converter o objeto em um array associativo.

Fim da Fase 1

Fase 2: O Formulário de Cadastro (FormularioItemGamer)
Objetivo da Fase
Nesta fase, construímos a classe responsável por gerar a interface de usuário para inserir e editar dados. Em vez de escrever HTML diretamente, seguimos um padrão de abstração de formulário, onde cada campo (texto, select, checkbox) é um objeto PHP reutilizável.

Arquivos Criados
app/AcaoDePagina/Sys/ItensGamer/FormularioItemGamer.php

Conceitos Abordados
Abstração de Formulário: Uso de classes (ex: CampoTextoUmaLinha, CampoSelect) para representar campos.

Herança: Nossa classe de formulário herda (extends) de uma classe base Formulario.

Lógica Condicional: Uso de if ($id > 0) para diferenciar a lógica de "edição" da de "inserção".

Configuração Fluente: Uso de métodos como ->required() e ->setOpcoes([...]).

Select Simples vs. Múltiplo: Entendemos as diferenças, como o [] no nome do campo.

Fim da Fase 2

Fase 3: A Camada de Consulta (DAO)
Objetivo da Fase
Aqui construímos a camada de acesso a dados para leitura. Criamos a classe ConsultaItensGamer, que centraliza toda a comunicação com o banco para realizar SELECTs, e a PesquisaItensGamerEntity para carregar os filtros de busca. Adotamos o padrão de não escrever SQL bruto, mas sim usar um Query Builder.

Arquivos Criados
app/Consultas/ItensGamer/ConsultaItensGamer.php

app/Entities/ItensGamer/PesquisaItensGamerEntity.php

Conceitos Abordados
DAO (Data Access Object): Uma classe com a responsabilidade única de acessar o banco de dados.

CQS (Command Query Separation): Descobrimos que a arquitetura do sistema separa as responsabilidades: classes de Consulta apenas leem, e classes de Gravar apenas escrevem.

Query Builder: Uso de métodos como ->where->equalTo() e ->where->like() para construir consultas SQL de forma programática, segura e legível.

Hidratação de Objetos: Processo de transformar um array de dados brutos vindo do banco em um objeto ItemGamerEntity estruturado.

Fim da Fase 3

Fase 4: A Ação de Gravação
Objetivo da Fase
Nesta fase, implementamos a lógica para escrever no banco (INSERT e UPDATE). Seguindo o padrão CQS, criamos uma classe de ação dedicada, a GravarItemGamer, cuja única responsabilidade é pegar uma entidade preenchida e persistir os dados.

Arquivos Criados
app/AcaoDePagina/Sys/ItensGamer/GravarItemGamer.php

Conceitos Abordados
Classes de Ação: Classes focadas em executar uma única tarefa ou caso de uso do sistema.

Validação de Entrada: Verificação de dados essenciais (if (empty(...))) antes de prosseguir com a gravação.

Preparação de Dados: Conversão de tipos de dados do PHP para formatos que o banco de dados entende (ex: array para string com implode, bool para 1 ou 0).

Lógica de INSERT vs. UPDATE: Uso do ID da entidade para decidir se a operação é uma atualização de um registro existente ou a inserção de um novo.

Fim da Fase 4

Fase 5: O Front Controller (O Cérebro)
Objetivo da Fase
Esta foi a fase final para conectar todas as peças. Criamos o arquivo itens_gamer.php, o ponto de entrada que age como um "controlador de tráfego". Ele analisa a requisição do usuário e decide qual classe de ação (FormularioItemGamer ou GravarItemGamer) deve ser executada.

Arquivos Criados
sys/itens_gamer/itens_gamer.php

Conceitos Abordados
Padrão Front Controller: Ter um único script como ponto de entrada para um módulo.

Roteamento Simples: Uso de $_REQUEST['act'] e $_POST['Gravar'] para decidir qual bloco de código executar.

Injeção de Dependência na Prática: Vimos o ContainerVios em ação, buscando e montando nossas classes e suas dependências automaticamente.

Hidratação de Entidade a partir do $_POST: O processo de pegar os dados crus do formulário e preencher nosso objeto ItemGamerEntity usando os setters.

Depuração Avançada: Aprendemos a usar var_dump() e blocos try...catch para diagnosticar erros "silenciosos" e problemas de namespace.

Fim da Fase 5