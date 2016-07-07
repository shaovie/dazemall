ROOT_PATH=/home/daze

#### install nginx 
nginx_version=1.10.0
rm -rf nginx-${nginx_version}
if [ ! -f "nginx-${nginx_version}.tar.gz" ]; then
    echo "not found nginx-${nginx_version}.tar.gz"
fi
tar -zxf nginx-${nginx_version}.tar.gz
cd nginx-${nginx_version}

./configure --prefix=${ROOT_PATH}/nginx --with-http_stub_status_module --with-http_ssl_module 

make
make install

cd ..

#### install php 
php_version=7.0.6
rm -rf php-${php_version}
if [ ! -f "php-${php_version}.tar.gz" ]; then
    echo "not found php-${php_version}.tar.gz"
fi
tar -zxf php-${php_version}.tar.gz
cd php-${php_version}

./configure \
    --prefix=${ROOT_PATH}/php \
    --with-config-file-path=${ROOT_PATH}/php/etc \
    --with-config-file-scan-dir=${ROOT_PATH}/php/etc/php.d \
    --enable-mbstring \
    --enable-xml \
    --enable-sockets \
    --enable-fpm \
    --enable-zip \
    --enable-gd-native-ttf \
    --enable-pdo \
    --enable-opcache \
    --enable-exif \
    --enable-bcmath \
    --enable-pcntl \
    --with-pear \
    --with-zlib \
    --with-libxml-dir \
    --with-mcrypt \
    --with-openssl \
    --with-curl \
    --with-mysql \
    --with-mysqli \
    --with-pdo-mysql \
    --with-mhash \
    --with-freetype-dir \
    --with-iconv-dir \
    --with-gd \
    --with-jpeg-dir \
    --with-png-dir \
    --with-xmlrpc

make && make install
cd ..

#### install modules
for mod in phpredis
do
  rm -rf $mod
  if [ ! -f "${mod}.tar.gz" ]; then
      echo "not found ${mod}.tar.gz"
  fi
  tar -zxf ${mod}.tar.gz
  cd $mod

  ${ROOT_PATH}/php/bin/phpize
  ./configure --with-php-config=${ROOT_PATH}/php/bin/php-config

  make && make install
  cd ..
done

#### install redis
redis_version=3.2.0
rm -rf redis-${redis_version}
if [ ! -f "redis-${redis_version}.tar.gz" ]; then
    echo "not found redis-${redis_version}.tar.gz"
fi
tar -zxf redis-${redis_version}.tar.gz
cd redis-${redis_version}

make
mkdir -p ${ROOT_PATH}/redis/bin
cp src/redis-server ${ROOT_PATH}/redis/bin
cp src/redis-sentinel ${ROOT_PATH}/redis/bin
cp src/redis-cli ${ROOT_PATH}/redis/bin
cp src/redis-check-aof ${ROOT_PATH}/redis/bin

cd ../

#### build ok...
echo "all build complete! begin to reset config ......"

#### conf
for f in nginx.conf php.ini php-fpm.conf redis.conf ; do
    if [ ! -f "conf/${f}" ]; then
        echo "not found conf/${f}"
        exit 1
    fi
done

#### init nginx config

cd conf/
cp -f nginx.conf ${ROOT_PATH}/nginx/conf/
rm -f ${ROOT_PATH}/nginx/conf/*.default
mkdir -p ${ROOT_PATH}/nginx/conf/params
mkdir -p ${ROOT_PATH}/nginx/conf/servers
cp -f localhost ${ROOT_PATH}/nginx/conf/servers/
echo 'fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;' >> ${ROOT_PATH}/nginx/conf/fastcgi_params

cp -f php.ini ${ROOT_PATH}/php/etc/
cp -f php-fpm.conf ${ROOT_PATH}/php/etc/
cp -f redis.conf ${ROOT_PATH}/redis/

mkdir -p ${ROOT_PATH}/shell
cp -f nginx_log_division.sh ${ROOT_PATH}/shell/
cp -f php-fpm.run.sh ${ROOT_PATH}/shell/
cp -f redis.run.sh ${ROOT_PATH}/shell/
cp -f nginx.run.sh ${ROOT_PATH}/shell/
chmod u+x ${ROOT_PATH}/shell/*

cd ../

echo "reset config ok"

#### tar
cd ${ROOT_PATH}/
tar -zcf tmp/daze-install.tar.gz nginx php redis shell

echo "tar daze-install.tar.gz ok"

