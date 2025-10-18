# Solução Rápida - Problema de Migrations

## 🚨 Problema Atual
```
SQLSTATE[42P07]: Duplicate table: 7 ERROR: relation "subscriptions_mp_subscription_id_index" already exists
```

## ⚡ Solução Rápida

### Opção 1: Limpar Índices Duplicados
```bash
# 1. Limpar índices duplicados
php artisan db:clean-indexes --force

# 2. Executar migrations novamente
php artisan migrate --force
```

### Opção 2: Reset Completo (Desenvolvimento)
```bash
# ⚠️ ATENÇÃO: Isso apagará todos os dados!
php artisan migrate:fresh --seed --force
```

### Opção 3: Reset e Rebuild (Produção)
```bash
# 1. Fazer backup do banco
pg_dump -h localhost -U qrcode_user qrcode_saas > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Limpar índices problemáticos manualmente
psql -h localhost -U qrcode_user -d qrcode_saas -c "DROP INDEX IF EXISTS subscriptions_mp_subscription_id_index;"
psql -h localhost -U qrcode_user -d qrcode_saas -c "DROP INDEX IF EXISTS subscriptions_user_id_index;"
psql -h localhost -U qrcode_user -d qrcode_saas -c "DROP INDEX IF EXISTS subscriptions_status_index;"

# 3. Executar migrations
php artisan migrate --force
```

## 🔧 Comandos de Diagnóstico

### Verificar Status das Migrations
```bash
php artisan migrate:status
```

### Verificar Índices Existentes
```bash
php artisan tinker
>>> DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'subscriptions'")
```

### Limpar Índices Duplicados
```bash
php artisan db:clean-indexes --force
```

### Verificar Estrutura do Banco
```bash
php artisan migrations:fix
```

## 🚀 Solução Definitiva

### 1. Limpar Banco Completamente
```bash
# Conectar ao PostgreSQL
psql -h localhost -U qrcode_user -d qrcode_saas

# Remover todos os índices problemáticos
DROP INDEX IF EXISTS subscriptions_mp_subscription_id_index;
DROP INDEX IF EXISTS subscriptions_user_id_index;
DROP INDEX IF EXISTS subscriptions_status_index;
DROP INDEX IF EXISTS subscriptions_current_period_end_index;
DROP INDEX IF EXISTS subscriptions_user_id_status_index;

# Sair do psql
\q
```

### 2. Executar Migrations
```bash
php artisan migrate --force
```

### 3. Verificar se Funcionou
```bash
php artisan migrate:status
php artisan migrations:fix
```

## 🔍 Verificação Final

### Testar Aplicação
```bash
# Verificar se as tabelas existem
php artisan tinker
>>> Schema::hasTable('users')
>>> Schema::hasTable('subscriptions')
>>> Schema::hasTable('qr_codes')

# Verificar se as colunas existem
>>> Schema::hasColumn('users', 'subscription_status')
>>> Schema::hasColumn('users', 'trial_ends_at')

# Verificar se os índices foram criados
>>> DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'subscriptions'")
```

## 📞 Se Ainda Houver Problemas

### Comandos de Emergência
```bash
# 1. Verificar logs
tail -f storage/logs/laravel.log

# 2. Verificar configuração do banco
php artisan config:show database

# 3. Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();

# 4. Reset completo (último recurso)
php artisan migrate:fresh --seed --force
```

### Contatar Suporte
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

---

**Esta solução deve resolver o problema de migrations duplicadas. Execute os comandos na ordem apresentada.**
