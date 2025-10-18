# Índice de Documentação - QR Code SaaS Platform

## 📚 Documentação Completa

Esta é a documentação completa do sistema QR Code SaaS Platform, desenvolvido em Laravel 11 com PostgreSQL. O sistema está otimizado para funcionar tanto em servidor compartilhado quanto em VPS/dedicado.

## 📋 Documentos Disponíveis

### 1. **PRODUCTION-DOCUMENTATION.md**
**Documentação Principal de Produção**
- Visão geral completa do sistema
- Todos os comandos Artisan disponíveis
- Funcionalidades e recursos
- Configurações de segurança
- Monitoramento e logs
- Troubleshooting
- Exemplos de uso

### 2. **ARTISAN-COMMANDS-GUIDE.md**
**Guia Completo de Comandos Artisan**
- Comandos por categoria
- Exemplos de saída
- Parâmetros e opções
- Comandos de diagnóstico
- Comandos de emergência
- Configuração de cron jobs
- Exemplos práticos de uso

### 3. **API-ENDPOINTS-GUIDE.md**
**Guia de API Endpoints**
- Todos os endpoints REST disponíveis
- Autenticação e headers
- Parâmetros e respostas
- Códigos de erro
- Rate limiting
- Exemplos em JavaScript, PHP e Python
- Documentação completa da API

### 4. **PRODUCTION-SETUP-GUIDE.md**
**Guia de Configuração de Produção**
- Configuração para servidor compartilhado
- Configuração para VPS/dedicado
- Instalação de dependências
- Configuração de Apache/Nginx
- Configuração de SSL
- Configurações de ambiente
- Cron jobs e monitoramento

### 5. **deploy/SHARED-HOSTING-README.md**
**Guia Específico para Servidor Compartilhado**
- Requisitos do servidor compartilhado
- Deploy automático e manual
- Configurações específicas
- Troubleshooting
- Otimizações
- Checklist de deploy

### 6. **deploy/SHARED-HOSTING-MODIFICATIONS.md**
**Resumo das Modificações para Servidor Compartilhado**
- Principais modificações realizadas
- Arquivos criados/modificados
- Instruções de deploy
- Configurações específicas
- Benefícios das modificações
- Checklist de deploy

## 🚀 Início Rápido

### Para Servidor Compartilhado
1. Leia **deploy/SHARED-HOSTING-README.md**
2. Execute o script de deploy: `./deploy/shared-hosting-deploy.sh`
3. Configure o arquivo `.env` usando **deploy/env.shared-hosting.example**
4. Consulte **PRODUCTION-SETUP-GUIDE.md** para configurações específicas

### Para VPS/Dedicado
1. Leia **PRODUCTION-SETUP-GUIDE.md**
2. Execute o script de deploy: `./deploy/deploy.sh`
3. Configure o arquivo `.env` usando **deploy/env.production.example**
4. Configure Nginx, PostgreSQL e Redis conforme o guia

## 🔧 Comandos Essenciais

### Configuração Inicial
```bash
# Verificar configuração do servidor
php artisan shared-hosting:configure --check

# Configurar aplicação
php artisan shared-hosting:configure

# Executar migrations
php artisan migrate --force

# Criar link de storage
php artisan storage:link
```

### Manutenção Diária
```bash
# Limpar cache
php artisan cache:clear-all

# Otimizar banco
php artisan db:optimize --analyze

# Limpeza automática
php artisan shared-hosting:cleanup
```

### Monitoramento
```bash
# Verificar performance
php artisan performance:monitor

# Auditoria de segurança
php artisan security:audit

# Verificar logs
tail -f storage/logs/laravel.log
```

## 📊 Funcionalidades do Sistema

### Sistema de QR Codes
- **20+ tipos** de QR Code (URL, vCard, Business, etc.)
- **Customização visual** completa (cores, logo, stickers)
- **Múltiplos formatos** (PNG, JPG, SVG, EPS)
- **Resolução ajustável** (100px - 2000px)
- **QR Codes dinâmicos** editáveis

### Sistema de Rastreamento
- **Estatísticas detalhadas** de scans
- **Geolocalização** (país, cidade, coordenadas)
- **Detecção de dispositivo** (mobile, tablet, desktop)
- **Relatórios exportáveis** (CSV)
- **Dashboard** com gráficos em tempo real

### Sistema de Assinatura
- **Trial grátis** de 7 dias
- **Plano Premium** R$ 29,90/mês
- **Integração Mercado Pago** completa
- **Webhooks** para atualizações automáticas

### Sistema de Equipes
- **Gestão de equipes** e membros
- **Permissões granulares** (visualizar, criar, editar, excluir)
- **Compartilhamento** de QR Codes
- **Gestão de pastas** compartilhadas

### Sistema de Domínios Personalizados
- **Domínios próprios** (whitelabel)
- **Verificação DNS** automática
- **Wildcard DNS** para subdomínios
- **SSL automático** com Let's Encrypt

### Sistema de Suporte
- **Integração WhatsApp Business** API
- **Tickets automáticos** de suporte
- **Respostas automáticas** fora do horário
- **Histórico de conversas**

### Compliance LGPD
- **Exportação de dados** (JSON/CSV)
- **Exclusão de dados** permanente
- **Anonimização** de dados de scans
- **Política de privacidade** detalhada

### Internacionalização
- **Português (Brasil)** 100% implementado
- **Estrutura preparada** para outros idiomas
- **Formatação** de moeda, data e números

## 🔒 Segurança Implementada

### Headers de Segurança
- X-Frame-Options, X-XSS-Protection
- Strict-Transport-Security (HSTS)
- Content-Security-Policy (CSP)
- Referrer-Policy

### Rate Limiting
- Login: 5 tentativas/5 minutos
- API: 100 requisições/minuto
- Upload: 10 arquivos/minuto
- QR Code: 20 gerações/minuto

### Validação de Uploads
- Tipos MIME permitidos
- Tamanho máximo: 10MB
- Detecção de malware
- Validação de SVG

### Detecção de Ameaças
- User-Agents suspeitos
- Tentativas de SQL injection
- Tentativas de XSS
- Path traversal
- Bloqueio automático de IPs

## 📱 API REST Completa

### Autenticação
- Registro com reCAPTCHA v3
- Login com rate limiting
- Refresh token
- Logout

### QR Codes
- CRUD completo
- Download em múltiplos formatos
- Preview em tempo real
- Estatísticas detalhadas

### Estatísticas
- Dashboard com métricas
- Relatórios por período
- Exportação CSV
- Gráficos de acesso

### Assinatura
- Status da assinatura
- Upgrade/cancelamento
- Webhooks de pagamento

### Equipes e Domínios
- Gestão de equipes
- Domínios personalizados
- Verificação DNS

## 🖥️ Configurações de Servidor

### Servidor Compartilhado
- Apache com mod_rewrite
- PHP 8.2+ com extensões
- PostgreSQL 12+
- Cache em arquivo
- Filas síncronas

### VPS/Dedicado
- Nginx com wildcard DNS
- PHP-FPM otimizado
- PostgreSQL 15+
- Redis para cache e filas
- Supervisor para workers

## 📊 Monitoramento e Logs

### Logs Disponíveis
- Laravel (aplicação)
- Segurança (tentativas de acesso)
- Pagamentos (transações)
- WhatsApp (mensagens)
- QR Codes (geração)
- Performance (métricas)

### Métricas Monitoradas
- Tempo de resposta
- Uso de memória
- Consultas de banco
- Cache hit/miss
- Espaço em disco

## 🚨 Troubleshooting

### Problemas Comuns
- Erro 500: Verificar logs e permissões
- Performance: Otimizar banco e cache
- Banco: Verificar conexão e migrations
- Segurança: Executar auditoria

### Comandos de Recuperação
- Backup de emergência
- Restaurar backup
- Recriar caches
- Reprocessar filas

## 📞 Suporte

### Canais de Suporte
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

### Horário de Atendimento
- **Segunda a Sexta**: 09:00 - 18:00 (Brasília)
- **Respostas automáticas** fora do horário
- **Suporte prioritário** para usuários premium

## 📝 Estrutura de Arquivos

```
qrcodesaas/
├── app/
│   ├── Console/Commands/          # Comandos Artisan
│   ├── Http/Controllers/          # Controllers
│   ├── Http/Middleware/           # Middleware
│   ├── Models/                    # Models Eloquent
│   ├── Services/                  # Services
│   └── Jobs/                      # Jobs assíncronos
├── config/                        # Configurações
├── database/
│   ├── migrations/                # Migrations
│   └── seeders/                   # Seeders
├── deploy/                        # Scripts de deploy
├── resources/
│   ├── views/                     # Views Blade
│   ├── lang/                      # Traduções
│   ├── css/                       # CSS
│   └── js/                        # JavaScript
├── routes/                        # Rotas
├── storage/                       # Storage
└── tests/                         # Testes
```

## 🎯 Próximos Passos

1. **Configurar servidor** conforme o guia apropriado
2. **Executar deploy** usando os scripts fornecidos
3. **Configurar variáveis** de ambiente
4. **Testar funcionalidades** básicas
5. **Configurar monitoramento** e logs
6. **Configurar backup** automático
7. **Testar em produção** com dados reais

## ✅ Checklist de Deploy

### Pré-Deploy
- [ ] Servidor com requisitos mínimos
- [ ] Banco de dados configurado
- [ ] SSL/HTTPS configurado
- [ ] Domínio apontando para servidor

### Deploy
- [ ] Código clonado/atualizado
- [ ] Dependências instaladas
- [ ] Arquivo .env configurado
- [ ] Migrations executadas
- [ ] Permissões configuradas
- [ ] Cache criado

### Pós-Deploy
- [ ] Health check funcionando
- [ ] Logs sendo gerados
- [ ] Cron jobs configurados
- [ ] Backup configurado
- [ ] Monitoramento ativo
- [ ] Testes funcionais realizados

---

**Esta documentação fornece tudo o que você precisa para configurar, manter e usar o sistema QR Code SaaS em produção.**
