<?php 
	/**
	* Pagina invokada desde Jquery Ajax para Mostrar una vista preliminar de los Datos de la Variable Medidor
	*
	* @param $obj_miconexion: instancia de la clase clspostgres para las operaciones CRUD sobre la 
	* base de datos que se deben ejecutar segun la tarea.
	*
	*/
	include_once ("sis_utilidad.php");
	$menu = $_POST["o_menu"];
	$newContent = '';


	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);



	$newContent = "<select name='o_sub_menu' class='col-xs-10 col-sm-7' >";

		if ($menu == 0){
			$newContent.= "<option value='' selected>Seleccionar --></option>";
		}else{
			$newContent.= "<option value=''>Seleccionar --></option>";
		}
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	$ls_sql = "SELECT  tx_submenu , co_menu_padre_hijo 
					FROM s06_menu_padre_hijo 
					WHERE co_menu_padre = '$menu'
					ORDER BY nu_orden ";
					
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		while($row = pg_fetch_row($ls_resultado)){
			$k = $row[0];
			$v = $row[1];
			$ls_cadenasel =($v == $menu)?'selected':'';
			$newContent.= "<option value='$v' $ls_cadenasel>$k</option>";                
		}
	$newContent.= "</select>";
	
	echo $newContent ;

?>
