# Utilise Nginx officiel
FROM nginx:1.29.7-alpine

# Copier la config Nginx personnalisée
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copier le code public
WORKDIR /var/www/public
COPY public /var/www/public

# HTTP + HTTPS
EXPOSE 80
EXPOSE 443

# Lancer Nginx
CMD ["nginx", "-g", "daemon off;"]