# Instruções de Teste (PAP - CRM)

Este guia explica passo a passo como configurar o ambiente e testar o sistema de CRM desenvolvido.

## 1. Requisitos do Sistema

Para correr este projeto, precisas de um servidor web com suporte a PHP e MySQL. A solução mais comum e fácil é usar o **XAMPP** (Windows/Linux/Mac).

### Instalação do XAMPP
1. Faz o download do [XAMPP](https://www.apachefriends.org/pt_br/index.html).
2. Instala o software (mantém as opções padrão).
3. Abre o **XAMPP Control Panel**.
4. Inicia os módulos **Apache** e **MySQL** clicando em "Start".

## 2. Instalação do Projeto

1. Copia a pasta deste projeto para dentro da pasta `htdocs` do XAMPP (geralmente em `C:\xampp\htdocs`).
   - Exemplo: `C:\xampp\htdocs\crm-pap`

## 3. Configuração da Base de Dados

O sistema precisa de uma base de dados para funcionar. O ficheiro `database/schema.sql` contém a estrutura e dados iniciais.

1. Abre o navegador e vai a: `http://localhost/phpmyadmin`
2. Clica em **"Base de Dados"** (Databases) no topo.
3. Cria uma nova base de dados chamada: `crm_database` (UTF-8/utf8mb4_general_ci).
4. Seleciona a base de dados criada na barra lateral esquerda.
5. Clica no separador **"Importar"** (Import) no topo.
6. Clica em "Escolher Ficheiro" e seleciona o ficheiro `database/schema.sql` que está na pasta do projeto.
7. Clica em "Importar" no fundo da página.

*Se tudo correr bem, verás uma mensagem verde de sucesso e as tabelas aparecerão à esquerda.*

## 4. Configuração da Conexão

Verifica o ficheiro `includes/db.php`. Por defeito, está configurado para o XAMPP padrão:
- Host: `127.0.0.1`
- User: `root`
- Pass: `` (vazio)
- DB: `crm_database`

Se o teu MySQL tiver password, altera a variável `$pass` neste ficheiro.

## 5. Como Testar

Abre o navegador e vai para: `http://localhost/crm-pap/index.php` (ou o nome da pasta que usaste).

### Acesso Admin
- **Email:** `admin@crm.local`
- **Password:** `admin123`

### Funcionalidades para Testar (Admin)
1. **Dashboard:**
   - Verifica se os gráficos aparecem (Chart.js) mostrando as encomendas por estado.
   - Verifica os contadores no topo.

2. **Gestão de Leads (Funnel):**
   - Vai ao menu "Leads".
   - Cria uma nova Lead.
   - Altera o estado da Lead (ex: de "Novo" para "Contactado"). A cor deve mudar.
   - Clica em "Detalhes" para ver a ficha da Lead.

3. **Interações:**
   - Dentro dos detalhes de uma Lead, regista uma nova interação (ex: "Chamada telefónica").
   - Verifica se aparece no histórico em baixo.

4. **Gestão de Clientes:**
   - (Funcionalidade existente) Verifica se consegues ver a lista.

5. **Gestão de Encomendas:**
   - (Funcionalidade existente) Verifica as encomendas.

## Notas Técnicas para a Defesa (PAP)
- **Segurança:** O sistema usa `PDO` com `Prepared Statements` para prevenir SQL Injection.
- **Passwords:** As passwords são guardadas com encriptação `password_hash()` (Bcrypt).
- **Interface:** Utiliza Bootstrap 5 para ser responsivo (funciona em telemóveis).
- **Gráficos:** Utiliza a biblioteca Chart.js para visualização de dados dinâmica.
