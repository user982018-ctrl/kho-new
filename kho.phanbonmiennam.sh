kho.phanbonmiennam.net

sudo mkdir -p /var/www/thuysanmiennam.com.vn
sudo chown -R $USER:$USER /var/www/thuysanmiennam.com.vn
sudo nano /etc/apache2/sites-available/thuysanmiennam.com.vn.conf
<VirtualHost *:80>
    ServerName thuysanmiennam.com.vn
    ServerAlias www.thuysanmiennam.com.vn
    DocumentRoot /var/www/thuysanmiennam.com.vn

    <Directory /var/www/thuysanmiennam.com.vn>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/example.com-error.log
    CustomLog ${APACHE_LOG_DIR}/example.com-access.log combined
</VirtualHost>

sudo a2ensite thuysanmiennam.com.vn
sudo systemctl reload apache2

sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d thuysanmiennam.com.vn -d www.thuysanmiennam.com.vn

sudo certbot --apache -d kho.phanbonmiennam.net -d www.kho.phanbonmiennam.net

sudo certbot certonly --standalone -d kho.phanbonmiennam.net -d www.kho.phanbonmiennam.`

mv /var/www/kho.phanbonmiennam.net/kho/* /var/www/kho.phanbonmiennam.net
mv /var/www/kho.phanbonmiennam.net/kho/.* /var/www/kho.phanbonmiennam.net/ 2>/dev/null

sudo chown -R www-data:www-data /var/www/kho.phanbonmiennam.net
sudo chmod -R 755 /var/www/kho.phanbonmiennam.net

sudo nano /etc/apache2/sites-available/kho.phanbonmiennam.net.conf

sudo mkdir -p /var/www/kho.phanbonmiennam.net
sudo chown -R www-data:www-data /var/www/kho.phanbonmiennam.net
sudo a2ensite kho.phanbonmiennam.net.conf
sudo systemctl reload apache2
sudo certbot --apache -d kho.phanbonmiennam.net -d www.kho.phanbonmiennam.net
