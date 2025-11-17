# Kill Automático de Processos Bloqueadores

## 📋 Visão Geral

O sistema possui funcionalidade de **kill automático** que finaliza processos bloqueadores que ultrapassam o **Tempo Z** configurado nos parâmetros.

---

## ⚙️ Como Funciona

### Critérios para Kill Automático:

1. ✅ O processo **DEVE** estar bloqueando outro processo
   - Ou seja, o `session_id` do processo aparece como `blocking_session_id` em outro processo

2. ✅ O tempo de execução **DEVE** ser maior ou igual ao **Tempo Z**
   - Tempo Z padrão: 15 minutos (900 segundos)
   - Configurável em: Parâmetros → Tempo Z

### O que NÃO é finalizado automaticamente:

- ❌ Processos que **NÃO** estão bloqueando outros
- ❌ Processos bloqueadores com tempo **MENOR** que Z
- ❌ Processos normais (mesmo que longos)

---

## 🚀 Ativando o Kill Automático

### Opção 1: Executar manualmente (teste)

```bash
php artisan processos:kill-automatico
```

### Opção 2: Script Windows (recomendado)

Execute o arquivo:
```
start_kill_automatico.bat
```

Este script:
- Executa a verificação a cada 60 segundos
- Mantém logs em `storage/logs/scheduler.log`
- Roda em loop contínuo
- Para parar: pressione CTRL+C

### Opção 3: Agendador de Tarefas do Windows

Para rodar como serviço permanente:

1. Abra o **Agendador de Tarefas** do Windows
2. Criar Tarefa Básica
3. Nome: "Kill Automático - Gerencia Processos"
4. Gatilho: Na inicialização do sistema
5. Ação: Iniciar programa
   - Programa: `D:\FONTES_IA\gerencia-processos\start_kill_automatico.bat`
6. Marcar: "Executar com privilégios mais altos"

---

## 📊 Monitoramento

### Ver processos finalizados automaticamente:

1. Acesse: **Logs** (menu do sistema)
2. Filtre por: **Tipo de Kill = Automático**

### Ver logs do scheduler:

```bash
type storage\logs\scheduler.log
```

### Testar manualmente:

```bash
php artisan processos:kill-automatico
```

---

## 🎯 Fluxo Completo

```
1. Sistema verifica processos a cada minuto
   ↓
2. Identifica processos BLOQUEADORES
   ↓
3. Calcula tempo de execução
   ↓
4. Se tempo >= Tempo Z (900s):
   ↓
5. Executa KILL no processo
   ↓
6. Registra no log como "automatico"
   ↓
7. Continua verificação no próximo minuto
```

---

## ⚠️ Observações Importantes

### Segurança:
- ✅ Apenas processos **BLOQUEADORES** são finalizados
- ✅ Respeita o **Tempo Z** configurado
- ✅ Registra tudo no log com tipo "automatico"
- ✅ Não tem usuário associado (killed_by = NULL)

### Configuração:
- 📝 Ajuste o **Tempo Z** em: Parâmetros
- 📝 Valores recomendados: 10-20 minutos
- 📝 Valor muito baixo pode finalizar processos legítimos

### Performance:
- ⚡ Execução rápida (< 1 segundo)
- ⚡ Não impacta o sistema
- ⚡ Usa mesma stored procedure que a interface

---

## 🔍 Troubleshooting

### Kill automático não está funcionando:

1. Verifique se o script está rodando:
   ```bash
   tasklist | findstr php
   ```

2. Verifique os logs:
   ```bash
   type storage\logs\scheduler.log
   ```

3. Execute manualmente para testar:
   ```bash
   php artisan processos:kill-automatico
   ```

### Processos não estão sendo finalizados:

1. Verifique se são realmente **bloqueadores**:
   - Devem aparecer como `blocking_session_id` em outros processos

2. Verifique o tempo:
   - Execute: `php check_parametros.php`
   - Confirme que o processo passou do Tempo Z

3. Verifique permissões:
   - Usuário do banco deve ter permissão para KILL

---

## 📝 Exemplos

### Processo que SERÁ finalizado automaticamente:

```
Session ID: 123
Tempo: 00:00:16:00.000 (16 minutos)
Bloqueando: Session 456
Resultado: ✅ KILL AUTOMÁTICO (tempo >= 15 min)
```

### Processo que NÃO SERÁ finalizado:

```
Session ID: 789
Tempo: 00:00:20:00.000 (20 minutos)
Bloqueando: Ninguém
Resultado: ❌ NÃO finalizar (não é bloqueador)
```

```
Session ID: 234
Tempo: 00:00:10:00.000 (10 minutos)
Bloqueando: Session 567
Resultado: ❌ NÃO finalizar (tempo < 15 min)
```

---

## 📞 Suporte

Para ajustar os parâmetros:
- Acesse: **Parâmetros** (menu do sistema)
- Altere o **Tempo Z** conforme necessário

Para ver histórico:
- Acesse: **Logs** (menu do sistema)
- Filtre por: **Tipo de Kill = Automático**

---

## ⚠️ Correções e Atualizações

### 🔴 Correção Crítica - 05/11/2025 (v1.1)

**Problema Identificado**: Bug crítico na conversão de tempo causava finalização incorreta de processos.

- **Impacto**: 22 processos foram finalizados com **menos de 1 minuto** de execução
- **Causa**: Função interpretava **segundos como minutos** devido ao formato do SQL Server
- **Solução**: Função de conversão corrigida para interpretar corretamente o formato `dd hh:mm:ss.mss`
- **Status**: ✅ **CORRIGIDO** - Sistema funcionando corretamente

📄 **Documentação completa**: Consulte `CORRECAO_BUG_KILL_AUTOMATICO.md` para detalhes técnicos.

**Após a correção**:
- ✅ Apenas processos com >= 15 minutos (900s) são finalizados
- ✅ Cálculo de tempo funcionando corretamente
- ✅ 100% dos testes passando

---

**Versão**: 1.1
**Data de Criação**: 03/11/2025
**Última Atualização**: 05/11/2025
