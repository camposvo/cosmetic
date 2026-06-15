<?php 
	include_once ("mae_utilidad.php");

	$co_categoria = $_POST["elegido"];
	$co_clase = $_POST["clasificacion"];

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	if ($co_clase == 0){
		$newContent.= "<option value='' selected></option>";
	}else{
		$newContent.= "<option value=''></option>";
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

	$ls_sql = " SELECT nb_clase, pk_clase FROM t05_clase WHERE fk_categoria = '$co_categoria' ORDER BY nb_clase ASC";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		while($row = pg_fetch_row($ls_resultado)){
			$k = $row[0];
			$v = $row[1];
			$ls_cadenasel =($v == $co_clase)?'selected':'';
			$newContent.= "<option value='$v' $ls_cadenasel>$k</option>";                
		}
	//$newContent.= "</select>";
	//$newContent = "hola";
	//$json = json_encode($a);
	echo $newContent ;


?>
