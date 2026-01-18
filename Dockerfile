FROM php:8.2-cli

# Install required dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy all application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Expose port
EXPOSE 8080

# Start server
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
