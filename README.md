# ecommerce

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


---------------------------------------------
Tengo adelantado los procedimientos( procedure ), en la base de datos, para hacer mas limpio el codigo en php, pero todavia no los he implementado. hice primero una carga de productos, directamente en el codigo. Pero he leido, que es mas eficiente hacer los procedimientos, por seguridad y eficiencia. 