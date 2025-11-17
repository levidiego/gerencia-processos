# 📚 Índice de Documentação - Gerência de Processos

Este documento lista toda a documentação disponível para o sistema de gerenciamento de processos SQL Server.

---

## 📄 Documentação Principal

### 1. [KILL_AUTOMATICO.md](KILL_AUTOMATICO.md)
**Descrição**: Documentação completa do sistema de kill automático

**Conteúdo**:
- Como funciona o kill automático
- Critérios para finalização de processos
- Ativação e configuração
- Monitoramento e troubleshooting
- Exemplos práticos

**Quando consultar**:
- Para entender como o sistema funciona
- Configurar o kill automático
- Verificar por que um processo foi finalizado
- Ajustar parâmetros (Tempo Z)

---

### 2. [CORRECAO_BUG_KILL_AUTOMATICO.md](CORRECAO_BUG_KILL_AUTOMATICO.md) 🔴
**Descrição**: Documentação técnica da correção crítica de bug

**Conteúdo**:
- Descrição detalhada do problema encontrado
- Análise da causa raiz
- Solução implementada (código completo)
- Testes realizados e validações
- Impacto e histórico

**Quando consultar**:
- Para entender o bug que foi corrigido
- Revisar implementação técnica
- Verificar testes de regressão
- Documentação de mudanças para auditoria

---

## 🔧 Scripts de Verificação e Teste

### Scripts Disponíveis

| Script | Descrição | Uso |
|--------|-----------|-----|
| `check_parametros.php` | Verifica parâmetros atuais (Tempo X, Y, Z) | `php check_parametros.php` |
| `test_funcao_corrigida.php` | Testa função de conversão de tempo | `php test_funcao_corrigida.php` |
| `analise_completa_logs.php` | Analisa todos os logs de kill automático | `php analise_completa_logs.php` |
| `verificar_logs_kill.php` | Verifica logs com análise detalhada | `php verificar_logs_kill.php` |
| `debug_formato_tempo.php` | Debug do formato de tempo do SQL Server | `php debug_formato_tempo.php` |

### Comandos Artisan

```bash
# Executar kill automático manualmente (teste)
php artisan processos:kill-automatico

# Executar scheduler (inclui kill automático)
php artisan schedule:run
```

### Scripts de Inicialização

```bash
# Iniciar kill automático em loop (Windows)
start_kill_automatico.bat
```

---

## 🎯 Guia Rápido por Cenário

### 🔍 Investigar por que um processo foi finalizado
1. Consulte: `KILL_AUTOMATICO.md` → Seção "Critérios para Kill Automático"
2. Verifique os logs: Menu do sistema → Logs → Filtrar por "Automático"
3. Execute: `php verificar_logs_kill.php`

### ⚙️ Configurar o sistema
1. Consulte: `KILL_AUTOMATICO.md` → Seção "Ativando o Kill Automático"
2. Ajuste parâmetros: Menu do sistema → Parâmetros → Editar Tempo Z
3. Verifique: `php check_parametros.php`

### 🐛 Entender a correção do bug
1. Consulte: `CORRECAO_BUG_KILL_AUTOMATICO.md` → Leia o documento completo
2. Veja logs antigos: `php analise_completa_logs.php`
3. Teste a correção: `php test_funcao_corrigida.php`

### 🧪 Testar o sistema
1. Execute testes: `php test_funcao_corrigida.php`
2. Teste manualmente: `php artisan processos:kill-automatico`
3. Verifique parâmetros: `php check_parametros.php`

### 📊 Monitorar o sistema
1. Logs do scheduler: `type storage\logs\scheduler.log`
2. Interface web: Menu → Logs → Tipo de Kill = Automático
3. Análise completa: `php analise_completa_logs.php`

---

## 📋 Parâmetros do Sistema

### Tempos Configuráveis

| Parâmetro | Nome | Padrão | Descrição |
|-----------|------|--------|-----------|
| Tempo X | Tempo de Destaque | 5 min | Marca processo em laranja na interface |
| Tempo Y | Tempo de Alerta | 10 min | Emite alerta sonoro |
| Tempo Z | Tempo de Kill | 15 min | Finaliza processo automaticamente |

**Configurar**: Menu do sistema → Parâmetros → Editar

---

## 🚨 Troubleshooting

### Kill automático não está funcionando
```bash
# 1. Verifique se o script está rodando
tasklist | findstr php

# 2. Verifique os logs
type storage\logs\scheduler.log

# 3. Execute manualmente para testar
php artisan processos:kill-automatico
```

### Processos não estão sendo finalizados
```bash
# 1. Verifique os parâmetros
php check_parametros.php

# 2. Verifique se são realmente bloqueadores
# Acesse a interface web e veja a coluna "Id Bloqueador"

# 3. Execute análise
php analise_completa_logs.php
```

### Verificar se a correção está ativa
```bash
# 1. Teste a função corrigida
php test_funcao_corrigida.php

# Resultado esperado: "✅ TODOS OS TESTES PASSARAM!"
```

---

## 📦 Estrutura de Arquivos

```
gerencia-processos/
│
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── KillProcessosAutomatico.php  ← Comando de kill automático
│   │
│   └── Http/
│       └── Controllers/
│           └── ProcessosController.php      ← Controller da interface
│
├── resources/
│   └── views/
│       ├── processos/
│       │   └── index.blade.php              ← Tela principal de processos
│       └── logs/
│           └── index.blade.php              ← Tela de logs
│
├── storage/
│   └── logs/
│       └── scheduler.log                    ← Logs do scheduler
│
├── KILL_AUTOMATICO.md                       ← Documentação principal
├── CORRECAO_BUG_KILL_AUTOMATICO.md         ← Documentação da correção
├── DOCUMENTACAO_INDEX.md                    ← Este arquivo
│
├── start_kill_automatico.bat                ← Script de inicialização
│
├── check_parametros.php                     ← Verificar parâmetros
├── test_funcao_corrigida.php               ← Testar função corrigida
├── analise_completa_logs.php               ← Analisar logs
├── verificar_logs_kill.php                 ← Verificar logs detalhados
└── debug_formato_tempo.php                 ← Debug de formato de tempo
```

---

## 📝 Histórico de Versões

| Versão | Data | Descrição |
|--------|------|-----------|
| 1.0 | 03/11/2025 | Implementação inicial do kill automático |
| 1.1 | 05/11/2025 | **Correção crítica**: Bug na conversão de tempo |

---

## 📞 Suporte

Para questões relacionadas ao sistema:

1. **Documentação**: Consulte os arquivos .md listados acima
2. **Scripts de Teste**: Execute os scripts de verificação
3. **Logs**: Verifique `storage/logs/scheduler.log`
4. **Interface**: Menu do sistema → Logs / Parâmetros

---

**Última Atualização**: 05/11/2025
**Versão da Documentação**: 1.0
