## BASE
FROM debian:bullseye

# basic packages
RUN \
  apt-get update && \
  apt-get upgrade -y && \
  apt-get install -y software-properties-common apt-utils tzdata locales cron vim htop zip bzip2 wget curl git mercurial supervisor build-essential make dnsutils subversion poppler-utils fontconfig xfonts-base xfonts-75dpi sudo locales && \
  cp -f /root/.bashrc /.bashrc && \
  mkdir -p /.config/htop/ && \
  echo "fields=0 48 17 18 38 39 40 2 46 47 49 1\nsort_key=46\nsort_direction=1\nhide_threads=0\nhide_kernel_threads=1\nhide_userland_threads=1\nshadow_other_users=0\nshow_thread_names=0\nhighlight_base_name=1\nhighlight_megabytes=1\nhighlight_threads=1\ntree_view=1\nheader_margin=1\ndetailed_cpu_time=0\ncpu_count_from_zero=0\ncolor_scheme=0\ndelay=15\nleft_meters=Hostname Clock Memory CPU Swap\nleft_meter_modes=2 2 2 1 1\nright_meters=Uptime Tasks LoadAverage AllCPUs\nright_meter_modes=2 2 2 1\n" > /.config/htop/htoprc && \
  mkdir -p /root/.config/htop/ && \
  cp -f /.config/htop/htoprc /root/.config/htop/htoprc && \
  apt-get autoremove -y && \
  apt-get clean all

# Timezone change
RUN \
    echo "Europe/Vilnius" > /etc/timezone         && \
    ln -sf /usr/share/zoneinfo/EET /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

# sudo password change
RUN echo 'root:pass' | chpasswd

# Set LOCALE to UTF8
ENV DEBIAN_FRONTEND noninteractive
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    locale-gen en_US.UTF-8 && \
    dpkg-reconfigure locales && \
    /usr/sbin/update-locale LANG=en_US.UTF-8
ENV LC_ALL en_US.UTF-8

# User
RUN adduser app --disabled-password
RUN usermod -a -G adm app
RUN sudo -u app mkdir /home/app/.ssh

# Certificates
COPY ca-certificates usr/local/share/ca-certificates
RUN update-ca-certificates

# Base supervisor
RUN touch /etc/supervisor/conf.d/supervisord.conf
RUN \
    echo "[supervisord]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "nodaemon=true" >> /etc/supervisor/conf.d/supervisord.conf

# Entry point
CMD ["/usr/bin/supervisord"]

## END BASE

## NGINX

# Web server
RUN \
    apt-get update           && \
    apt-get -y install nginx

# nginx supervisor config
RUN \
    echo "[program:nginx]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command = /usr/sbin/nginx -g 'daemon off;'" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "user = root" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "autostart = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf

## END NGINX

## PHP

# Install php
RUN \
    apt-get update && \
    apt-get install -y gnupg2 ca-certificates apt-transport-https software-properties-common lsb-release && \
    wget -qO - https://packages.sury.org/php/apt.gpg | sudo apt-key add - && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list && \
    apt-get update && \
    apt-get -y install php8.0 && \
    apt-cache search php8.0 && \
    apt-get -y install php8.0-cli \
                       php8.0-common \
                       php8.0-dev \
                       php8.0-sqlite3 \
                       php8.0-readline \
                       php8.0-curl \
                       php8.0-fpm \
                       php8.0-intl \
                       php8.0-memcache \
                       php8.0-memcached \
                       php8.0-mbstring \
                       php8.0-redis \
                       php8.0-xdebug \
                       php8.0-imap \
                       php8.0-apcu \
                       php8.0-bcmath \
                       php8.0-soap \
                       php8.0-xml \
                       php8.0-pgsql


# Disable phpmods
RUN \
    phpdismod xdebug

# Configure php-fpm
RUN \
    echo "upload_max_filesize = 32M" >> /etc/php/8.0/fpm/php.ini && \
    mkdir -p /var/www /run/php

# Set default timezone
RUN \
    sed -i 's/;date.timezone =/date.timezone = Europe\/Vilnius/g' /etc/php/8.0/fpm/php.ini && \
    sed -i 's/;date.timezone =/date.timezone = Europe\/Vilnius/g' /etc/php/8.0/cli/php.ini

# memcached
RUN \
    apt-get update && apt-get -y install memcached

# redis
RUN \
    sudo apt -y install redis-server

# Composer
RUN \
    php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php && \
    php composer-setup.php --install-dir '/usr/local/bin' --filename 'composer' --version 2.2.7 && \
    chmod +x /usr/local/bin/composer                                             && \
    php -r "unlink('composer-setup.php');"

# nodejs
RUN \
    curl -sL https://deb.nodesource.com/setup_12.x | sudo -E bash - && \
    apt-get update                                                  && \
    apt-get install -y nodejs                                       && \
    npm install npm@6.13.4 -g                                       && \
    rm -rf /var/lib/apt/lists/*

# php-fpm supervisor config
RUN \
    echo "[program:php-fpm]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command = /usr/sbin/php-fpm8.0 --nodaemonize" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "user = root" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "autostart = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf

# redis supervisor config
RUN \
    echo "[program:redis]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command = /usr/local/bin/redis-server" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "user = root" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "autostart = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf


## END PHP

## SYMFONY

ADD nginx/sites-enabled/app.conf /etc/nginx/sites-enabled/default
ADD nginx/nginx.conf /etc/nginx/nginx.conf
ADD nginx/certificates/wildcard.dev.docker.cert.pem /etc/nginx/certificates/wildcard.dev.docker.cert.pem
ADD nginx/certificates/wildcard.dev.docker.key.pem /etc/nginx/certificates/wildcard.dev.docker.key.pem
ADD nginx/certificates/ssl_evp_client_certificate.crt /etc/nginx/certificates/ssl_evp_client_certificate.crt
ADD php/8.0/fpm/pool.d/app.conf /etc/php/8.0/fpm/pool.d/app.conf

ADD php/8.0/mods-available/opcache.ini /etc/php/8.0/mods-available/opcache.ini
ADD php/8.0/mods-available/xdebug.ini /etc/php/8.0/mods-available/xdebug.ini

# remove duplicated opcache config symlink
RUN sh -c '[ -e "/etc/php/8.0/cli/conf.d/10-opcache.ini" ] && rm "/etc/php/8.0/cli/conf.d/20-opcache.ini" || true'

RUN sudo -u app mkdir /home/app/log
RUN ln -s /var/log/nginx/access.log /home/app/log/nginx-access.log
RUN ln -s /var/log/nginx/error.log /home/app/log/nginx-error.log
RUN ln -s /var/log/php-fpm/slow.log /home/app/log/php-fpm-slow.log

## END SYMFONY

# postgresql
RUN \
    apt-get update && \
    apt-get install -y postgresql postgresql-contrib && \
    rm -rf /var/lib/apt/lists/*
RUN \
    service postgresql start                                                                     && \
    sudo -u postgres psql -c "CREATE USER app WITH ENCRYPTED PASSWORD 'pass';"                   && \
    sudo -u postgres psql -c "CREATE DATABASE app ENCODING 'UTF8' OWNER app TEMPLATE template1;" && \
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE app to app;"                      && \
    service postgresql stop

RUN \
    echo "host    all             all             172.17.0.1/8           md5" >> /etc/postgresql/13/main/pg_hba.conf && \
    sed -i "s|#listen_addresses =.*|listen_addresses = '*'|g" /etc/postgresql/13/main/postgresql.conf

# postgresql supervisor config
RUN \
    echo "[program:postgresql]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command = /usr/lib/postgresql/13/bin/postgres -D /var/lib/postgresql/13/main -c config_file=/etc/postgresql/13/main/postgresql.conf" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "user = postgres" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "autostart = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf

# cron supervisor config
RUN \
    echo "[program:cron]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command = cron -f" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "user = root" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "autostart = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_events_enabled = true" >> /etc/supervisor/conf.d/supervisord.conf

RUN \
    apt-get -y purge '^php7.4.*' && \
    apt-get -y purge '^php8.1.*'

WORKDIR /home/app/src