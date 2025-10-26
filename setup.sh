#!/bin/bash

echo "🚀 Iniciando entorno Laravel + Reverb..."

# Verificar dependencias
command -v docker >/dev/null 2>&1 || { echo >&2 "❌ Docker no está instalado. Aborta."; exit 1; }
command -v composer >/dev/null 2>&1 || { echo >&2 "❌ Composer no está instalado. Aborta."; exit 1; }

# Crear .env si no existe
if [ ! -f MyApp/.env ]; then
  cp MyChat/.env.example MyChat/.env
  echo "✅ Archivo .env creado en MyChat"
fi

# Generar clave de Laravel
cd MyChat
php artisan key:generate
cd ..

# Instalar dependencias
echo "📦 Instalando dependencias PHP y JS..."
docker run --rm -v $(pwd)/MyApp:/var/www -w /var/www php:8.2-cli composer install
docker run --rm -v $(pwd)/MyApp:/var/www -w /var/www node:20 npm install
docker run --rm -v $(pwd)/MyApp:/var/www -w /var/www node:20 npm run build

# Levantar contenedores
echo "🐳 Levantando contenedores con Docker Compose..."
docker-compose up -d --build

# Migraciones (opcional)
echo "🧬 Ejecutando migraciones..."
docker-compose exec mychat php artisan migrate

echo "🎉 Entorno listo. Accede a http://localhost:8020"
