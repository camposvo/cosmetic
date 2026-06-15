SELECT t02_proyecto.pk_rubro as fk_proyecto,
sum(nu_monto) as AbonoVenta, 
sum (t01_movimiento.nu_cantidad * nu_precio) - (case when sum(nu_monto)isnull then 0 else sum(nu_monto) end) AS DebeVenta, 
sum (t01_movimiento.nu_cantidad * nu_precio) as TotalVenta, 
sum (t01_movimiento.nu_cantidad) as CantidadVenta, 
sum (t01_movimiento.nu_cant_item) as ItemVenta
FROM t01_movimiento INNER JOIN t02_proyecto ON t02_proyecto.pk_rubro = t01_movimiento.fk_rubro 
LEFT JOIN t04_abono ON t01_movimiento.pk_movimiento = t04_abono.fk_movimiento 
WHERE t01_movimiento.tx_tipo='VENTA' 
GROUP BY t02_proyecto.pk_rubro,t02_proyecto.tx_nombre ORDER BY t02_proyecto.tx_nombre DESC