
 -- SELECT fk_proyecto,tipo_operacion, sum(valortotal)        
--    FROM v05_mov_proyectos
--    group by fk_proyecto, tipo_operacion;



 SELECT fk_proyecto, fe_fecha_factura, cantidad, items,
	CASE
            WHEN tipo_operacion = 'GASTO'::text THEN valortotal
            ELSE 0::numeric
        END AS gasto,
       CASE
            WHEN tipo_operacion = 'VENTA'::text THEN valortotal
            ELSE 0::numeric
        END AS venta        
   FROM v05_mov_proyectos;
   

