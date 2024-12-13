# Proyecto: ecommerce
---> nombre del proyecto: ecommerce


Pautas de entrega del trabajo final: 

1) visualizacion del cliente => vista moderna y copada para mostrar los productos a vender (no se puede comprar nada): 

# localhost/ecommerce/index.php
 ---> se entra a una vista con las imagenes de los productos, sin opcion a nada. Si te logueas, optienes paso al panel de administrradores. Sin funciones todavia. 


2) una visualizacion de los productos por parte del administrador, en forma de tablita para poder gestionarlos. => esta pantalla se accede solamente logueandote:

# localhost/ecommerce/crudAdmin.php 
---> vista para administradores, falta funciones de crud, y sus correspondientes vistas. 


3) una visualizacion para dar de alta un producto => esta pantalla se accede solamente logueandote: 

# localhost/ecommerce/crudAdmin.php
 ---> vista para admins. No implementada todavia

    Si implementé una carga de imagen y producto, con descripcion, en:

# localhost/ecommerce/productos/productos.php

4) y luego esta el login, que te sirve para loguearte a la parte del administrador:


# localhost/ecommerce/index.php
 ---> te registras y accedes alpanel de administrador ---> ecommerce/productos/productos.php. Puedes cargar un producto, y su imagen desde tu ordenador, a la base de datos, y la misma se encarga de pasar la imagen a blob.

5) tambien esta la visualizacion para editar un producto => esta pantalla se accede solamente logueandote:

# localhost/ecommerce/crudAdmin.php
 ---> vista para admins. No implementada todavia


6) luego de levantar xampp: previamente instalado:

 ---> configuracion para probar el proyecto en navegador: 
# url: localhost/ecommerce/index.php

    Tengo adelantado los procedimientos( procedure ), en la base de datos, para hacer mas limpio el codigo en php, pero todavia no los he implementado. hice primero una carga de productos, directamente en el codigo. Pero he leido, que es mas eficiente hacer los procedimientos, por seguridad y eficiencia. 

7) primer vista 
implementada, no se puede comprar. 
# 

8) vista login
hecha.carga en la base, y registra la sesion.
# 

9) vista register
realizada y funcional. carga en la base
# 

10) vista login cliente
no implementada, no llego. 
# 

11) vista administrador. hay dos formas, de cargar productos, la que quieroimplementar, que en parte lo hice, es usando la folder: productos. Pero falta mas codigo, para que esté completo. La otra forma, es: crudAdmin.php como muestro abajo.

# 
http://localhost/ecommerce/src/Log_Reg/crudAdmin.php

12) Para la carga de imagenes: para ello proceder: dejo en la carpeta imagenes, las mismas, con las descripciones. El proyecto no tiene imagenes cargadas.   
 ---> Para cargar las mismas: localhost/ecommerce/productos/productos.php

13) carpeta compras, no llego a implementar.
    carpeta crud_logic, para hacer el codigo mas limpio y modular, no llego a implementar. Todo se encuentrra junto en la folder Log_Reg/crudAdmin.php, para administradores.