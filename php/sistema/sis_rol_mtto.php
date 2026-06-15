<?php
/*-------------------------------------------------------------------------------------------
	Nombre: sis_rol_privilegio.php                                            
	Descripcion: Esta interfaz permite AGREGAR/MODIFICAR un nombre o descripcion de ROL 
--------------------------------------------------------------------------------------------*/

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

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>BellinghieriCosmetic</title>
		
	<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	
	<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/chosen.min.css" /> -->
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/bootstrap-timepicker.min.css" /> -->
		<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.min.css" /> -->
		<!-- <link rel="stylesheet" href="../../assets/css/colorpicker.min.css" /> -->

	<!-- text fonts -->
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="../../assets/js/ace-extra.min.js"></script>
				
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
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

/*-------------------------------------------------------------------------------------------
	RUTINA: se procede a cargar el nombre del rol a Editar.
--------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT  tx_rol, tx_descripcion_rol FROM s04_rol WHERE co_rol = '$co_rol' ";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			while ($row = pg_fetch_row($ls_resultado)){
				$o_nombre_rol  = $row[0];
				$o_descripcion = $row[1];
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINA: MODIFICAR el nombre del Rol
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql = "SELECT *FROM s04_rol WHERE UPPER(tx_rol) = UPPER('$o_nombre_rol')  and co_rol <> '$co_rol'";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){
				
				$ls_campos = " tx_rol = '$o_nombre_rol' ";
				$ls_sql = "UPDATE s04_rol SET 
							tx_rol = '$o_nombre_rol', 
							tx_descripcion_rol = '$o_descripcion'
				WHERE co_rol = '$co_rol'";
				$ls_sql = strtoupper($ls_sql);
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if ($ls_resultado != 0){
					$msg = "Guardado Exitosamente!.";
					echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href = 'sis_rol.php';</script>";
				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}
			}else{
				$msg = "Ya un rol posee este mismo nombre!.";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";			
				$tarea = "E";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINA: AGREGAR un nuevo rol.
-------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		$ls_sql = "SELECT *FROM s04_rol WHERE UPPER(tx_rol) = UPPER('$o_nombre_rol')";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){
				
				$ls_campos = " '$o_nombre_rol' ";
				$ls_sql = "INSERT INTO s04_rol(tx_rol, tx_descripcion_rol)
					VALUES('$o_nombre_rol' , '$o_descripcion')";
								
				$ls_sql = strtoupper($ls_sql);
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if ($ls_resultado != 0){
					$msg = "Guardado Exitosamente!.";
					echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href = 'sis_rol.php';</script>";
				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}
			}else{
				$msg = "Ya un rol posee este mismo nombre!.";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";			
				$tarea = "N";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}
	
	$obj_miconexion->fun_closepg($li_id_conex);
	
/*-------------------------------------------------------------------------------------------
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.     
|------------------------------------------------------------------------------------------*/
?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			<?php echo  "Modificar ROL";?>
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">		
		
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> Rol </h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
										
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_nombre_rol">Nombre Rol</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_nombre_rol" value="<?php echo $o_nombre_rol;?>" id="o_nombre_rol" type="text"  placeholder="Nombre del Rol">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" for="o_descripcion" >Descripcion</label>
												<div class="col-sm-9" >
													<textarea name="o_descripcion" cols="10" id="o_descripcion" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $o_descripcion;?></textarea>
												</div>
											</div>								
										
										
											<div class="form-group center">												
												<button type="button" onClick="location.href = 'sis_rol.php'"  class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
													Regresar
												</button>
												
												<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
											
											<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
											<input name="co_rol" type="hidden" value="<?php echo $co_rol;?>">
										</form>
									</div>
								</div>
						</div>
					</div>
					
		
					</div> <!-- /.row tabla principal -->
		</div> <!-- /.page-content -->
	</div> <!-- /.page-content -->
</div> <!-- /.page-content -->

</body>



	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/jquery.autosize.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>


	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
		} );
	</script>


				
<script src="../../js/funciones.js"></script>  
<script type="text/javascript">

	//SCRIPT: Invoca a esta misma pagina para Agregar o Editar un nombre de ROL
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (document.formulario.tarea.value == "E"){/*editar*/
				document.formulario.tarea.value = "M";
			}else{
				document.formulario.tarea.value = "A";
			}
			document.formulario.action = "sis_rol_mtto.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
	}
	
	function set_vacio(items){
		str = "document.formulario."+items+".value = ''";
		eval(str);
		str = "document.formulario."+items+".focus()";
		eval(str);
	}
	
</script>

</html>