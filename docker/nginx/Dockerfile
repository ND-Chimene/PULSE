# Stage 1 : compile JS/CSS assets
FROM node:22-alpine AS assets

WORKDIR /app

RUN npm install -g pnpm

COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile --ignore-scripts

COPY webpack.config.js postcss.config.js tailwind.config.js ./
COPY assets ./assets

RUN pnpm run build

# Stage 2 : Nginx serving
FROM nginx:1.29.7-alpine

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/public

COPY public /var/www/public
COPY --from=assets /app/public/build /var/www/public/build

EXPOSE 80
EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]
