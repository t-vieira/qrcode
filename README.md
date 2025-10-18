# QR Code SaaS Platform

Sistema completo de geração e rastreamento de QR Codes desenvolvido em Laravel 11 com PostgreSQL.

## 🚀 Funcionalidades

### Sistema de QR Codes
- **20+ tipos** de QR Code (URL, vCard, Business, Coupon, Text, MP3, PDF, Image, Video, App, Menu, Email, Phone, SMS, Social, Wi-Fi, Event, Location, Feedback, Crypto)
- **Customização visual** completa (cores, logo, stickers, formas, resolução)
- **Múltiplos formatos** (PNG, JPG, SVG, EPS)
- **QR Codes dinâmicos** editáveis sem alterar o código físico

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

### Funcionalidades Avançadas
- **Sistema de equipes** com permissões granulares
- **Domínios personalizados** (whitelabel)
- **URLs curtas** personalizadas
- **Compartilhamento social** integrado
- **Suporte WhatsApp** Business API
- **Compliance LGPD** completo

## 🛠️ Tecnologias

### Backend
- **Laravel 11** - Framework PHP
- **PostgreSQL 15+** - Banco de dados
- **Redis** - Cache e filas (VPS)
- **Laravel Sanctum** - Autenticação API
- **Spatie Laravel Permission** - ACL e permissões

### Frontend
- **Alpine.js** - JavaScript reativo
- **Tailwind CSS** - Framework CSS
- **Laravel Mix** - Build tool
- **Chart.js** - Gráficos
- **Sortable.js** - Drag and drop

### Integrações
- **Mercado Pago** - Pagamentos
- **WhatsApp Business API** - Suporte
- **Google reCAPTCHA v3** - Segurança
- **endroid/qr-code** - Geração de QR Codes

## 📋 Requisitos

### Servidor Compartilhado
- PHP 8.2+
- PostgreSQL 12+
- Apache com mod_rewrite
- Composer
- SSL/HTTPS

### VPS/Dedicado
- PHP 8.2+ com PHP-FPM
- PostgreSQL 15+
- Nginx
- Redis
- Supervisor
- SSL/HTTPS

## 🚀 Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/t-vieira/qrcode.git
cd qrcode
```

### 2. Instalar dependências
```bash
composer install
npm install
```

### 3. Configurar ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar banco de dados
```bash
# Criar banco PostgreSQL
createdb qrcode_saas

# Executar migrations
php artisan migrate

# Executar seeders
php artisan db:seed
```

### 5. Compilar assets
```bash
npm run dev
# ou para produção
npm run production
```

### 6. Criar link de storage
```bash
php artisan storage:link
```

## 🔧 Configuração

### Variáveis de Ambiente Obrigatórias

```env
# Aplicação
APP_NAME="QR Code SaaS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Banco de dados
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=qrcode_saas
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Mercado Pago
MERCADOPAGO_ACCESS_TOKEN=seu_access_token
MERCADOPAGO_PUBLIC_KEY=sua_public_key
MERCADOPAGO_PLAN_ID=seu_plan_id

# WhatsApp Business API
WHATSAPP_ACCESS_TOKEN=seu_token
WHATSAPP_PHONE_NUMBER_ID=seu_phone_id

# reCAPTCHA
RECAPTCHA_SITE_KEY=sua_site_key
RECAPTCHA_SECRET_KEY=sua_secret_key
```

## 📚 Documentação

### Documentação Completa
- [Documentação de Produção](PRODUCTION-DOCUMENTATION.md)
- [Guia de Comandos Artisan](ARTISAN-COMMANDS-GUIDE.md)
- [Guia de API Endpoints](API-ENDPOINTS-GUIDE.md)
- [Guia de Configuração](PRODUCTION-SETUP-GUIDE.md)
- [Índice de Documentação](DOCUMENTATION-INDEX.md)

### Deploy
- [Deploy para Servidor Compartilhado](deploy/SHARED-HOSTING-README.md)
- [Modificações para Servidor Compartilhado](deploy/SHARED-HOSTING-MODIFICATIONS.md)

## 🔧 Comandos Úteis

### Configuração
```bash
# Configurar para servidor compartilhado
php artisan shared-hosting:configure

# Verificar configuração
php artisan shared-hosting:configure --check
```

### Manutenção
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

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=QrCodeControllerTest

# Executar com cobertura
php artisan test --coverage
```

## 📊 API

O sistema inclui uma API REST completa:

### Autenticação
- `POST /api/auth/register` - Registro
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout

### QR Codes
- `GET /api/qrcodes` - Listar QR Codes
- `POST /api/qrcodes` - Criar QR Code
- `GET /api/qrcodes/{id}` - Visualizar QR Code
- `PUT /api/qrcodes/{id}` - Atualizar QR Code
- `DELETE /api/qrcodes/{id}` - Excluir QR Code

### Estatísticas
- `GET /api/stats/dashboard` - Dashboard
- `GET /api/stats/qrcode/{id}` - Estatísticas do QR Code
- `GET /api/stats/export/{id}` - Exportar dados

Veja a [documentação completa da API](API-ENDPOINTS-GUIDE.md) para mais detalhes.

## 🔒 Segurança

### Recursos de Segurança
- **Headers de segurança** completos
- **Rate limiting** configurado
- **Validação de uploads** rigorosa
- **Detecção de ameaças** automática
- **Bloqueio de IPs** suspeitos
- **Auditoria de segurança** completa

### Compliance LGPD
- **Exportação de dados** (JSON/CSV)
- **Exclusão de dados** permanente
- **Anonimização** de dados de scans
- **Política de privacidade** detalhada

## 🌐 Internacionalização

- **Português (Brasil)** 100% implementado
- **Estrutura preparada** para outros idiomas
- **Formatação** de moeda, data e números

## 📱 Suporte

### Canais de Suporte
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

### Horário de Atendimento
- **Segunda a Sexta**: 09:00 - 18:00 (Brasília)
- **Respostas automáticas** fora do horário
- **Suporte prioritário** para usuários premium

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📞 Contato

**Thiago Vieira**
- Email: thiago@fluxti.com.br
- GitHub: [@t-vieira](https://github.com/t-vieira)
- LinkedIn: [Thiago Vieira](https://linkedin.com/in/thiago-vieira)

---

**Desenvolvido com ❤️ por Thiago Vieira**