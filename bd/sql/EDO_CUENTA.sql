 SELECT t20_factura.pk_factura,
    t01_detalle.nu_referencia AS ref,
    t01_detalle.fe_fecha_registro AS fecha,
    t01_detalle.tx_observacion,
    t20_factura.tx_tipo,
       -- EN CASO DE SER UN GASTO DEVUELVE EL NOMBRE DEL PROVEEDOR
	CASE
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN t03_proveedor.nb_proveedor
            ELSE UPPER(s01_persona.tx_nombre||' '||s01_persona.tx_apellido)
        END AS Cliente,
	
        -- CLASIFICA LOS DISTINTOS TIPOS DE OPERACION
	CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN 'CREDITO'
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN 'PRESTAMO'            
            ELSE 'NA'
        END AS operacion,
	
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0::numeric
        END AS ingreso,
        
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0::numeric
        END AS egreso,
        
	f_cuenta(t01_detalle.nu_referencia) AS f_cuenta
	
   FROM t01_detalle
     LEFT JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
     LEFT JOIN s01_persona ON t20_factura.fk_cliente = s01_persona.co_persona 
     LEFT JOIN t03_proveedor ON t20_factura.fk_proveedor = t03_proveedor.pk_proveedor 
     
  WHERE t20_factura.tx_tipo::text <> 'VENTA'::text AND t20_factura.tx_tipo::text <> 'GASTO'::text
UNION ALL
 SELECT t20_factura.pk_factura,
    t04_abono.nu_referencia AS ref,
    t04_abono.fe_fecha AS fecha,
    t04_abono.tx_observacion,
    t20_factura.tx_tipo,
     -- EN CASO DE SER UN GASTO DEVUELVE EL NOMBRE DEL PROVEEDOR
	CASE
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN t03_proveedor.nb_proveedor
            ELSE UPPER(s01_persona.tx_nombre||' '||s01_persona.tx_apellido)
        END AS Cliente,
    -- CLASIFICA LOS DISTINTOS TIPOS DE OPERACION
	CASE
            WHEN t20_factura.tx_tipo::text = 'VENTA'::text THEN 'VENTA'
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN 'GASTO'
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN 'PAGO CRED.'
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN 'COBR PREST.'
            ELSE 'NA'
        END AS operacion,
    -- DEFINE SI ES UN INGRESO O EGRESO
        CASE
            WHEN t20_factura.tx_tipo::text = 'VENTA'::text OR t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN t04_abono.nu_monto
            ELSE 0::numeric
        END AS ingreso,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text OR t20_factura.tx_tipo::text = 'GASTO'::text THEN t04_abono.nu_monto
            ELSE 0::numeric
        END AS egreso,
    f_cuenta(t04_abono.nu_referencia) AS f_cuenta
   FROM t04_abono
   LEFT JOIN t20_factura ON t20_factura.pk_factura = t04_abono.fk_factura
   LEFT JOIN s01_persona ON t20_factura.fk_cliente = s01_persona.co_persona 
   LEFT JOIN t03_proveedor ON t20_factura.fk_proveedor = t03_proveedor.pk_proveedor 

     