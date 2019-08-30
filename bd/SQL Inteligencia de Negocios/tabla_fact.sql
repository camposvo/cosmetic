SELECT t20_factura.pk_factura,
    t01_detalle.nu_referencia AS ref,
    to_char(fe_fecha_factura, 'mm'),
        CASE
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN t03_proveedor.nb_proveedor::text
            ELSE upper((s01_persona.tx_nombre::text || ' '::text) || s01_persona.tx_apellido::text)
        END AS cliente,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN 'CREDITO'::text
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN 'PRESTAMO'::text
            WHEN t20_factura.tx_tipo::text = 'VENTA'::text THEN 'VENTA'::text
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN 'GASTO'::text
            ELSE 'NA'::text
        END AS operacion,
        CASE
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0
        END AS Gasto,
        CASE
            WHEN t20_factura.tx_tipo::text = 'VENTA'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0
        END AS Venta,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0
        END AS Credito,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0
        END AS Prestado
        
   FROM t01_detalle
     LEFT JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
     LEFT JOIN s01_persona ON t20_factura.fk_cliente = s01_persona.co_persona
     LEFT JOIN t03_proveedor ON t20_factura.fk_proveedor = t03_proveedor.pk_proveedor
