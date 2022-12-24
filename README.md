# INSTALLATION FROM SCRATCH
## Preparation
- PHP 8.x
- MySQL 8.x
- Composer 2.x
- NodeJs 16.13
- Npm 8.1.x


## Install required packages
1. Update software list

```
sudo apt-get update
sudo apt-get upgrade
sudo apt install ca-certificates apt-transport-https software-properties-common
```

2. Add Ondrej PPA
```
sudo add-apt-repository ppa:ondrej/php
```

3. Install PHP
```
sudo apt -y install php8.0
sudo apt -y install php8.0-fpm php8.0-xml php8.0-mysql php8.0-curl php8.0-gd php8.0-zip php8.0-mbstring
```

4. Install Nginx (for production)
```
sudo apt -y install nginx
```

5. Regsier services (for production)
```
sudo systemctl enable nginx
sudo systemctl enable php8.0-fpm
```

6. Install MySQL
 - for production, only to install mysql command
```
apt search mysql-client
sudo apt install mysql-client-core-8.0
```
- for development
```
wget https://dev.mysql.com/get/mysql-apt-config_0.8.14-1_all.deb
sudo dpkg -i ./mysql-apt-config_0.8.14-1_all.deb
sudo apt-get install mysql-community-server
```

7. Install composer
```
sudo apt install wget php-cli php-zip unzip
wget -O composer-setup.php https://getcomposer.org/installer
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer -v
```

8. Install Nodejs
```
sudo apt install nodejs npm
sudo npm install -g n
sudo n --stable
sudo n 16.14.2
node -v
npm -v
```

## Setting

1. Pull source code from github
```
sudo apt-get install git
cd /var/www
sudo git clone https://[here your token]@github.com/ShingoMatsuura/netmaster.git
```

- change project folder permission
```
sudo chown -R ubuntu.www-data netmaster/
sudo chmod -R 775 /var/www/netmaster/storage/
sudo chmod -R 775 /var/www/netmaster/bootstrap/cache/
sudo usermod -aG www-data $USER
```

2. Move to project directory
```
cd path_of_project
```

3. Setting local environment config
```
cp .env.example .env
```

4. edit file .env as other project (project name, database connection, logs, etc) and save it

5. Update all dependency
```
composer update
```

6. Generate key
```
php artisan key:generate
```

7. Generate JWT token
```
php artisan jwt:secret
```

8. Create table of database and create test account (admin-netmaster-dev@gmail.com / 123456a@)
```
php artisan migrate --seed
```

9. Create language for javascript
```
php artisan make:lang-js
```

10. Install node module
```
npm i
```

11. Build frontend (vuejs)

* For development
```
npm run dev
```

* For Production
```
npm run prod
```

12. Config web server (apache2 or Nginx) and access to website

Notes:
Run `php artisan make:lang-js` every time deploy source code to server

## Other

### If use S3 for storage, setting in .env as below:

```
FILESYSTEM_DRIVER=s3
...
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=ap-northeast-1
AWS_BUCKET=netmaster-develop
AWS_URL=https://dml378l589efb.cloudfront.net  # cloudfront's URL
```