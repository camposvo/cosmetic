SELECT t02_proyecto.tx_nombre, UPPER(RESPONSABLE.tx_indicador) as Respon, UPPER(CLIENTE.tx_indicador) as Clien, fk_proveedor, to_char(fe_fecha_factura, 'dd/mm/yyyy'), 
tx_factura, t01_movimiento.nu_cantidad, nu_precio, 
(t01_movimiento.nu_cantidad * nu_precio) AS TOTAL, tx_observacion, 
tx_unidad, fk_tipo_clase, fk_ubicacion, fk_marca, t13_tipo_clase.fk_clase, 
t10_ubicacion.fk_almacen, t01_movimiento.tx_tipo 
FROM t01_movimiento 
LEFT JOIN t13_tipo_clase ON t01_movimiento.fk_tipo_clase = t13_tipo_clase.pk_tipo_clase 
LEFT JOIN t10_ubicacion ON t01_movimiento.fk_ubicacion = t10_ubicacion.pk_ubicacion 
LEFT JOIN t02_proyecto ON t01_movimiento.fk_rubro=t02_proyecto.pk_rubro 
INNER JOIN s01_persona ON t01_movimiento.fk_responsable =s01_persona.co_persona
INNER JOIN s01_persona as RESPONSABLE ON t01_movimiento.fk_responsable = RESPONSABLE.co_persona
INNER JOIN s01_persona as CLIENTE ON t01_movimiento.fk_responsable = CLIENTE.co_persona   
WHERE pk_movimiento = 103