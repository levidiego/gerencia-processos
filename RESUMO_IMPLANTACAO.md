# 📋 Resumo Executivo - Implantação no Servidor

## Sistema: Gerenciamento de Processos SQL Server

---

## ⚡ Quick Start (Resumo Rápido)

### 1. Requisitos Mínimos do Servidor

**Software:**
- PHP 7.4+ (com extensões: sqlsrv, pdo_sqlsrv, mbstring, openssl, curl)
- Composer 2.x
- IIS 10+ / Apache 2.4+ / Nginx 1.18+
- Microsoft Drivers for PHP for SQL Server

**Conectividade:**
- Acesso ao SQL Server 192.168.254.75:1433
- Firewall liberado para porta 1433

**Sistema Operacional:**
- Windows Server 2016+ (recomendado)
- OU Linux (Ubuntu 20.04+)

---

## 📦 Checklist de Implantação (Passo a Passo)

### Fase 1: Preparação do Servidor (30-60 min)

#### Windows Server

```powershell
# 1. Instalar PHP 7.4 Thread Safe x64
#    Baixar de: https://windows.php.net/download/
#    Extrair para: C:\PHP74

# 2. Instalar Microsoft Drivers for PHP for SQL Server
#    Baixar de: https://learn.microsoft.com/en-us/sql/connect/php/
#    Copiar DLLs para: C:\PHP74\ext\

# 3. Editar php.ini (C:\PHP74\php.ini)
extension=mbstring
extension=openssl
extension=pdo_sqlsrv
extension=sqlsrv
extension=curl
extension=fileinfo

# 4. Instalar Composer
#    Baixar de: https://getcomposer.org/Composer-Setup.exe

# 5. Instalar IIS e URL Rewrite Module
#    IIS: Server Manager → Add Roles → Web Server (IIS)
#    URL Rewrite: https://www.iis.net/downloads/microsoft/url-rewrite
```

#### Linux (Ubuntu)

```bash
# 1. Instalar PHP e extensões
sudo apt update
sudo apt install -y php7.4 php7.4-cli php7.4-fpm php7.4-curl \
    php7.4-mbstring php7.4-xml php7.4-zip

# 2. Instalar Microsoft ODBC Driver
curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -
curl https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list | \
    sudo tee /etc/apt/sources.list.d/mssql-release.list
sudo apt update
sudo ACCEPT_EULA=Y apt install -y msodbcsql17

# 3. Instalar drivers PHP para SQL Server
sudo pecl install sqlsrv pdo_sqlsrv
echo "extension=sqlsrv.so" | sudo tee -a /etc/php/7.4/cli/php.ini
echo "extension=pdo_sqlsrv.so" | sudo tee -a /etc/php/7.4/cli/php.ini

# 4. Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

### Fase 2: Deploy da Aplicação (15-30 min)

```bash
# 1. Transferir arquivos para o servidor
#    Destino Windows: C:\inetpub\wwwroot\gerencia-processos
#    Destino Linux:   /var/www/gerencia-processos

# 2. Navegar para o diretório
cd C:\inetpub\wwwroot\gerencia-processos    # Windows
cd /var/www/gerencia-processos               # Linux

# 3. Criar arquivo .env
copy .env.example .env    # Windows
cp .env.example .env      # Linux

# 4. Editar .env com as configurações do servidor
APP_NAME="Gerencia Processos"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://seu-servidor.dominio.com.br

DB_CONNECTION=sqlsrv
DB_HOST=192.168.254.75
DB_PORT=1433
DB_DATABASE=gerencia_processos
DB_USERNAME=gprocessos
DB_PASSWORD=Gpr0c35505

# 5. Instalar dependências
composer install --no-dev --optimize-autoloader

# 6. Gerar chave da aplicação
php artisan key:generate

# 7. Cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Fase 3: Configuração do Servidor Web (15-30 min)

#### IIS (Windows)

```
1. Abrir IIS Manager
2. Criar novo site:
   - Nome: GerenciaProcessos
   - Caminho: C:\inetpub\wwwroot\gerencia-processos\public  ⚠️ IMPORTANTE
   - Port: 80
   - Host: gerenciaprocessos.local

3. Application Pool:
   - .NET CLR Version: No Managed Code
   - Pipeline Mode: Integrated

4. Handler Mappings:
   - Request path: *.php
   - Module: FastCgiModule
   - Executable: C:\PHP74\php-cgi.exe

5. Criar web.config em public/ (ver guia completo)
```

#### Apache (Linux)

```bash
# Criar VirtualHost
sudo nano /etc/apache2/sites-available/gerenciaprocessos.conf

# Conteúdo (ajustar caminhos):
<VirtualHost *:80>
    ServerName gerenciaprocessos.local
    DocumentRoot /var/www/gerencia-processos/public
    <Directory /var/www/gerencia-processos/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Habilitar site
sudo a2ensite gerenciaprocessos.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### Fase 4: Permissões (5 min)

#### Windows

```powershell
icacls "C:\inetpub\wwwroot\gerencia-processos\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\gerencia-processos\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

#### Linux

```bash
sudo chown -R www-data:www-data /var/www/gerencia-processos
sudo chmod -R 775 /var/www/gerencia-processos/storage
sudo chmod -R 775 /var/www/gerencia-processos/bootstrap/cache
```

---

### Fase 5: Kill Automático (Tarefa Agendada) (10 min)

#### Windows - Agendador de Tarefas

```
1. Abrir "Agendador de Tarefas"
2. Criar Tarefa Básica:
   - Nome: GerenciaProcessos - Kill Automático
   - Gatilho: Repetir a cada 1 minuto
   - Ação:
     * Programa: C:\PHP74\php.exe
     * Argumentos: artisan processos:kill-automatico
     * Iniciar em: C:\inetpub\wwwroot\gerencia-processos
   - Configurar para executar sempre, mesmo sem usuário logado
```

#### Linux - Cron

```bash
crontab -e

# Adicionar linha:
* * * * * cd /var/www/gerencia-processos && php artisan schedule:run >> /dev/null 2>&1
```

---

### Fase 6: Testes (10 min)

```bash
# 1. Testar conexão com banco
php test_connection.php

# 2. Acessar via navegador
http://seu-servidor

# 3. Fazer login
# (Criar usuário se necessário)

# 4. Testar tela de processos
Menu → Processos

# 5. Testar kill manual
Selecionar processo → Kill

# 6. Verificar kill automático
php artisan processos:kill-automatico

# 7. Verificar logs
Menu → Logs (Admin)
```

---

## 🔑 Informações Importantes

### Banco de Dados (Já Existente)
```
Servidor: 192.168.254.75
Porta: 1433
Database: gerencia_processos
Usuário: gprocessos
Senha: Gpr0c35505
```

### Estrutura de Diretórios
```
gerencia-processos/
├── public/          ← DocumentRoot do servidor web (IMPORTANTE!)
│   └── index.php    ← Entrada da aplicação
├── app/             ← Código da aplicação
├── storage/         ← Logs e cache (precisa permissão de escrita)
├── .env             ← Configurações (criar do .env.example)
└── artisan          ← CLI do Laravel
```

### Portas e Acessos
- **Aplicação Web**: Porta 80 (ou configurada no servidor web)
- **SQL Server**: 192.168.254.75:1433
- **Liberações de Firewall**: Servidor → SQL Server (porta 1433)

### Primeiro Acesso
```
1. Acessar: http://seu-servidor/register
2. Criar usuário
3. Executar SQL para tornar admin:
   UPDATE users SET is_admin = 1 WHERE email = 'seu-email@exemplo.com';
4. Fazer login
```

---

## ⚠️ Pontos Críticos de Atenção

### 1. DocumentRoot DEVE apontar para /public
```
❌ ERRADO: C:\inetpub\wwwroot\gerencia-processos
✅ CORRETO: C:\inetpub\wwwroot\gerencia-processos\public
```

### 2. APP_DEBUG=false em Produção
```env
APP_DEBUG=false   # NUNCA deixar true em produção
APP_ENV=production
```

### 3. Permissões de Escrita
```
storage/
bootstrap/cache/
```
Devem ter permissão de escrita para o usuário do servidor web.

### 4. Drivers SQL Server
Certifique-se de que `php_sqlsrv.dll` e `php_pdo_sqlsrv.dll` estão instalados:
```bash
php -m | findstr sqlsrv    # Windows
php -m | grep sqlsrv       # Linux
```

### 5. Kill Automático
A tarefa agendada é ESSENCIAL para o funcionamento do kill automático.
Sem ela, apenas o kill manual funcionará.

---

## 🆘 Problemas Comuns e Soluções Rápidas

### Erro 500 - Internal Server Error
```bash
# Verificar logs
type storage\logs\laravel-*.log

# Reconfigurar permissões (ver Fase 4)
# Limpar cache
php artisan config:clear
php artisan cache:clear
```

### Erro de Conexão com Banco
```bash
# Testar conexão
php test_connection.php

# Verificar firewall
ping 192.168.254.75
telnet 192.168.254.75 1433

# Verificar drivers
php -m | findstr sqlsrv
```

### CSS/JS Não Carregam
```
Verificar se DocumentRoot aponta para /public
```

### Kill Automático Não Funciona
```bash
# Executar manualmente para testar
php artisan processos:kill-automatico

# Verificar tarefa agendada
# Windows: Agendador de Tarefas
# Linux: crontab -l
```

---

## 📞 Documentação Completa

Para detalhes técnicos completos, consulte:
- **[GUIA_IMPLANTACAO_SERVIDOR.md](GUIA_IMPLANTACAO_SERVIDOR.md)** - Guia completo com todos os detalhes
- **[DOCUMENTACAO_INDEX.md](DOCUMENTACAO_INDEX.md)** - Índice de toda documentação
- **[KILL_AUTOMATICO.md](KILL_AUTOMATICO.md)** - Documentação do kill automático
- **[instalacao/INSTRUCOES_DE_USO.md](instalacao/INSTRUCOES_DE_USO.md)** - Manual de uso

---

## ✅ Checklist Final (Marcar ao Concluir)

**Preparação:**
- [ ] PHP 7.4+ instalado
- [ ] Extensões SQL Server instaladas
- [ ] Composer instalado
- [ ] Servidor Web configurado

**Deploy:**
- [ ] Arquivos transferidos
- [ ] .env configurado
- [ ] `composer install` executado
- [ ] `php artisan key:generate` executado
- [ ] Permissões configuradas

**Configuração:**
- [ ] DocumentRoot apontando para /public
- [ ] Site acessível via navegador
- [ ] Login funcionando
- [ ] Conexão com banco testada

**Kill Automático:**
- [ ] Tarefa agendada criada
- [ ] Executar manualmente para testar
- [ ] Verificar logs

**Testes:**
- [ ] Tela de processos carregando
- [ ] Kill manual funcionando
- [ ] Logs sendo gravados
- [ ] Admin pode acessar todas as funcionalidades

---

## 🎯 Tempo Estimado Total

- **Preparação do Servidor**: 30-60 min
- **Deploy da Aplicação**: 15-30 min
- **Configuração do Servidor Web**: 15-30 min
- **Permissões**: 5 min
- **Kill Automático**: 10 min
- **Testes**: 10 min

**TOTAL: 1h30min - 2h30min**

---

**Versão**: 1.0
**Data**: 17/11/2025
**Desenvolvido por**: Levi Miranda
**Framework**: Laravel 8
