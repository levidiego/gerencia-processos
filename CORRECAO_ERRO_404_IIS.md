# 🔧 Correção do Erro 404 no IIS

## Problema Identificado

**Erro:** HTTP 404.0 - Not Found ao acessar `/processos`

**Causa:** O IIS está tentando buscar um arquivo físico em vez de usar as rotas do Laravel. O URL Rewrite não está configurado corretamente.

**Informações do Erro:**
- URL Solicitada: `http://192.168.254.74:8001/processos`
- Caminho Físico: `C:\xampp\htdocs\gerencia-processos\public\processos`
- Módulo: IIS Web Core
- Manipulador: StaticFile (❌ Deveria ser PHP)

---

## ✅ Solução (Passo a Passo)

### Etapa 1: Verificar URL Rewrite Module

O IIS precisa do **URL Rewrite Module 2.1** instalado.

**Verificar se está instalado:**
1. Abrir **IIS Manager**
2. Selecionar o servidor (raiz)
3. Procurar ícone **"URL Rewrite"** na lista de recursos

**Se NÃO estiver instalado:**
1. Baixar de: https://www.iis.net/downloads/microsoft/url-rewrite
2. Executar o instalador: `rewrite_amd64_en-US.msi`
3. Reiniciar o IIS

---

### Etapa 2: Copiar web.config para o Servidor

Foi criado um arquivo `web.config` otimizado em:
```
d:\FONTES_IA\gerencia-processos\public\web.config
```

**Copiar para o servidor:**
```
De:   d:\FONTES_IA\gerencia-processos\public\web.config
Para: C:\xampp\htdocs\gerencia-processos\public\web.config
```

**Via FTP/SFTP/Compartilhamento de Rede:**
- Transferir o arquivo `web.config` para a pasta `public` no servidor

**Via Remote Desktop (se tiver acesso):**
- Copiar e colar diretamente

---

### Etapa 3: Configurar o FastCGI no IIS

O `web.config` criado usa o caminho do XAMPP para o PHP:
```xml
scriptProcessor="C:\xampp\php\php-cgi.exe"
```

**⚠️ Se o PHP estiver em outro local no servidor, ajuste no web.config:**

**Localizações comuns:**
- XAMPP: `C:\xampp\php\php-cgi.exe`
- PHP standalone: `C:\PHP74\php-cgi.exe` ou `C:\PHP80\php-cgi.exe`
- WAMP: `C:\wamp64\bin\php\php7.4.x\php-cgi.exe`

**Para descobrir o caminho no servidor:**
```powershell
# PowerShell no servidor
Get-Command php-cgi.exe | Select-Object Source

# OU via CMD
where php-cgi.exe
```

---

### Etapa 4: Verificar Configuração do Site no IIS

1. Abrir **IIS Manager** no servidor
2. Expandir **Sites** → Selecionar site (porta 8001)
3. Verificar **Caminho Físico**:
   - ✅ Deve ser: `C:\xampp\htdocs\gerencia-processos\public`
   - ❌ NÃO pode ser: `C:\xampp\htdocs\gerencia-processos`

**Se estiver errado:**
- Botão direito no site → **Editar Ligações** → **Editar Caminho Físico**
- Alterar para: `C:\xampp\htdocs\gerencia-processos\public`

---

### Etapa 5: Configurar Handler Mappings (Se Necessário)

Se o web.config não for suficiente, configure manualmente:

1. Selecionar o site no IIS
2. Clicar em **Handler Mappings**
3. No painel direito, clicar **Add Module Mapping**

**Configurações:**
```
Request path:          *.php
Module:                FastCgiModule
Executable:            C:\xampp\php\php-cgi.exe
Name:                  PHP_via_FastCGI
Request Restrictions:  Desmarcar "Invoke handler only if request is mapped to:"
```

4. Clicar **OK**
5. Confirmar quando perguntar sobre criar aplicação FastCGI

---

### Etapa 6: Configurar FastCGI Settings (Opcional mas Recomendado)

1. Selecionar o **servidor** (raiz) no IIS
2. Clicar em **FastCGI Settings**
3. Verificar se existe entrada para o PHP:
   - Full Path: `C:\xampp\php\php-cgi.exe`

**Se não existir, adicionar:**
- Full Path: `C:\xampp\php\php-cgi.exe`
- Monitor changes to file: `C:\xampp\php\php.ini`
- Instance MaxRequests: 10000
- Activity Timeout: 300
- Request Timeout: 300

---

### Etapa 7: Reiniciar o IIS

```powershell
# PowerShell (como Administrador)
iisreset
```

---

### Etapa 8: Testar

**Abrir navegador e acessar:**
1. `http://192.168.254.74:8001` → Deve carregar a página de login
2. `http://192.168.254.74:8001/login` → Deve carregar a tela de login
3. `http://192.168.254.74:8001/processos` → Deve redirecionar para login (se não estiver logado)

---

## 📋 Conteúdo do web.config (Referência)

O arquivo `web.config` que deve estar em `public/`:

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
                <add value="index.html" />
            </files>
        </defaultDocument>
        <handlers>
            <remove name="PHP_via_FastCGI" />
            <add name="PHP_via_FastCGI" path="*.php" verb="GET,HEAD,POST,PUT,PATCH,DELETE,OPTIONS" modules="FastCgiModule" scriptProcessor="C:\xampp\php\php-cgi.exe" resourceType="Either" requireAccess="Script" />
        </handlers>
    </system.webServer>
</configuration>
```

---

## 🔍 Troubleshooting

### Ainda dá erro 404 após web.config

**1. Verificar se URL Rewrite está instalado:**
```powershell
# PowerShell
Get-WindowsFeature | Where-Object {$_.Name -like "*rewrite*"}
```

**2. Verificar se web.config existe:**
```powershell
Test-Path "C:\xampp\htdocs\gerencia-processos\public\web.config"
# Deve retornar: True
```

**3. Verificar permissões do arquivo:**
```powershell
icacls "C:\xampp\htdocs\gerencia-processos\public\web.config"
# IIS_IUSRS deve ter permissão de leitura
```

### Erro 500 Internal Server Error

**Causa:** Caminho do PHP incorreto no web.config

**Solução:**
1. Descobrir caminho correto do PHP no servidor
2. Editar `web.config`, linha do `scriptProcessor`
3. Salvar e testar novamente

### Erro "Cannot read configuration file"

**Causa:** Permissões incorretas

**Solução:**
```powershell
icacls "C:\xampp\htdocs\gerencia-processos\public" /grant "IIS_IUSRS:(OI)(CI)R" /T
```

### Teste de Rota Simples

Criar arquivo de teste `C:\xampp\htdocs\gerencia-processos\public\test.php`:

```php
<?php
echo "PHP está funcionando!<br>";
echo "Path: " . __DIR__ . "<br>";
echo "URL: " . $_SERVER['REQUEST_URI'];
?>
```

Acessar: `http://192.168.254.74:8001/test.php`

Se funcionar, o problema é apenas com o URL Rewrite.

---

## ✅ Checklist de Verificação

Marque cada item após verificar:

- [ ] URL Rewrite Module instalado no IIS
- [ ] Arquivo `web.config` existe em `public/`
- [ ] Caminho do PHP correto no `web.config` (scriptProcessor)
- [ ] Caminho físico do site aponta para `/public`
- [ ] Handler Mappings configurado para PHP
- [ ] Permissões corretas em `public/` e `web.config`
- [ ] IIS reiniciado (iisreset)
- [ ] Teste: `http://192.168.254.74:8001` carrega
- [ ] Teste: `http://192.168.254.74:8001/login` carrega
- [ ] Teste: `http://192.168.254.74:8001/processos` redireciona

---

## 📞 Comandos Úteis

```powershell
# Reiniciar IIS
iisreset

# Verificar sites no IIS
Get-IISSite

# Verificar Application Pools
Get-IISAppPool

# Testar conectividade PHP
php -v

# Localizar php-cgi.exe
where php-cgi.exe

# Verificar logs do IIS
Get-Content "C:\inetpub\logs\LogFiles\W3SVC*\*.log" -Tail 50

# Verificar logs do Laravel
Get-Content "C:\xampp\htdocs\gerencia-processos\storage\logs\laravel-*.log" -Tail 50
```

---

**Após seguir estes passos, o erro 404 deve ser resolvido e as rotas do Laravel funcionarão corretamente!** 🎉
