SELECT   t20_factura.tx_tipo     AS Tipo_Operacion, 
    t01_detalle.fk_rubro    AS fk_proyecto, 
    t20_factura.fe_fecha_factura,    
    t01_detalle.nu_precio    AS valorunit,
    t01_detalle.nu_cantidad  AS cantidad,
    t01_detalle.nu_cantidad * t01_detalle.nu_precio AS valortotal,
    t01_detalle.nu_cant_item AS items
   FROM t01_detalle
     left JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura;
   
