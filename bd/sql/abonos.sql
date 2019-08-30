SELECT t20_factura.pk_factura,
    t01_detalle.nu_referencia AS ref,
    t01_detalle.fe_fecha_registro AS fecha,
    t01_detalle.tx_observacion,
    t20_factura.tx_tipo,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0::numeric
        END AS ingreso,
        CASE
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text or t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0::numeric
        END AS egreso,
        f_cuenta(t01_detalle.nu_referencia)
   FROM t01_detalle
   LEFT JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura 
  WHERE t20_factura.tx_tipo <> 'VENTA' 
UNION ALL
  SELECT t20_factura.pk_factura,
    t04_abono.nu_referencia AS ref,
    t04_abono.fe_fecha AS fecha,
    t04_abono.tx_observacion,
    t20_factura.tx_tipo,
	CASE
            WHEN t20_factura.tx_tipo::text = 'VENTA'::text or t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN t04_abono.nu_monto
            ELSE 0::numeric
        END AS ingreso,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN t04_abono.nu_monto
            ELSE 0::numeric
        END AS egreso,
        f_cuenta(t04_abono.nu_referencia)
        
   FROM t04_abono
   LEFT JOIN t20_factura ON t20_factura.pk_factura = t04_abono.fk_factura 
      
  WHERE t20_factura.tx_tipo <> 'GASTO'