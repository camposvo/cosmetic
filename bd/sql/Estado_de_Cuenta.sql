SELECT 	t01_movimiento.nu_referencia as ref,
	tx_nombre, 
	fe_fecha_factura as Fecha, tx_factura, 
	tx_observacion,
	t01_movimiento.tx_tipo,
	t01_movimiento.nu_cantidad * nu_precio as Total,
	(case when (t01_movimiento.tx_tipo = 'CTAXPAGAR') then t01_movimiento.nu_cantidad * nu_precio else 0 end) AS 	Ingreso,
	(case when (t01_movimiento.tx_tipo = 'GASTO') OR (t01_movimiento.tx_tipo = 'CTAXCOBRAR') then t01_movimiento.nu_cantidad * nu_precio else 0 end) AS Egreso,
	f_cuenta(t01_movimiento.nu_referencia) as valor
FROM t01_movimiento
LEFT JOIN  t02_rubro ON t01_movimiento.fk_rubro = t02_rubro.pk_rubro
WHERE t01_movimiento.tx_tipo <> 'VENTA'

UNION all

SELECT 	t04_abono.nu_referencia as ref,
	tx_nombre, 
	t04_abono.fe_fecha as Fecha, 
	t01_movimiento.tx_factura, 
	t01_movimiento.tx_observacion,
	t01_movimiento.tx_tipo,
	t01_movimiento.nu_cantidad * nu_precio as total,
	(case when (t01_movimiento.tx_tipo = 'CTAXCOBRAR') OR (t01_movimiento.tx_tipo = 'VENTA') then t04_abono.nu_monto else 0 end) AS Ingreso,
	(case when (t01_movimiento.tx_tipo = 'CTAXPAGAR') then t04_abono.nu_monto else 0 end) AS Egreso,
	f_cuenta(t04_abono.nu_referencia) as valor
FROM t04_abono
INNER JOIN  t01_movimiento ON t01_movimiento.Pk_movimiento = t04_abono.fk_movimiento
LEFT JOIN  t02_rubro ON t01_movimiento.fk_rubro = t02_rubro.pk_rubro
order by ref desc
