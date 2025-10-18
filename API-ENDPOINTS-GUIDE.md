# Guia de API Endpoints - QR Code SaaS Platform

## 📋 Visão Geral

Esta documentação contém todos os endpoints da API REST disponíveis no sistema QR Code SaaS, incluindo autenticação, parâmetros, respostas e exemplos de uso.

## 🔐 Autenticação

### Base URL
```
https://qr.fluxti.com.br/api
```

### Headers Obrigatórios
```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## 🔑 Endpoints de Autenticação

### 1. Registro de Usuário
```http
POST /api/auth/register
```

**Parâmetros:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "recaptcha_token": "03AGdBq25..."
}
```

**Resposta de Sucesso (201):**
```json
{
  "message": "Usuário registrado com sucesso",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "trial_ends_at": "2024-01-22T10:30:00Z",
    "subscription_status": "trialing"
  },
  "token": "1|abc123def456..."
}
```

### 2. Login
```http
POST /api/auth/login
```

**Parâmetros:**
```json
{
  "email": "joao@example.com",
  "password": "senha123",
  "recaptcha_token": "03AGdBq25..."
}
```

**Resposta de Sucesso (200):**
```json
{
  "message": "Login realizado com sucesso",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "trial_ends_at": "2024-01-22T10:30:00Z",
    "subscription_status": "trialing"
  },
  "token": "1|abc123def456..."
}
```

### 3. Logout
```http
POST /api/auth/logout
```

**Resposta de Sucesso (200):**
```json
{
  "message": "Logout realizado com sucesso"
}
```

### 4. Refresh Token
```http
POST /api/auth/refresh
```

**Resposta de Sucesso (200):**
```json
{
  "token": "1|new123token456..."
}
```

## 📱 Endpoints de QR Codes

### 1. Listar QR Codes
```http
GET /api/qrcodes
```

**Parâmetros de Query:**
- `page` (opcional): Número da página
- `per_page` (opcional): Itens por página (máximo 50)
- `folder_id` (opcional): Filtrar por pasta
- `type` (opcional): Filtrar por tipo
- `status` (opcional): Filtrar por status
- `search` (opcional): Buscar por nome

**Exemplo:**
```http
GET /api/qrcodes?page=1&per_page=20&type=url&status=active
```

**Resposta de Sucesso (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Meu QR Code",
      "type": "url",
      "short_code": "abc123",
      "is_dynamic": true,
      "status": "active",
      "scans_count": 45,
      "created_at": "2024-01-15T10:30:00Z",
      "updated_at": "2024-01-15T10:30:00Z",
      "folder": {
        "id": 1,
        "name": "Marketing"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45
  }
}
```

### 2. Criar QR Code
```http
POST /api/qrcodes
```

**Parâmetros:**
```json
{
  "name": "QR Code do Site",
  "type": "url",
  "folder_id": 1,
  "short_code": "meusite",
  "is_dynamic": true,
  "content": {
    "url": "https://meusite.com.br"
  },
  "design": {
    "foregroundColor": "#000000",
    "backgroundColor": "#ffffff",
    "resolution": 300,
    "logo": "logos/logo.png"
  },
  "format": "png"
}
```

**Resposta de Sucesso (201):**
```json
{
  "message": "QR Code criado com sucesso",
  "data": {
    "id": 1,
    "name": "QR Code do Site",
    "type": "url",
    "short_code": "meusite",
    "is_dynamic": true,
    "status": "active",
    "file_path": "qrcodes/1/meu_qr_code_1.png",
    "download_url": "https://qr.fluxti.com.br/storage/qrcodes/1/meu_qr_code_1.png",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

### 3. Visualizar QR Code
```http
GET /api/qrcodes/{id}
```

**Resposta de Sucesso (200):**
```json
{
  "data": {
    "id": 1,
    "name": "QR Code do Site",
    "type": "url",
    "short_code": "meusite",
    "is_dynamic": true,
    "content": {
      "url": "https://meusite.com.br"
    },
    "design": {
      "foregroundColor": "#000000",
      "backgroundColor": "#ffffff",
      "resolution": 300
    },
    "status": "active",
    "scans_count": 45,
    "file_path": "qrcodes/1/meu_qr_code_1.png",
    "download_url": "https://qr.fluxti.com.br/storage/qrcodes/1/meu_qr_code_1.png",
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z",
    "folder": {
      "id": 1,
      "name": "Marketing"
    }
  }
}
```

### 4. Atualizar QR Code
```http
PUT /api/qrcodes/{id}
```

**Parâmetros:**
```json
{
  "name": "QR Code Atualizado",
  "content": {
    "url": "https://novosite.com.br"
  },
  "design": {
    "foregroundColor": "#FF0000",
    "backgroundColor": "#FFFFFF"
  }
}
```

**Resposta de Sucesso (200):**
```json
{
  "message": "QR Code atualizado com sucesso",
  "data": {
    "id": 1,
    "name": "QR Code Atualizado",
    "content": {
      "url": "https://novosite.com.br"
    },
    "design": {
      "foregroundColor": "#FF0000",
      "backgroundColor": "#FFFFFF"
    },
    "updated_at": "2024-01-15T11:00:00Z"
  }
}
```

### 5. Excluir QR Code
```http
DELETE /api/qrcodes/{id}
```

**Resposta de Sucesso (200):**
```json
{
  "message": "QR Code excluído com sucesso"
}
```

### 6. Download QR Code
```http
GET /api/qrcodes/{id}/download
```

**Parâmetros de Query:**
- `format` (opcional): Formato do arquivo (png, jpg, svg, eps)
- `resolution` (opcional): Resolução (100-2000)

**Exemplo:**
```http
GET /api/qrcodes/1/download?format=svg&resolution=500
```

**Resposta:** Arquivo binário do QR Code

### 7. Preview QR Code
```http
POST /api/qrcodes/preview
```

**Parâmetros:**
```json
{
  "type": "url",
  "content": {
    "url": "https://example.com"
  },
  "design": {
    "foregroundColor": "#000000",
    "backgroundColor": "#ffffff",
    "resolution": 300
  }
}
```

**Resposta de Sucesso (200):**
```json
{
  "preview": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
}
```

## 📊 Endpoints de Estatísticas

### 1. Dashboard
```http
GET /api/stats/dashboard
```

**Resposta de Sucesso (200):**
```json
{
  "data": {
    "total_qr_codes": 25,
    "total_scans": 1250,
    "unique_scans": 890,
    "today_scans": 45,
    "recent_qr_codes": [
      {
        "id": 1,
        "name": "QR Code Recente",
        "scans_count": 12,
        "created_at": "2024-01-15T10:30:00Z"
      }
    ],
    "top_qr_codes": [
      {
        "id": 2,
        "name": "QR Code Popular",
        "scans_count": 156,
        "created_at": "2024-01-10T10:30:00Z"
      }
    ],
    "scans_chart": {
      "labels": ["01/01", "02/01", "03/01", "04/01", "05/01"],
      "data": [10, 15, 8, 22, 18]
    }
  }
}
```

### 2. Estatísticas de QR Code
```http
GET /api/stats/qrcode/{id}
```

**Parâmetros de Query:**
- `period` (opcional): Período (7d, 30d, 90d, 1y)
- `start_date` (opcional): Data inicial (YYYY-MM-DD)
- `end_date` (opcional): Data final (YYYY-MM-DD)

**Exemplo:**
```http
GET /api/stats/qrcode/1?period=30d
```

**Resposta de Sucesso (200):**
```json
{
  "data": {
    "qr_code": {
      "id": 1,
      "name": "QR Code do Site",
      "type": "url"
    },
    "stats": {
      "total_scans": 156,
      "unique_scans": 98,
      "today_scans": 5,
      "avg_daily_scans": 5.2
    },
    "scans_by_date": [
      {
        "date": "2024-01-15",
        "total_scans": 8,
        "unique_scans": 6
      }
    ],
    "scans_by_device": [
      {
        "device_type": "mobile",
        "count": 89,
        "percentage": 57.1
      },
      {
        "device_type": "desktop",
        "count": 45,
        "percentage": 28.8
      },
      {
        "device_type": "tablet",
        "count": 22,
        "percentage": 14.1
      }
    ],
    "scans_by_country": [
      {
        "country": "Brasil",
        "count": 120,
        "percentage": 76.9
      },
      {
        "country": "Estados Unidos",
        "count": 25,
        "percentage": 16.0
      }
    ],
    "recent_scans": [
      {
        "id": 1,
        "ip_address": "192.168.1.1",
        "device_type": "mobile",
        "country": "Brasil",
        "city": "São Paulo",
        "scanned_at": "2024-01-15T10:30:00Z"
      }
    ]
  }
}
```

### 3. Exportar Estatísticas
```http
GET /api/stats/export/{id}
```

**Parâmetros de Query:**
- `format` (opcional): Formato (csv, json)
- `period` (opcional): Período (7d, 30d, 90d, 1y)
- `start_date` (opcional): Data inicial
- `end_date` (opcional): Data final

**Exemplo:**
```http
GET /api/stats/export/1?format=csv&period=30d
```

**Resposta:** Arquivo CSV com dados de scans

## 💳 Endpoints de Assinatura

### 1. Status da Assinatura
```http
GET /api/subscription/status
```

**Resposta de Sucesso (200):**
```json
{
  "data": {
    "status": "trialing",
    "trial_ends_at": "2024-01-22T10:30:00Z",
    "plan_name": "Premium",
    "amount": 29.90,
    "currency": "BRL",
    "current_period_start": "2024-01-15T10:30:00Z",
    "current_period_end": "2024-02-15T10:30:00Z",
    "features": {
      "dynamic_qr_codes": true,
      "advanced_analytics": true,
      "custom_domains": true,
      "team_collaboration": true,
      "api_access": false
    }
  }
}
```

### 2. Upgrade de Assinatura
```http
POST /api/subscription/upgrade
```

**Parâmetros:**
```json
{
  "payment_method": "credit_card",
  "card_token": "card_token_123"
}
```

**Resposta de Sucesso (200):**
```json
{
  "message": "Assinatura ativada com sucesso",
  "data": {
    "status": "active",
    "plan_name": "Premium",
    "amount": 29.90,
    "current_period_end": "2024-02-15T10:30:00Z"
  }
}
```

### 3. Cancelar Assinatura
```http
POST /api/subscription/cancel
```

**Resposta de Sucesso (200):**
```json
{
  "message": "Assinatura cancelada com sucesso",
  "data": {
    "status": "canceled",
    "canceled_at": "2024-01-15T10:30:00Z",
    "current_period_end": "2024-02-15T10:30:00Z"
  }
}
```

## 📁 Endpoints de Pastas

### 1. Listar Pastas
```http
GET /api/folders
```

**Resposta de Sucesso (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Marketing",
      "slug": "marketing",
      "qr_codes_count": 5,
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### 2. Criar Pasta
```http
POST /api/folders
```

**Parâmetros:**
```json
{
  "name": "Nova Pasta",
  "parent_id": null
}
```

**Resposta de Sucesso (201):**
```json
{
  "message": "Pasta criada com sucesso",
  "data": {
    "id": 2,
    "name": "Nova Pasta",
    "slug": "nova-pasta",
    "qr_codes_count": 0,
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

### 3. Atualizar Pasta
```http
PUT /api/folders/{id}
```

**Parâmetros:**
```json
{
  "name": "Pasta Atualizada"
}
```

### 4. Excluir Pasta
```http
DELETE /api/folders/{id}
```

## 👥 Endpoints de Equipes

### 1. Listar Equipes
```http
GET /api/teams
```

**Resposta de Sucesso (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Equipe Marketing",
      "slug": "equipe-marketing",
      "members_count": 3,
      "role": "owner",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### 2. Criar Equipe
```http
POST /api/teams
```

**Parâmetros:**
```json
{
  "name": "Nova Equipe",
  "description": "Descrição da equipe"
}
```

### 3. Adicionar Membro
```http
POST /api/teams/{id}/members
```

**Parâmetros:**
```json
{
  "email": "membro@example.com",
  "role": "editor",
  "permissions": ["view", "create", "edit"]
}
```

### 4. Remover Membro
```http
DELETE /api/teams/{id}/members/{user_id}
```

## 🌐 Endpoints de Domínios Personalizados

### 1. Listar Domínios
```http
GET /api/domains
```

**Resposta de Sucesso (200):**
```json
{
  "data": [
    {
      "id": 1,
      "domain": "meusite.com.br",
      "status": "verified",
      "is_primary": true,
      "verified_at": "2024-01-15T10:30:00Z",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### 2. Adicionar Domínio
```http
POST /api/domains
```

**Parâmetros:**
```json
{
  "domain": "meusite.com.br"
}
```

**Resposta de Sucesso (201):**
```json
{
  "message": "Domínio adicionado com sucesso",
  "data": {
    "id": 1,
    "domain": "meusite.com.br",
    "status": "pending",
    "dns_record": "TXT meusite.com.br qr-verification=abc123",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

### 3. Verificar Domínio
```http
POST /api/domains/{id}/verify
```

**Resposta de Sucesso (200):**
```json
{
  "message": "Domínio verificado com sucesso",
  "data": {
    "id": 1,
    "domain": "meusite.com.br",
    "status": "verified",
    "verified_at": "2024-01-15T10:30:00Z"
  }
}
```

## 🎫 Endpoints de Suporte

### 1. Criar Ticket
```http
POST /api/support/tickets
```

**Parâmetros:**
```json
{
  "subject": "Problema com QR Code",
  "message": "Meu QR Code não está funcionando",
  "priority": "medium"
}
```

**Resposta de Sucesso (201):**
```json
{
  "message": "Ticket criado com sucesso",
  "data": {
    "id": 1,
    "subject": "Problema com QR Code",
    "status": "open",
    "priority": "medium",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

### 2. Listar Tickets
```http
GET /api/support/tickets
```

### 3. Responder Ticket
```http
POST /api/support/tickets/{id}/reply
```

**Parâmetros:**
```json
{
  "message": "Obrigado pelo contato. Vamos resolver isso."
}
```

## 📱 Endpoints de Redirecionamento

### 1. Redirecionar QR Code
```http
GET /{short_code}
```

**Exemplo:**
```http
GET /abc123
```

**Resposta:** Redirecionamento HTTP 302 para o conteúdo do QR Code

### 2. Exibir Conteúdo de Texto
```http
GET /qr/text/{encoded_content}
```

**Resposta:** Página HTML com o texto decodificado

## 🚨 Códigos de Erro

### Códigos HTTP Comuns
- `200` - Sucesso
- `201` - Criado com sucesso
- `400` - Dados inválidos
- `401` - Não autenticado
- `403` - Não autorizado
- `404` - Não encontrado
- `422` - Erro de validação
- `429` - Rate limit excedido
- `500` - Erro interno do servidor

### Formato de Erro
```json
{
  "message": "Dados inválidos",
  "errors": {
    "email": ["O campo email é obrigatório"],
    "password": ["A senha deve ter pelo menos 8 caracteres"]
  }
}
```

## 🔒 Rate Limiting

### Limites por Endpoint
- **Autenticação**: 5 requisições/5 minutos
- **API Geral**: 100 requisições/minuto
- **Upload**: 10 requisições/minuto
- **QR Code**: 20 gerações/minuto

### Headers de Rate Limit
```http
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1642248000
```

## 📝 Exemplos de Uso

### JavaScript (Fetch)
```javascript
// Login
const response = await fetch('/api/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password123',
    recaptcha_token: 'token'
  })
});

const data = await response.json();
const token = data.token;

// Criar QR Code
const qrResponse = await fetch('/api/qrcodes', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    name: 'Meu QR Code',
    type: 'url',
    content: {
      url: 'https://example.com'
    }
  })
});
```

### PHP (cURL)
```php
// Login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://qr.fluxti.com.br/api/auth/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'user@example.com',
    'password' => 'password123',
    'recaptcha_token' => 'token'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
$token = $data['token'];
curl_close($ch);
```

### Python (Requests)
```python
import requests

# Login
response = requests.post('https://qr.fluxti.com.br/api/auth/login', json={
    'email': 'user@example.com',
    'password': 'password123',
    'recaptcha_token': 'token'
})

data = response.json()
token = data['token']

# Criar QR Code
headers = {'Authorization': f'Bearer {token}'}
qr_response = requests.post('https://qr.fluxti.com.br/api/qrcodes', 
    json={
        'name': 'Meu QR Code',
        'type': 'url',
        'content': {'url': 'https://example.com'}
    },
    headers=headers
)
```

## 📞 Suporte

Para dúvidas sobre a API:
- **Email**: api@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br/api

---

**Esta documentação contém todos os endpoints da API REST disponíveis no sistema QR Code SaaS.**
