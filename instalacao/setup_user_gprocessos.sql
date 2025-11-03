-- ===================================================================
-- SCRIPT PARA CONFIGURAR USUÁRIO gprocessos NO SQL SERVER
-- ===================================================================
-- Execute este script no SQL Server Management Studio (SSMS)
-- como usuário administrador (sa)
-- ===================================================================

USE [master]
GO

-- Verificar se o login já existe e dropar se necessário
IF EXISTS (SELECT * FROM sys.server_principals WHERE name = N'gprocessos')
BEGIN
    PRINT 'Login gprocessos já existe. Reconfigurando...'

    -- Remover usuário do banco antes de dropar o login
    IF EXISTS (SELECT * FROM sys.databases WHERE name = N'gerencia_processos')
    BEGIN
        USE [gerencia_processos]
        IF EXISTS (SELECT * FROM sys.database_principals WHERE name = N'gprocessos')
        BEGIN
            DROP USER [gprocessos]
            PRINT 'Usuário gprocessos removido do banco gerencia_processos'
        END
    END

    USE [master]
    DROP LOGIN [gprocessos]
    PRINT 'Login gprocessos removido'
END
GO

-- Criar o login com senha e desabilitar políticas de segurança
CREATE LOGIN [gprocessos]
    WITH PASSWORD = N'Gpr0c35505',
    CHECK_POLICY = OFF,
    CHECK_EXPIRATION = OFF,
    DEFAULT_DATABASE = [gerencia_processos]
GO

PRINT 'Login gprocessos criado com sucesso!'
GO

-- Verificar se o banco gerencia_processos existe
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = N'gerencia_processos')
BEGIN
    CREATE DATABASE [gerencia_processos]
    PRINT 'Banco de dados gerencia_processos criado!'
END
GO

-- Criar usuário no banco e conceder permissões
USE [gerencia_processos]
GO

CREATE USER [gprocessos] FOR LOGIN [gprocessos]
GO

-- Conceder todas as permissões (db_owner)
ALTER ROLE [db_owner] ADD MEMBER [gprocessos]
GO

PRINT 'Usuário gprocessos adicionado ao banco com permissões db_owner!'
GO

-- Verificação final
USE [master]
GO

SELECT
    'Configuração concluída!' AS Status,
    name AS Login,
    default_database_name AS DatabasePadrao,
    is_policy_checked AS PoliticaAtiva,
    is_expiration_checked AS ExpiracaoAtiva
FROM sys.server_principals
WHERE name = 'gprocessos'
GO

PRINT ''
PRINT '================================================================='
PRINT 'CONFIGURAÇÃO CONCLUÍDA COM SUCESSO!'
PRINT '================================================================='
PRINT ''
PRINT 'Credenciais configuradas:'
PRINT '  - Usuário: gprocessos'
PRINT '  - Senha: Gpr0c35505'
PRINT '  - Banco: gerencia_processos'
PRINT '  - Permissões: db_owner (controle total)'
PRINT ''
PRINT 'Agora você pode:'
PRINT '1. Executar: php test_connection.php'
PRINT '2. Iniciar o sistema: php artisan serve --host=0.0.0.0 --port=8001'
PRINT ''
PRINT '================================================================='
GO
