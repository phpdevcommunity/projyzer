FROM debian:bookworm-slim

RUN apt-get update -y && apt-get upgrade -y

RUN apt-get install ca-certificates apt-transport-https software-properties-common wget curl nano lsb-release gnupg2 -y
RUN apt-get update -y && apt-get install -y zip unzip git curl
RUN apt-get install -y php8.2-fpm php8.2-apcu php8.2-curl php8.2-intl php8.2-xml php8.2-zip php8.2-mbstring php8.2-mysql php8.2-gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
RUN apt-get update && apt-get install nodejs -y

RUN apt-get install -y build-essential
RUN apt-get install -y nginx
RUN apt-get install -y nano
RUN apt-get install -y mariadb-client
RUN npm install -g yarn -y
ENV TZ Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN rm -rf /etc/nginx/sites-enabled/default
COPY default.conf /etc/nginx/conf.d/default.conf
COPY php.ini /etc/php/8.2/fpm/php.ini
EXPOSE 80
CMD service php8.2-fpm start && nginx -g "daemon off;"