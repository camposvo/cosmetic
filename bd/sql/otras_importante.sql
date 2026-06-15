
-- ABONOS POR VENTAS O CUENTAS QUE ESTAN PENDIENTE POR COBRAR
SELECT sum(nu_monto) INTO ingreso_1
  FROM t04_abono
  inner join t20_factura ON t04_abono.fk_factura = t20_factura.pk_factura
  where t04_abono.nu_referencia <= 64 and (t20_factura.tx_tipo = 'VENTA' OR t20_factura.tx_tipo = 'CTAXCOBRAR');

-- SOLICITUDE DE CREDITO O CUENTA POR PAGAR
SELECT sum(nu_cantidad * nu_precio) into ingreso_2
  FROM t01_detalle
   inner join t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
  where (t01_detalle.nu_referencia <= 64) and  t20_factura.tx_tipo = 'CTAXPAGAR';

-- PAGO O ABONO DE UN CREDITO O CUENTA POR PAGAR
SELECT sum(nu_monto) into egreso_1
  FROM t04_abono
   inner join t20_factura ON t04_abono.fk_factura = t20_factura.pk_factura
  where t04_abono.nu_referencia <= 64 and t20_factura.tx_tipo = 'CTAXPAGAR';

-- EMISION DE UN PRESTAMO (CTA POR COBRAR) Y GASTOS
SELECT sum(nu_cantidad * nu_precio) into egreso_2
  FROM t01_detalle
   inner join t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
  where (t01_detalle.nu_referencia <= 64) and  (t20_factura.tx_tipo = 'GASTO' OR t20_factura.tx_tipo = 'CTAXCOBRAR');
