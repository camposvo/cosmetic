SELECT tx_factura, c008t_persona.tx_nombre, t02_rubro.tx_nombre,to_char(fe_fecha_factura, 'dd/mm/yyyy'),  
			t01_movimiento.nu_cantidad, tx_unidad, nu_precio, t01_movimiento.nu_cantidad * nu_precio as Total, sum as abono, pk_movimiento
			FROM t01_movimiento
			INNER JOIN c008t_persona ON c008t_persona.co_persona = t01_movimiento.fk_cliente
			INNER JOIN t02_rubro ON t02_rubro.pk_rubro = t01_movimiento.fk_rubro
			LEFT JOIN v01_pagos ON t01_movimiento.pk_movimiento = v01_pagos.fk_movimiento
		WHERE t01_movimiento.tx_tipo='VENTA' and sum <= (t01_movimiento.nu_cantidad * nu_precio)