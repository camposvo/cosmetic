<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: adm_venta.php                                                    
	Descripcion: 
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("man_utilidad.php");
	/* require_once("../../clases/xajax/xajax_core/xajax.inc.php"); */
	$usu_autentico= isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';

	if ($usu_autentico != "SI") {
		session_destroy();
    	echo"<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
		exit();
	}
	
/*-------------------------------------------------------------------------------------------|
|		Implantacin De Una Funcin Ajax Utilizando La Clase XAJAX     		                 |
|-------------------------------------------------------------------------------------------*/
	/* $xajax = new xajax(); // Creo La Instancia.
	$xajax->registerfunction("Fun_Ajax_Mensaje"); // Asociamos La Funcin Al Objeto XAJAX. */

	
 	function Fun_Ajax_Mensaje($id_operacion, $valor){
		//$objResponse = new xajaxResponse();
		$co_usuario  =  $_SESSION["li_cod_usuario"];
		
		if ($id_operacion == 1){
		$ls_sql = "SELECT to_char(t16_mensaje.fe_registro, 'dd/mm/yyyy'), to_char(t16_mensaje.fe_registro, 'HH:MI am'),
			UPPER(Emisor.tx_indicador), 
			t16_mensaje.tx_mensaje,
			UPPER(Destinatario.tx_nombre||' '|| Destinatario.tx_apellido) as NombreDestino, t17_mensaje_persona.fe_fecha_leido, 
		 	in_leido, t16_mensaje.pk_mensaje, Emisor.co_persona,
			UPPER(Emisor.tx_nombre||' '||Emisor.tx_apellido)
			FROM t17_mensaje_persona
			INNER JOIN s01_persona AS Destinatario ON t17_mensaje_persona.fk_destinatario = Destinatario.co_persona 
			INNER JOIN t16_mensaje ON t17_mensaje_persona.fk_mensaje = t16_mensaje.pk_mensaje
			INNER JOIN s01_persona AS Emisor ON t16_mensaje.fk_emisor = Emisor.co_persona
			WHERE Destinatario.co_persona = ".$co_usuario." AND UPPER(t16_mensaje.tx_mensaje) LIKE '%".strtoupper($valor)."%'
			ORDER BY t16_mensaje.fe_registro DESC
			";
		}else{
			$ls_sql = "SELECT to_char(t16_mensaje.fe_registro, 'dd/mm/yyyy'), to_char(t16_mensaje.fe_registro, 'HH:MI am'),
			UPPER(Emisor.tx_indicador), 
			t16_mensaje.tx_mensaje,
			UPPER(Destinatario.tx_nombre||' '|| Destinatario.tx_apellido) as NombreDestino, t17_mensaje_persona.fe_fecha_leido, 
		 	in_leido, t16_mensaje.pk_mensaje, Emisor.co_persona,
			UPPER(Emisor.tx_nombre||' '||Emisor.tx_apellido)
			FROM t17_mensaje_persona
			INNER JOIN s01_persona AS Destinatario ON t17_mensaje_persona.fk_destinatario = Destinatario.co_persona 
			INNER JOIN t16_mensaje ON t17_mensaje_persona.fk_mensaje = t16_mensaje.pk_mensaje
			INNER JOIN s01_persona AS Emisor ON t16_mensaje.fk_emisor = Emisor.co_persona
			WHERE Destinatario.co_persona = ".$co_usuario." 
			ORDER BY t16_mensaje.fe_registro DESC
			";			
			
			
		}	
		
		
		//echo $ls_sql;
		$obj_miconexion = fun_crear_objeto_conexion();
		$li_id_conex = fun_conexion($obj_miconexion);
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		$num_reg = $obj_miconexion->fun_numregistros($ls_resultado);
		
		$newContent='';
		while($row = pg_fetch_row($ls_resultado)){
			$i=0;
				$fecha_registro   = $row[$i++];
				$hora_registro   = $row[$i++];
				$emisor       = $row[$i++];
				$mensaje      = $row[$i++];
				$destinatario = $row[$i++];
				$fecha_leido  = $row[$i++];
				$sw_leido     = $row[$i++];
				$pk_mensaje   = $row[$i++];
				$co_emisor    = $row[$i++];
				$nb_emisor    = $row[$i++];
				
				
				if($id_operacion == 1)
					$mensaje = str_replace(strtoupper($valor), '<span class="bg-info">'.strtoupper($valor).'</span>', strtoupper($mensaje));

				if($co_emisor==$co_usuario ){
					$newContent.= '<div class="itemdiv dialogdiv">
										<div class="user">
											<img alt="Bobs Avatar" src="../imagenes/img_21.png" />
										</div>

										<div class="body">
											<div class="time">
												<i class="ace-icon fa fa-clock-o"></i>
												<span class="blue">'.$fecha_registro.' '.$hora_registro. '</span>
											</div>

											<div class="name">
												' .$nb_emisor.'
											</div>
											<div class="text">'.$mensaje.'</div>

											
										</div>
									</div>';

					
				}else{
					$newContent.= '<div class="itemdiv dialogdiv">
										<div class="user">
											<img alt="Bobs Avatar" src="../imagenes/img_21.png"/>
										</div>

										<div class="body">
											<div class="time">
												<i class="ace-icon fa fa-clock-o"></i>
												<span class="blue">'.$fecha_registro.' '.$hora_registro. '</span>
											</div>

											<div class="name">
												<span class="label label-success arrowed">' .$nb_emisor.'</span>
											</div>
											<div class="text">'.$mensaje.'</div>
											
										</div>
									</div>';
					
				}
				
				
		}

		
			$objResponse->assign("respuesta","innerHTML", $newContent);
			
			return $newContent;
			
	}
	 
//	$xajax->processRequest(); // Escribimos En La Capa Con id="respuesta" El Texto Que Aparece En $newContent.
	
	
?>
<!DOCTYPE html>
<html>
<head>
<?php 
	// En El <head> Indicamos Al Objeto XAJAX Se Encargue De Generar El Javascript Necesario.
	//$xajax->printJavascript('../../clases/xajax'); 
?>

<meta charset="UTF-8" />
<title>BellinghieriCosmetic</title>
			
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
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/

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
	$arr_prioridad  =  Combo_Prioridad();
	$arr_cliente =  Combo_Cliente();

	
/*-------------------------------------------------------------------------------------------
	VERIFICA SI EL USUARIO ESTA EN LA LISTA DE CORREO
-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT in_grupo_correo
			FROM s01_persona
			WHERE co_persona= $co_usuario";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
			$x_grupo_correo     = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Actualiza el estado a Leido
-------------------------------------------------------------------------------------------*/
	$ls_sql = "UPDATE t17_mensaje_persona
			SET fe_fecha_leido=now(), in_leido='S'
			WHERE fk_destinatario =  ".$co_usuario;
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado == 0){
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}else{
		echo "<script language='javascript' type='text/javascript'>xajax_Fun_Ajax_Mensaje(0);</script>";
	}

	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para INGRESAR una actividad 
-------------------------------------------------------------------------------------------*/
	
if ($tarea == "I"){
		$o_total = number_format($o_total,2,".","");
		$o_precio = number_format($o_precio,2,".","");
		$x_cant_item= $x_cant_item==''?0:$x_cant_item;
		
		$ls_sql = "INSERT INTO t16_mensaje(
			 tx_mensaje, fk_emisor, fe_registro, tx_prioridad
			)
		VALUES (
			'$o_descripcion', $co_usuario,now(),'$o_prioridad'
			);";
						
		//echo $ls_sql;
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>xajax_Fun_Ajax_Mensaje(0);</script>";
		}
		$tarea = 'A';
	}	
	
	if ($x_grupo_correo!='S'){
		
		$sw_correo_input  = 'readonly';
		$sw_correo_button = 'disabled';
		$acceso = '<div class="alert alert-danger">
						<strong>
							<i class="ace-icon fa fa-times"></i>
							No puede Emitir Mensajes
						</strong>

						Consulte al Administrador para tener acceso al grupo de Mensajes
						<br />
					</div>';
		
	}
	
	/*-------------------------------------------------------------------------------------------
	DEFINE EL CRITERIO DE LA BUSQUEDA AVANZADA
--------------------------------------------------------------------------------------------*/
	$i=0;

	$ls_criterio = "";

	if($x_tipo != ''){ $arr_criterio[$i++]=" operacion = '".$x_tipo."'"; $sw = 1;}
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){ $arr_criterio[$i++]=" fecha >= '".strtoupper($x_fecha_ini)."' and fecha <= '".strtoupper($x_fecha_fin)."' ";  }
		
		
	for($j=0;$j<$i;$j++) $ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];	
	
	$ls_criterio = $ls_criterio==""?"":" WHERE ".$ls_criterio;	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULT DE MOVIMIENTOS
--------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT to_char(pk_factura,'0000000'), to_char(fecha, 'dd/mm/yyyy'), operacion,				
			UPPER(vm02_edo_cuenta.cliente),  
			ingreso, egreso, f_cuenta, pk_factura
			FROM vm02_edo_cuenta "
			.$ls_criterio.
           "ORDER BY ref DESC  ";
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	
	
	
	$collaps = ($sw==1)?'':'collapsed';	

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Mensajes
				</h1>
			</div><!-- /.page-header -->
				
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					<?php echo $acceso?>
				</div>
			</div>
			<form class="form-horizontal" name="formulario">
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col ">
							<div class="widget-box <?php echo $collaps; ?> ">
								<div class="widget-header  widget-header-small">
									<h5 class="widget-title"> Busqueda </h5>
									<div class="widget-toolbar">
																			
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-down"></i>
										</a>

									</div>
								</div>

								<div class="widget-body">
									<div class="widget-main">	
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="x_referencia">Filtrar</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="x_referencia" value="<?php echo $x_referencia;?>" id="x_referencia" type="text" >
												</div>
											</div>	
											
																						
																				
											<div class="form-group center ">
												<button type="button" class="btn btn-sm btn-info"  onClick="Buscar()">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Buscar
												</button>		
												<button type="button" class="btn btn-sm btn-info"  onClick="Limpiar('<?php echo "tarea=X"; ?>')">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Reset
												</button>				
											</div>											
											
											
									
									</div>
								</div>
							</div>
						</div>
					</div> <!-- ROW BUSQUEDA AVANZADA -->
			
			
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
						<div class="widget-box ">
							
								<div class="form-actions">
									<div class="input-group">
										<input name="o_descripcion" <?php echo $sw_correo_input ?> placeholder="Escriba su mensaje aqui ..." type="text" class="form-control"  />
										<span class="input-group-btn">
											<button <?php echo $sw_correo_button ?> class="btn btn-sm btn-info no-radius" onClick="Agregar();" type="button">
												<i class="ace-icon fa fa-share"></i>
												Enviar
											</button>
										</span>
									</div>
								</div>
								<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
								<input type="hidden" name="modo" value="<?php echo $modo;?>">
								<input type="hidden" name="x_mensaje" value="<?php echo $x_mensaje;?>">
			

							<div class="widget-body">
								<div class="widget-main no-padding">
									<div class="dialogs">
												
										<div id="respuesta"></div>

									</div>

									
								</div><!-- /.widget-main -->
							</div><!-- /.widget-body -->
						</div><!-- /.widget-box -->
					
					
					
				</div>
			</div> <!-- ROW CONTENT END -->
				</form>
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->

	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
			$('input[name=date-range-picker]').daterangepicker({
				'applyClass' : 'btn-sm btn-success',
				'cancelClass' : 'btn-sm btn-default',
				locale: {
					applyLabel: 'Aplicar',
					cancelLabel: 'Cancelar',
				}
			})
			.prev().on(ace.click_event, function(){
				$(this).next().focus();
			});
			
				
			function cb(start, end) {
				$('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
			}
			
			cb(moment().subtract(29, 'days'), moment());

			$('#reportrange').daterangepicker({
				format: "DD/MM/YYYY",
				ranges: {
				   'Hoy': [moment(), moment()],
				   'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				   'Ultimos 7 Dias': [moment().subtract(6, 'days'), moment()],
				   'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
				   'Este Mes': [moment().startOf('month'), moment().endOf('month')],
				   'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}
			}, cb);
			
		
		} );
	
	</script>


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript">
/*----------- LOGICA DE NEGOCIO    ----------------------*/	
	xajax_Fun_Ajax_Mensaje(0,0); // INICIALIZA EL DIV CON LA TABLA 
//	timer = window.setInterval("xajax_Fun_Ajax_Mensaje(0,0)", 4000);
	timerID = window.setInterval("Buscar()", 4000);

	function Agregar(){
		
		if(campos_blancos(document.formulario) == false){		
			document.formulario.tarea.value = "I";
			document.formulario.action = "man_mensaje_view.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
	}
	
	function Buscar(){		
		valor = document.formulario.x_referencia.value;
		
		if(valor != ''){ // Aqui entra y realiza una busqueda
			//alert("entro 1");	
			clearInterval(timerID);
			xajax_Fun_Ajax_Mensaje(1, valor);
		}	
		else{
		//	alert("entro 2");
			xajax_Fun_Ajax_Mensaje(0, valor);
		}	
	}
	
	function Limpiar(parametros){	
		location.href='man_mensaje_view.php'
	}
		
	
	function Marcar(identificador){
		document.formulario.tarea.value = "U";
		document.formulario.x_mensaje.value = identificador;
		document.formulario.action = "man_mensaje_view.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	
    </script>



</body>
</html>