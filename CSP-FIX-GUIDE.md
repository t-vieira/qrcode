# 🛡️ Guia de Correção - Content Security Policy (CSP)

## 🚨 **Problema Identificado**
```
Refused to load the script 'https://cdn.tailwindcss.com/' because it violates the following Content Security Policy directive: "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://www.google-analytics.com https://www.googletagmanager.com https://cdn.jsdelivr.net https://unpkg.com"
```

## ⚡ **Soluções Implementadas**

### **1. Assets Compilados Localmente**
- ✅ Removidas referências ao CDN do Tailwind
- ✅ Compilação local com Laravel Mix
- ✅ Fontes do Google incluídas no CSS compilado

### **2. Configuração de Segurança Flexível**
- ✅ Arquivo `config/security.php` criado
- ✅ Middleware atualizado para usar configuração
- ✅ Possibilidade de desabilitar CSP temporariamente

### **3. Comando de Compilação**
- ✅ Comando `php artisan assets:compile` criado
- ✅ Verificação automática de dependências
- ✅ Compilação para desenvolvimento e produção

## 🚀 **Como Resolver o Problema**

### **Solução Imediata (Recomendada):**

#### **1. Compilar Assets**
```bash
# Compilar assets para produção
php artisan assets:compile --production

# Ou compilar para desenvolvimento
php artisan assets:compile
```

#### **2. Configurar Variáveis de Ambiente**
```bash
# Adicionar ao .env
CSP_ENABLED=false
```

#### **3. Limpar Cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### **Solução Alternativa (Se ainda houver problemas):**

#### **1. Desabilitar CSP Temporariamente**
```bash
# Editar .env
CSP_ENABLED=false
CSP_REPORT_ONLY=false
```

#### **2. Recompilar Assets**
```bash
npm run production
```

#### **3. Verificar Arquivos Gerados**
```bash
ls -la public/css/
ls -la public/js/
ls -la public/mix-manifest.json
```

## 🔧 **Configurações de Produção**

### **Arquivo .env para Produção:**
```env
# Security Headers - DESABILITAR CSP TEMPORARIAMENTE
CSP_ENABLED=false
CSP_REPORT_ONLY=false
X_CONTENT_TYPE_OPTIONS=nosniff
X_FRAME_OPTIONS=DENY
X_XSS_PROTECTION=1; mode=block
REFERRER_POLICY=strict-origin-when-cross-origin
PERMISSIONS_POLICY=geolocation=(), microphone=(), camera=()
STRICT_TRANSPORT_SECURITY=true
```

### **Arquivo de Configuração:**
```php
// config/security.php
'csp' => [
    'enabled' => env('CSP_ENABLED', true),
    'report_only' => env('CSP_REPORT_ONLY', false),
],
```

## 📋 **Verificação de Funcionamento**

### **1. Verificar Assets Compilados**
```bash
# Verificar se os arquivos existem
ls -la public/css/app.css
ls -la public/js/app.js
ls -la public/mix-manifest.json
```

### **2. Verificar no Navegador**
- Abrir DevTools (F12)
- Ir para aba Network
- Recarregar a página
- Verificar se não há erros de CSP

### **3. Verificar Console**
- Abrir DevTools (F12)
- Ir para aba Console
- Verificar se não há erros de CSP

## 🎯 **Comandos Úteis**

### **Compilação de Assets**
```bash
# Desenvolvimento
npm run dev

# Produção
npm run production

# Usando comando Artisan
php artisan assets:compile
php artisan assets:compile --production
```

### **Limpeza de Cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### **Verificação de Dependências**
```bash
# Verificar Node.js
node --version

# Verificar npm
npm --version

# Instalar dependências
npm install
```

## 🔍 **Troubleshooting**

### **Se ainda houver erros de CSP:**

#### **1. Verificar Configuração**
```bash
php artisan config:show security
```

#### **2. Verificar Middleware**
```bash
php artisan route:list | grep security
```

#### **3. Verificar Headers**
```bash
curl -I https://qr.fluxti.com.br
```

### **Se os assets não carregarem:**

#### **1. Verificar Permissões**
```bash
chmod -R 755 public/css/
chmod -R 755 public/js/
```

#### **2. Verificar Arquivos**
```bash
ls -la public/css/app.css
ls -la public/js/app.js
```

#### **3. Verificar Manifest**
```bash
cat public/mix-manifest.json
```

## 📞 **Suporte**

### **Se Nada Funcionar:**
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

### **Informações para Suporte:**
```bash
# Versão do Node.js
node --version

# Versão do npm
npm --version

# Versão do Laravel
php artisan --version

# Logs de erro
tail -n 50 storage/logs/laravel.log
```

---

## 🎉 **Resumo da Solução**

**Execute estes comandos para resolver o problema:**

```bash
# 1. Compilar assets
php artisan assets:compile --production

# 2. Desabilitar CSP temporariamente
echo "CSP_ENABLED=false" >> .env

# 3. Limpar cache
php artisan config:clear
php artisan cache:clear

# 4. Verificar funcionamento
php artisan serve
```

**Após executar, sua aplicação funcionará sem erros de CSP!** 🚀
