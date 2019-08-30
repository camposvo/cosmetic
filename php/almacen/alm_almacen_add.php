<?php 
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("alm_utilidad.php");
	$usu_autentico = isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI"){
		session_destroy();
		echo"<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
		exit();
	}
?>
<!DOCTYPE html>
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
/*------------------------------------------------------------------------------------------|
|					Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 				|
|------------------------------------------------------------------------------------------*/
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
/*------------------------------------------------------------------------------------------|
|							Rutina: Insertar Un Nuevo Almacén.							    |
|------------------------------------------------------------------------------------------*/	
	if($tarea == "G"){
		$ls_sql ="SELECT pk_almacen FROM t09_almacen 
				  WHERE UPPER(nb_almacen) = '".strtoupper($o_nombre)."'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){ // Si El Nombre No Existe Procede A Insertar.
       			
				if( $obj_miconexion->fun_consult(" BEGIN TRANSACTION ") == 0){
					$error_sql = true;
				}
				$ls_sql = "INSERT INTO t09_almacen(nb_almacen, tx_descripcion) 
							VALUES('".ucwords($o_nombre)."', '".ucwords($x_direccion)."')";			
				if($obj_miconexion->fun_consult($ls_sql) == 0){
					$error_sql = true;
				}
				
				
				if(!$error_sql){
					$ls_resultado =  $obj_miconexion->fun_consult(" COMMIT ");
					$parametros = "tarea=X";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Almacén Ingresado Satisfactoriamente!');location.href='alm_almacen_add.php?$parametros';</script>";										
				}else{
					$ls_resultado =  $obj_miconexion->fun_consult(" ROLLBACK ");
					$msg = "¡Error En La Consulta!";
					echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
				}
			}else{
				$msg = "¡El Nombre Del Almacén, Ya Estan Registrados!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
				$tarea = "G";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}
	$obj_miconexion->fun_closepg($li_id_conex);
	$parametros = !isset($co_almacen)?'tarea=X':'co_almacen='.$co_almacen.'&tarea=B';
	
/*--------------------------------------------------------------------------------------------------|
|          						Fin De Rutinas Para El Mantenimiento.         	                 	|
|--------------------------------------------------------------------------------------------------*/
?>



<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			Almacen
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
							<div class="widget-main">
								<form class="form-horizontal" name="formulario">
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >Nombre del Almacen</label>
										<div class="col-sm-7" >
											<input class="col-xs-10 col-sm-7" name="o_nombre" value="<?php echo $o_nombre;?>" type="text"  placeholder="Nombre del Almacen">
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right">Descripcion</label>
										<div class="col-sm-9" >
											<textarea name="x_direccion" cols="50" id="x_direccion" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_direccion;?></textarea>
										</div>
									</div>					
									
									
								
									<div class="form-group center">												
										<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
											<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
											Regresar
										</button>
										
										<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
											<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
											Guardar
										</button>																								
									</div>
									
									

									<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
									<input type="hidden" name="co_almacen" value="<?php echo $co_almacen; ?>">
									
								</form>
							</div>
						</div>
					</div>
				</div>			
		
				<div class="space-4"></div>
				
			</div>
		</div> <!-- /.row tabla principal -->
	</div> <!-- /.page-content -->

</div>
</body>

<!--  SISTEMA   -->	
<script src="../../js/funciones.js"></script>  
<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		
	<script src="../../assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/dataTables.tableTools.min.js"></script>
	<!-- <script src="../../assets/js/dataTables.colVis.min.js"></script> -->
	<script src="../../assets/js/daterangepicker.min.js"></script>

	
<!-- page specific plugin scripts -->
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>



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
			
			} );
			
		</script>
				
				

<script type="text/javascript">
	function Guardar(){
	if(campos_blancos(document.formulario) == false){
		if (confirm('¿Esta Conforme Con Los Datos Ingresados?') == true){	
			document.formulario.tarea.value = "G";
			document.formulario.action = "alm_almacen_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
			}
		}
	}
	
/*-------------------------------------------------------------------------------------------|
|	Función: 'Atras'															 	  		 |
|	Descripción: Permite Regresar A La Página De Maestro De Almacenes.						 |
|-------------------------------------------------------------------------------------------*/
	function Atras(parametros){
		location.href = "alm_almacen_view.php?" + parametros;
	}	
	
</script>
</html>