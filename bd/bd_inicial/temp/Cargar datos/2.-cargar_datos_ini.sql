-- Considerar donde quedan las secuencias despues de realizar la copia 
-- Se deben copiar por columna cada tabla para que genere automaticamente el autonumerico


SET client_encoding=LATIN1; -- Evita los errores por acentos y caracteres especiales

copy t21_categoria  from 'C:\wamp\www\siscamp_1.7\bd\bd_inicial\categoria.txt' DELIMITERS ';' ;
copy t05_clase         from 'C:\wamp\www\siscamp_1.7\bd\bd_inicial\clase.txt' DELIMITERS ';' ;
copy t13_articulo  from 'C:\wamp\www\siscamp_1.7\bd\bd_inicial\articulo.txt' DELIMITERS ';' ;
copy t03_proveedor ( nb_proveedor, tx_rif, tx_objeto, tx_telefono, tx_direccion, tx_correo, tx_sitio_web) 	from 'C:\wamp\www\siscamp_1.7\bd\bd_inicial\proveedores.txt' DELIMITERS ';' ;
copy gan_raza (nb_raza, tx_coment_raza) from 'C:\wamp\www\siscamp_1.7\bd\bd_inicial\raza.txt' DELIMITERS ';' ;



