<?php
/*------------------------------------------------------------------------------------------
	Nombre: sesion.php                                                    
	Descripcion: contiene las funcionalidades para crear una sesin aun usuario y permitir 
	acceso a la Aplicacin
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("clases/clspostgres.php");
	
/*-------------------------------------------------------------------------------------------
	INICIALIZACION: asigna los valores iniciales a las VARIABLES DE SESION
--------------------------------------------------------------------------------------------*/
	$_SESSION["gs_inivitado"]   = "N";  /*no invitado*/
	$_SESSION["autentificado"]  = "NO"; /*usuario autentificado*/
	$_SESSION["li_cod_usuario"] = "";   /*codigo del usuario*/
	$_SESSION["menu"]           = "";   /*arreglo del menu*/
	$_SESSION["gs_usuario"]     = "";   /*nombre y apellido del usuario*/
	$_SESSION["usuario"]        = "";   /*indicador del usuario*/
	$_SESSION["num_mensaje"]        = 0;   /*indicador del usuario*/

	$o_usuario = isset($_POST['o_usuario'])?$_POST['o_usuario']:'';
	$o_clave =  isset($_POST['o_clave'])?$_POST['o_clave']:'';

	?>
<!DOCTYPE html>
<html>
<head>
<title>Inicio de Sesion</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script language="JavaScript" type="text/JavaScript">

	function ir(){
		document.formulario.action = "interface.php";
		document.formulario.submit();
	}
	
</script>
</head>
<body>
<form name="formulario">
<?php 	
/*--------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
---------------------------------------------------------------------------------------*/
	if (!$_GET){
		foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}else{
		foreach($_GET as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*--------------------------------------------------------------------------------------
	Limpia el Indicador 
---------------------------------------------------------------------------------------*/
	
	$o_usuario = trim($o_usuario);

/*--------------------------------------------------------------------------------------
	Realiza la validacion de Ingreso al Sistema
---------------------------------------------------------------------------------------*/
	$sw=false;
	if(dir_local($o_usuario,$o_clave)){ // Valida contra la Base de Datos de sistema
		$sw=true;
	}

/*--------------------------------------------------------------------------------------
	Obtiene datos del Usuario
---------------------------------------------------------------------------------------*/
	$co_usuario  = -1;
	if($sw==true)	$co_usuario = get_nombre_usuarios($o_usuario);

/*--------------------------------------------------------------------------------------
	Si esta en el Sistema ---> INGRESA
---------------------------------------------------------------------------------------*/
	if($co_usuario>0){
				
		/*------------------------------------------------------------------------|
			Guarda los datos de inicio de Sesion en la Bitacora
		|------------------------------------------------------------------------*/
		$fecha = date('Y/m/d H:i');
		$ls_sql = "INSERT INTO t11_bitacora(co_persona,fe_fecha,tx_tabla,tx_accion,tx_sql) 
						VALUES ($co_usuario,'$fecha','INICIO DE SESION','L','".strtoupper($o_usuario)."')";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			//Correcto
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF]);
		}
		
		/*------------------------------------------------------------------------|
			Carga Variables de Sesion
		|------------------------------------------------------------------------*/
		$_SESSION["gs_inivitado"] == "S";
		$_SESSION["autentificado"] = "SI";/*si pasa las pruebas se dice q es usuario registrado*/
		$_SESSION["usuario"] = $o_usuario;/*indicador del usuario*/
		$_SESSION["li_cod_usuario"] = $co_usuario;/*codigo que apunta al usuario en bd personas*/
		$_SESSION["menu"] = Cargar_Menu($co_usuario);/*cargo el menu del usuario*/
		
		echo"<script language='JavaScript' type='text/JavaScript'> ir();</script>";	
	}else{
		$_SESSION["autentificado"] = "NO";
		echo"<script language='JavaScript' type='text/JavaScript'> location.href='html/error_login.html'</script>";
	}


/*--------------------------------------------------------------------------------------
	FUNCIN dir_local:	funcion que se usa para validar contra la Base de Datos 
		del Sistema sistema 
---------------------------------------------------------------------------------------*/
function dir_local($uid,$pwd){
	$obj_conexion = fun_crear_objeto_conexion();
    $li_id_conex = fun_conexion($obj_conexion);
	
	$uid= strtoupper($uid);
	$ls_sql = "SELECT co_password FROM s01_persona 
				WHERE UPPER(tx_indicador) = '$uid' AND co_password = MD5('$pwd') AND in_activo = 'S'";
	
	$ls_resultado =  $obj_conexion->fun_consult($ls_sql);/*consulta en la tabla personas*/
	if ($ls_resultado != 0){
		if ($obj_conexion->fun_numregistros() != 0){/*encontrado... **/
			return true;
		}else{
			return false;
		}
	}
}


/*--------------------------------------------------------------------------------------
	FUNCIN chequeo_usuario_bd_sistema para verificar si un usuario esta en las bd de 
		sistema especificamente
---------------------------------------------------------------------------------------*/
function get_nombre_usuarios($o_usuario){
	$nombre_apellido_usuario = "";
	$co_usuario = -1;
	$o_usuario = strtoupper($o_usuario);
	
	$obj_conexion = fun_crear_objeto_conexion();
    $li_id_conex = fun_conexion($obj_conexion);

	$ls_sql = "SELECT co_persona,tx_nombre,tx_apellido 
				FROM s01_persona 
				WHERE UPPER(tx_indicador) = '$o_usuario' AND in_activo = 'S'";

	$ls_resultado =  $obj_conexion->fun_consult($ls_sql);
	
	if ($ls_resultado != 0){
		if ($obj_conexion->fun_numregistros() != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$co_usuario = $row[0];
			$nombre_apellido_usuario = $row[1]." ".$row[2];
		}
	}else{
		$obj_conexion->fun_closepg($li_id_conex,$ls_resultado);
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF]);
	}
	
	$_SESSION["gs_usuario"] = $nombre_apellido_usuario;
	return $co_usuario;
}

/*--------------------------------------------------------------------------------------
	FUNCIN Cargar_Menu: se encarga de guardar en un arreglo los menus que la aplicacin
		va a posser segun el rol que possea  el usuario, en caso de no estar registrado 
		o no posseer rol, se le asigna uno por default, que es invitado
---------------------------------------------------------------------------------------*/
function Cargar_Menu($co_usuario){
	$obj_conexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_conexion);
	
	if ($li_id_conex != 0){
	
		$ls_sql = "SELECT DISTINCT s05_menu_padre.co_menu_padre, s06_menu_padre_hijo.tx_submenu,
						s06_menu_padre_hijo.tx_pagina, s06_menu_padre_hijo.tx_icono, s05_menu_padre.nu_orden,	s06_menu_padre_hijo.nu_orden 
					FROM s03_privilegio 
						INNER JOIN (s06_menu_padre_hijo INNER JOIN s05_menu_padre ON s06_menu_padre_hijo.co_menu_padre = s05_menu_padre.co_menu_padre)
						ON s06_menu_padre_hijo.co_menu_padre_hijo= s03_privilegio.co_menu_padre_hijo
						INNER JOIN (s04_rol INNER JOIN s02_persona_rol ON s04_rol.co_rol = s02_persona_rol.co_rol)
						ON s03_privilegio.co_rol= s04_rol.co_rol
					WHERE s02_persona_rol.co_persona = $co_usuario	AND s06_menu_padre_hijo.in_activo = 'S'		
					ORDER BY s05_menu_padre.nu_orden ASC, s06_menu_padre_hijo.nu_orden ASC";
		
		$ls_resultado =  $obj_conexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if ($obj_conexion->fun_numregistros() != 0){
				$i=0;
				while($fila = pg_fetch_row($ls_resultado)){
					$arr_menu[$i][0]=$fila[0];//co padre
					$arr_menu[$i][1]=$fila[1];//nombre hijo
					$arr_menu[$i][2]=$fila[2];//direccion de la pag.
					$arr_menu[$i][3]=$fila[3];//icono
					$i++;
				}
			}else{
				$_SESSION["autentificado"] = "NO";
				echo"<script language='JavaScript' type='text/JavaScript'> location.href='html/error_permiso.html'</script>";				
				
			}
		}else{
			$obj_conexion->fun_closepg($li_id_conex,$ls_resultado);
	    	fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF]);
    	    exit;
		}	
	}else{
		$obj_conexion->fun_closepg($li_id_conex,$ls_resultado);
    	fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF]);
        exit;
	}
	return $arr_menu;
}
?>
</form>
</body>
</html>
