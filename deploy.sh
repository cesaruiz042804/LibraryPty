#!/bin/bash

# Script de Deploy para Laravel Library en Hostinger
# Ejecutar: chmod +x deploy.sh && ./deploy.sh

echo "🚀 Iniciando deploy de Laravel Library..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
print_message() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Verificar si estamos en modo producción
if [ "$APP_ENV" != "production" ]; then
    print_warning "No estás en modo producción. Configurando para desarrollo..."
fi

# 1. Instalar dependencias
print_message "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

if [ $? -ne 0 ]; then
    print_error "Error al instalar dependencias de Composer"
    exit 1
fi

# 2. Limpiar cache
print_message "Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 3. Crear directorios necesarios
print_message "Creando directorios necesarios..."
mkdir -p storage/app/public/libros_covers
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# 4. Configurar permisos
print_message "Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 public/storage

# 5. Crear symlink de storage si no existe
if [ ! -L "public/storage" ]; then
    print_message "Creando symlink de storage..."
    php artisan storage:link
fi

# 6. Ejecutar migraciones
print_message "Ejecutando migraciones..."
php artisan migrate --force

if [ $? -ne 0 ]; then
    print_error "Error al ejecutar migraciones"
    exit 1
fi

# 7. Optimizar para producción
print_message "Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Verificar configuración
print_message "Verificando configuración..."
php artisan about

# 9. Verificar rutas
print_message "Verificando rutas..."
php artisan route:list --compact

print_message "🎉 Deploy completado exitosamente!"
print_message "📝 Recuerda verificar:"
echo "   - Variables de entorno (.env)"
echo "   - Base de datos configurada"
echo "   - SSL configurado"
echo "   - Permisos de archivos"
echo "   - Logs en storage/logs/laravel.log"

# 10. Mostrar información del sistema
echo ""
print_message "Información del sistema:"
echo "   - PHP Version: $(php -v | head -n 1)"
echo "   - Laravel Version: $(php artisan --version)"
echo "   - App Environment: $(php artisan env)"
echo "   - App URL: $(php artisan tinker --execute='echo config("app.url");')" 