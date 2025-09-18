# CEMEAC Estúdios
### Sistema de Gerenciamento

🎬 Sistema de Gerenciamento de Gravações – v2
Este projeto tem como objetivo facilitar o controle e a organização das gravações de videoaulas em iniciativas de Educação Híbrida. A versão 2 traz melhorias significativas na usabilidade, no fluxo de agendamento e no acompanhamento das etapas de produção dos conteúdos educacionais.
🚀 Funcionalidades
• 	Cadastro de professores, disciplinas e turmas
• 	Agendamento de sessões de gravação com controle de status
• 	Relatórios de pendências e produtividade
• 	Interface otimizada para equipes pedagógicas e técnicas
• 	Histórico de gravações e acompanhamento por etapa (planejamento, gravação, edição, publicação)
🧠 Motivação
A crescente demanda por conteúdos digitais na educação exige ferramentas que tornem o processo de produção mais ágil, organizado e colaborativo. Este sistema foi criado para atender essa necessidade, conectando todos os envolvidos em um fluxo claro e eficiente.
🛠️ Tecnologias utilizadas
• 	PHP
• 	HTML/CSS
• 	JavaScript
• 	MySQL
(Adapte conforme as tecnologias que você realmente usou)
📦 Instalação
1. 	Clone o repositório:

2. 	Configure o ambiente local (ex: Laragon, XAMPP)
3. 	Importe o banco de dados e ajuste os arquivos de configuração
📄 Licença
Este projeto está sob a licença MIT. Sinta-se livre para usar, modificar e contribuir.


## Sobre o Projeto
O **CEMEAC Estúdios** é um sistema de gerenciamento projetado para gerenciar usuários e suas respectivas funções dentro da estrutura do CEMEAC. O sistema conta com funcionalidades de autenticação segura, cadastro de novos usuários e redefinição de senha, com foco em uma experiência de usuário profissional e na segurança dos dados.

## Funcionalidades Principais
- **Autenticação Segura:** Login de usuários com verificação de credenciais e gerenciamento de sessão.
- **Cadastro de Usuários:** Novo usuário pode se cadastrar usando um e-mail institucional e um código de convite.
- **Recuperação de Senha:** Funcionalidade de redefinição de senha com token de segurança.
- **Painel de Controle:** Página de acesso restrito para usuários autenticados.
- **Log de Atividades:** Registro de tentativas de cadastro para monitoramento.

## Tecnologias Utilizadas
- **Back-end:** PHP
- **Banco de Dados:** MySQL
- **Conexão com o DB:** PDO (PHP Data Objects) com Prepared Statements
- **Front-end:** HTML5, CSS3, JavaScript
- **Frameworks/Bibliotecas:** Bootstrap, Font Awesome

## Estrutura de Arquivos
## SOFTWAREGERENCIAMENTO/
##  ├── api/ OK
##  │   ├── adicionar_agendamento.php ===> 10
##  │   ├── atualizar_status.php ===> 11
##  │   ├── obter_agendamentos.php ===> 12
##  │   └── obter_professores.php ===> 13
##  ├── assets/
##  │   ├── css/ Ok
##  │   │   ├── bootstrap.min.css===> 15
##  │   │   ├── owl.carousel.min.css ===> 16
##  │   │   ├── owl.theme.default.css ===> 17
##  │   │   └── painel.css ===> 18
##  │   ├── img/
##  │   └── js/OK 
##  │       ├── bootstrap.bundle.min.js ===> 19
##  │       ├── bootstrap.min.js ===> 20
##  │       ├── jquery-3.6.0.min.js ===> 21
##  │       ├── jquery.min.js ===> 22
##  │       ├── owl.carousel.min.js ===> 23
##  │       └── painel.js ===> 23
##  ├── auth/ Ok
##  │   ├── cadastrar_usuario.php ===> 04
##  │   ├── gerar_hash.php ===> 05
##  │   ├── login.php ===> 06
##  │   ├── logout.php ===> 07
## │   ├── redefinir-senha.php ===> 08
## │   └── register.php ===> 09
##  ├── config/ Ok
##  │   ├── config.php  ===>01 
## │   ├── database.php ===> 02
## │   └── url.php ===> 03
## ├── pages/OK
## │   └── painel-atualizacao.php ===> 14
## ├── .htaccess
## ├── index.php ===> 25
## ├── README.md
## └── test_connection.php ===> 24



##  -- Usuário para o Estúdio 1
## INSERT INTO usuarios (username, password, perfil, studio_responsavel) 
## VALUES ('adaorios', '$2y$10$cOfim0W7OjhVJMdhTfp3V.FCot3WKZz4n/NbQtwb6sJy5wK4ASrYW', 'editor', 'Estúdio 1');

## -- Usuário para o Estúdio 2
## INSERT INTO usuarios (username, password, perfil, studio_responsavel) 
## VALUES ('keile', '$2y$10$TaZmXwsTbLBuooWEfSJ7bOLcsEcFGHS3UNIXMNBuZpAUoUcn2gfdu', 'editor', 'Estúdio 2');

##  -- Usuário para o Estúdio 3
## INSERT INTO usuarios (username, password, perfil, studio_responsavel) 
## VALUES ('luizao', '$2y$10$FDB78XtPo6J2oLJie5Ict.b863rzEgFyce2Pk7fA5SesIEMMXFE5W', 'editor', 'Estúdio 3');

## -- Usuário para o Estúdio 4
## INSERT INTO usuarios (username, password, perfil, studio_responsavel) 
## VALUES ('edinardo', '$2y$10$LMMH3uWDZxIluIXjLxUXpOq7zocU657RJ8bClH8QhfbrwErlo6Bly', 'editor', 'Estúdio 4');

