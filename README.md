# ecommercePaso 1: Instalar Apache

Apache es uno de los servidores web más populares y puedes instalarlo fácilmente en Linux Mint.

    Abre una terminal.
    Actualiza la lista de paquetes e instala Apache:

sudo apt update
sudo apt install apache2

Inicia el servicio de Apache:

sudo systemctl start apache2

Asegúrate de que Apache se inicie automáticamente al arrancar el sistema:

sudo systemctl enable apache2

Verifica que Apache esté funcionando abriendo tu navegador web y escribiendo:

    http://localhost

    Si todo está bien, deberías ver la página predeterminada de Apache que indica que el servidor está funcionando correctamente.

Paso 2: Instalar PHP

Si planeas usar PHP para tus scripts o páginas dinámicas, debes instalar PHP y el módulo de Apache para PHP.

    Instala PHP y el módulo para Apache:

sudo apt install php libapache2-mod-php

Reinicia Apache para que se cargue el módulo PHP:

sudo systemctl restart apache2

Verifica que PHP esté funcionando creando un archivo PHP en el directorio de tu servidor web (por lo general /var/www/html/):

sudo nano /var/www/html/info.php

Y agrega lo siguiente al archivo:

<?php
phpinfo();
?>

Luego, abre en tu navegador:

    http://localhost/info.php

    Si ves la página de información de PHP, significa que todo está funcionando correctamente.

Paso 3: Crear un sistema de base de datos (si es necesario)

Si tu sistema requiere una base de datos, puedes instalar MySQL o MariaDB.

    Instala MariaDB (un fork de MySQL y completamente compatible):

sudo apt install mariadb-server

Inicia MariaDB y configura la seguridad inicial:

sudo systemctl start mariadb
sudo mysql_secure_installation

Verifica que MariaDB esté funcionando correctamente:

sudo systemctl status mariadb

Si necesitas acceder a la consola de MariaDB:

    sudo mysql -u root -p

Paso 4: Configurar el directorio de trabajo

El directorio donde Apache busca tus archivos web es /var/www/html/. Puedes cambiar la ubicación de tus archivos web o configurarlo para que apunte a un directorio específico, dependiendo de tu preferencia.

    Puedes cambiar los permisos del directorio para poder escribir en él sin ser superusuario:

    sudo chown -R $USER:$USER /var/www/html

    Luego, crea tus archivos de tu aplicación web (HTML, PHP, etc.) dentro de /var/www/html/ o cualquier otro directorio que hayas configurado.

Paso 5: Configurar Virtual Hosts (opcional)

Si deseas manejar varios proyectos o sitios web en tu servidor, puedes configurar Virtual Hosts en Apache.

    Crea un archivo de configuración para tu sitio en /etc/apache2/sites-available/:

sudo nano /etc/apache2/sites-available/mi_sitio.conf

Agrega la siguiente configuración:

<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/mi_sitio
    ServerName mi_sitio.local
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

Habilita el sitio y reinicia Apache:

sudo a2ensite mi_sitio.conf
sudo systemctl restart apache2

Asegúrate de que tu sistema pueda resolver mi_sitio.local modificando el archivo /etc/hosts:

sudo nano /etc/hosts

Añade la siguiente línea:

127.0.0.1   mi_sitio.local

Ahora podrás acceder a tu sitio desde el navegador con:

    http://mi_sitio.local

Paso 6: Configurar HTTPS (opcional)

Si deseas servir tu aplicación web de manera segura con HTTPS, puedes usar Let's Encrypt para obtener un certificado SSL gratuito.

    Instala Certbot (herramienta para gestionar certificados SSL de Let's Encrypt):

sudo apt install certbot python3-certbot-apache

Obtén un certificado SSL:

    sudo certbot --apache

    Sigue las instrucciones para obtener el certificado y configurar automáticamente HTTPS en tu servidor.

Paso 7: Probar y desarrollar

Con todo configurado, ahora puedes empezar a desarrollar tu aplicación web directamente en el directorio de tu servidor (/var/www/html/ o el que hayas elegido), y Apache servirá tus archivos estáticos o dinámicos según sea necesario.
Resumen

    Apache: servidor web.
    PHP: para crear aplicaciones dinámicas.
    MariaDB/MySQL: base de datos (si es necesario).
    Virtual Hosts: para manejar varios proyectos o sitios.
    Certbot: para configurar HTTPS (opcional).
    