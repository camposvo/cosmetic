-- SELECT valor FROM v02_edo_cuenta WHERE ref = (SELECT Max(ref) from v02_edo_cuenta)

--SELECT SUM(ingreso) as Ingreso, SUM(egreso) as Egreso FROM v02_edo_cuenta WHERE EXTRACT(month FROM fecha) = 10


-- SELECT sum(nu_monto) as Abono, sum (t01_movimiento.nu_cantidad * nu_precio) as SumaTotal FROM t01_movimiento 
-- INNER JOIN s01_persona ON s01_persona.co_persona = t01_movimiento.fk_cliente 
-- LEFT JOIN t04_abono ON t01_movimiento.pk_movimiento = t04_abono.fk_movimiento 
-- WHERE t01_movimiento.tx_tipo='CTAXPAGAR' 

--SELECT Max(ref) from v02_edo_cuenta