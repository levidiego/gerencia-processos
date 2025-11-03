# Sistema de Gerenciamento de Processos SQL Server

## Manual de Uso Inicial

### 1. Visão Geral do Sistema

Este sistema foi desenvolvido para monitorar e gerenciar processos do SQL Server em tempo real, com funcionalidades de:
- Visualização de processos ativos
- Identificação de bloqueios
- Kill manual e automático de processos
- Logs de processos finalizados
- Gerenciamento de usuários com perfis
- Personalização de cores da interface

---

### 2. Requisitos do Sistema

- **PHP**: 7.4 ou superior
- **Composer**: 2.x
- **Laravel**: 8.x
- **SQL Server**: 2012 ou superior
- **Extensões PHP**:
  - pdo_sqlsrv
  - sqlsrv
  - mbstring
  - openssl
  - json

---

### 3. Configuração Inicial

#### 3.1. Configuração do Banco de Dados

Edite o arquivo `.env` na raiz do projeto:

```env
DB_CONNECTION=sqlsrv
DB_HOST=192.168.254.93
DB_PORT=1433
DB_DATABASE=gerencia_processos
DB_USERNAME=sa
DB_PASSWORD=bomixsa
```

#### 3.2. Instalação das Dependências

```bash
composer install
```

#### 3.3. Geração da Chave da Aplicação

```bash
php artisan key:generate
```

#### 3.4. Executar Migrations

```bash
php artisan migrate
```

---

### 4. Primeiro Acesso

#### 4.1. Criar Primeiro Usuário

Acesse a tela de registro em: `http://localhost:8001/register`

**IMPORTANTE**: O primeiro usuário criado NÃO será administrador automaticamente.

#### 4.2. Tornar o Usuário Administrador

Execute o seguinte comando SQL no banco de dados:

```sql
UPDATE users
SET is_admin = 1
WHERE email = 'seu-email@exemplo.com';
```

---

### 5. Funcionalidades por Perfil

#### 5.1. Usuário Comum

Acesso a:
- ✅ Visualizar processos ativos
- ✅ Finalizar processos manualmente (kill)
- ✅ Alterar própria senha

NÃO tem acesso a:
- ❌ Logs de processos
- ❌ Gerenciamento de usuários
- ❌ Configuração de parâmetros
- ❌ Personalização de tema

#### 5.2. Administrador

Acesso a TUDO que o usuário comum tem, mais:
- ✅ Visualizar logs de processos finalizados
- ✅ Gerenciar usuários (criar, editar, excluir)
- ✅ Configurar parâmetros de tempo (X, Y, Z)
- ✅ Personalizar cores da interface (tema)

---

### 6. Configuração de Parâmetros

Os parâmetros controlam o comportamento do sistema de alertas:

#### 6.1. Tempo X - Destaque (Laranja)
- **Função**: Identifica processos bloqueadores de longa duração
- **Padrão**: 5 minutos e 0 segundos
- **Efeito**: Destaca a linha em laranja

#### 6.2. Tempo Y - Alerta Sonoro
- **Função**: Emite alerta sonoro
- **Padrão**: 10 minutos e 0 segundos
- **Efeito**: Toca som de alerta a cada 10 segundos

#### 6.3. Tempo Z - Kill Automático
- **Função**: Marca processos para kill automático
- **Padrão**: 15 minutos e 0 segundos
- **Efeito**: Linha pisca em vermelho (kill ainda é manual)

**REGRA IMPORTANTE**: X < Y < Z (os tempos devem seguir essa ordem)

---

### 7. Tela de Processos

#### 7.1. Informações Exibidas

1. **Session ID**: ID da sessão do processo
2. **Tempo**: Tempo de execução no formato dd:hh:mm:ss.mss
3. **Login**: Usuário que iniciou o processo
4. **Host**: Máquina de origem
5. **Database**: Banco de dados em uso
6. **Status**: Estado do processo (running, sleeping, etc.)
7. **Bloqueando**: Session ID que está bloqueando este processo
8. **Reads**: Quantidade de leituras
9. **Writes**: Quantidade de escritas
10. **CPU**: Tempo de CPU em milissegundos
11. **SQL Text**: Comando SQL sendo executado
12. **Wait Info**: Informação de espera
13. **Program**: Programa/aplicação que iniciou o processo

#### 7.2. Filtros Disponíveis

- **Atualizar**: Recarrega a página
- **Apenas Bloqueados**: Mostra somente processos com bloqueio

#### 7.3. Cores e Destaques

- **Branco/Normal**: Processo normal
- **Laranja**: Bloqueador há mais de X tempo
- **Amarelo**: Bloqueador não encontrado
- **Vermelho Piscante**: Marcado para kill automático (≥ Z tempo)

#### 7.4. Auto-refresh

A tela atualiza automaticamente a cada 30 segundos.

---

### 8. Logs de Processos Finalizados

Disponível apenas para **administradores**.

#### 8.1. Informações Registradas

- Data e hora do kill
- Tipo de kill (manual ou automático)
- Usuário que executou (se manual)
- Todos os dados do processo no momento da finalização

#### 8.2. Filtros Disponíveis

- **Tipo de Kill**: Manual, Automático ou Todos
- **Session ID**: Buscar por ID específico
- **Período**: Data início e data fim

---

### 9. Gerenciamento de Usuários

Disponível apenas para **administradores**.

#### 9.1. Criar Novo Usuário

1. Acesse: **Usuários → Novo Usuário**
2. Preencha:
   - Nome completo
   - Email (será o login)
   - Senha (mínimo 8 caracteres)
   - Confirmar senha
3. Marque **"Administrador"** se desejar dar privilégios de admin
4. Clique em **Salvar**

#### 9.2. Editar Usuário

1. Na lista de usuários, clique no ícone de editar
2. Altere os dados necessários
3. Marque/desmarque **"Administrador"** conforme necessário
4. Clique em **Atualizar**

**NOTA**: A senha NÃO pode ser alterada pela edição. O usuário deve usar "Trocar Senha" no menu.

#### 9.3. Excluir Usuário

1. Clique no ícone de lixeira
2. Confirme a exclusão

**PROTEÇÃO**: Não é possível excluir o próprio usuário logado.

---

### 10. Personalização de Tema

Disponível apenas para **administradores**.

#### 10.1. Temas Pré-Definidos

Clique em qualquer um dos 8 temas disponíveis:
- Roxo Gradiente (Padrão)
- Azul Oceano
- Verde Natureza
- Laranja Pôr do Sol
- Rosa Romântico
- Vermelho Intenso
- Cinza Escuro
- Azul Índigo

#### 10.2. Cores Personalizadas

1. Use os seletores de cor (Color Picker)
2. **Cor Primária**: Primeira cor do gradiente
3. **Cor Secundária**: Segunda cor do gradiente
4. Veja o preview em tempo real
5. Clique em **Salvar Tema**

**EFEITO**: As cores são aplicadas imediatamente em:
- Fundo da página (gradiente)
- Barra de navegação
- Cabeçalhos de cards
- Cabeçalhos de tabelas
- Botões primários
- Links de paginação

---

### 11. Trocar Senha

Todos os usuários podem trocar a própria senha.

1. Clique no nome do usuário (canto superior direito)
2. Selecione **"Trocar Senha"**
3. Digite:
   - Senha atual
   - Nova senha (mínimo 8 caracteres)
   - Confirmar nova senha
4. Clique em **Salvar**

---

### 12. Stored Procedure Necessária

O sistema depende da stored procedure `sp_whoisactive2`. Certifique-se de que ela existe no banco de dados.

```sql
-- Exemplo de criação (ajuste conforme sua implementação)
CREATE PROCEDURE sp_whoisactive2
AS
BEGIN
    -- Lógica da procedure que retorna os processos
    -- Deve retornar os 13 campos esperados pelo sistema
END
```

---

### 13. Tabelas do Sistema

O sistema cria as seguintes tabelas:

1. **users**: Usuários do sistema
2. **parametros**: Configurações de tempo (X, Y, Z)
3. **processo_logs**: Logs de processos finalizados
4. **configuracao_tema**: Cores personalizadas da interface

---

### 14. Iniciar o Sistema

#### 14.1. Modo Desenvolvimento

```bash
php artisan serve --port=8001
```

Acesse: `http://127.0.0.1:8001`

#### 14.2. Modo Produção

Configure um servidor web (Apache/Nginx) apontando para a pasta `public`.

---

### 15. Troubleshooting

#### 15.1. Erro "A non well formed numeric value encountered"

**Causa**: Valores NULL ou strings do SQL Server

**Solução**: Já implementado com type casting e null coalescing

#### 15.2. Erro 403 ao acessar páginas de admin

**Causa**: Usuário não é administrador

**Solução**:
```sql
UPDATE users SET is_admin = 1 WHERE email = 'seu-email@exemplo.com';
```

#### 15.3. Página não atualiza automaticamente

**Causa**: JavaScript desabilitado

**Solução**: Habilite JavaScript no navegador

#### 15.4. Alerta sonoro não funciona

**Causa**: Navegador bloqueou autoplay de áudio

**Solução**: Permita autoplay de áudio nas configurações do navegador

---

### 16. Segurança

#### 16.1. Recomendações

1. Altere as credenciais do banco no `.env`
2. Use senhas fortes para usuários
3. Mantenha poucos usuários como administradores
4. Revise regularmente os logs de processos
5. Configure firewall para restringir acesso ao sistema

#### 16.2. Proteções Implementadas

- ✅ Autenticação obrigatória
- ✅ Middleware para rotas administrativas
- ✅ CSRF protection
- ✅ Proteção contra SQL injection (Eloquent/Query Builder)
- ✅ Hash de senhas (bcrypt)
- ✅ Confirmação antes de kill de processos

---

### 17. Contato e Suporte

Para dúvidas ou problemas:
- Consulte a documentação do Laravel: https://laravel.com/docs/8.x
- Verifique os logs em `storage/logs/laravel.log`

---

**Versão do Sistema**: 1.0
**Data**: 01/11/2025
**Desenvolvido com**: Laravel 8 + Bootstrap 5
**Autor**: Levi Miranda
