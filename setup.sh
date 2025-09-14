#!/bin/bash

echo "�� Setting up Task Management System..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker and try again."
    exit 1
fi

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

# Build and start containers
echo "🐳 Building and starting Docker containers..."
docker-compose up -d --build

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Install dependencies
echo "📦 Installing PHP dependencies..."
docker-compose exec app composer install --no-interaction

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose exec app php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

echo "✅ Setup completed successfully!"
echo ""
echo "🌐 Application: http://localhost:8000"
echo "🗄️ MySQL: localhost:3306"
echo "🍃 MongoDB: localhost:27017"
echo "📊 Mongo Express: http://localhost:8081 (admin/admin)"
echo ""
echo "📚 Check README.md for API documentation and usage instructions."
