SELECT to_char(fe_fecha_factura, 'mm') , tx_tipo, 
	CASE
	    WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN nb_proveedor
	    ELSE upper(tx_nombre||' '||tx_apellido)
	END AS Cliente,
	CASE
	    WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN f_calcular_factura(pk_factura)
	    ELSE 0::numeric
	END AS CREDITO,
	CASE
	    WHEN t20_factura.tx_tipo::text = 'VENTA'::text THEN f_calcular_factura(pk_factura)
	    ELSE 0::numeric
	END AS VENTA,
	CASE
	    WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN f_calcular_factura(pk_factura)
	    ELSE 0::numeric
	END AS GASTO,
	CASE
	    WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN f_calcular_factura(pk_factura)
	    ELSE 0::numeric
	END AS PRESTAR

	
  FROM t20_factura
  LEFT JOIN s01_persona  ON s01_persona.co_persona = t20_factura.fk_cliente
  LEFT JOIN t03_proveedor  ON t03_proveedor.pk_proveedor = t20_factura.fk_proveedor;
