<?php 
	include_once ("utilidad.php");

	$id_vendedor = $_POST["id_vendedor"];
	$newContent = '';

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$newContent .= '<table class="table table-bordered table-striped" >';
	$newContent .= '<tr>
						<th><small>Cliente</small></th>
						<th><small>Deuda</small></th>
					</tr>';
		

		$ls_sql = "SELECT UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		sum(detalle) - sum(abono) as Debe 
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_cliente 		
		WHERE v01_pago.fk_responsable =$id_vendedor AND v01_pago.tx_tipo='VENTA' and (detalle - abono) > 0 
		GROUP BY s01_persona.tx_nombre, s01_persona.tx_apellido 	";
			
			
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
			while($row = pg_fetch_row($ls_resultado)){
				$newContent .='<tr>
									<td width="50%" class="text-primary"><small>'.$row[0].'</small></td>
									<td width="25%" class="text-primary"><small>'.number_format($row[1],2,",",".").'</small></td>
								</tr>';	

				$entro = 'entro:'.$row[0];					
			}
		$newContent .= '</table>';
	
	//$json = json_encode($a);
	echo $newContent ;


?>
