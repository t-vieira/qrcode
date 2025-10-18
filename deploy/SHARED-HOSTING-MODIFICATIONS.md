# Modificações para Servidor Compartilhado - qr.fluxti.com.br

## 📋 Resumo das Modificações

O sistema foi adaptado para funcionar em servidor compartilhado com Apache. Aqui estão as principais modificações realizadas:

## 🔧 Arquivos Criados/Modificados

### 1. Configurações Apache
- **`deploy/.htaccess`** - Configuração completa do Apache com:
  - Headers de segurança
  - Redirecionamento HTTP → HTTPS
  - Bloqueio de arquivos sensíveis
  - Compressão Gzip
  - Cache de arquivos estáticos
  - Rate limiting básico
  - Configurações PHP otimizadas

- **`deploy/apache-vhost.conf`** - Configuração de Virtual Host para:
  - Domínio principal: `qr.fluxti.com.br`
  - Wildcard para domínios customizados: `*.qr.fluxti.com.br`
  - SSL/TLS configurado
  - Configurações de segurança

### 2. Configurações de Ambiente
- **`deploy/env.shared-hosting.example`** - Arquivo `.env` otimizado para servidor compartilhado:
  - Cache usando `file` driver
  - Filas síncronas (`sync`)
  - Sessões em arquivo
  - Configurações de memória otimizadas
  - Limites de upload ajustados

### 3. Scripts de Deploy
- **`deploy/shared-hosting-deploy.sh`** - Script automatizado de deploy:
  - Verificação de requisitos
  - Backup automático
  - Configuração de permissões
  - Otimização para servidor compartilhado
  - Health check

### 4. Services Adaptados
- **`app/Services/SharedHostingService.php`** - Service para:
  - Verificar limitações do servidor
  - Otimizar configurações
  - Gerenciar cache e storage
  - Limpeza automática
  - Monitoramento de espaço em disco

- **`app/Services/QrCodeGeneratorService.php`** - Modificado para:
  - Limitar resolução máxima (1500px)
  - Otimizar uso de memória
  - Configurações adaptadas para servidor compartilhado

### 5. Comandos Artisan
- **`app/Console/Commands/ConfigureSharedHosting.php`** - Comando para:
  - Verificar configuração do servidor
  - Configurar aplicação automaticamente
  - Otimizar para servidor compartilhado
  - Verificar suporte a funcionalidades

- **`app/Console/Commands/CleanupSharedHosting.php`** - Comando para:
  - Limpeza de arquivos antigos
  - Otimização de storage
  - Liberação de espaço em disco
  - Manutenção automática

### 6. Configurações
- **`config/qrcode.php`** - Configurações específicas para QR Codes:
  - Limites de resolução
  - Configurações de cache
  - Otimizações para servidor compartilhado
  - Configurações de segurança

## 🚀 Instruções de Deploy

### 1. Preparar o Servidor
```bash
# Conectar ao servidor
ssh usuario@servidor.com

# Navegar para o diretório
cd /home/usuario/public_html/qr.fluxti.com.br
```

### 2. Deploy Automático
```bash
# Clonar repositório
git clone https://github.com/yourusername/qrcodesaas.git .

# Executar deploy
chmod +x deploy/shared-hosting-deploy.sh
./deploy/shared-hosting-deploy.sh
```

### 3. Configurar Ambiente
```bash
# Copiar configurações
cp deploy/env.shared-hosting.example .env

# Editar configurações
nano .env
```

### 4. Configurar Apache
```bash
# Copiar .htaccess
cp deploy/.htaccess public/.htaccess

# Verificar mod_rewrite
apache2ctl -M | grep rewrite
```

### 5. Configurar Aplicação
```bash
# Configurar para servidor compartilhado
php artisan shared-hosting:configure

# Executar migrations
php artisan migrate --force

# Criar link de storage
php artisan storage:link
```

## ⚙️ Configurações Específicas

### 1. Cache
- **Driver**: `file` (em vez de Redis)
- **Sessões**: `file` (em vez de Redis)
- **Filas**: `sync` (em vez de Redis)

### 2. Storage
- **Driver**: `local` (em vez de S3)
- **Path**: `storage/app/public`
- **URL**: `https://qr.fluxti.com.br/storage`

### 3. Limitações
- **Resolução máxima**: 1500px (em vez de 2000px)
- **Tamanho de upload**: 10MB
- **Tempo de execução**: 300s
- **Memória**: 256MB

### 4. Segurança
- **Headers de segurança** via `.htaccess`
- **Bloqueio de arquivos sensíveis**
- **Rate limiting** básico
- **Validação de uploads**

## 🔒 Configurações de Segurança

### 1. Headers de Segurança
```apache
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

### 2. Bloqueio de Arquivos
```apache
<FilesMatch "\.(env|log|sql|bak|backup|old|tmp)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 3. Configurações PHP
```ini
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
display_errors = Off
log_errors = On
```

## 📊 Monitoramento

### 1. Comandos Úteis
```bash
# Verificar configuração
php artisan shared-hosting:configure --check

# Limpeza automática
php artisan shared-hosting:cleanup

# Auditoria de segurança
php artisan security:audit

# Monitorar performance
php artisan performance:monitor
```

### 2. Cron Jobs
```bash
# Adicionar ao crontab
* * * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan schedule:run >> /dev/null 2>&1
0 2 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan shared-hosting:cleanup >> /dev/null 2>&1
0 3 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan cache:clear-all >> /dev/null 2>&1
```

## 🚨 Troubleshooting

### 1. Problemas Comuns
- **Erro 500**: Verificar permissões e logs
- **Erro de cache**: Limpar cache e recriar
- **Problemas de storage**: Recriar link simbólico
- **Limite de memória**: Reduzir resolução de QR Codes

### 2. Logs Importantes
- **Apache**: `/var/log/apache2/error.log`
- **Aplicação**: `storage/logs/laravel.log`
- **Segurança**: `storage/logs/security.log`

## 📈 Otimizações

### 1. Performance
- Cache agressivo de arquivos estáticos
- Compressão Gzip
- Otimização de imagens
- Limpeza automática de arquivos antigos

### 2. Storage
- Limpeza automática de QR Codes antigos
- Otimização de cache
- Remoção de diretórios vazios
- Compressão de logs

### 3. Segurança
- Headers de segurança completos
- Bloqueio de arquivos sensíveis
- Rate limiting
- Validação rigorosa de uploads

## 🔄 Atualizações

### 1. Deploy de Atualizações
```bash
# Atualizar código
git pull origin main

# Executar deploy
./deploy/shared-hosting-deploy.sh

# Limpar cache
php artisan cache:clear-all
```

### 2. Backup
```bash
# Backup automático antes do deploy
# Mantém últimos 3 backups
# Inclui arquivos e banco de dados
```

## 📞 Suporte

Para suporte específico de servidor compartilhado:
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

## ✅ Checklist de Deploy

- [ ] Servidor com PHP 8.2+ e PostgreSQL
- [ ] Apache com mod_rewrite habilitado
- [ ] SSL/HTTPS configurado
- [ ] Composer disponível
- [ ] Permissões corretas (755/644)
- [ ] Arquivo `.env` configurado
- [ ] Banco de dados criado
- [ ] Migrations executadas
- [ ] Storage link criado
- [ ] Cron jobs configurados
- [ ] Health check funcionando
- [ ] Logs sendo gerados
- [ ] Backup configurado

## 🎯 Próximos Passos

1. **Testar em ambiente de desenvolvimento**
2. **Configurar domínio e SSL**
3. **Executar deploy em produção**
4. **Configurar monitoramento**
5. **Testar todas as funcionalidades**
6. **Configurar backup automático**
7. **Documentar procedimentos**

---

**O sistema está pronto para deploy em servidor compartilhado com todas as otimizações e configurações necessárias!**
