<?php
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/

	session_start();
	include_once ("sis_utilidad.php");
	$usu_autentico= isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI") {
	    session_destroy();
    	echo"<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
	    exit();
	}
	
	

/*----------------------------------------------------------------------------------------------------------------------|
|----------------------------------------------------------------------------------------------------------------------*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ficha de vacunas otras</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="../../css/style.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript">
	function Cancelar(){
        location.href = "sis_usuario.php";
    }
		 	
	function agregar_file() {
		document.frm_anexos.action="sis_usuario_foto.php";
		document.frm_anexos.tarea_archivo.value="F";
        document.frm_anexos.method = "post";
        document.frm_anexos.submit();
	}
	
	function elimina_archivo(id) {
			document.frm_anexos.action="sis_usuario_foto.php";
			document.frm_anexos.tarea_archivo.value="E";
			document.frm_anexos.ls_ruta_archivo.value=id;
        	document.frm_anexos.method = "post";
        	document.frm_anexos.submit();
	}
	
	function Examinar(){
          document.frm_anexos.action="sis_usuario_foto.php";
          document.frm_anexos.method = "post";
          document.frm_anexos.submit();
        }	
			
</script>
	<?php
/*----------------------------------------------------------------------------------------------------------------------|
|                                    INICIO DE RUTINAS PARA EL MANTENIMIENTO.                                           |
|----------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
---------------------------------------------------------------------------------------------------------------------------------*/
 
if (!$_GET){
			foreach($_POST as $nombre_campo => $valor){ 
				$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
				eval($asignacion);	}
		}else{
			foreach($_GET as $nombre_campo => $valor){ 
				$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
				eval($asignacion);	}
		}

		
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);	
	
	
/*-----------------------------------------------------------------------------------------
	RUTINA: Muestra datos del Usuario
------------------------------------------------------------------------------------------*/		
	
	$ls_sql = "SELECT tx_cedula, UPPER(tx_nombre), UPPER(tx_apellido), tx_dir_foto
				FROM s01_persona 	
				WHERE co_persona = '$co_usuario'";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_cedula           = $row[0];
			$o_nombre           = $row[1];
			$o_apellido         = $row[2];
			$x_foto           = $row[3];
			$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}		
 	
	// GUARDA UN ARCHIVO EN EL SERVIDOR
	if($tarea_archivo == "F") { 
		if (is_uploaded_file ($HTTP_POST_FILES['archivo']['tmp_name'])) { 

						
			if($HTTP_POST_FILES['archivo']['size'] < 2000000) { 
				$path = '../foto/' ;
				$nombrefoto = 'foto'.$co_usuario.'.jpg';
				$RutaDestino = $path . $nombrefoto;
				
				$nombre_file =  ereg_replace( "([     ]+)", "_", $HTTP_POST_FILES['archivo']['name'] ); 
				copy($HTTP_POST_FILES['archivo']['tmp_name'], $RutaDestino); 
							
				//$RutaAcceso = 'php/'.'foto'.$co_usuario.'.jpg';		
				
				
				$ls_sql= "UPDATE s01_persona
                            SET tx_dir_foto = '".$nombrefoto."' WHERE co_persona = '$co_usuario'";
	
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if ($ls_resultado != 0){
					$msg = "¡Guardado Exitosamente!.";
					echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}
				$subio = true; 
				
  			} 
 		} 
		if (!$subio){
			$msg = "¡Falla en la carga del archivo !. El archivo no debe exceder de 2 MB";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}
	
	}
	
		// MUESTRA LOS ARCHIVOS ANEXADOS
		


	
	
	
?>

</head>
<body>
<form enctype="multipart/form-data"  method="post" name="frm_anexos">
<table width="100%" align="center">
		<tr class="ventana_tit"> 
			<td height="22" colspan="3">Datos de la Persona</td>
		</tr>
		<tr class='Tabla_fila_claro'> 
			<td width="532" height="38" class="cont_plain" >Cedula:</td>
			<td width="429" class="cont_bold"><?php echo $o_cedula;?></td>
		<td width="252" rowspan="3" class="cont_bold"><img  width="100" height="100" src=" <?php echo '../foto/'.$x_foto; ?>" alt = 'Photo' /></td>
		</tr>
		<tr class="Tabla_fila_blanco"> 
			<td height="31" class="cont_plain">Nombre:</td>
			<td class="cont_bold" ><?php echo $x_foto;?></td>
		</tr>
		<tr class='Tabla_fila_claro'>
			<td height="35" class="cont_plain">Apellido:</td>
			<td class="cont_bold"><?php echo $o_apellido;?></td>
		</tr>
		<tr>
			<td colspan="3" height="20" align="center"><img src="../../img/iconos_pagina/linea_roja.png" width="670" height="1" /></td>
		</tr>
		<tr> 
		<td colspan="3"  align="center" > 
			<input name="btn_atras" type="button" class="asterisco2"<?php echo $ls_style_boton;?> onClick="Cancelar()" value="<< Atras" style="cursor:pointer">
		</td>
	</tr>
	</table>

  <table width="100%">
    
	 <tr align="center"> 
      <td width="29%"><input name="archivo" type="file" id="archivo"></td>
      <td width="12%"> <div align="left"> 
          <input name="boton_enviar" type="button" id="boton" value="Enviar"  onClick="agregar_file();">
        </div></td>
      <td><div align="left"> </div></td>
    </tr>
    <tr align="center">
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr align="center">
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr align="center"> 
      <td colspan="3">&nbsp;      </td>
    </tr>
    <tr align="center">
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
		 <div align="center">
  	<input type="hidden" name="nu_solicitud" value="<?php echo $nu_solicitud;?>">
    <input type="hidden" name="co_solicita_viviend" value="<?php echo $co_solicita_viviend;?>">
    <input type="hidden" name="tarea" value="<?php echo $tarea;?>">
	<input type="hidden" name="tarea_archivo" value="<?php echo $tarea_archivo;?>">
	<input type="hidden" name="ls_ruta_archivo" value="<?php echo $ls_ruta_archivo;?>">
	<input type="hidden" name="co_usuario" value="<?php echo $co_usuario;?>">
	<input type="hidden" name="ls_pagina_origen" value="<?php echo $ls_pagina_origen;?>">
  </div>
</form>
</body>
</html>
