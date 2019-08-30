<?php
/*----------------------------------------------------------------------------------------------------------------------|
| VERIFICACION Y AUTENTIFICACIÓN DE USUARIO.                                                                            |
|----------------------------------------------------------------------------------------------------------------------*/

	include("comun.php");
	include("sentencia_sql.php");
	include_once('../../config/conexiones/conexiones.php');
	include_once('../../clases/adodb/adodb.inc.php');
		
/*----------------------------------------------------------------------------------------------------------------------|
| VERIFICACION Y AUTENTIFICACIÓN DE USUARIO.                                                                            |
|----------------------------------------------------------------------------------------------------------------------*/ 	
	session_start();
	$usu_autentico= isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI") {
		//si no existe, envio a la página de autentificacion
		session_destroy();
    	echo"<script language='JavaScript' type='text/JavaScript'>location.href='../index.php'</script>";
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
<link href="../../css/pdvsaStyle.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript">
	 function Cancelar(){
         document.frm_anexos.action=document.frm_anexos.ls_pagina_origen.value;
         document.frm_anexos.method = "post";	
         document.frm_anexos.submit();
        }
		 	
	function agregar_file() {
		document.frm_anexos.action="mtto_anexos.php";
		document.frm_anexos.tarea_archivo.value="F";
        document.frm_anexos.method = "post";
        document.frm_anexos.submit();
	}
	
	function elimina_archivo(id) {
			document.frm_anexos.action="mtto_anexos.php";
			document.frm_anexos.tarea_archivo.value="E";
			document.frm_anexos.ls_ruta_archivo.value=id;
        	document.frm_anexos.method = "post";
        	document.frm_anexos.submit();
	}
	
	function Examinar(){
          document.frm_anexos.action="mtto_anexos.php";
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
		
		
	$db = ADONewConnection($bdtype); # eg 'mysql' o 'postgres'
	$db->Connect($dbHost, $dbUser, $dbPassword, $dbName);

/*-----------------------------------------------------------------------------------------
	RUTINA: Obtiene datos de la solicitud de vivienda
------------------------------------------------------------------------------------------*/
	$ls_indicador= strtoupper($_SESSION["usuario"]);
	
	//echo $co_solicita_viviend;
	$ls_criterio = "where SV.co_solicita_viviend = $co_solicita_viviend";
	$ls_sql = fun_cadena_sql(40, $ls_criterio);
	//echo $ls_sql;
	$rs = $db->Execute($ls_sql);
	while($row = $rs->fetchrow()){
		$ls_nu_solicitud = $row[0];
   		$ls_cedula= $row[1];
		$ls_nombre= $row[2];
		$ls_apellido= $row[3];
		$ls_indicador_red= $row[4];
	}
 
/*-----------------------------------------------------------------------------------------
	RUTINA: Elimina los anexos - la ruta de un archivo
------------------------------------------------------------------------------------------*/
	if($tarea_archivo == "E") { 
			$ls_criterio = "co_ruta_archivo = $ls_ruta_archivo";
			$ls_sql = fun_cadena_sql(35, $ls_criterio);
			if($rs = $db->Execute($ls_sql)){
				$msg = "¡Se elimino correctamente el archivo !";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
	}	
?>

</head>
<body>
<form enctype="multipart/form-data"  method="post" name="frm_anexos">
  <table width="100%">
    <tr> 
      <td colspan="6" class="ventana_tit" bgcolor="#EBEBEB">Datos de la solicitud</td>
    </tr>
    <tr align="left"> 
      <td width="25%"  class="cont_bold">Numero de Solicitud: </td>
      <td width="19%"><input name="o_cedula" type="text" class="cont_plain" readonly="true" value="<?php echo $ls_nu_solicitud;?>" size="12" maxlength="8"></td>
      <td width="20%"  class="cont_bold">C&eacute;dula ID:</td>
      <td width="36%"><input name="o_nombre" type="text" class="cont_plain" readonly="true" value="<?php echo $ls_cedula;?>" size="30" maxlength="40"></td>
    </tr>
    <tr align="left"> 
      <td class="cont_bold">Apellido(S):</td>
      <td><input name="o_apellido" type="text" class="cont_plain" readonly="true" value="<?php echo $ls_apellido;?>" size="30" maxlength="40"></td>
      <td class="cont_bold">Nombre(S):</td>
      <td><input name="o_gerencia2" type="text" class="cont_plain" readonly="true" value="<?php echo $ls_nombre;?>" size="30" maxlength="100"></td>
    </tr>
  </table>

  <table width="100%">
    <tr >
      <td align='left' colspan="6">&nbsp;</td>
    </tr>
    <tr > 
      <td align='left' colspan="6" class="ventana_tit" bgcolor="#EBEBEB">archivos 
        adjuntos (Max. 2 Mb)</td>
    </tr>
    <tr align="center"> 
      <td width="29%"><input name="archivo" type="file" id="archivo"></td>
      <td width="12%"> <div align="left"> 
          <input name="boton_enviar" type="button" id="boton" value="Enviar"  onClick="agregar_file();">
        </div></td>
      <td width="49%"><div align="left"> </div></td>
      <td width="12%">&nbsp;</td>
    </tr>
    <tr class="Tabla_encabezado_stm"> 
      <td>Nombre</td>
      <td>Responsable</td>
	  <td>Descargar</td>
	  <td>Eliminar</td>
    </tr>
    <?php // GUARDA UN ARCHIVO EN EL SERVIDOR
	if($tarea_archivo == "F") { 
		if (is_uploaded_file ($HTTP_POST_FILES['archivo']['tmp_name'])) { 
			
			if($HTTP_POST_FILES['archivo']['size'] < 2000000) { 
				$destino = '../anexos' ;
				$nombre_file =  ereg_replace( "([     ]+)", "_", $HTTP_POST_FILES['archivo']['name'] ); 
				copy($HTTP_POST_FILES['archivo']['tmp_name'], $destino . '/' .$nombre_file); 
							
				// Guarda la ruta en la base de datos
				$db = ADONewConnection($bdtype); # eg 'mysql' o 'postgres'
				$db->Connect($dbHost, $dbUser, $dbPassword, $dbName);
				$ls_campos= "'".$destino . '/' .$nombre_file."',".$co_solicita_viviend.",'$ls_indicador'"; 
				$ls_sql= fun_cadena_sql(34, $ls_campos);
				
	
				if($rs = $db->Execute($ls_sql)){
					$msg = "¡Se guardaron correctamente los Datos !";
					echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
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
		  
		$ls_criterio = "co_solicita_viviend = $co_solicita_viviend";
		$ls_sql = fun_cadena_sql(33, $ls_criterio);
		$rs = $db->Execute($ls_sql);
		$li_totcampos = $rs->FieldCount()-1;
		$li_indice = $li_totcampos;
		gestion_archivos($rs,$li_totcampos,$li_indice,"elimina_archivo", "ELIMINA_ARCHIVO", $pagina_mtto);
		$rs->Close(); 
	 ?>
    <tr align="center"> 
      <td colspan="4"> <input name="btn_boton1322" type="button" value="<< Atras" onClick="Cancelar();"> 
      </td>
    </tr>
  </table>
		 <div align="center">
  	<input type="hidden" name="nu_solicitud" value="<?php echo $nu_solicitud;?>">
    <input type="hidden" name="co_solicita_viviend" value="<?php echo $co_solicita_viviend;?>">
    <input type="hidden" name="tarea" value="<?php echo $tarea;?>">
	<input type="hidden" name="tarea_archivo" value="<?php echo $tarea_archivo;?>">
	<input type="hidden" name="ls_ruta_archivo" value="<?php echo $ls_ruta_archivo;?>">
	<input type="hidden" name="co_aprobacion" value="<?php echo $co_aprobacion;?>">
	<input type="hidden" name="ls_pagina_origen" value="<?php echo $ls_pagina_origen;?>">
  </div>
</form>
</body>
</html>
