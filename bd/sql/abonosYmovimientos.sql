SELECT pk_abono, fk_movimiento, fe_fecha, nu_cantidad * nu_precio, fk_indicador, t04_abono.nu_referencia
  FROM t04_abono
  inner join t01_movimiento on t04_abono.fk_movimiento = t01_movimiento.pk_movimiento
  --where nu_monto >= nu_cantidad * nu_precio or nu_monto >= (nu_cantidad * nu_precio)-40
 --where nu_monto <> nu_cantidad * nu_precio
   where abs(nu_monto -(nu_cantidad * nu_precio)) < 50
   
