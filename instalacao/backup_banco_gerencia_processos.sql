-- ===================================================================
-- BACKUP DO BANCO DE DADOS: gerencia_processos
-- Sistema de Gerenciamento de Processos SQL Server
-- Data: 01/11/2025
-- Versão: 1.0
-- ===================================================================

-- ===================================================================
-- 1. CRIAÇÃO DO BANCO DE DADOS
-- ===================================================================

IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'gerencia_processos')
BEGIN
    CREATE DATABASE gerencia_processos;
    PRINT 'Banco de dados gerencia_processos criado com sucesso!';
END
GO

USE gerencia_processos;
GO

-- ===================================================================
-- 2. TABELA: migrations (controle de migrations do Laravel)
-- ===================================================================

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[migrations]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[migrations] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [migration] NVARCHAR(255) NOT NULL,
        [batch] INT NOT NULL
    );
    PRINT 'Tabela migrations criada com sucesso!';
END
GO

-- ===================================================================
-- 3. TABELA: users (usuários do sistema)
-- ===================================================================

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[users]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[users] (
        [id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [name] NVARCHAR(255) NOT NULL,
        [email] NVARCHAR(255) NOT NULL UNIQUE,
        [is_admin] BIT NOT NULL DEFAULT 0,
        [email_verified_at] DATETIME2(0) NULL,
        [password] NVARCHAR(255) NOT NULL,
        [remember_token] NVARCHAR(100) NULL,
        [created_at] DATETIME2(0) NULL,
        [updated_at] DATETIME2(0) NULL
    );
    PRINT 'Tabela users criada com sucesso!';
END
GO

-- Criar índice no email
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'users_email_unique' AND object_id = OBJECT_ID('users'))
BEGIN
    CREATE UNIQUE INDEX users_email_unique ON users(email);
END
GO

-- ===================================================================
-- 4. TABELA: password_resets
-- ===================================================================

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[password_resets]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[password_resets] (
        [email] NVARCHAR(255) NOT NULL,
        [token] NVARCHAR(255) NOT NULL,
        [created_at] DATETIME2(0) NULL
    );
    PRINT 'Tabela password_resets criada com sucesso!';
END
GO

-- Criar índice no email
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'password_resets_email_index' AND object_id = OBJECT_ID('password_resets'))
BEGIN
    CREATE INDEX password_resets_email_index ON password_resets(email);
END
GO

-- ===================================================================
-- 5. TABELA: failed_jobs
-- ===================================================================

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[failed_jobs]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[failed_jobs] (
        [id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [uuid] NVARCHAR(255) NOT NULL UNIQUE,
        [connection] TEXT NOT NULL,
        [queue] TEXT NOT NULL,
        [payload] TEXT NOT NULL,
        [exception] TEXT NOT NULL,
        [failed_at] DATETIME2(0) NOT NULL DEFAULT GETDATE()
    );
    PRINT 'Tabela failed_jobs criada com sucesso!';
END
GO

-- ===================================================================
-- 6. TABELA: parametros (configurações de tempo do sistema)
-- ===================================================================

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[parametros]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[parametros] (
        [id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [tempo_destaque_minutos] INT NOT NULL DEFAULT 5,
        [tempo_destaque_segundos] INT NOT NULL DEFAULT 0,
        [tempo_alerta_minutos] INT NOT NULL DEFAULT 10,
        [tempo_alerta_segundos] INT NOT NULL DEFAULT 0,
        [tempo_kill_minutos] INT NOT NULL DEFAULT 15,
        [tempo_kill_segundos] INT NOT NULL DEFAULT 0,
        [created_at] DATETIME2(0) NULL,
        [updated_at] DATETIME2(0) NULL
    );
    PRINT 'Tabela parametros criada com sucesso!';
END
GO

-- Inserir configuração padrão
IF NOT EXISTS (SELECT * FROM parametros)
BEGIN
    INSERT INTO parametros (
        tempo_destaque_minutos, tempo_destaque_segundos,
        tempo_alerta_minutos, tempo_alerta_segundos,
        tempo_kill_minutos, tempo_kill_segundos,
        created_at, updated_at
    )
    VALUES (
        5, 0,   -- Tempo X: 5 minutos
        10, 0,  -- Tempo Y: 10 minutos
        15, 0,  -- Tempo Z: 15 minutos
        GETDATE(), GETDATE()
    );
    PRINT 'Parâmetros padrão inseridos com sucesso!';
END
GO

-- ===================================================================
-- 7. TABELA: processo_logs (logs de processos finalizados)
-- ===================================================================

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[processo_logs]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[processo_logs] (
        [id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [session_id] INT NOT NULL,
        [sql_text] TEXT NULL,
        [dd_hh_mm_ss_mss] NVARCHAR(255) NULL,
        [login_name] NVARCHAR(255) NULL,
        [status] NVARCHAR(255) NULL,
        [host_name] NVARCHAR(255) NULL,
        [database_name] NVARCHAR(255) NULL,
        [program_name] NVARCHAR(255) NULL,
        [tipo_kill] NVARCHAR(255) NOT NULL,
        [killed_by] BIGINT NULL,
        [killed_at] DATETIME2(0) NOT NULL DEFAULT GETDATE(),
        [created_at] DATETIME2(0) NULL,
        [updated_at] DATETIME2(0) NULL,
        CONSTRAINT FK_processo_logs_killed_by FOREIGN KEY ([killed_by])
            REFERENCES [users]([id]) ON DELETE SET NULL
    );
    PRINT 'Tabela processo_logs criada com sucesso!';
END
GO

-- Criar índice no killed_by
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'processo_logs_killed_by_foreign' AND object_id = OBJECT_ID('processo_logs'))
BEGIN
    CREATE INDEX processo_logs_killed_by_foreign ON processo_logs(killed_by);
END
GO

-- ===================================================================
-- 8. TABELA: configuracao_tema (cores personalizadas da interface)
-- ===================================================================

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[configuracao_tema]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[configuracao_tema] (
        [id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [cor_primaria] NVARCHAR(255) NOT NULL DEFAULT '#667eea',
        [cor_secundaria] NVARCHAR(255) NOT NULL DEFAULT '#764ba2',
        [created_at] DATETIME2(0) NULL,
        [updated_at] DATETIME2(0) NULL
    );
    PRINT 'Tabela configuracao_tema criada com sucesso!';
END
GO

-- Inserir tema padrão
IF NOT EXISTS (SELECT * FROM configuracao_tema)
BEGIN
    INSERT INTO configuracao_tema (cor_primaria, cor_secundaria, created_at, updated_at)
    VALUES ('#667eea', '#764ba2', GETDATE(), GETDATE());
    PRINT 'Tema padrão inserido com sucesso!';
END
GO

-- ===================================================================
-- 9. POPULAR TABELA DE MIGRATIONS
-- ===================================================================

-- Limpar tabela de migrations para reiniciar
TRUNCATE TABLE migrations;
GO

INSERT INTO migrations (migration, batch) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2025_10_31_200000_create_parametros_table', 1),
('2025_10_31_211051_add_segundos_to_parametros_table', 2),
('2025_11_01_163345_create_processo_logs_table', 3),
('2025_11_01_165220_add_is_admin_to_users_table', 4),
('2025_11_01_171502_create_configuracao_tema_table', 5);
GO

PRINT 'Migrations registradas com sucesso!';
GO

-- ===================================================================
-- 10. VERIFICAÇÃO FINAL
-- ===================================================================

PRINT '';
PRINT '=================================================================';
PRINT 'BACKUP RESTAURADO COM SUCESSO!';
PRINT '=================================================================';
PRINT '';
PRINT 'Tabelas criadas:';
PRINT '  - migrations';
PRINT '  - users';
PRINT '  - password_resets';
PRINT '  - failed_jobs';
PRINT '  - parametros (com dados padrão)';
PRINT '  - processo_logs';
PRINT '  - configuracao_tema (com tema padrão)';
PRINT '';
PRINT 'PRÓXIMOS PASSOS:';
PRINT '1. Criar um usuário através do registro (http://localhost:8001/register)';
PRINT '2. Tornar o usuário administrador:';
PRINT '   UPDATE users SET is_admin = 1 WHERE email = ''seu-email@exemplo.com'';';
PRINT '3. Fazer login e começar a usar o sistema!';
PRINT '';
PRINT '=================================================================';
GO
