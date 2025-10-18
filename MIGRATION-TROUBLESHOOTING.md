# Guia de Troubleshooting - Migrations

## 🚨 Problemas Comuns e Soluções

### 1. Erro: "column does not exist"

#### Problema
```
SQLSTATE[42703]: Undefined column: 7 ERROR: column "subscription_status" does not exist
```

#### Causa
A migration de índices está sendo executada antes da migration que cria a coluna.

#### Solução
```bash
# 1. Verificar ordem das migrations
php artisan migrate:status

# 2. Verificar se a migration de extensão da tabela users foi executada
php artisan migrations:fix

# 3. Se necessário, executar migrations específicas
php artisan migrate --path=database/migrations/2025_10_17_213241_extend_users_table_for_saas.php

# 4. Depois executar a migration de índices
php artisan migrate --path=database/migrations/2025_10_17_213400_add_performance_indexes.php
```

### 2. Erro: "table does not exist"

#### Problema
```
SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "subscriptions" does not exist
```

#### Solução
```bash
# 1. Verificar se todas as migrations foram executadas
php artisan migrate:status

# 2. Executar todas as migrations
php artisan migrate --force

# 3. Se ainda houver problemas, verificar a ordem
php artisan migrations:fix
```

### 3. Erro: "duplicate key value"

#### Problema
```
SQLSTATE[23505]: Unique violation: 7 ERROR: duplicate key value violates unique constraint
```

#### Solução
```bash
# 1. Verificar se há dados duplicados
php artisan tinker
>>> DB::table('users')->where('email', 'test@example.com')->count();

# 2. Limpar dados duplicados se necessário
# 3. Executar migrations novamente
php artisan migrate --force
```

### 4. Erro: "permission denied"

#### Problema
```
SQLSTATE[42501]: Insufficient privilege: 7 ERROR: permission denied for table users
```

#### Solução
```bash
# 1. Verificar permissões do usuário do banco
# 2. Conceder permissões necessárias
GRANT ALL PRIVILEGES ON DATABASE qrcode_saas TO qrcode_user;
GRANT ALL ON SCHEMA public TO qrcode_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO qrcode_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO qrcode_user;
```

## 🔧 Comandos de Diagnóstico

### Verificar Status das Migrations
```bash
# Ver status de todas as migrations
php artisan migrate:status

# Ver migrations pendentes
php artisan migrations:fix

# Verificar estrutura do banco
php artisan tinker
>>> Schema::hasTable('users')
>>> Schema::hasColumn('users', 'subscription_status')
```

### Verificar Estrutura do Banco
```bash
# Conectar ao PostgreSQL
psql -h localhost -U qrcode_user -d qrcode_saas

# Listar tabelas
\dt

# Descrever tabela
\d users

# Listar índices
\di

# Verificar colunas
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'users';
```

## 🚀 Soluções por Cenário

### Cenário 1: Primeira Instalação
```bash
# 1. Configurar banco de dados
createdb qrcode_saas

# 2. Executar todas as migrations
php artisan migrate --force

# 3. Executar seeders
php artisan db:seed --force

# 4. Verificar se tudo está funcionando
php artisan migrations:fix
```

### Cenário 2: Atualização em Produção
```bash
# 1. Fazer backup do banco
pg_dump -h localhost -U qrcode_user qrcode_saas > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Verificar migrations pendentes
php artisan migrate:status

# 3. Executar migrations pendentes
php artisan migrate --force

# 4. Verificar se não há erros
php artisan migrations:fix
```

### Cenário 3: Problemas de Ordem
```bash
# 1. Verificar ordem das migrations
ls -la database/migrations/ | grep 2025_10_17

# 2. Se necessário, renomear migrations para ordem correta
# 3. Executar migrations específicas na ordem
php artisan migrate --path=database/migrations/2025_10_17_213241_extend_users_table_for_saas.php
php artisan migrate --path=database/migrations/2025_10_17_213247_create_subscriptions_table.php
# ... outras migrations na ordem correta
php artisan migrate --path=database/migrations/2025_10_17_213400_add_performance_indexes.php
```

### Cenário 4: Reset Completo (Desenvolvimento)
```bash
# ⚠️ ATENÇÃO: Isso apagará todos os dados!
php artisan migrate:fresh --seed --force
```

## 📋 Checklist de Verificação

### Antes de Executar Migrations
- [ ] Backup do banco de dados feito
- [ ] Variáveis de ambiente configuradas
- [ ] Usuário do banco tem permissões necessárias
- [ ] Conexão com banco funcionando

### Durante a Execução
- [ ] Monitorar logs de erro
- [ ] Verificar se cada migration foi executada
- [ ] Testar funcionalidades básicas

### Após a Execução
- [ ] Verificar estrutura das tabelas
- [ ] Verificar se índices foram criados
- [ ] Testar aplicação
- [ ] Verificar logs de erro

## 🔍 Comandos de Verificação

### Verificar Tabelas
```bash
php artisan tinker
>>> Schema::hasTable('users')
>>> Schema::hasTable('subscriptions')
>>> Schema::hasTable('qr_codes')
>>> Schema::hasTable('qr_scans')
```

### Verificar Colunas
```bash
php artisan tinker
>>> Schema::hasColumn('users', 'subscription_status')
>>> Schema::hasColumn('users', 'trial_ends_at')
>>> Schema::hasColumn('users', 'deleted_at')
```

### Verificar Índices
```bash
php artisan tinker
>>> DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'users'")
```

### Verificar Dados
```bash
php artisan tinker
>>> DB::table('users')->count()
>>> DB::table('subscriptions')->count()
>>> DB::table('qr_codes')->count()
```

## 🚨 Comandos de Emergência

### Rollback de Migrations
```bash
# Reverter última migration
php artisan migrate:rollback

# Reverter múltiplas migrations
php artisan migrate:rollback --step=3

# Reverter todas as migrations
php artisan migrate:reset
```

### Recriar Banco (Desenvolvimento)
```bash
# ⚠️ ATENÇÃO: Apaga todos os dados!
php artisan migrate:fresh --seed
```

### Verificar e Corrigir
```bash
# Comando personalizado para verificar migrations
php artisan migrations:fix

# Verificar status detalhado
php artisan migrate:status
```

## 📞 Suporte

Se você ainda estiver enfrentando problemas:

1. **Verificar logs**: `tail -f storage/logs/laravel.log`
2. **Executar diagnóstico**: `php artisan migrations:fix`
3. **Verificar configuração**: `php artisan config:show`
4. **Contatar suporte**: support@qr.fluxti.com.br

## 📚 Recursos Adicionais

- [Documentação Laravel Migrations](https://laravel.com/docs/11.x/migrations)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Guia de Deploy](PRODUCTION-SETUP-GUIDE.md)
- [Comandos Artisan](ARTISAN-COMMANDS-GUIDE.md)

---

**Este guia deve resolver a maioria dos problemas de migration. Se o problema persistir, consulte a documentação oficial ou entre em contato com o suporte.**
