SELECT sum(nu_cantidad * nu_precio) AS Precio_Total, nb_articulo, nb_clase, nb_categoria
  FROM t01_detalle
  INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
 LEFT JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
LEFT JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
LEFT JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria
WHERE t20_factura.tx_tipo = 'GASTO'

