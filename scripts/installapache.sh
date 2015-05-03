#!/bin/bash
cd /var/www/html
rm -R LysaghtMalaysia
mkdir LysaghtMalaysia
rm /etc/nginx/conf.d/Malaysia.conf
touch /etc/nginx/conf.d/Malaysia.conf
echo "server {" >> /etc/nginx/conf.d/Malaysia.conf
echo "    listen	 80;" >> /etc/nginx/conf.d/Malaysia.conf
echo "    server_name  manshrestha.com;" >> /etc/nginx/conf.d/Malaysia.conf
echo "" >> /etc/nginx/conf.d/Malaysia.conf
echo "    location / {" >> /etc/nginx/conf.d/Malaysia.conf
echo "        root   /var/www/html/LysaghtMalaysia;" >> /etc/nginx/conf.d/Malaysia.conf
echo "        index  index.php;" >> /etc/nginx/conf.d/Malaysia.conf
echo '        if (!-e $request_filename){ rewrite ^(.*)$ /index.php; } if ($http_host ~* "^www\.(.*)$"){ rewrite ^(.*)$ http://%1/$1 redirect; }' >> /etc/nginx/conf.d/Malaysia.conf
echo "    }" >> /etc/nginx/conf.d/Malaysia.conf
echo "    error_page  404              /404.html;" >> /etc/nginx/conf.d/Malaysia.conf
echo "    location = /404.html {" >> /etc/nginx/conf.d/Malaysia.conf
echo "        root   /usr/share/nginx/html;" >> /etc/nginx/conf.d/Malaysia.conf
echo "    }" >> /etc/nginx/conf.d/Malaysia.conf
echo "   location ~ \.php$ {" >> /etc/nginx/conf.d/Malaysia.conf
echo "            fastcgi_pass 127.0.0.1:9000;" >> /etc/nginx/conf.d/Malaysia.conf
echo "        fastcgi_param  SCRIPT_FILENAME  /var/www/html/LysaghtMalaysia/$fastcgi_script_name;" >> /etc/nginx/conf.d/Malaysia.conf
echo "        fastcgi_index index.php;" >> /etc/nginx/conf.d/Malaysia.conf
echo "        include fastcgi_params;" >> /etc/nginx/conf.d/Malaysia.conf
echo "    }" >> /etc/nginx/conf.d/Malaysia.conf
echo "}" >> /etc/nginx/conf.d/Malaysia.conf
service nginx restart
service php-fpm restart
