<?php 
	include_once ("nom_utilidad.php");

	$co_empleado = $_POST["elegido"];

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	if ($co_empleado > 0){
							
		$obj_miconexion = fun_crear_objeto_conexion();
		$li_id_conex = fun_conexion($obj_miconexion);
		
		$ls_sql = "SELECT UPPER(tx_apellido) || ' '|| UPPER(tx_nombre), tx_cedula
				FROM s01_persona 
				WHERE co_persona = $co_empleado";
				
		
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			while($row = pg_fetch_row($ls_resultado)){
				$x_nombre = $row[0];
				$x_cedula = $row[1];
				
				        
			}
		$newContent= '	<div class="form-group">
							<label class="col-sm-3 control-label no-padding-right">Nombre</label>
							<div class="col-sm-6">
								<input  readonly class="input-sm form-control" name="x_nombre"  value="'.$x_cedula.'"  type="text"  />
							</div>
						</div>';
	}
									
	
	//$json = json_encode($a);
	echo $newContent ;


?>