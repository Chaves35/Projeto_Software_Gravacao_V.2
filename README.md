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
##  ├── api/
##  │   ├── adicionar_agendamento.php
##  │   ├── atualizar_status.php
##  │   ├── obter_agendamentos.php
##  │   └── obter_professores.php
##  ├── assets/
##  │   ├── css/ Ok
##  │   │   ├── bootstrap.min.css
##  │   │   ├── owl.carousel.min.css
##  │   │   ├── owl.theme.default.css
##  │   │   └── painel.css
##  │   ├── img/
##  │   └── js/OK 
##  │       ├── bootstrap.bundle.min.js
##  │       ├── bootstrap.min.js
##  │       ├── jquery-3.6.0.min.js
##  │       ├── jquery.min.js
##  │       ├── owl.carousel.min.js
##  │       └── painel.js
##  ├── auth/
##  │   ├── cadastrar_usuario.php
##  │   ├── gerar_hash.php
##  │   ├── login.php
##  │   ├── logout.php
## │   ├── redefinir-senha.php
## │   └── register.php
##  ├── config/ 
##  │   ├── config.php
## │   ├── database.php
## │   └── url.php
## ├── pages
## │   └── painel-atualizacao.php 
## ├── .htaccess
## ├── index.php 
## ├── README.md
## └── test_connection.php





