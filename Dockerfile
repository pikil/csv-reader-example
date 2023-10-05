# Using the node's image for building the app, repos 
FROM node:latest

# Setting the working directory in the container to /app
WORKDIR /app

# Copying package.json (no need in package-lock.json, as the build is simple, otherwise replace package.json with package*.json)
COPY package.json ./

# Copying files for building the Vue app
COPY src /app/src
COPY index.html ./
COPY public /app/public

COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./

# Installing project dependencies and building the working directory
RUN npm install && npm run build

# Just for the sake of the sample, we are copying api to outside of html folder, which with the proper nginx config should be stored in a separate place relating to the domain folder
COPY api /var/www/api

# APT releases repository for node image contains adequate PHP versions, so we can use the default one which is already high enough for our purposes
# For the simplicity of the example, we will be using the default Apache http server which is pre-installed on node image
RUN apt-get update && apt-get install -y php php-mysql

# Installing composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer update -d /var/www/api

# Enabling Apache domain and modules
COPY httpd.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite && \
    a2enmod headers && \
    a2ensite 000-default.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

ADD startup.sh /
RUN chmod +x /startup.sh

CMD ["/startup.sh"]
