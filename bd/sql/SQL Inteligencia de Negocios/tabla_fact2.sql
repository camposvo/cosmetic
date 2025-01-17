 SELECT t01_movimiento.pk_movimiento,	 	
       t01_movimiento.tx_tipo as operacion,
    	CASE
            WHEN t01_movimiento.tx_tipo::text = 'GASTO'::text THEN t03_proveedor.nb_proveedor::text
            ELSE upper((s01_persona.tx_nombre::text || ' '::text) || s01_persona.tx_apellido::text)
        END AS cliente,
	
        CASE
	    WHEN t01_movimiento.tx_tipo::text = 'CTAXPAGAR'::text THEN t01_movimiento.nu_cantidad * t01_movimiento.nu_precio
	    ELSE 0::numeric
	END AS CREDITO,
	CASE
	    WHEN t01_movimiento.tx_tipo::text = 'VENTA'::text THEN t01_movimiento.nu_cantidad * t01_movimiento.nu_precio
	    ELSE 0::numeric
	END AS VENTA,
	CASE
	    WHEN t01_movimiento.tx_tipo::text = 'GASTO'::text THEN t01_movimiento.nu_cantidad * t01_movimiento.nu_precio
	    ELSE 0::numeric
	END AS GASTO,
	CASE
	    WHEN t01_movimiento.tx_tipo::text = 'CTAXCOBRAR'::text THEN t01_movimiento.nu_cantidad * t01_movimiento.nu_precio
	    ELSE 0::numeric
	END AS PRESTAR,
	to_char(fe_fecha_factura, 'mm/yyyy') as fk_fecha,
	CASE
	    WHEN fk_rubro is null THEN 0
	    ELSE  fk_rubro
	END AS fk_proyecto,
	CASE
            WHEN fk_tipo_clase is null THEN 0
            ELSE fk_tipo_clase
        END as fk_clasificacion
   FROM t01_movimiento
    LEFT JOIN s01_persona ON t01_movimiento.fk_cliente = s01_persona.co_persona
     LEFT JOIN t03_proveedor ON t01_movimiento.fk_proveedor = t03_proveedor.pk_proveedor

