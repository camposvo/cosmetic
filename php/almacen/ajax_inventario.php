<?php 
	include_once ("alm_utilidad.php");

	$co_almacen = $_POST["elegido"];

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	if ($co_ubicacion == 0){
				$newContent.= "<option value='' selected>Seleccionar --></option>";
			}else{
				$newContent.= "<option value=''>Seleccionar --></option>";
			}
		$obj_miconexion = fun_crear_objeto_conexion();
		$li_id_conex = fun_conexion($obj_miconexion);
		$ls_sql = " SELECT nb_ubicacion, pk_ubicacion FROM t10_ubicacion WHERE fk_almacen = '$co_almacen' ORDER BY nb_ubicacion ASC";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			while($row = pg_fetch_row($ls_resultado)){
				$k = $row[0];
				$v = $row[1];
				$ls_cadenasel =($v == $co_ubicacion)?'selected':'';
				$newContent.= "<option value='$v' $ls_cadenasel>$k</option>";                
			}
		$newContent.= "</select>";
	
	//$json = json_encode($a);
	echo $newContent ;


?>
