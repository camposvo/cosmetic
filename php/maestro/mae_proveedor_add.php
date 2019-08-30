<?php 
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("mae_utilidad.php");
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
<title>La Peperana</title>
			
	<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />	
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="../../assets/js/ace-extra.min.js"></script>
		
				
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------|
|					Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 			     |
|-------------------------------------------------------------------------------------------*/
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
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Mostrar Los Datos Del Proveedor A Editar En La Interfaz.			 |
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT nb_proveedor, tx_rif, tx_telefono, tx_direccion, tx_correo, tx_sitio_web
				   FROM   t03_proveedor
				   WHERE pk_proveedor = '$pk_proveedor'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado != 0){
			$row           = pg_fetch_row($ls_resultado,0);
			$o_nombre      = $row[0];
			$o_rif         = $row[1];
			$x_telefono    = $row[2];
			$x_direccion   = $row[3];
			$x_correo      = $row[4];
			$x_web         = $row[5];
			
		$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}		
	}
	
/*-------------------------------------------------------------------------------------------|
|				Rutina: Actualizo Los Datos Del Proveedor Una Vez Editados.					 |
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "B"){
		$error_sql = false;
		$o_nombre = $o_nombre;
		$ls_sql = "SELECT pk_proveedor FROM t03_proveedor 
					WHERE (tx_rif='$o_rif' OR UPPER(nb_proveedor)='$o_nombre' ) AND pk_proveedor <> '$pk_proveedor' "; 
		
		$o_nombre =  $o_nombre==''?'NULL':"$o_nombre";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	
       							
				$ls_sql = "UPDATE t03_proveedor SET 
								nb_proveedor = '".$o_nombre."',           
								tx_rif = '".strtoupper($o_rif)."',           
								tx_telefono = '$x_telefono',
								tx_direccion = '".ucwords($x_direccion)."',
								tx_correo = '$x_correo',   
								tx_sitio_web = '$x_web'        
				 		   WHERE pk_proveedor = '$pk_proveedor'";
				
				if ($obj_miconexion->fun_consult($ls_sql)== 0)	{
					$error_sql = true;
				}

				$parametros = "tarea=B&pk_proveedor=$pk_proveedor";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Satisfactoriamente!');location.href='mae_proveedor.php?$parametros';</script>";

			}else{
				$msg = "¡El Rif y/o Nombre Del Proveedor, Ya Estan Registrados!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$tarea = "M";  // Vuelve A Colocar La Tarea.
	}
/*-------------------------------------------------------------------------------------------|
|							Rutina: Insertar Un Nuevo Proveedor.							 |
|-------------------------------------------------------------------------------------------*/		
	if($tarea == "A"){
		$ls_sql ="SELECT pk_proveedor FROM t03_proveedor WHERE tx_rif='$o_rif' or UPPER(nb_proveedor) = '".strtoupper($o_nombre)."'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){ // Si La Cedula No Existe Procede A Insertar.
				
				
				$ls_sql = "INSERT INTO t03_proveedor(nb_proveedor, tx_rif, tx_telefono, tx_direccion, tx_correo, tx_sitio_web) 
							VALUES('".$o_nombre."','".strtoupper($o_rif)."','$x_telefono', '".ucwords($x_direccion)."', '$x_correo','$x_web')";			
				
				//echo $ls_sql; 
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);	
				if ($ls_resultado != 0){
					$parametros = "tarea=B&pk_proveedor=$pk_proveedor";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Ingresados Satisfactoriamente!');location.href='mae_proveedor.php?$parametros';</script>";

				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
				}
				
				
				
			}else{
				$msg = "¡El Rif y/o Nombre Del Proveedor, Ya Estan Registrados!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
				$tarea = "A";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}
	$obj_miconexion->fun_closepg($li_id_conex);
	$parametros = !isset($pk_proveedor)?'tarea=X':'pk_proveedor='.$pk_proveedor.'&tarea=B';

?>


<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Proveedor
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title">Crear Proveedor</h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_nombre">Nombre</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_nombre" value="<?php echo $o_nombre;?>" id="o_nombre" type="text"  placeholder="Ingrese Nombre">
												</div>
											</div>  
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="x_correo">Correo</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="x_correo" value="<?php echo $x_correo;?>" id="x_correo" type="text"  placeholder="Correo">
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" for="form-field-phone">Telefono</label>

												<div class="col-sm-9">
													<span class="input-icon input-icon-right">
														<input class="input-medium input-mask-phone" name="x_telefono" value="<?php echo $x_telefono;?>" type="text" id="form-field-phone" />
														<i class="ace-icon fa fa-phone fa-flip-horizontal"></i>
													</span>
												</div>
											</div>
											
																						
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="x_web">Web</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="x_web" value="<?php echo $x_web;?>" id="x_web" type="text"  placeholder="Web">
												</div>
											</div> 
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_rif">RIF</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_rif" value="<?php echo $o_rif;?>" id="o_rif" type="text"  placeholder="RIF">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Direccion</label>
												<div class="col-sm-9" >
													<textarea name="x_direccion" cols="10" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_direccion;?></textarea>
												</div>
											</div>
											
											
										</div>	
										
										<div class="form-group center">												
											<button type="button" onClick="location.href = 'mae_proveedor.php'" class="btn btn-sm  btn-danger">
												<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
												Regresar
											</button>
											
											<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
												<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
												Guardar
											</button>																								
										</div>
											
										<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
										<input type="hidden" name="pk_proveedor" value="<?php echo $pk_proveedor; ?>">
										
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
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/chosen.jquery.min.js"></script>
	<!-- Mascara telefono -->
	<script src="../../assets/js/jquery.maskedinput.min.js"></script> 



<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
		
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			//show datepicker when clicking on the icon
			.next().on(ace.click_event, function(){
				$(this).prev().focus();
			});
			
				// Mascarra Telefono
			$('.input-mask-phone').mask('(9999) 999-9999');
			
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
		} );
		
	</script>
				
				

<script type="text/javascript">
	
/*------------------------------------------------------------------------------------------------|
|	Función 1: 'Guardar'															 	  		  |
|	Descripción: Permite Guardar Los Cambios Echos En El Formulario y/o Guardar Nuevos Proveedores|
|------------------------------------------------------------------------------------------------*/			
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (document.formulario.tarea.value == "M"){
				document.formulario.tarea.value =  "B";
			}else{
				document.formulario.tarea.value =  "A";
			}	
			document.formulario.method = "POST";
			document.formulario.action = "mae_proveedor_add.php";
			document.formulario.submit();
		}
	}
	
</script>
</html>