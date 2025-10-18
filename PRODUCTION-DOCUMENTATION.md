# QR Code SaaS Platform - Documentação de Produção

## 📋 Visão Geral

Esta documentação contém todos os comandos Artisan, funcionalidades, configurações e recursos disponíveis para o sistema QR Code SaaS em produção.

## 🚀 Comandos Artisan Disponíveis

### 1. Comandos de Configuração e Manutenção

#### Configuração de Servidor Compartilhado
```bash
# Verificar configuração do servidor compartilhado
php artisan shared-hosting:configure --check

# Configurar aplicação para servidor compartilhado
php artisan shared-hosting:configure

# Limpeza e otimização para servidor compartilhado
php artisan shared-hosting:cleanup
php artisan shared-hosting:cleanup --force
```

#### Auditoria de Segurança
```bash
# Executar auditoria completa de segurança
php artisan security:audit

# Exportar relatório de segurança
php artisan security:audit --export

# Corrigir problemas de segurança automaticamente
php artisan security:audit --fix
```

#### Monitoramento de Performance
```bash
# Monitorar métricas de performance
php artisan performance:monitor

# Exportar métricas para arquivo
php artisan performance:monitor --export
```

#### Otimização de Banco de Dados
```bash
# Otimizar banco de dados (PostgreSQL)
php artisan db:optimize --analyze --vacuum

# Apenas ANALYZE
php artisan db:optimize --analyze

# Apenas VACUUM (PostgreSQL)
php artisan db:optimize --vacuum
```

#### Limpeza de Cache
```bash
# Limpar todo o cache da aplicação
php artisan cache:clear-all

# Limpar cache para usuário específico
php artisan cache:clear-all --user=123

# Limpar cache por tipo
php artisan cache:clear-all --type=dashboard
php artisan cache:clear-all --type=qrcode
```

### 2. Comandos de Assinatura e Pagamento

#### Gerenciamento de Trials
```bash
# Expirar trials de usuários
php artisan subscriptions:expire-trials

# Enviar notificações de trial expirando
php artisan subscriptions:send-trial-expiring-notifications
```

### 3. Comandos de Dados e Seeders

#### Seeders de Dados
```bash
# Executar todos os seeders
php artisan db:seed

# Executar seeder específico
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=QrCodeSeeder
php artisan db:seed --class=TeamSeeder
php artisan db:seed --class=SupportTicketSeeder

# Seeders com dados de demonstração
php artisan demo:seed
php artisan demo:seed --fresh
```

### 4. Comandos de Migração

#### Migrações de Banco
```bash
# Executar migrações
php artisan migrate

# Executar migrações em produção
php artisan migrate --force

# Reverter última migração
php artisan migrate:rollback

# Reverter todas as migrações
php artisan migrate:reset

# Recriar banco (desenvolvimento)
php artisan migrate:fresh
php artisan migrate:fresh --seed
```

### 5. Comandos de Cache e Otimização

#### Cache da Aplicação
```bash
# Limpar cache de configuração
php artisan config:clear

# Cache de configuração
php artisan config:cache

# Limpar cache de rotas
php artisan route:clear

# Cache de rotas
php artisan route:cache

# Limpar cache de views
php artisan view:clear

# Cache de views
php artisan view:cache

# Limpar cache de eventos
php artisan event:clear

# Cache de eventos
php artisan event:cache
```

#### Otimização Geral
```bash
# Otimizar aplicação para produção
php artisan optimize

# Otimizar com cache de configuração
php artisan optimize:clear
```

### 6. Comandos de Filas

#### Gerenciamento de Filas
```bash
# Processar filas
php artisan queue:work

# Processar filas com timeout
php artisan queue:work --timeout=300

# Processar filas de fila específica
php artisan queue:work --queue=webhooks,notifications

# Limpar filas falhadas
php artisan queue:prune-failed

# Reprocessar jobs falhados
php artisan queue:retry all

# Reprocessar job específico
php artisan queue:retry 5

# Limpar todos os jobs falhados
php artisan queue:flush
```

### 7. Comandos de Storage

#### Gerenciamento de Arquivos
```bash
# Criar link simbólico para storage
php artisan storage:link

# Limpar arquivos antigos
php artisan storage:cleanup
```

### 8. Comandos de Testes

#### Execução de Testes
```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=QrCodeControllerTest
php artisan test --filter=AuthenticationTest
php artisan test --filter=SubscriptionTest

# Executar testes com cobertura
php artisan test --coverage

# Executar testes em paralelo
php artisan test --parallel
```

## 🔧 Funcionalidades do Sistema

### 1. Sistema de Autenticação

#### Recursos Disponíveis
- **Registro com reCAPTCHA v3**
- **Login com rate limiting**
- **Verificação de email obrigatória**
- **Reset de senha**
- **Autenticação Sanctum para API**

#### Middleware de Segurança
- `CheckSubscription` - Verifica status da assinatura
- `SecurityHeaders` - Headers de segurança
- `ValidateFileUpload` - Validação de uploads
- `BlockSuspiciousActivity` - Bloqueio de atividade suspeita
- `SetLocale` - Configuração de idioma

### 2. Sistema de QR Codes

#### Tipos de QR Code Suportados
1. **URL** - Links para websites
2. **vCard** - Cartões de visita digitais
3. **Business** - Páginas de negócio
4. **Coupon** - Cupons de desconto
5. **Text** - Texto livre
6. **MP3** - Links para áudio
7. **PDF** - Links para documentos
8. **Image** - Links para imagens
9. **Video** - Links para vídeos
10. **App** - Links para aplicativos
11. **Menu** - Menus digitais
12. **Email** - Emails pré-formatados
13. **Phone** - Números de telefone
14. **SMS** - Mensagens SMS
15. **Social** - Redes sociais
16. **Wi-Fi** - Configuração de Wi-Fi
17. **Event** - Eventos
18. **Location** - Localização
19. **Feedback** - Formulários de feedback
20. **Crypto** - Carteiras de criptomoedas

#### Customização Visual
- **Cores personalizadas** (frente, fundo, olhos)
- **Logo central** (PNG, JPG, SVG)
- **Stickers/CTAs** personalizados
- **Formas** (quadrado, arredondado)
- **Resolução** (100px - 2000px)
- **Formatos** (PNG, JPG, SVG, EPS)

### 3. Sistema de Rastreamento

#### Dados Coletados
- **Total de scans** e **scans únicos**
- **Geolocalização** (país, cidade, coordenadas)
- **Dispositivo** (mobile, tablet, desktop)
- **Sistema operacional** e **navegador**
- **Data e hora** dos scans
- **IP address** (para detecção única)

#### Relatórios Disponíveis
- **Dashboard** com estatísticas em tempo real
- **Gráficos** de acesso (últimos 30 dias)
- **Exportação CSV** com dados detalhados
- **Filtros** por período, dispositivo, localização

### 4. Sistema de Assinatura

#### Planos Disponíveis
- **Trial Grátis** - 7 dias com todas as funcionalidades
- **Plano Premium** - R$ 29,90/mês com acesso completo

#### Funcionalidades por Plano
**Trial/Premium:**
- QR Codes dinâmicos
- Estatísticas avançadas
- Domínio personalizado
- Sistema de equipes
- Suporte prioritário

**Apenas Premium:**
- QR Codes ilimitados
- Exportação de relatórios
- API access
- Webhooks personalizados

### 5. Sistema de Equipes

#### Recursos Disponíveis
- **Criação de equipes**
- **Adição/remoção de membros**
- **Permissões granulares** (visualizar, criar, editar, excluir)
- **Compartilhamento de QR Codes**
- **Gestão de pastas compartilhadas**

### 6. Sistema de Domínios Personalizados

#### Funcionalidades
- **Adição de domínios** personalizados
- **Verificação DNS** automática
- **Wildcard DNS** para subdomínios
- **SSL automático** com Let's Encrypt
- **Instruções de configuração** DNS

### 7. Sistema de Suporte

#### Integração WhatsApp Business
- **Chat integrado** via WhatsApp
- **Tickets de suporte** automáticos
- **Respostas automáticas** fora do horário comercial
- **Histórico de conversas**

#### Horário de Atendimento
- **Segunda a Sexta**: 09:00 - 18:00
- **Respostas automáticas** fora do horário
- **Notificações** para a equipe

### 8. Sistema de Ajuda

#### Recursos Disponíveis
- **FAQ** com categorias organizadas
- **Tutoriais** passo a passo
- **Política de Privacidade** (LGPD)
- **Termos de Uso**
- **Página de Contato**

### 9. Compliance LGPD

#### Funcionalidades
- **Exportação de dados** (JSON/CSV)
- **Exclusão de dados** permanente
- **Anonimização** de dados de scans
- **Política de privacidade** detalhada
- **Consentimento** explícito

### 10. Internacionalização

#### Idiomas Suportados
- **Português (Brasil)** - 100% implementado
- **Estrutura preparada** para outros idiomas

#### Recursos de Tradução
- **Detecção automática** de idioma
- **Seletor de idioma** no frontend
- **Formatação** de moeda, data e números
- **Diretivas Blade** personalizadas

## 🔒 Segurança Implementada

### 1. Proteções de Segurança

#### Headers de Segurança
- **X-Frame-Options**: DENY
- **X-Content-Type-Options**: nosniff
- **X-XSS-Protection**: 1; mode=block
- **Strict-Transport-Security**: HSTS
- **Content-Security-Policy**: CSP completo
- **Referrer-Policy**: strict-origin-when-cross-origin

#### Rate Limiting
- **Login**: 5 tentativas/5 minutos
- **Registro**: 3 tentativas/10 minutos
- **API**: 100 requisições/minuto
- **Upload**: 10 arquivos/minuto
- **QR Code**: 20 gerações/minuto

#### Validação de Uploads
- **Tipos MIME** permitidos
- **Tamanho máximo**: 10MB
- **Dimensões**: máximo 2000x2000px
- **Detecção de malware**
- **Validação de SVG**

### 2. Detecção de Ameaças

#### Atividade Suspeita
- **User-Agents** suspeitos
- **Tentativas de SQL injection**
- **Tentativas de XSS**
- **Path traversal**
- **Acesso a arquivos sensíveis**

#### Bloqueio Automático
- **IPs suspeitos** bloqueados temporariamente
- **Emails** com muitas tentativas falhadas
- **Logs de segurança** detalhados

## 📊 Monitoramento e Logs

### 1. Logs Disponíveis

#### Canais de Log
- **Laravel** - `storage/logs/laravel.log`
- **Segurança** - `storage/logs/security.log`
- **Pagamentos** - `storage/logs/payment.log`
- **WhatsApp** - `storage/logs/whatsapp.log`
- **QR Codes** - `storage/logs/qrcode.log`
- **Performance** - `storage/logs/performance.log`
- **Auditoria** - `storage/logs/audit.log`

#### Rotação de Logs
- **Retenção**: 7-365 dias (configurável)
- **Compressão** automática
- **Limpeza** automática

### 2. Métricas Monitoradas

#### Performance
- **Tempo de resposta** das páginas
- **Uso de memória** PHP
- **Consultas** de banco de dados
- **Cache hit/miss** ratio
- **Espaço em disco**

#### Segurança
- **Tentativas de login** falhadas
- **IPs bloqueados**
- **Atividades suspeitas**
- **Uploads maliciosos**
- **Rate limiting** ativado

## 🚀 Deploy e Infraestrutura

### 1. Configurações de Produção

#### Servidor Compartilhado
- **Apache** com mod_rewrite
- **PHP 8.2+** com extensões necessárias
- **PostgreSQL 15+**
- **SSL/HTTPS** obrigatório
- **Composer** disponível

#### VPS/Dedicado
- **Nginx** com wildcard DNS
- **PHP-FPM** otimizado
- **Redis** para cache e filas
- **Supervisor** para workers
- **Let's Encrypt** SSL

### 2. Scripts de Deploy

#### Deploy Automático
```bash
# Servidor compartilhado
./deploy/shared-hosting-deploy.sh

# VPS/Dedicado
./deploy/deploy.sh
```

#### Configurações
- **Backup automático** antes do deploy
- **Verificação de requisitos**
- **Configuração de permissões**
- **Health check** pós-deploy

### 3. Cron Jobs Necessários

#### Tarefas Agendadas
```bash
# Laravel Scheduler
* * * * * cd /path/to/app && php artisan schedule:run

# Limpeza de filas falhadas
0 2 * * * cd /path/to/app && php artisan queue:prune-failed

# Limpeza de cache
0 3 * * * cd /path/to/app && php artisan cache:clear-all

# Limpeza de arquivos antigos
0 4 * * * cd /path/to/app && php artisan shared-hosting:cleanup

# Expirar trials
0 5 * * * cd /path/to/app && php artisan subscriptions:expire-trials

# Verificar domínios customizados
0 6 * * * cd /path/to/app && php artisan domains:verify
```

## 🔧 Configurações de Ambiente

### 1. Variáveis de Ambiente Obrigatórias

#### Aplicação
```env
APP_NAME="QR Code SaaS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=false
APP_URL=https://qr.fluxti.com.br
```

#### Banco de Dados
```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=qr_fluxti_production
DB_USERNAME=qr_fluxti_user
DB_PASSWORD=YOUR_SECURE_PASSWORD
```

#### Cache e Sessões
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

#### Mercado Pago
```env
MERCADOPAGO_ACCESS_TOKEN=YOUR_ACCESS_TOKEN
MERCADOPAGO_PUBLIC_KEY=YOUR_PUBLIC_KEY
MERCADOPAGO_PLAN_ID=YOUR_PLAN_ID
MERCADOPAGO_WEBHOOK_SECRET=YOUR_WEBHOOK_SECRET
```

#### WhatsApp Business
```env
WHATSAPP_ACCESS_TOKEN=YOUR_ACCESS_TOKEN
WHATSAPP_PHONE_NUMBER_ID=YOUR_PHONE_NUMBER_ID
WHATSAPP_APP_ID=YOUR_APP_ID
WHATSAPP_APP_SECRET=YOUR_APP_SECRET
```

#### reCAPTCHA
```env
RECAPTCHA_SITE_KEY=YOUR_SITE_KEY
RECAPTCHA_SECRET_KEY=YOUR_SECRET_KEY
RECAPTCHA_MIN_SCORE=0.5
```

### 2. Configurações de Performance

#### PHP (php.ini)
```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M
max_input_vars = 3000
max_file_uploads = 20
```

#### PostgreSQL
```sql
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 64MB
```

## 📱 API Endpoints

### 1. Autenticação
```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh
```

### 2. QR Codes
```
GET    /api/qrcodes
POST   /api/qrcodes
GET    /api/qrcodes/{id}
PUT    /api/qrcodes/{id}
DELETE /api/qrcodes/{id}
GET    /api/qrcodes/{id}/download
GET    /api/qrcodes/{id}/stats
```

### 3. Estatísticas
```
GET /api/stats/dashboard
GET /api/stats/qrcode/{id}
GET /api/stats/export/{id}
```

### 4. Assinatura
```
GET  /api/subscription/status
POST /api/subscription/upgrade
POST /api/subscription/cancel
```

## 🆘 Troubleshooting

### 1. Problemas Comuns

#### Erro 500 Internal Server Error
```bash
# Verificar logs
tail -f storage/logs/laravel.log
tail -f /var/log/apache2/error.log

# Verificar permissões
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Limpar cache
php artisan cache:clear-all
```

#### Problemas de Performance
```bash
# Verificar configuração
php artisan shared-hosting:configure --check

# Otimizar aplicação
php artisan optimize

# Limpar arquivos antigos
php artisan shared-hosting:cleanup
```

#### Problemas de Banco de Dados
```bash
# Verificar conexão
php artisan tinker
>>> DB::connection()->getPdo();

# Executar migrations
php artisan migrate --force

# Otimizar banco
php artisan db:optimize --analyze --vacuum
```

### 2. Comandos de Diagnóstico

#### Verificar Sistema
```bash
# Status geral
php artisan about

# Verificar configuração
php artisan config:show

# Verificar rotas
php artisan route:list

# Verificar cache
php artisan cache:table
```

#### Verificar Segurança
```bash
# Auditoria de segurança
php artisan security:audit

# Verificar logs de segurança
tail -f storage/logs/security.log
```

## 📞 Suporte e Contato

### 1. Canais de Suporte
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

### 2. Horário de Atendimento
- **Segunda a Sexta**: 09:00 - 18:00 (Brasília)
- **Respostas automáticas** fora do horário
- **Suporte prioritário** para usuários premium

### 3. Recursos de Ajuda
- **FAQ** completo no sistema
- **Tutoriais** passo a passo
- **Documentação** técnica
- **Vídeos** explicativos

---

**Esta documentação cobre todos os recursos, comandos e funcionalidades disponíveis no sistema QR Code SaaS em produção.**
