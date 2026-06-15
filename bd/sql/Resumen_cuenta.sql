-- SELECT sum(nu_monto) 
--   FROM t04_abono
--   inner join t01_movimiento ON t04_abono.fk_movimiento = t01_movimiento.pk_movimiento
--   where t04_abono.nu_referencia <= 2 and (t01_movimiento.tx_tipo = 'VENTA' OR t01_movimiento.tx_tipo = 'CTAXCOBRAR')
-- 
SELECT sum(nu_monto)
  FROM t04_abono
  inner join t01_movimiento ON t04_abono.fk_movimiento = t01_movimiento.pk_movimiento
  where t04_abono.nu_referencia <= 2 and t01_movimiento.tx_tipo = 'CTAXPAGAR'

-- SELECT sum(nu_cantidad * nu_precio) 
--   FROM t01_movimiento
--   where (t01_movimiento.nu_referencia <= 2) and  t01_movimiento.tx_tipo = 'GASTO'