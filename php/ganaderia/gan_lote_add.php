<?php 
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("gan_utilidad.php");
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
<title>BellinghieriCosmetic</title>
			
	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />	
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>	
				
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------|
|	Rutina: Se Utiliza Para Recibir Las Variables Por La URL.                 
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
|	Rutina: Mostrar Los Datos Del Proveedor A Editar En La Interfaz.			 
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT nb_lote, tx_descripcion_lote
				   FROM   gan_lote
				   WHERE pk_lote = '$pk_lote'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado != 0){
			$row           = pg_fetch_row($ls_resultado,0);
			$o_lote      = $row[0];
			$x_comentario  = $row[1];
			
		$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}		
	}
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Actualizo Los Datos Del Proveedor Una Vez Editados.	
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "B"){
		$error_sql = false;
		$o_lote = strtoupper($o_lote);
		$ls_sql = "SELECT pk_lote FROM gan_lote 
					WHERE (nb_lote='$o_lote' AND pk_lote <> '$pk_lote')"; 
		
		$o_lote =  $o_lote==''?'NULL':"$o_lote";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	
       							
				$ls_sql = "UPDATE gan_lote SET 
								nb_lote = '".strtoupper($o_lote)."',           
								tx_descripcion_lote = '".ucwords($x_comentario)."'								
				 		   WHERE pk_lote = '$pk_lote'";
				
				if ($obj_miconexion->fun_consult($ls_sql)== 0)	{
					$error_sql = true;
				}

				$parametros = "tarea=B&pk_lote=$pk_lote";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Satisfactoriamente!');location.href='gan_lote_view.php?$parametros';</script>";

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
		
		$ls_sql ="SELECT pk_lote FROM gan_lote WHERE nb_lote ='$o_lote' or UPPER(nb_lote) = '".strtoupper($o_lote)."'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){ // Si La Cedula No Existe Procede A Insertar.
				
				
				$ls_sql = "INSERT INTO gan_lote(nb_lote, tx_descripcion_lote) 
							VALUES('".strtoupper($o_lote)."','".strtoupper($x_comentario)."')";			
				
				echo $ls_sql; 
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);	
				if ($ls_resultado != 0){
					$parametros = "tarea=B";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Ingresados Satisfactoriamente!');location.href='gan_lote_view.php?$parametros';</script>";

				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
				}
								
			}else{
				$msg = "¡El Nombre, Ya Estan Registrados!";
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
					Lote
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="right" 
					data-content="Agrupacion de animales por criterios especificos, pueden incluir animales que fisicamente se encuentren en Sectores y
									Potreros diferentes" 
					title="Lote">?
				</span>
									<h4 class="widget-title">Crear Lotes</h4>
								
								</div>
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Nombre</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_lote" value="<?php echo $o_lote;?>"  type="text"  placeholder="Ingrese Nombre">
												</div>
											</div>  											
															
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Comentario</label>
												<div class="col-sm-9" >
													<textarea name="x_comentario" cols="10" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_comentario;?></textarea>
												</div>
											</div>
											
											
										</div>	
										
										<div class="form-group center">												
											<button type="button" onClick="location.href = 'gan_lote_view.php'" class="btn btn-sm  btn-danger">
												<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
												Regresar
											</button>
											
											<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
												<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
												Guardar
											</button>																								
										</div>
											
										<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
										<input type="hidden" name="pk_lote" value="<?php echo $pk_lote; ?>">
										
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



<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			/* Tooltip */
			$('[data-rel=tooltip]').tooltip({container:'body'});
			$('[data-rel=popover]').popover({container:'body'});
		
		} );
		
	</script>
				
				

<script type="text/javascript">

	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (document.formulario.tarea.value == "M"){
				document.formulario.tarea.value =  "B";
			}else{
				document.formulario.tarea.value =  "A";
			}	
			document.formulario.method = "POST";
			document.formulario.action = "gan_lote_add.php";
			document.formulario.submit();
		}
	}
	
</script>
</html>