<?php 
/*-------------------------------------------------------------------------------------------|
|  	Verificación Y Autentificación De Usuario.                           |
|-------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("gan_utilidad.php");
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
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="../../assets/js/ace-extra.min.js"></script>
		
</head>
<body>
<?php
/*-------------------------------------------------------------------------------------------|
|				Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					 |
|-------------------------------------------------------------------------------------------*/
	if (!$_GET)	{
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
	$parametros = "tarea=B";
	$arr_sector    =  Combo_Sector();


/*-------------------------------------------------------------------------------------------|
	ACTUALIZA UN REGISTRO DE LA TABLA GAN_POTRERO
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "B"){
		$error_sql = false;
		$o_potrero = strtoupper($o_potrero);
		$ls_sql = "SELECT pk_potrero FROM gan_potrero 
					WHERE (nb_potrero='$o_potrero') AND pk_potrero <> '$pk_potrero' "; 
		
		//echo $ls_sql;
		$o_potrero =  $o_potrero==''?'NULL':"$o_potrero";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	
       							
				$ls_sql = "UPDATE gan_potrero SET 
								nb_potrero = '".strtoupper($o_potrero)."',           
							    nu_hectareas = '$x_hectarea',
								nu_capacidad = '$x_capacidad',
								tx_ubicacion = '".ucwords($x_comentario)."',
								fk_sector = '$o_sector'  
			 		   WHERE pk_potrero = '$pk_potrero'";
				
				if ($obj_miconexion->fun_consult($ls_sql)== 0)	{
					$error_sql = true;
				}

				$parametros = "tarea=B&pk_potrero=$pk_potrero";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Satisfactoriamente!');location.href='gan_potrero_view.php?$parametros';</script>";

			}else{
				$msg = "¡Nombre Duplicado, Ya Estan Registrados!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$tarea = "M";  // Vuelve A Colocar La Tarea.
	}

	
/*-------------------------------------------------------------------------------------------|
	AGREGA UN POTRERO A LA TABLA GAN_POTRERO
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		
		if($co_ubicacion==''){
			$ls_sql = " SELECT pk_potrero FROM gan_potrero 
						WHERE (nb_potrero) = '".strtoupper($o_potrero)."' AND fk_sector=$o_sector";
						
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
			if($ls_resultado != 0){
				if($obj_miconexion->fun_numregistros() == 0){
		
					$ls_sql = " INSERT INTO gan_potrero (fk_sector, nb_potrero, tx_ubicacion, nu_hectareas, nu_capacidad)	
								VALUES ('$o_sector', '".strtoupper($o_potrero)."','".$x_comentario."','".$x_hectarea."','".$x_capacidad."' )";
				
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado == 0){
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}else{
						echo "<script language='javascript' type='text/javascript'>alert('¡Operacion realizada Exitosamente!');</script>";
					}
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('¡Nombre Duplicado!');</script>";
				}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}else{
			$ls_sql = " UPDATE gan_potrero SET nb_potrero = '".strtoupper($o_potrero)."'
						WHERE pk_potrero = $pk_potrero";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				echo "<script language='javascript' type='text/javascript'>alert('¡Ubicación Actualizada Satisfactoriamente!');</script>";
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}
		$co_ubicacion = '';
		$o_ubicacion = '';
	}
/*-------------------------------------------------------------------------------------------|
| Rutina: Para Eliminar Un Potrero
|-------------------------------------------------------------------------------------------*/	
	if ($tarea == "E"){
		$ls_sql = "SELECT * FROM  gan_ganado WHERE fk_potrero= '$pk_potrero' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM gan_potrero WHERE pk_potrero = '$pk_potrero' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Ubicación Eliminada Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Esta Ubicacón Esta Asociada A Diferentes Productos!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}
/*-------------------------------------------------------------------------------------------|
	LEE DATOS DE UN POTRERO
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql = " SELECT nb_potrero, tx_ubicacion, nu_hectareas, nu_capacidad, fk_sector FROM gan_potrero WHERE pk_potrero = $pk_potrero";	
		
		//echo $ls_sql;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_potrero 	  = $row[0];
			$x_comentario = $row[1];
			$x_hectarea =   $row[2];
			$x_capacidad   =   $row[3];
			$o_sector   =   $row[4];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}

?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Potreros
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
					<div class="space-6"></div>	
								
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> Crear Potrero </h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
												
										<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Sector</label>
												<div class="col-sm-7" >	
													<select name="o_sector" class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Selecciona un Sector...">
														<?php
															if ($o_sector == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_sector as $k => $v) {
																$ls_cadenasel =($k == $o_sector)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>							
													</select>
												</div>													
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_factura">Potrero</label>
												<div class="col-sm-7" >
													<input name="o_potrero" value="<?php echo $o_potrero;?>" type="text" class="form-control" placeholder="Nombre del Potrero">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_factura">Hectareas</label>
												<div class="col-sm-7" >
													<input name="x_hectarea" value="<?php echo $x_hectarea;?>" type="text" class="form-control" onkeypress = "return validardec(event)" placeholder="Numero de Hectareas">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_factura">Capacidad</label>
												<div class="col-sm-7" >
													<input name="x_capacidad" value="<?php echo $x_capacidad;?>" type="text" class="form-control" onkeypress = "return validardec(event)" placeholder="Carga Max.">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Descripcion</label>
												<div class="col-sm-9" >
													<textarea name="x_comentario" cols="10" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_comentario;?></textarea>
												</div>
											</div>
											
											<div class="form-group center">												
												<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
													Regresar
												</button>
												
												<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
									
										
										</div>
										
										<input type="hidden" name="tarea" value="<?php echo $tarea;?>"> 
										<input type="hidden" name="pk_sector" value="<?php echo $pk_sector;?>">
										<input type="hidden" name="pk_potrero" value="<?php echo $pk_potrero;?>">   
											
										</form>
									</div>
								</div>
							</div>
						</div>	
							
				</div>
			
		</div> <!-- /.page-content -->


</body>

		<!--  SISTEMA   -->	
		<script src="../../js/funciones.js"></script>  
		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
			
		<script src="../../assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="../../assets/js/dataTables.tableTools.min.js"></script>

		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
			
		<!-- ace scripts -->
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>



<script type="text/javascript">

	$(document).ready(function () {
		$('.date').datepicker({
		});
		
		window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		if(!ace.vars['touch']) {
			$('.chosen-select').chosen({allow_single_deselect:true}); 
			//resize the chosen on window resize
			$(window)
			.off('resize.chosen')
			.on('resize.chosen', function() {
				$('.chosen-select').each(function() {
					 var $this = $(this);	
					 ancho = $(window).width() < 580 ? 180:350; // Establece el ancho dependiendo de la ventana
					 $this.next().css({'width': ancho});
				})
			}).trigger('resize.chosen');
			//resize chosen on sidebar collapse/expand
			$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
				if(event_name != 'sidebar_collapsed') return;
				$('.chosen-select').each(function() {
					 var $this = $(this);
					 $this.next().css({'width': $this.parent().width()});
				})
			});			
		}
		
		
	});
    
	</script>	
	
	<script type="text/javascript">
	    
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (document.formulario.tarea.value == "M"){
				document.formulario.tarea.value =  "B";
			}else{
				document.formulario.tarea.value =  "A";
			}	
		  document.formulario.action = "gan_potrero_add.php";			
		  document.formulario.method = "POST";
		  document.formulario.submit();
		}
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Eliminar'																		 |
|	Descripción: Permite Eliminar Una Ubicación De La Base De Datos.					 	 |
|-------------------------------------------------------------------------------------------*/		
	function Eliminar_Potrero(identificador){
		if (confirm('¿Realmente Desea Eliminar Esta Ubicación?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.action = "gan_potrero_add.php";
			document.formulario.pk_potrero.value = identificador;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}


	function Atras(parametros){
		location.href = "gan_potrero_view.php?" + parametros;
	}	
	
</script>
 

</html>