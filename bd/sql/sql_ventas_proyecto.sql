SELECT to_char(pk_factura,'0000000'), t02_proyecto.tx_nombre, nb_articulo, t01_detalle.nu_cantidad, nu_precio,  t01_detalle.nu_cantidad * nu_precio as total,  
       nu_cant_item, UPPER(VENDEDOR.tx_nombre) AS vend, UPPER(CLIENTE.tx_nombre) AS client
  FROM t01_detalle
   INNER JOIN t20_factura ON t20_factura.pk_factura    = t01_detalle.fk_factura
   INNER JOIN t02_proyecto ON t02_proyecto.pk_proyecto = t01_detalle.fk_rubro 
   INNER JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
   INNER JOIN s01_persona AS VENDEDOR ON VENDEDOR.co_persona = t20_factura.fk_responsable
   INNER JOIN s01_persona AS CLIENTE ON CLIENTE.co_persona = t20_factura.fk_cliente 
   WHERE t01_detalle.fk_rubro = 3 	 	
 ;
