# CRUD de Itens Gamer em PHP

Este projeto é um sistema CRUD (Create, Read, Update, Delete) completo para gerenciar um catálogo de itens gamer, desenvolvido em PHP.

O sistema foi criado como uma tarefa de integração e aprendizado durante minhas primeiras semanas em um novo ambiente profissional. O objetivo principal foi me familiarizar com a base de código da empresa, o fluxo de trabalho e, principalmente, servir como minha primeira imersão prática na linguagem PHP, que eu não conhecia previamente.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![PHPStorm](https://img.shields.io/badge/PHPStorm-000000?style=for-the-badge&logo=phpstorm&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

---

## 📜 Sobre o Projeto

A aplicação permite que um usuário realize as quatro operações básicas de gerenciamento de dados em um banco de dados MySQL, simulando uma pequena parte de um e-commerce ou catálogo de produtos. Todo o desenvolvimento foi focado em aplicar conceitos fundamentais de PHP em um cenário prático e real.

## ✨ Funcionalidades

-   **Listar Itens:** Visualização de todos os itens cadastrados.
-   **Adicionar Novos Itens:** Formulário para inserção de novos produtos.
-   **Editar Itens:** Modificação das informações de um item existente.
-   **Excluir Itens:** Remoção de um item do banco de dados.

---

## 🚀 Diário de Bordo: Fases do Desenvolvimento e Aprendizados

Este projeto foi construído em etapas. Cada fase representa um novo conjunto de conceitos aprendidos e aplicados. Abaixo está o registro detalhado dessa jornada.

<details>
<summary><strong>▶️ Fase 0: Configuração e Versionamento Inicial</strong></summary>

### Objetivo da Fase
Preparar o ambiente de desenvolvimento para o controle de versão com Git e GitHub. Esta fase foi crucial para garantir que todo o progresso do projeto fosse devidamente registrado e pudesse ser compartilhado.

### Ferramentas e Comandos
-   **Git:** `git init`, `git config`, `git add .`, `git commit`
-   **GitHub:** Criação de um repositório remoto.
-   **Conexão Local/Remoto:** `git remote add origin`, `git push -u origin main`

### Conceitos Abordados
-   **Inicialização de um repositório Git local** em um projeto já existente.
-   **Configuração de identidade do autor** (user.name e user.email) no Git.
-   **Processo de Staging, Commit e Push:** O fluxo fundamental para salvar e enviar alterações para o GitHub.
-   **Conexão entre o repositório local e o remoto** no GitHub.

</details>

<details>
<summary><strong>▶️ Fase 1: A Entidade de Dados (ItemGamerEntity)</strong></summary>

### Objetivo da Fase
Construir a fundação do sistema criando a classe `ItemGamerEntity`. Ela serve como um "molde" para representar os dados de um item em nosso código PHP, garantindo que a aplicação manipule os dados de forma segura e estruturada.

### Arquivos Criados
- `app/Entities/ItensGamer/ItemGamerEntity.php`

### Conceitos Abordados
-   **Entidade:** Uma classe simples cuja única responsabilidade é carregar e transportar dados.
-   **Encapsulamento:** Uso de propriedades `private` para proteger os dados.
-   **Getters e Setters:** Métodos públicos para uma interface controlada.
-   **Tipagem no PHP:** Uso de PHPDoc (`@var tipo|null`) para documentar os tipos.
-   **Interface Fluente:** Retornar `$this` (`: self`) nos setters para encadear chamadas.
-   **Padrões de Nomenclatura:** Adoção de `camelCase` e o prefixo `is` para booleanos.
-   **Método `toArray()`:** Para converter o objeto em um array associativo.

</details>

<details>
<summary><strong>▶️ Fase 2: Conexão com o Banco e Listagem de Itens (Read)</strong></summary>

### Objetivo da Fase
Estabelecer a conexão com o banco de dados MySQL e implementar a funcionalidade de leitura (Read), exibindo todos os itens cadastrados em uma tabela na página principal.

### Arquivos Envolvidos
-   `db.php` (ou arquivo de configuração de banco de dados)
-   `index.php`

### Conceitos Abordados
-   **Conexão com Banco de Dados:** Uso de PHP para se conectar a um servidor MySQL.
-   **SQL `SELECT`:** Execução de consultas para buscar todos os registros (`SELECT * FROM itens`).
-   **Laços de Repetição em PHP:** Uso de `while` ou `foreach` para percorrer os resultados da consulta.
-   **HTML Dinâmico:** Geração de linhas de uma tabela (`<tr>` e `<td>`) dinamicamente com dados vindos do PHP.

</details>

<details>
<summary><strong>▶️ Fase 3: Inserção de Novos Itens (Create)</strong></summary>

### Objetivo da Fase
Implementar a funcionalidade de criação (Create), permitindo que o usuário adicione novos itens ao catálogo através de um formulário HTML.

### Arquivos Envolvidos
-   `create.php` (ou um formulário na `index.php`)
-   Um script PHP para processar o envio do formulário.

### Conceitos Abordados
-   **Formulários HTML:** Criação de formulários com o método `POST`.
-   **Variáveis Superglobais:** Captura de dados enviados pelo formulário usando `$_POST` no PHP.
-   **SQL `INSERT`:** Construção e execução de uma instrução SQL para inserir um novo registro no banco de dados.
-   **Redirecionamento:** Uso da função `header('Location: ...')` para redirecionar o usuário de volta à página principal após a inserção.

</details>

<details>
<summary><strong>▶️ Fase 4: Edição e Atualização de Itens (Update)</strong></summary>

### Objetivo da Fase
Desenvolver a funcionalidade de atualização (Update), que permite ao usuário editar as informações de um item já existente.

### Arquivos Envolvidos
-   `edit.php` (formulário de edição)
-   Um script PHP para processar a atualização.

### Conceitos Abordados
-   **Passagem de Parâmetros via URL:** Envio do ID do item a ser editado (ex: `edit.php?id=5`).
-   **Captura de Parâmetros GET:** Uso de `$_GET['id']` para identificar qual registro buscar.
-   **SQL `SELECT ... WHERE`:** Busca dos dados de um item específico para preencher o formulário de edição.
-   **SQL `UPDATE`:** Construção e execução de uma instrução para atualizar o registro no banco com os novos dados.

</details>

<details>
<summary><strong>▶️ Fase 5: Exclusão de Itens (Delete)</strong></summary>

### Objetivo da Fase
Implementar a funcionalidade final do CRUD, a exclusão (Delete), permitindo que itens sejam removidos permanentemente do banco de dados.

### Arquivos Envolvidos
-   `delete.php` (ou um link com parâmetro na `index.php`)

### Conceitos Abordados
-   **Confirmação de Ação:** (Idealmente) uso de JavaScript para pedir confirmação ao usuário antes de excluir.
-   **SQL `DELETE`:** Execução de uma instrução `DELETE FROM ... WHERE id = ?` para remover o registro específico.
-   **Segurança:** Importância de garantir que apenas o item correto seja excluído, validando o ID recebido.

</details>

<details>
<summary><strong>▶️ Fase 6: Documentação e Finalização</strong></summary>

### Objetivo da Fase
Consolidar a documentação do projeto, criando um arquivo `README.md` detalhado que explica o propósito, as funcionalidades e a jornada de desenvolvimento do projeto.

### Arquivos Envolvidos
-   `README.md`

### Conceitos Abordados
-   **Markdown:** Utilização da linguagem de marcação para formatar textos, listas, links e blocos de código.
-   **Documentação de Software:** A importância de explicar o projeto para futuros desenvolvedores (incluindo você mesmo) e para quem visita o repositório.
-   **Versionamento de Documentação:** Commitar o `README.md` como parte essencial do código do projeto.

</details>