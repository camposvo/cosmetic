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
		

		$ls_sql = "SELECT to_char(fe_fecha_factura, 'dd-TMMon-yyyy'), UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		(detalle - abono) as Debe,
		 CASE WHEN fe_fecha_factura <= CURRENT_DATE - INTERVAL '10 days' THEN 'VENCIDA'
         ELSE 'EN CURSO'
    		END AS antiguedad		
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_cliente 		
		WHERE v01_pago.fk_responsable =$id_vendedor AND v01_pago.tx_tipo='VENTA' and (detalle - abono) > 0 
		order by  fe_fecha_factura asc";
			
			
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);


			
			while($row = pg_fetch_row($ls_resultado)){
				$bg_color = ($row[3] == "VENCIDA") ? "style='background-color: #FA5858;'":"";
				$txt_color = ($row[3] == "VENCIDA") ? "style='color: #FFFFFF;'":"";

				$newContent .='<tr '.$bg_color.'>
									<td width="20%" '.$txt_color.'>'.$row[0].'</td>
									<td width="50%" '.$txt_color.'>'.$row[1].'</td>
									<td width="30%" '.$txt_color.'>'.number_format($row[2],2,",",".").'</td>
								</tr>';	

				$entro = 'entro:'.$row[0];					
			}
		$newContent .= '</table>';
	
	//$json = json_encode($a);
	echo $newContent ;


?>
