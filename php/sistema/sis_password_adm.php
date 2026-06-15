<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: sis_password_adm.php                                                     
	Descripcion: Permite a un usuario con rol administrador cambiar el password de otro usuario
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------|
| VERIFICACION Y AUTENTIFICACIN DE USUARIO.                                                  |
|-------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("sis_utilidad.php");
	$usu_autentico = isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI"){
		session_destroy();
		echo"<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
		exit();
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta charset="UTF-8" />
<title>BellinghieriCosmetic</title>
		
	<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="../../assets/js/ace-extra.min.js"></script>
				
</head>

<body>
<?php 
/*------------------------------------------------------------------------------------------
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
	FUNCIÓN : Actualiza el Password
--------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		if($o_password==$o_confirm){ 
			
			$ls_sql = "UPDATE s01_persona SET co_password = md5('$o_password')           
						WHERE co_persona = '$co_usuario'";
		
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				$msg = "El Password ha sido Guardado Exitosamente!";
				$parametros = "tarea=B&co_usuario=$co_usuario";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg');location.href='sis_usuario.php?$parametros';</script>";
				$obj_miconexion->fun_closepg($li_id_conex); 
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
			}
		}else{
			$msg = "Verifique el Password Ingresado";
			echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg');</script>";
		
		}
	}
	
/*-------------------------------------------------------------------------------------------
	FUNCIÓN : Carga los Datos del Usuario 
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT s01_persona.tx_cedula, s01_persona.tx_nombre
				FROM s01_persona 
				WHERE s01_persona.co_persona = $co_usuario";
						
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		$fila = pg_fetch_row($ls_resultado);
		$o_cedula_repr          = $fila[0];
		$o_nombre_repr          = $fila[1];

	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
	$obj_miconexion->fun_closepg($li_id_conex); 
/*---------------------------------------------------------------------------------------------|
            FIN DE RUTINAS PARA EL MANTENIMIENTO.                                              |
|---------------------------------------------------------------------------------------------*/
?>




<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Cambiar Clave
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> <?php echo $modo;?> </h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main ">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Nombre</label>
												<div class="col-sm-7" >
													<input readonly class="col-xs-10 col-sm-7" name="o_nombre_repr" value="<?php echo $o_nombre_repr." ".$o_apellidos_repr;?>" id="o_nombre_repr" type="text"  placeholder="Ingrese Nro. de Factura">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Cedula</label>
												<div class="col-sm-7" >
													<input readonly class="col-xs-10 col-sm-7" name="o_cedula_repr" value="<?php echo $o_cedula_repr;?>" id="o_cedula_repr" type="text"  placeholder="Ingrese Nro. de Factura">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Password</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_password" type="password" value="<?php echo $o_password;?>" id="o_password" type="text"  placeholder="Password">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Confirm Password</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_confirm" type="password" value="<?php echo $o_confirm;?>" id="o_confirm" type="text"  placeholder="Confirm Password">
												</div>
											</div>
												
											<div class="form-group center">
												<button class="btn btn-danger btn-sm " onClick="Atras()">
													<i class="ace-icon fa 	fa-arrow-left align-top bigger-125 "></i>
													Regresar
												</button>
												
												<button type="button" onClick="Guardar();"class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>
												
												
																								
											</div>
											
											
											
										</div>	
										<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
										<input type="hidden" name="co_usuario" value="<?php echo $co_usuario; ?>">
											
										</form>
									</div>
								</div>
						</div>
					</div>
					
				</div>
			</div> <!-- /.row tabla principal -->
		</div> <!-- /.page-content -->

</body>

<!--  SISTEMA   -->	
	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>


<script language="javascript" type="text/javascript">
	
	function Atras(){
			document.formulario.tarea.value = "X";
			document.formulario.method = "POST";
			document.formulario.action = "sis_usuario.php";
			document.formulario.submit();
	}
	
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			document.formulario.tarea.value = "U";
			document.formulario.method = "POST";
			document.formulario.action = "sis_password_adm.php";
			document.formulario.submit();
		}
	}
	
</script>
</html>