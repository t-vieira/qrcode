<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $site_name }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">
    
    <!-- Additional Meta Tags -->
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }
        
        h1 {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 16px 0;
            line-height: 1.2;
        }
        
        .description {
            color: #6b7280;
            font-size: 18px;
            line-height: 1.5;
            margin: 0 0 30px 0;
        }
        
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto 30px;
            border: 3px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
        }
        
        .qr-code img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 8px;
        }
        
        .cta-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            transition: transform 0.2s;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 14px;
        }
        
        @media (max-width: 640px) {
            .container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .description {
                font-size: 16px;
            }
            
            .qr-code {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">QR</div>
        
        <h1>{{ $title }}</h1>
        
        <p class="description">{{ $description }}</p>
        
        <div class="qr-code">
            <img src="{{ $image }}" alt="QR Code" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div style="display: none; flex-direction: column; align-items: center; justify-content: center; color: #9ca3af;">
                <svg width="48" height="48" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zM13 3v8h8V3h-8zm6 6h-4V5h4v4zM3 21h8v-8H3v8zm2-6h4v4H5v-4zM16 13h2v2h-2v-2zM16 17h2v2h-2v-2zM20 13h2v2h-2v-2zM20 17h2v2h-2v-2z"/>
                </svg>
                <span style="margin-top: 8px; font-size: 12px;">QR Code</span>
            </div>
        </div>
        
        <a href="{{ $url }}" class="cta-button">
            Escanear QR Code
        </a>
        
        <div class="footer">
            <p>Criado com {{ $site_name }}</p>
        </div>
    </div>
    
    <script>
        // Redirecionar automaticamente após 3 segundos
        setTimeout(function() {
            window.location.href = '{{ $url }}';
        }, 3000);
    </script>
</body>
</html>
