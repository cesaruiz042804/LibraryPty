# Deploy Laravel Library en Hostinger

## Configuración para Deploy Automático

### 1. Configuración del Repositorio

Asegúrate de que tu repositorio tenga la siguiente estructura:
```
Library/
├── app/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── .env.example
├── composer.json
└── artisan
```

### 2. Configuración en Hostinger

#### Variables de Entorno (.env)
```env
APP_NAME="Laravel Library"
APP_ENV=production
APP_KEY=base64:tu_app_key_aqui
APP_DEBUG=false
APP_URL=https://tudominio.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_database_name
DB_USERNAME=tu_database_user
DB_PASSWORD=tu_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mail.tudominio.com
MAIL_PORT=587
MAIL_USERNAME=tu_email
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Comandos de Deploy

#### Comandos Pre-Deploy:
```bash
# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Generar APP_KEY
php artisan key:generate

# Crear symlink de storage
php artisan storage:link

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Comandos Post-Deploy:
```bash
# Ejecutar migraciones
php artisan migrate --force

# Crear directorios necesarios
mkdir -p storage/app/public/libros_covers
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 4. Configuración de Web Server

#### Apache (.htaccess en public/)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 5. Configuración de GitHub Actions (Opcional)

Crear `.github/workflows/deploy.yml`:
```yaml
name: Deploy to Hostinger

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Deploy to Hostinger
      uses: easingthemes/ssh-deploy@main
      env:
        SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
        REMOTE_HOST: ${{ secrets.REMOTE_HOST }}
        REMOTE_USER: ${{ secrets.REMOTE_USER }}
        SOURCE: "/"
        TARGET: "/public_html/"
```

### 6. Checklist de Deploy

- [ ] Configurar variables de entorno en Hostinger
- [ ] Crear base de datos MySQL
- [ ] Configurar dominio y SSL
- [ ] Ejecutar migraciones
- [ ] Crear symlink de storage
- [ ] Configurar permisos de archivos
- [ ] Probar funcionalidades principales
- [ ] Configurar backup automático

### 7. Troubleshooting

#### Problemas Comunes:
1. **Error 500**: Verificar permisos y APP_KEY
2. **Imágenes no cargan**: Verificar symlink de storage
3. **Base de datos**: Verificar credenciales y conexión
4. **Cache**: Limpiar cache después de cambios

#### Logs de Error:
- Laravel: `storage/logs/laravel.log`
- Apache: Panel de Hostinger > Logs
- PHP: Panel de Hostinger > Logs de Error 