<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: sis_submenu.php                                            
	Descripcion: Esta interfaz permite AGREGAR/EDITAR/ELIMINAR EL MENU PRINCIPAL
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
--------------------------------------------------------------------------------------------*/
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

/*-------------------------------------------------------------------------------------------
	RUTINAS: Agrega un Nuevo Menu Padre o Principal al final de la lista
--------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		if($x_cod_menu_padre==''){
			$ls_sql = "SELECT co_menu_padre FROM s05_menu_padre	WHERE UPPER(tx_descripcion) = 'strtoupper($o_menu_padre)'";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
			
			if($ls_resultado != 0){
				if($obj_miconexion->fun_numregistros() == 0){
		
					$ls_sql = "INSERT INTO s05_menu_padre(tx_descripcion, nu_orden)	
								SELECT '$o_menu_padre',max(nu_orden)+1 FROM s05_menu_padre";

					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado == 0){
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
					}else{
						echo "<script language='javascript' type='text/javascript'>alert('Nueva Registro Ingresado Satisfactoriamente');</script>";
					}
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('Nombre Duplicado');</script>";
				}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
			}
		}else{
			$ls_sql = "UPDATE s05_menu_padre SET tx_descripcion='$o_menu_padre' WHERE co_menu_padre=$x_cod_menu_padre";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				echo "<script language='javascript' type='text/javascript'>alert('Nombre Actualizado Satisfactoriamente');</script>";
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
			}
		}
		$x_cod_menu_padre = '';
		$o_menu_padre='';
		$modo = 'Insertar Nuevo Registro';
	}

/*-------------------------------------------------------------------------------------------
	RUTINAS: Elimina un Menu Padre sino tiene submenu enlazado
--------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT co_menu_padre FROM s06_menu_padre_hijo WHERE co_menu_padre = '$x_cod_menu_padre' ";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql); //Verifica si esta asociado
		if($ls_resultado != 0){ 
			if($obj_miconexion->fun_numregistros() == 0){ //Borra el registro sino NO esta asociado
				$ls_sql = "DELETE FROM s05_menu_padre WHERE co_menu_padre = '$x_cod_menu_padre' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if($ls_resultado == 0){
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}
			}else{
				echo "<script language='javascript' type='text/javascript'>alert('El registro esta Asociado, No se puede Eliminar');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}

		$x_cod_menu_padre = '';
		$o_menu_padre='';
		$modo = 'Insertar Nuevo Registro';
	}

/*-------------------------------------------------------------------------------------------
	RUTINAS: Sube o Baja el Menu en el orden de aparicion
--------------------------------------------------------------------------------------------*/
	if ($tarea == "C"){
		if($mover=='U'){
			$ls_sql = "SELECT max(nu_orden) FROM s05_menu_padre WHERE nu_orden < $orden"; 
			
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				$row = pg_fetch_row($ls_resultado,0);
				$orden_new = $row[0];
			}

			$ls_sql = "UPDATE s05_menu_padre SET nu_orden=  $orden WHERE nu_orden = $orden_new";
			
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado == 0){
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
			}
			
			$ls_sql = "UPDATE s05_menu_padre SET nu_orden=$orden_new WHERE co_menu_padre = '$x_cod_menu_padre' ";
			
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado == 0){
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
			}
		}else{
			$ls_sql = "SELECT min(nu_orden) FROM s05_menu_padre WHERE nu_orden > $orden"; 
			
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				$row = pg_fetch_row($ls_resultado,0);
				$orden_new = $row[0];
			}
			
			$ls_sql = "UPDATE s05_menu_padre SET nu_orden= $orden WHERE nu_orden = $orden_new";
			
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado == 0){
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
			}
			
			$ls_sql = "UPDATE s05_menu_padre SET nu_orden=$orden_new WHERE co_menu_padre = '$x_cod_menu_padre' ";
			
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado == 0){
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
			}
		
		}
		$x_cod_menu_padre = '';
		$o_menu_padre='';
		$modo = 'Insertar Nuevo Registro';
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Carga los datos del Menu Padre para Modificarlos
--------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql ="SELECT tx_descripcion FROM s05_menu_padre 
				WHERE co_menu_padre = $x_cod_menu_padre";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_menu_padre = $row[0];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}
		$modo = "Editar Registro";
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Crea SQL con Todos los Menu Padre para luego Dibujarlos
--------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT tx_descripcion, 
				(SELECT MIN(nu_orden) FROM s05_menu_padre) as orden_min,
				(SELECT MAX(nu_orden) FROM s05_menu_padre) as orden_max, 
				nu_orden, co_menu_padre
 			FROM s05_menu_padre ORDER BY nu_orden ";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
	
	$color_modo = $modo == "Editar Registro" ?"widget-color-orange":"widget-color-green";
	
/*----------------------------------------------------------------------------------------------------------------------|
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.                                              |
|----------------------------------------------------------------------------------------------------------------------*/
?>



<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Menu
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-5 widget-container-col">
							<div class="widget-box <?php echo $color_modo;?>">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> <?php echo $modo?> </h4>									
								</div>
								
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">

											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Menu Padre</label>
												<div class="col-sm-6">
													<input  class="input-sm form-control" name="o_menu_padre"  value="<?php echo $o_menu_padre;?>" id="o_menu_padre" placeholder="Menu Pdre" type="text"  />
												</div>
											</div>
											
											<div class="form-group center">												
												<button type="button" onClick="Limpiar();" class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
													Limpiar
												</button>
												
												<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
											
											<div class="space-4"></div>
											
											<input type="hidden" name="x_cod_menu_padre" value="<?php echo $x_cod_menu_padre;?>">
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" value="<?php echo $modo;?>"> 
											<input type="hidden" name="mover" value="<?php echo $mover;?>"> 
											<input type="hidden" name="orden" value="<?php echo $orden;?>"> 					
										
									</form>
									</div>
								</div>
							</div>
						</div>
					
					</div>	
						
								
													
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista Menu
							</div>

							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Menu Padre </th>
										<th>Submenu</th>
										<th>Modificar</th>
										<th>Subir</th>
										<th>Bajar</th>
										<th>Eliminar </th>
									</tr>
								</thead>
								<tbody>	
									<?php    
											$li_numcampo = $obj_miconexion->fun_numcampos()-4; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_MENU_PADRE'); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex);  
									?>
								</tbody>
							</table>
							
							
							
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- /.main-content-inner -->






		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		
		<script src="../../assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<!-- <script src="../../assets/js/dataTables.tableTools.min.js"></script> -->
		<!-- <script src="../../assets/js/dataTables.colVis.min.js"></script> -->
		<script src="../../assets/js/daterangepicker.min.js"></script>

		
	<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<!-- <script src="../../assets/js/jquery.ui.touch-punch.min.js"></script> -->
		<!-- <script src="../../assets/js/chosen.jquery.min.js"></script> -->
		<!--<script src="../../assets/js/fuelux.spinner.min.js"></script>  -->
		<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
		<!-- <script src="../../assets/js/bootstrap-timepicker.min.js"></script> -->
		<!-- <script src="../../assets/js/moment.min.js"></script> -->
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<!-- <script src="../../assets/js/bootstrap-datetimepicker.min.js"></script> -->
		<!-- <script src="../../assets/js/bootstrap-colorpicker.min.js"></script> -->
		<!-- <script src="../../assets/js/jquery.knob.min.js"></script> -->
		<script src="../../assets/js/jquery.autosize.min.js"></script>
		<!-- <script src="../../assets/js/jquery.inputlimiter.1.3.1.min.js"></script> -->
		<!-- <script src="../../assets/js/jquery.maskedinput.min.js"></script> -->
		<!-- <script src="../../assets/js/bootstrap-tag.min.js"></script> -->

		
		<!-- ace scripts -->
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			
		
				//or change it into a date range picker
				$('.input-daterange').datepicker({
					
					autoclose:true,
					format: "dd/mm/yyyy"
					
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
	
	/*----------- LOGICA DE NEGOCIO    ----------------------*/	
	function Cancelar(parametros){
		location.href = "inscripcion_ficha_fichas.php?" + parametros;
	}
	
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				document.formulario.tarea.value = "A";
				document.formulario.action = "sis_menu.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}
	
	function Eliminar(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.x_cod_menu_padre.value = identificador;
			document.formulario.action = "sis_menu.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	function Editar(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_cod_menu_padre.value = identificador;
			document.formulario.action = "sis_menu.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function Submenu(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.x_cod_menu_padre.value = identificador;
			document.formulario.action = "sis_submenu.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function Mover(identificador, orden, mover){
		
			document.formulario.tarea.value = "C";
			document.formulario.x_cod_menu_padre.value = identificador;
			
			document.formulario.orden.value = orden;
			document.formulario.mover.value = mover;
			
			document.formulario.action = "sis_menu.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function Limpiar(identificador){
			document.formulario.modo.value = "Insertar Nuevo Registro";
			document.formulario.x_cod_menu_padre.value = '';
			document.formulario.o_menu_padre.value = '';
			document.formulario.tarea.value = "X";
			document.formulario.action = "sis_menu.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	</script>

</body>
</html>