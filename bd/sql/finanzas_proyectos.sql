SELECT sum (itemventa) as item, 
	sum(abonoventa) as totalabono, sum(debeventa) as totaldebe,
	sum(totalventa) as totalventa, 
	sum(totalgasto) as totalgasto
 FROM t02_proyecto
 INNER JOIN t08_tipo_rubro ON t02_proyecto.fk_tipo_rubro = t08_tipo_rubro.pk_tipo_rubro
 LEFT JOIN v03_resumen_venta ON t02_proyecto.pk_rubro = v03_resumen_venta.fk_proyecto
 LEFT JOIN v04_resumen_gasto ON t02_proyecto.pk_rubro = v04_resumen_gasto.fk_proyecto
 WHERE EXTRACT(year FROM t02_proyecto.fe_inicial) = 2014

