# 🎲 Calculando a Sorte

> Plataforma interativa de jogos matemáticos e calculadora da sorte desenvolvida com as quatro operações básicas (adição, subtração, multiplicação e divisão).

---

## 📋 Sumário
- [Visão Geral](#-visão-geral)
- [Stack Tecnológica](#-stack-tecnológica)
- [Regras de Negócio (CORE)](#-regras-de-negócio-core)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Como Rodar o Projeto](#-como-rodar-o-projeto)
- [Contas de Acesso para Testes](#-contas-de-acesso-para-testes)
- [Auditoria e Segurança](#-auditoria-e-segurança)
- [Licença](#-licença)

---

## 🎯 Visão Geral
**Calculando a Sorte** transforma o uso clássico de uma calculadora em uma experiência gamificada e emocionante:
1. Os números (0 a 9) podem ser digitados normalmente como em qualquer calculadora.
2. A cada rodada, **uma operação matemática básica (+, −, ×, ÷) é sorteada pela roleta** para o usuário poder usar.
3. As operações bloqueadas podem ser liberadas de duas formas:
   - **Assistindo a um anúncio rápido de 5 segundos** (que libera todas as operações temporariamente e concede moedas bônus).
   - **Assinando o VIP Premium** (que libera todas as 4 operações de forma definitiva).
4. Plataforma com **minijogos matemáticos** (Blitz de 45 segundos, Qual é o Sinal? e Roleta da Fortuna) para treinar velocidade de cálculo e acumular moedas.
5. Sistema completo de **históricos e logs de auditoria** que registra detalhadamente qualquer alteração feita por qualquer usuário.

---

## 🚀 Stack Tecnológica
- **Backend:** PHP estruturado moderno com gerenciamento seguro de sessões nativas (`session_start()`, cookies HttpOnly e SameSite).
- **Banco de Dados:** MySQL com **PDO** (Prepared Statements) para blindagem completa contra SQL Injections.
- **Frontend:** HTML5 semântico, PHP Views, CSS3 e **Tailwind CSS** com design responsivo **Mobile-First**.
- **Segurança de Credenciais:** Hashes criptográficos com algoritmo **Bcrypt** (`password_hash` e `password_verify`).
- **Feedback & UX:**
  - Sistema de notificações **Toast** customizadas para erros, alertas e sucessos.
  - Substituição total de caixas nativas do navegador (`alert`, `confirm`) por **Modais estilizados em Tailwind**.

---

## ⚙️ Regras de Negócio (CORE)

| Regra | Implementação |
|---|---|
| **Senhas Bcrypt** | Tratadas com `password_hash($senha, PASSWORD_BCRYPT)` e verificadas via `password_verify`. |
| **Logs de Auditoria** | Tabela `logs` com registro de IP, User-Agent, ID do usuário, tipo de ação e descrição detalhada de qualquer alteração. |
| **Operação Sorteada** | Modo Gratuito restringe o cálculo à operação atualmente sorteada, solicitando desbloqueio para as demais. |
| **Desbloqueio por Anúncio** | Modal com cronômetro de 5s e barra de progresso que libera todas as 4 operações e credita +25 moedas. |
| **Desbloqueio VIP Premium** | Desbloqueio permanente de todas as operações (+, −, ×, ÷) via moedas ou modo demonstração. |
| **Arquitetura Modular** | Separação entre conexão (`config/db.php` e `bd.php`), scripts de backend (`actions/`), componentes (`includes/`) e páginas. |
| **Modais Universais** | Nenhum popup nativo feio ou bloqueante; todos os avisos e confirmações usam modais em Tailwind. |
| **Feedback via Toast** | Mensagens claras de sucesso, erro ou alerta exibidas em cards flutuantes. |

---

## 📂 Estrutura do Projeto

```text
Calculando a sorte/
│
├── actions/                       # Scripts de backend isolados (Regras de negócio)
│   ├── ad_reward.php              # Processa recompensa por anúncio assistido
│   ├── auth_login.php             # Autenticação com verificação de hash bcrypt
│   ├── auth_logout.php            # Encerramento seguro de sessão com log
│   ├── auth_register.php          # Cadastro de usuário com validação e hash bcrypt
│   ├── calc_process.php           # Validação matemática, permissões e gravação
│   ├── calc_spin.php              # Sorteio aleatório de operações da sorte
│   ├── game_save.php              # Gravação de pontuação e moedas de jogos
│   ├── premium_upgrade.php        # Upgrade para plano VIP Premium
│   └── profile_update.php         # Atualização de dados cadastrais e senha
│
├── assets/                        # Recursos de mídia, scripts e estilos
│   └── js/
│       └── app.js                 # Gerenciamento de anúncios, modais e AJAX
│
├── config/                        # Configurações do sistema
│   ├── config.php                 # Constantes globais e parâmetros de conexão
│   ├── db.php                     # Conexão PDO segura com tratamento de erros
│   └── session.php                # Gerenciamento de sessão nativa e mensagens Flash
│
├── database/                      # Modelagem do banco de dados
│   └── schema.sql                 # Criação do banco, tabelas, chaves e dados padrão
│
├── includes/                      # Componentes reutilizáveis
│   ├── auth_check.php             # Middleware de proteção de páginas
│   ├── footer.php                 # Rodapé HTML com carregamento de scripts
│   ├── header.php                 # Cabeçalho HTML, Tailwind CDN e FontAwesome
│   ├── logger.php                 # Função central de registro de auditoria
│   ├── modal.php                  # Modais universais (substituem alert e confirm)
│   ├── navbar.php                 # Barra de navegação responsiva mobile-first
│   └── toast.php                  # Componente visual flutuante de Toast
│
├── bd.php                         # Atalho de conexão direta conforme requesitos.md
├── calculadora.php                # Tela principal da Calculadora da Sorte
├── historico.php                  # Página de histórico de cálculos e logs de auditoria
├── index.php                      # Landing page e dashboard da plataforma
├── instalar.php                   # Instalador guiado com 1 clique para o MySQL
├── jogos.php                      # Arena com os minijogos matemáticos
├── login.php                      # Tela de login
├── perfil.php                     # Tela de perfil do usuário e estatísticas
├── premium.php                    # Página comparativa e adesão ao VIP Premium
├── register.php                   # Tela de cadastro
└── requesitos.md                  # Especificação original dos requisitos
```

---

## 💻 Como Rodar o Projeto

### Opção 1: Com XAMPP, WampServer ou Laragon (Recomendado)
1. Copie esta pasta (`Calculando a sorte`) para o diretório raiz do seu servidor web:
   - **XAMPP:** `C:\xampp\htdocs\Calculando a sorte`
   - **Laragon:** `C:\laragon\www\Calculando a sorte`
   - **WampServer:** `C:\wamp64\www\Calculando a sorte`
2. Certifique-se de que o **Apache** e o **MySQL** estejam iniciados no painel de controle.
3. Abra seu navegador e acesse o instalador automático:
   ```text
   http://localhost/Calculando%20a%20sorte/instalar.php
   ```
4. Clique no botão **"Criar Banco e Tabelas com 1 Clique"**. O banco `calculando_a_sorte`, tabelas e dados iniciais serão configurados automaticamente!
5. Pronto! Acesse a plataforma e divirta-se.

---

### Opção 2: Com o Servidor Embutido do PHP (CLI)
1. Certifique-se de que o serviço do MySQL esteja rodando (porta 3306 padrão, usuário `root` e senha vazia).
2. No terminal, dentro da pasta do projeto, inicie o servidor:
   ```bash
   php -S localhost:8000
   ```
3. Acesse `http://localhost:8000/instalar.php` para instalar o banco de dados.

---

## 🔑 Contas de Acesso para Testes Rápidos

O instalador já cria duas contas de demonstração:

| Tipo | E-mail | Senha | Características |
|---|---|---|---|
| **Jogador Free** | `jogador@sorte.com` | `123456` | 100 Moedas, 1 operação sorteada por vez, desbloqueio via anúncio. |
| **Membro VIP** | `vip@sorte.com` | `123456` | 500 Moedas, todas as 4 operações liberadas permanentemente. |

*Você também pode cadastrar novos usuários a qualquer momento pela tela `register.php`.*

---

## 🛡️ Auditoria e Segurança
- **Proteção contra SQL Injection:** 100% das consultas utilizam `PDO::prepare()` com binding de parâmetros.
- **Proteção de Senhas:** Senhas nunca são salvas em texto plano; utiliza-se `password_hash($senha, PASSWORD_BCRYPT)`.
- **Rastreabilidade (Logs):** Todas as ações críticas (login, falha de login, logout, realização de cálculos, sorteio de operações, recompensas de anúncios, upgrade VIP e partidas de jogos) registram data/hora, IP de origem, User-Agent e descrição no painel `historico.php`.
- **Interface Mobile-First:** Totalmente adaptada para celulares, tablets e desktops com Tailwind CSS.
- **Sem Popups Nativos:** Qualquer confirmação ou aviso crítico é processado através do componente modal customizado.
