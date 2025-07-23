FROM php:8.1-cli

WORKDIR /app

COPY . .

CMD ["php", "-S", "0.0.0.0:80", "-t", "."]
