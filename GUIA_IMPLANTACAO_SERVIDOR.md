# 🚀 Guia de Implantação - Servidor de Aplicação

## Sistema: Gerenciamento de Processos SQL Server
**Versão**: 1.0
**Framework**: Laravel 8
**Data**: 17/11/2025

---

## 📋 Índice

1. [Requisitos do Servidor](#requisitos-do-servidor)
2. [Instalação de Dependências](#instalação-de-dependências)
3. [Configuração do Ambiente](#configuração-do-ambiente)
4. [Configuração do Servidor Web](#configuração-do-servidor-web)
5. [Configuração de Permissões](#configuração-de-permissões)
6. [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
7. [Deploy da Aplicação](#deploy-da-aplicação)
8. [Configuração do Kill Automático](#configuração-do-kill-automático)
9. [Testes Pós-Implantação](#testes-pós-implantação)
10. [Monitoramento e Logs](#monitoramento-e-logs)
11. [Troubleshooting](#troubleshooting)
12. [Backup e Recuperação](#backup-e-recuperação)

---

## 1. Requisitos do Servidor

### 1.1. Sistema Operacional
- **Windows Server 2016/2019/2022** (recomendado)
- OU **Linux** (Ubuntu 20.04/22.04, CentOS 7/8)

### 1.2. Software Base

#### PHP
- **Versão**: 7.4 ou 8.0 ou 8.1
- **Arquitetura**: x64 (64 bits)
- **Thread Safety**: Recomendado (para IIS/Apache)

#### Extensões PHP Obrigatórias
```ini
extension=mbstring
extension=openssl
extension=pdo_sqlsrv
extension=sqlsrv
extension=curl
extension=fileinfo
extension=tokenizer
extension=json
extension=xml
```

#### Composer
- **Versão**: 2.x (mais recente)
- Download: https://getcomposer.org/

#### Servidor Web
**Opção 1: IIS (Windows)**
- IIS 10.0 ou superior
- URL Rewrite Module 2.1
- FastCGI

**Opção 2: Apache (Windows/Linux)**
- Apache 2.4 ou superior
- mod_rewrite habilitado

**Opção 3: Nginx (Linux)**
- Nginx 1.18 ou superior

### 1.3. Banco de Dados
- **SQL Server**: 2012 ou superior
- **Conexão**: Deve estar acessível do servidor de aplicação
- **Credenciais**: Usuário com permissões adequadas (já criado: `gprocessos`)

### 1.4. Drivers SQL Server para PHP

**Windows:**
- Microsoft Drivers for PHP for SQL Server 5.10
- Download: https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server

**Linux:**
- Microsoft ODBC Driver 17 for SQL Server
- PHP SQLSRV e PDO_SQLSRV extensions

---

## 2. Instalação de Dependências

### 2.1. Windows Server

#### Instalar PHP 7.4 (ou 8.0/8.1)

```powershell
# Baixar PHP (escolha a versão Thread Safe x64)
# https://windows.php.net/download/

# Extrair para C:\PHP74
# Copiar php.ini-production para php.ini
# Editar php.ini e descomentar/adicionar as extensões necessárias
```

Editar `C:\PHP74\php.ini`:
```ini
extension_dir = "ext"
extension=mbstring
extension=openssl
extension=pdo_sqlsrv
extension=sqlsrv
extension=curl
extension=fileinfo
extension=tokenizer

date.timezone = America/Sao_Paulo
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

#### Instalar Microsoft Drivers for PHP for SQL Server

```powershell
# 1. Baixar os drivers
# https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server

# 2. Copiar os arquivos .dll apropriados para C:\PHP74\ext\
# Para PHP 7.4 Thread Safe x64:
#   - php_sqlsrv_74_ts_x64.dll
#   - php_pdo_sqlsrv_74_ts_x64.dll

# 3. Renomear (remover a parte da versão):
#   - php_sqlsrv.dll
#   - php_pdo_sqlsrv.dll
```

#### Instalar Composer

```powershell
# Baixar e executar: https://getcomposer.org/Composer-Setup.exe
# Seguir o instalador
# Verificar instalação:
composer --version
```

### 2.2. Linux (Ubuntu)

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP 7.4 e extensões
sudo apt install -y php7.4 php7.4-cli php7.4-fpm php7.4-curl \
    php7.4-mbstring php7.4-xml php7.4-zip php7.4-bcmath \
    php7.4-json php7.4-tokenizer

# Instalar Microsoft ODBC Driver
curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -
curl https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list | sudo tee /etc/apt/sources.list.d/mssql-release.list
sudo apt update
sudo ACCEPT_EULA=Y apt install -y msodbcsql17 unixodbc-dev

# Instalar drivers PHP para SQL Server
sudo pecl install sqlsrv pdo_sqlsrv

# Adicionar extensões ao php.ini
echo "extension=sqlsrv.so" | sudo tee -a /etc/php/7.4/cli/php.ini
echo "extension=pdo_sqlsrv.so" | sudo tee -a /etc/php/7.4/cli/php.ini
echo "extension=sqlsrv.so" | sudo tee -a /etc/php/7.4/fpm/php.ini
echo "extension=pdo_sqlsrv.so" | sudo tee -a /etc/php/7.4/fpm/php.ini

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 3. Configuração do Ambiente

### 3.1. Estrutura de Diretórios

**Local recomendado:**
- Windows: `C:\inetpub\wwwroot\gerencia-processos`
- Linux: `/var/www/gerencia-processos`

### 3.2. Transferir Arquivos

**Arquivos a transferir do ambiente de desenvolvimento:**

```
gerencia-processos/
├── app/                    # Código da aplicação
├── bootstrap/              # Bootstrap do Laravel
├── config/                 # Arquivos de configuração
├── database/               # Migrations e seeds
├── public/                 # Ponto de entrada web (index.php)
├── resources/              # Views, CSS, JS
├── routes/                 # Rotas da aplicação
├── storage/                # Logs e cache (será criado)
├── vendor/                 # Dependências (composer install)
├── .env.example            # Exemplo de configuração
├── artisan                 # CLI do Laravel
├── composer.json           # Dependências PHP
├── composer.lock           # Lock de versões
└── start_kill_automatico.bat  # Script Windows
```

**Métodos de transferência:**
- FTP/SFTP
- Git (recomendado)
- Cópia direta via rede

### 3.3. Criar arquivo .env

No servidor, copie `.env.example` para `.env`:

```bash
# Windows (PowerShell)
Copy-Item .env.example .env

# Linux
cp .env.example .env
```

Edite o arquivo `.env` com as configurações do servidor:

```env
APP_NAME="Gerencia Processos"
APP_ENV=production
APP_KEY=                                    # Será gerado
APP_DEBUG=false                             # IMPORTANTE: false em produção
APP_URL=http://seu-servidor.dominio.com.br

LOG_CHANNEL=stack
LOG_LEVEL=error                             # error em produção

# Configuração do Banco de Dados
DB_CONNECTION=sqlsrv
DB_HOST=192.168.254.75                      # IP do SQL Server
DB_PORT=1433
DB_DATABASE=gerencia_processos
DB_USERNAME=gprocessos
DB_PASSWORD=Gpr0c35505

# Sessão
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file

# Fila
QUEUE_CONNECTION=sync

# Email (se for usar)
MAIL_MAILER=log
```

### 3.4. Instalar Dependências

```bash
# Navegar até o diretório da aplicação
cd C:\inetpub\wwwroot\gerencia-processos    # Windows
cd /var/www/gerencia-processos               # Linux

# Instalar dependências do Composer
composer install --no-dev --optimize-autoloader

# Gerar chave da aplicação
php artisan key:generate

# Limpar cache de configuração
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Configuração do Servidor Web

### 4.1. IIS (Windows)

#### Instalar IIS e módulos necessários

```powershell
# Habilitar IIS
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServer
Enable-WindowsOptionalFeature -Online -FeatureName IIS-CommonHttpFeatures
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HttpErrors
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HttpRedirect
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ApplicationDevelopment
Enable-WindowsOptionalFeature -Online -FeatureName IIS-NetFxExtensibility45
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HealthAndDiagnostics
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HttpLogging
Enable-WindowsOptionalFeature -Online -FeatureName IIS-LoggingLibraries
Enable-WindowsOptionalFeature -Online -FeatureName IIS-RequestMonitor
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HttpTracing
Enable-WindowsOptionalFeature -Online -FeatureName IIS-Security
Enable-WindowsOptionalFeature -Online -FeatureName IIS-RequestFiltering
Enable-WindowsOptionalFeature -Online -FeatureName IIS-Performance
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerManagementTools
Enable-WindowsOptionalFeature -Online -FeatureName IIS-IIS6ManagementCompatibility
Enable-WindowsOptionalFeature -Online -FeatureName IIS-Metabase
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ManagementConsole
Enable-WindowsOptionalFeature -Online -FeatureName IIS-BasicAuthentication
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WindowsAuthentication
Enable-WindowsOptionalFeature -Online -FeatureName IIS-StaticContent
Enable-WindowsOptionalFeature -Online -FeatureName IIS-DefaultDocument
Enable-WindowsOptionalFeature -Online -FeatureName IIS-DirectoryBrowsing
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebSockets
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ApplicationInit
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ISAPIExtensions
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ISAPIFilter
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ASPNET45

# Instalar URL Rewrite Module
# Baixar de: https://www.iis.net/downloads/microsoft/url-rewrite
```

#### Configurar Site no IIS

1. Abrir IIS Manager
2. Criar novo site:
   - Nome: `GerenciaProcessos`
   - Caminho físico: `C:\inetpub\wwwroot\gerencia-processos\public` ⚠️ **IMPORTANTE: apontar para /public**
   - Port: 80 (ou outro)
   - Host name: `gerenciaprocessos.local` (ou seu domínio)

3. Configurar FastCGI:
   - Application Pools → GerenciaProcessos → .NET CLR Version: "No Managed Code"
   - Application Pools → GerenciaProcessos → Advanced Settings → Enable 32-Bit Applications: False

4. Configurar Handler Mappings:
   - Adicionar Module Mapping
   - Request path: `*.php`
   - Module: FastCgiModule
   - Executable: `C:\PHP74\php-cgi.exe`
   - Name: PHP_via_FastCGI

5. Criar arquivo `web.config` em `public/`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Imported Rule 1" stopProcessing="true">
                    <match url="^(.*)/$" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Redirect" redirectType="Permanent" url="/{R:1}" />
                </rule>
                <rule name="Imported Rule 2" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
        <defaultDocument>
            <files>
                <clear />
                <add value="index.php" />
            </files>
        </defaultDocument>
    </system.webServer>
</configuration>
```

### 4.2. Apache (Windows/Linux)

#### Configurar VirtualHost

Criar arquivo: `/etc/apache2/sites-available/gerenciaprocessos.conf` (Linux)
ou `C:\Apache24\conf\extra\httpd-vhosts.conf` (Windows)

```apache
<VirtualHost *:80>
    ServerName gerenciaprocessos.local
    ServerAlias www.gerenciaprocessos.local

    DocumentRoot /var/www/gerencia-processos/public

    <Directory /var/www/gerencia-processos/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gerenciaprocessos-error.log
    CustomLog ${APACHE_LOG_DIR}/gerenciaprocessos-access.log combined
</VirtualHost>
```

**Linux:**
```bash
# Habilitar site
sudo a2ensite gerenciaprocessos.conf

# Habilitar mod_rewrite
sudo a2enmod rewrite

# Reiniciar Apache
sudo systemctl restart apache2
```

**Windows:**
```powershell
# Reiniciar Apache
httpd -k restart
```

### 4.3. Nginx (Linux)

Criar arquivo: `/etc/nginx/sites-available/gerenciaprocessos`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name gerenciaprocessos.local www.gerenciaprocessos.local;
    root /var/www/gerencia-processos/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Habilitar site
sudo ln -s /etc/nginx/sites-available/gerenciaprocessos /etc/nginx/sites-enabled/

# Testar configuração
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

---

## 5. Configuração de Permissões

### 5.1. Windows (IIS)

```powershell
# Dar permissão de escrita para o IIS nas pastas storage e bootstrap/cache
icacls "C:\inetpub\wwwroot\gerencia-processos\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\gerencia-processos\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T

# Se necessário, também para o Application Pool específico
icacls "C:\inetpub\wwwroot\gerencia-processos\storage" /grant "IIS APPPOOL\GerenciaProcessos:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\gerencia-processos\bootstrap\cache" /grant "IIS APPPOOL\GerenciaProcessos:(OI)(CI)F" /T
```

### 5.2. Linux

```bash
# Definir proprietário correto
sudo chown -R www-data:www-data /var/www/gerencia-processos

# Permissões de diretórios
sudo find /var/www/gerencia-processos -type d -exec chmod 755 {} \;

# Permissões de arquivos
sudo find /var/www/gerencia-processos -type f -exec chmod 644 {} \;

# Permissões especiais para storage e cache
sudo chmod -R 775 /var/www/gerencia-processos/storage
sudo chmod -R 775 /var/www/gerencia-processos/bootstrap/cache

# Se usar Apache
sudo chown -R www-data:www-data /var/www/gerencia-processos/storage
sudo chown -R www-data:www-data /var/www/gerencia-processos/bootstrap/cache
```

---

## 6. Configuração do Banco de Dados

### 6.1. Verificar Conectividade

**Teste de conexão do servidor de aplicação para o SQL Server:**

```bash
# Navegar até o diretório do projeto
cd C:\inetpub\wwwroot\gerencia-processos    # Windows
cd /var/www/gerencia-processos               # Linux

# Executar teste de conexão
php test_connection.php
```

Se der erro, verifique:
- Firewall do SQL Server (porta 1433)
- Credenciais no arquivo .env
- Driver SQL Server instalado

### 6.2. Banco já está criado

O banco `gerencia_processos` já existe no servidor SQL Server (192.168.254.75).

**Usuário e senha:**
- Usuário: `gprocessos`
- Senha: `Gpr0c35505`

**Tabelas existentes:**
- users
- parametros
- processo_logs
- configuracao_tema
- migrations
- password_resets
- failed_jobs

### 6.3. Verificar Stored Procedure

Verificar se a stored procedure `sp_whoisactive2` existe no banco:

```sql
USE gerencia_processos;
GO

SELECT * FROM sys.procedures WHERE name = 'sp_whoisactive2';
```

Se não existir, será necessário criá-la ou apontá-la no servidor SQL correto.

---

## 7. Deploy da Aplicação

### 7.1. Checklist de Deploy

```bash
# 1. Transferir arquivos para o servidor
# 2. Configurar .env
# 3. Instalar dependências
composer install --no-dev --optimize-autoloader

# 4. Gerar chave da aplicação
php artisan key:generate

# 5. Limpar e cachear configurações
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Criar diretórios de storage se não existirem
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs

# 7. Testar conectividade com banco
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# 8. Verificar se site está acessível
# Acesse via navegador: http://seu-servidor
```

### 7.2. Criar Primeiro Usuário (se necessário)

Se o banco estiver vazio ou precisar criar novo admin:

```bash
# Opção 1: Via script
php create_admin.php

# Opção 2: Via registro + SQL
# 1. Acesse: http://seu-servidor/register
# 2. Crie o usuário
# 3. Execute no SQL Server:

USE gerencia_processos;
UPDATE users SET is_admin = 1 WHERE email = 'seu-email@exemplo.com';
```

---

## 8. Configuração do Kill Automático

### 8.1. Tarefa Agendada (Windows)

O sistema possui um comando Artisan que executa o kill automático:

```bash
php artisan processos:kill-automatico
```

#### Criar Tarefa no Agendador de Tarefas

1. Abrir **Agendador de Tarefas** (Task Scheduler)
2. Criar Tarefa Básica:
   - Nome: `GerenciaProcessos - Kill Automático`
   - Descrição: `Executa verificação de processos bloqueadores e kill automático`

3. Gatilho:
   - Tipo: Diariamente
   - Repetir tarefa a cada: **1 minuto**
   - Duração: Indefinidamente

4. Ação:
   - Programa: `C:\PHP74\php.exe`
   - Argumentos: `artisan processos:kill-automatico`
   - Iniciar em: `C:\inetpub\wwwroot\gerencia-processos`

5. Configurações:
   - ✅ Executar independentemente se o usuário está conectado
   - ✅ Executar com privilégios mais altos
   - ✅ Se a tarefa já estiver em execução, não iniciar nova instância

#### Script alternativo (BAT)

Existe um arquivo `start_kill_automatico.bat` que executa em loop:

```batch
@echo off
:loop
php artisan processos:kill-automatico
timeout /t 60 /nobreak
goto loop
```

Para executar:
```powershell
# Iniciar manualmente
.\start_kill_automatico.bat

# OU criar serviço Windows com NSSM
# https://nssm.cc/
```

### 8.2. Cron Job (Linux)

```bash
# Editar crontab
crontab -e

# Adicionar linha (executa a cada minuto):
* * * * * cd /var/www/gerencia-processos && php artisan schedule:run >> /dev/null 2>&1
```

O Laravel scheduler executará o comando de kill automático.

### 8.3. Verificar se está funcionando

```bash
# Verificar logs do scheduler
type storage\logs\scheduler.log              # Windows
cat storage/logs/scheduler.log               # Linux

# Executar manualmente para testar
php artisan processos:kill-automatico

# Verificar parâmetros configurados
php check_parametros.php

# Ver logs de kill no banco
# Acessar interface web → Menu Logs → Tipo: Automático
```

---

## 9. Testes Pós-Implantação

### 9.1. Testes de Funcionalidade

```bash
# 1. Teste de acesso ao site
curl http://seu-servidor

# 2. Teste de login
# Acessar via navegador e fazer login

# 3. Teste de visualização de processos
# Menu → Processos → Verificar se carrega

# 4. Teste de kill manual
# Selecionar um processo de teste e finalizar

# 5. Teste de logs (Admin)
# Menu → Logs → Verificar registros

# 6. Teste de parâmetros (Admin)
# Menu → Parâmetros → Editar e salvar

# 7. Teste de usuários (Admin)
# Menu → Usuários → Criar/Editar

# 8. Teste de tema (Admin)
# Menu → Tema → Alterar cores
```

### 9.2. Testes de Performance

```bash
# Verificar consumo de memória
php artisan processos:kill-automatico

# Monitorar durante 5 minutos
# Verificar logs de erro
```

### 9.3. Testes de Conectividade

```bash
# Teste de conexão com SQL Server
php test_connection.php

# Verificar tempo de resposta
php artisan tinker
>>> \DB::connection()->getPdo();
>>> \DB::table('users')->count();
>>> exit
```

---

## 10. Monitoramento e Logs

### 10.1. Logs do Laravel

**Localização:**
- `storage/logs/laravel-YYYY-MM-DD.log`

**Monitorar em tempo real:**
```bash
# Windows (PowerShell)
Get-Content storage\logs\laravel-2025-11-17.log -Wait -Tail 50

# Linux
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

### 10.2. Logs do Scheduler

**Localização:**
- `storage/logs/scheduler.log`

**Verificar:**
```bash
# Windows
type storage\logs\scheduler.log

# Linux
cat storage/logs/scheduler.log
```

### 10.3. Logs do Servidor Web

**IIS:**
- `C:\inetpub\logs\LogFiles\W3SVC1\`

**Apache:**
- `/var/log/apache2/gerenciaprocessos-error.log`
- `/var/log/apache2/gerenciaprocessos-access.log`

**Nginx:**
- `/var/log/nginx/error.log`
- `/var/log/nginx/access.log`

### 10.4. Logs de Kill Automático

Consultar via interface web:
- Menu → Logs → Filtrar por "Automático"

Ou consultar diretamente no banco:
```sql
USE gerencia_processos;
SELECT TOP 100 * FROM processo_logs
WHERE tipo_kill = 'Automático'
ORDER BY data_hora DESC;
```

---

## 11. Troubleshooting

### 11.1. Erro 500 - Internal Server Error

**Causas comuns:**
- Permissões incorretas em storage/
- .env mal configurado
- Erro de conexão com banco

**Solução:**
```bash
# Verificar logs
type storage\logs\laravel-*.log    # Windows
cat storage/logs/laravel-*.log     # Linux

# Reconfigurar permissões
# Ver seção 5

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 11.2. Erro de Conexão com Banco

**Sintoma:**
```
SQLSTATE[HY000] Unable to connect
```

**Solução:**
```bash
# 1. Verificar .env
DB_HOST=192.168.254.75
DB_PORT=1433
DB_DATABASE=gerencia_processos
DB_USERNAME=gprocessos
DB_PASSWORD=Gpr0c35505

# 2. Testar conexão
php test_connection.php

# 3. Verificar drivers SQL Server
php -m | findstr sqlsrv    # Windows
php -m | grep sqlsrv       # Linux

# 4. Testar conectividade de rede
ping 192.168.254.75
telnet 192.168.254.75 1433

# 5. Verificar firewall do SQL Server
```

### 11.3. Kill Automático Não Funciona

**Solução:**
```bash
# 1. Verificar parâmetros
php check_parametros.php

# 2. Executar manualmente
php artisan processos:kill-automatico

# 3. Verificar tarefa agendada
# Windows: Agendador de Tarefas
# Linux: crontab -l

# 4. Verificar logs
type storage\logs\scheduler.log

# 5. Testar função de conversão
php test_funcao_corrigida.php
```

### 11.4. Página em Branco

**Solução:**
```bash
# 1. Habilitar debug temporariamente
# Editar .env:
APP_DEBUG=true

# 2. Acessar novamente e ver erro

# 3. Desabilitar debug após resolver
APP_DEBUG=false
```

### 11.5. CSS/JS Não Carregam

**Solução:**
```bash
# 1. Verificar se pasta public/ é o DocumentRoot

# 2. Limpar cache de view
php artisan view:clear

# 3. Verificar permissões
# Ver seção 5

# 4. Verificar URL no .env
APP_URL=http://seu-servidor
```

---

## 12. Backup e Recuperação

### 12.1. Backup da Aplicação

**Arquivos importantes:**
```
- .env
- storage/logs/
- storage/app/
```

**Script de backup (Windows):**
```batch
@echo off
set DATA=%date:~-4,4%%date:~-7,2%%date:~-10,2%
set HORA=%time:~0,2%%time:~3,2%%time:~6,2%
set BACKUP_DIR=C:\Backups\GerenciaProcessos
set APP_DIR=C:\inetpub\wwwroot\gerencia-processos

mkdir %BACKUP_DIR%\%DATA%_%HORA%
xcopy /E /I /Y %APP_DIR%\.env %BACKUP_DIR%\%DATA%_%HORA%\
xcopy /E /I /Y %APP_DIR%\storage\logs %BACKUP_DIR%\%DATA%_%HORA%\logs\
xcopy /E /I /Y %APP_DIR%\storage\app %BACKUP_DIR%\%DATA%_%HORA%\app\
```

**Script de backup (Linux):**
```bash
#!/bin/bash
DATA=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/backups/gerencia-processos
APP_DIR=/var/www/gerencia-processos

mkdir -p $BACKUP_DIR/$DATA
cp -r $APP_DIR/.env $BACKUP_DIR/$DATA/
cp -r $APP_DIR/storage/logs $BACKUP_DIR/$DATA/
cp -r $APP_DIR/storage/app $BACKUP_DIR/$DATA/

# Compactar
tar -czf $BACKUP_DIR/backup_$DATA.tar.gz $BACKUP_DIR/$DATA
rm -rf $BACKUP_DIR/$DATA
```

### 12.2. Backup do Banco de Dados

```sql
-- Execute no SQL Server Management Studio
BACKUP DATABASE gerencia_processos
TO DISK = 'C:\Backups\gerencia_processos_backup.bak'
WITH FORMAT, COMPRESSION, STATS = 10;
GO
```

### 12.3. Restauração

```bash
# 1. Restaurar arquivos
# Copiar backup para o servidor

# 2. Restaurar banco
# Usar SQL Server Management Studio

# 3. Configurar .env
cp backup/.env .env

# 4. Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 5. Reconfigurar permissões
# Ver seção 5
```

---

## 13. Contatos e Suporte

### Documentação Adicional
- [DOCUMENTACAO_INDEX.md](DOCUMENTACAO_INDEX.md)
- [KILL_AUTOMATICO.md](KILL_AUTOMATICO.md)
- [INSTRUCOES_DE_USO.md](instalacao/INSTRUCOES_DE_USO.md)

### Arquivos de Configuração
- `.env` - Configurações do ambiente
- `config/database.php` - Configuração de banco
- `routes/web.php` - Rotas da aplicação

### Scripts Úteis
- `check_parametros.php` - Verifica parâmetros do sistema
- `test_connection.php` - Testa conexão com banco
- `test_funcao_corrigida.php` - Testa função de conversão de tempo
- `create_admin.php` - Cria usuário administrador

---

**Versão do Documento**: 1.0
**Data**: 17/11/2025
**Autor**: Levi Miranda
**Sistema**: Gerenciamento de Processos SQL Server
**Framework**: Laravel 8

---

## ✅ Checklist Final de Implantação

- [ ] PHP 7.4+ instalado com extensões obrigatórias
- [ ] Drivers SQL Server para PHP instalados
- [ ] Composer instalado
- [ ] Servidor Web configurado (IIS/Apache/Nginx)
- [ ] Arquivos transferidos para o servidor
- [ ] Arquivo .env criado e configurado
- [ ] Dependências instaladas (`composer install`)
- [ ] Chave da aplicação gerada (`php artisan key:generate`)
- [ ] Permissões de storage/ e bootstrap/cache/ configuradas
- [ ] Conexão com banco de dados testada
- [ ] Site acessível via navegador
- [ ] Login funcionando
- [ ] Tela de processos carregando
- [ ] Kill manual funcionando
- [ ] Tarefa agendada para kill automático criada
- [ ] Logs sendo gerados corretamente
- [ ] Backup configurado

---

**🎉 Implantação Concluída! Sistema pronto para uso em produção.**
