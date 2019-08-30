<?php 
	include_once ("pro_utilidad.php");

	$id_vendedor = $_POST["id_vendedor"];
	$id_proyecto = $_POST["id_proyecto"];
	$newContent = '';

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$newContent .= '<table class="table table-bordered table-striped" >';
	$newContent .= '<tr>
						<th><small>Cliente</small></th>
						<th><small>Items</small></th>
						<th><small>Total</small></th>
					</tr>';
		

		$ls_sql = " SELECT   UPPER(CLIENTE.tx_nombre)||' '||UPPER(CLIENTE.tx_apellido) as Clien,						
						SUM(t01_detalle.nu_cant_item) as item,	
						SUM(t01_detalle.nu_cantidad * t01_detalle.nu_precio) as monto						
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
				LEFT JOIN s01_persona AS CLIENTE ON t20_factura.fk_cliente = CLIENTE.co_persona					
			WHERE t20_factura.tx_tipo='VENTA' AND t01_detalle.fk_rubro = ".$id_proyecto. " AND
				t20_factura.fk_responsable = ".$id_vendedor."
			 GROUP BY CLIENTE.tx_nombre,CLIENTE.tx_apellido ORDER BY CLIENTE.tx_nombre ASC";
			
			
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
			while($row = pg_fetch_row($ls_resultado)){
				$newContent .='<tr>
									<td width="50%" class="text-primary"><small>'.$row[0].'</small></td>
									<td width="25%" class="text-primary"><small>'.$row[1].'</small></td>
									<td width="25%" class="text-primary"><small>'.number_format($row[2],2,",",".").'</small></td>
								</tr>';	

				$entro = 'entro:'.$row[0];					
			}
		$newContent .= '</table>';
	
	//$json = json_encode($a);
	echo $newContent ;


?>
