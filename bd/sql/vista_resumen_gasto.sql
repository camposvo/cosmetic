SELECT t02_proyecto.pk_rubro as fk_proyecto,  
sum (t01_movimiento.nu_cantidad * nu_precio) as TotalGasto 
FROM t01_movimiento INNER JOIN t02_proyecto ON t02_proyecto.pk_rubro = t01_movimiento.fk_rubro 
WHERE t01_movimiento.tx_tipo='GASTO' 
GROUP BY t02_proyecto.pk_rubro, t02_proyecto.tx_nombre ORDER BY t02_proyecto.tx_nombre DESC 