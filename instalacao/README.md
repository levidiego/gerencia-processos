# Pasta de Instalação - Gerenciamento de Processos SQL Server

## 📋 Conteúdo desta Pasta

### 1. `INSTRUCOES_DE_USO.md`
Manual completo de uso do sistema com:
- Visão geral do sistema
- Requisitos e configuração
- Guia de uso de todas as funcionalidades
- Documentação de perfis de usuário
- Troubleshooting

### 2. `backup_banco_gerencia_processos.sql`
Backup completo do banco de dados com:
- Estrutura de todas as tabelas
- Dados padrão (parâmetros e tema)
- Índices e relacionamentos
- Scripts de verificação

---

## 🚀 Instalação Rápida

### Passo 1: Restaurar o Banco de Dados

Execute o arquivo SQL no SQL Server Management Studio (SSMS):

```sql
-- Abra o arquivo backup_banco_gerencia_processos.sql
-- Execute todo o script (F5)
```

### Passo 2: Configurar o .env

Edite o arquivo `.env` na raiz do projeto:

```env
DB_CONNECTION=sqlsrv
DB_HOST=192.168.254.93
DB_PORT=1433
DB_DATABASE=gerencia_processos
DB_USERNAME=sa
DB_PASSWORD=bomixsa
```

### Passo 3: Instalar Dependências

```bash
composer install
php artisan key:generate
```

### Passo 4: Iniciar o Sistema

```bash
php artisan serve --port=8001
```

Acesse: http://127.0.0.1:8001

### Passo 5: Criar Primeiro Usuário

1. Acesse: http://127.0.0.1:8001/register
2. Preencha o formulário de registro
3. Execute no SQL Server:

```sql
UPDATE users
SET is_admin = 1
WHERE email = 'seu-email@exemplo.com';
```

4. Faça login e comece a usar!

---

## 📂 Estrutura do Banco de Dados

### Tabelas Criadas:

1. **users** - Usuários do sistema
   - Campos: id, name, email, is_admin, password, etc.

2. **parametros** - Configurações de tempo
   - Campos: tempo_destaque_*, tempo_alerta_*, tempo_kill_*

3. **processo_logs** - Logs de processos finalizados
   - Campos: session_id, sql_text, tipo_kill, killed_by, etc.

4. **configuracao_tema** - Personalização de cores
   - Campos: cor_primaria, cor_secundaria

5. **migrations** - Controle de versão do banco

6. **password_resets** - Reset de senhas

7. **failed_jobs** - Jobs com falha

---

## ⚙️ Configurações Padrão

### Parâmetros de Tempo:
- **Tempo X (Destaque)**: 5 minutos e 0 segundos
- **Tempo Y (Alerta)**: 10 minutos e 0 segundos
- **Tempo Z (Kill)**: 15 minutos e 0 segundos

### Tema:
- **Cor Primária**: #667eea (roxo)
- **Cor Secundária**: #764ba2 (roxo escuro)

---

## 🔐 Perfis de Usuário

### Usuário Comum
✅ Visualizar processos
✅ Finalizar processos (kill manual)
✅ Alterar própria senha

### Administrador
✅ Tudo que usuário comum tem
✅ Visualizar logs
✅ Gerenciar usuários
✅ Configurar parâmetros
✅ Personalizar tema

---

## 📞 Suporte

Para mais informações, consulte o arquivo `INSTRUCOES_DE_USO.md`.

---

**Versão**: 1.0
**Data**: 01/11/2025
**Desenvolvido com**: Laravel 8 + Bootstrap 5
