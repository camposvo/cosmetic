<?php 

	session_start();
	include_once ("pro_utilidad.php");
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
<title>La Peperana</title>
		
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
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$o_cantidad  = 0;
	$o_cantidad2 = 0;
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nuevo Registro';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	
	/*-------------------------------------------------------------------------------------------
	ELIMINA UN EVENTO DEL PROYECTO
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "UPDATE t18_evento
				   SET in_evento_activo='N'
				WHERE pk_evento = '$x_evento' ";
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}	
	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: permite MOSTRAR LOS DATOS
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT tx_nombre, to_char(fe_inicial, 'dd/mm/yyyy') , nu_cantidad, fk_responsable, 
				t02_proyecto.tx_descripcion, fk_tipo_rubro, nu_muerte, t08_tipo_proyecto.nb_tipo_rubro
		FROM t02_proyecto
		LEFT JOIN t08_tipo_proyecto ON t02_proyecto.fk_tipo_rubro = t08_tipo_proyecto.pk_tipo_rubro
		WHERE pk_proyecto = $pk_proyecto";
		
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$o_nombre        = $row[0];
		$x_fecha	     = $row[1];
		$o_cantidad  	 = $row[2];	
		$x_responsable   = $row[3];
		$o_descripcion   = $row[4];
		$o_tipo_rubro    = $row[5];
		$x_muerte        = $row[6];
		$x_tipo_rubro  = $row[7];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}


	
/*-------------------------------------------------------------------------------------------
	RUTINAS: PARA AGREGAR UN EVENTO
-------------------------------------------------------------------------------------------*/
	
	if ($tarea == "I"){
		
		$ls_sql = "INSERT INTO t18_evento(
				fe_evento, fe_registro_evento, fk_persona, tx_descripcion, 	fk_proyecto
			)
		VALUES (
			'$o_fecha', now(), $co_usuario, '$o_registro',	$pk_proyecto
			);";
						
		//echo $ls_sql;
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Operacion realizada Exitosamente');</script>";
		}
		$tarea = 'A';
	}

	if ($tarea == "U"){
		
		$ls_sql = "INSERT INTO t18_evento(
				fe_evento, fe_registro_evento, fk_persona, tx_descripcion, 	fk_proyecto
			)
		VALUES (
			'$o_fecha', now(), $co_usuario, '$o_registro',	$pk_proyecto
			);";
						
		//echo $ls_sql;
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Operacion realizada Exitosamente');</script>";
		}
		$tarea = 'A';
	}

	
	
	
/*-------------------------------------------------------------------------------------------
	PARA MOSTRAR LOS ULTIMO EVENTOS
-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT to_char(fe_evento, 'dd/mm/yyyy'), UPPER(RESPONSABLE.tx_nombre||' '|| RESPONSABLE.tx_apellido) as NombreDestino , tx_descripcion, pk_evento
				FROM t18_evento
			INNER JOIN s01_persona as RESPONSABLE ON RESPONSABLE.co_persona  = t18_evento.fk_persona	
			WHERE fk_proyecto = $pk_proyecto and in_evento_activo = 'S'
			ORDER BY fe_evento DESC";
		
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		/*while($row = pg_fetch_row($ls_resultado)){
			$historico = $historico.'['.$row[0].'] ['.$row[1]."]\n".strtoupper($row[2]). "\n\n ";
			//$historico = $historico + $row[3];
		}*/
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>



<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			<?php echo  "Notas";?>
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
			
			<!-- PAGE CONTENT BEGINS -->
			
			<div class="row">
				<div class="col-xs-12 ">
						<div class="alert alert-block alert-success">							
							<i class="ace-icon fa fa-check green"></i>							
							<strong class="green">
								<?php echo strtoupper($o_nombre);?>(<?php echo $x_fecha;?>)
							</strong>,
							<?php echo $o_descripcion;?>.
						</div>
				</div>
			</div>
							
		
			<div class="row">
				<div class="col-xs-12 col-sm-12 widget-container-col">
					<div class="widget-box ">
						
			
						<div class="widget-body">
							<div class="widget-main">
								<form class="form-horizontal" name="formulario">
									<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha</label>
												<div class="col-sm-4" >	
													<div class="input-group">
														<input name="o_fecha" value="<?php echo $o_fecha;?>" class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd/mm/yyyy" readonly />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>	
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right">Nota</label>
										<div class="col-sm-9" >
											<textarea name="o_registro" cols="50" id="o_registro" class="form-control" rows="2" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $o_registro;?></textarea>
										</div>
									</div>	
									
								
									<input type="hidden" name="x_actividad" value="<?php echo $x_actividad;?>">
									<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
									<input type="hidden" name="modo" value="<?php echo $modo;?>">
									<input type="hidden" name="pk_proyecto" value="<?php echo $pk_proyecto;?>">
									<input type="hidden" name="x_evento" value="<?php echo $x_evento;?>">									
									
								</form>
							</div>
						</div>
					</div>
				</div>
			
				
			
			</div> <!-- /.Row datos -->
			
	
			<div class="space-4"></div>
	
			<div class="row">
				<div class="col-xs-12 ">
					<div class="form-group center">		
					<button class="btn btn-danger btn-sm " onClick="Atras('<?php echo "tarea=B"; ?>')">
						<i class="ace-icon fa 	fa-reply align-top bigger-125 "></i>
						Regresar
					</button>
					
					<button class="btn btn-success btn-sm " onClick="Guardar('<?php echo $tarea;?>');">
						<i class="ace-icon fa fa-check align-top bigger-125 "></i>
						Guardar
					</button>
					</div>
				</div>
			</div>
	
			<div class="space-4"></div>
			
			<div class="row">
				<div class="col-xs-12">
				
				<?php					
					while($row = pg_fetch_row($ls_resultado)){
					$i=0;
						$fecha_registro   = $row[$i++];
						$nb_emisor       = $row[$i++];
						$mensaje      = $row[$i++];
						$ls_cod      = $row[$i++];

						$Content.= '<div class="itemdiv dialogdiv">
										<div class="user">
											<img alt="Bobs Avatar" src="../../assets/avatars/user.jpg" />
										</div>

										<div class="body">
											<div class="time">
												
												<i class="ace-icon fa fa-clock-o"></i>
												<span class="blue">'.$fecha_registro.' '.$hora_registro. '</span>
											</div>

											<div class="name">
												<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Evento("'.$ls_cod.'");return false;\'>
													<i class="ace-icon fa fa-trash-o bigger-140"></i>
												</a>												
												' .$nb_emisor.'
											</div>
											<div class="text">'.$mensaje.'</div>
											
										</div>
									</div>';						
					
					}	
					echo $Content;
					
				?>	
				</div>
			</div>
	
		</div> <!-- /.row Datos  -->
	</div> <!-- /.row tabla principal -->
</div> <!-- /.page-content -->

</body>



		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<script src="../../assets/js/jquery.autosize.min.js"></script>
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>


		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
				$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
				//  Tooltip
				$( ".open-event" ).tooltip({
					show: null,
					position: {
						my: "left top",
						at: "left bottom"
					},
					open: function( event, ui ) {
						ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
					}
				});
			
				
			} );
		</script>


				
<script src="../../js/funciones.js"></script>  
<script type="text/javascript">

	
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				document.formulario.tarea.value = "I";
				document.formulario.action = "pro_evento_add.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}
		
	function Eliminar_Evento(identificador){
		if (confirm('Esta seguro de eliminar el Evento?') == true){	
			document.formulario.tarea.value = "E";
			document.formulario.x_evento.value = identificador;
			document.formulario.action = "pro_evento_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	
	
	function Atras(parametros){
		location.href = "pro_proyecto_view.php?" + parametros;
	}	

	
</script>

</html>