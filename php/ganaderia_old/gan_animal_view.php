<?php 
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
	
		<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
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
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$co_usuario   =  $_SESSION["li_cod_usuario"];
	$arr_cliente  =  Combo_Cliente();
	$arr_vendedor =  Combo_Cliente();
	$arr_rubro    =  Combo_Rubro();
	$arr_abono    =  Combo_Abono();
	$arr_salida         = Combo_Salida();

	
/*-------------------------------------------------------------------------------------------
	ELIMINA UN REGISTRO DE LA TABLA GAN_GANADO
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT f_borrar_animal_temp($pk_animal )";		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);		
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$Result    = $row[0];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}
		
		if ($Result==0){
			$msg = "¡La factura existe pero no puede ser borrada !";    			
		}elseif($Result==1){
			$msg = "¡La factura fue borrada Satisfactoriamente !";			
		}elseif($Result==2){
			$msg = "¡La factura No existe !";
		}		
		echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='gan_animal_view.php'</script>";		
	}

/*-------------------------------------------------------------------------------------------
RUTINAS: Consulta  datos resumen
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT sum (f_calcular_factura(pk_factura)) as SumaTotal, 
				sum(f_calcular_abono(pk_factura)) as SumaAbono
				FROM t20_factura 
				INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente 
			WHERE t20_factura.tx_tipo='VENTA'  ".$ls_criterio;			

	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$SumaTotal    = $row[0];
		$SumaAbono    = $row[1];
		$SumaDebe     = $SumaTotal - $SumaAbono ;
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
			
/*-------------------------------------------------------------------------------------------
	CONSULTA LISTA DE ANIMALES
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT id_numero, UPPER(nb_nombre_animal), in_sexo, to_char(fe_nacimiento, 'dd-TMMon-yyyy'),  age(current_date, fe_nacimiento),
				UPPER(gan_raza.nb_raza), f_grupo_etareo(fe_nacimiento,in_sexo),	pk_ganado
			FROM gan_ganado
			LEFT JOIN gan_raza ON gan_raza.pk_raza = gan_ganado.fk_raza ";
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
		//$collaps = ($sw==1)?'':'collapsed';	
	$collaps = 'collapsed';		
	if($filtro =='NO_ALL'){
		$filtro_deudas = '<span class="badge badge-pill badge-success">Solo Deudas </span>';
		//$filtro_deudas = '<span class="label label-sm label-success arrowed-right">Solo Deudas</span>';
	}else{
		$filtro_deudas = '';
	}
	
	

?>

<div class="container-fluid">
			

			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button class="btn btn-success btn-sm pull-left " onclick="Agregar()">
							<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
							Animal
						</button>
					</div>
				</div>
			</div><!-- /.page-header -->
								
			<div class="row">
				<div class="col-xs-12">					
					<div class="btn-group pull-left">
						<button title="Sanidad" type="button" class="btn btn-white btn-sm btn-primary tooltip-info open-event" onclick="Control_Sanitario()">
							<i class="red ace-icon fa fa-ambulance  "></i>
						</button>
						<button title="Ubicacion" type="button" class="btn btn-white btn-sm btn-primary tooltip-info open-event" onclick="Asignar_Potrero()">
							<i class="green ace-icon fa fa-cubes  "></i>
						</button>						
					</div>				
				</div>
			</div>
			
						<div class="row">
				<div class="col-xs-12">
				<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col ">
							<div class="widget-box <?php echo $collaps; ?> ">
								<div class="widget-header  widget-header-small">
									<h5 class="widget-title"> Busqueda Avanzada </h5>
									<div class="widget-toolbar">			
																			
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-down"></i>
										</a>

									</div>
								</div>

								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right"> Estado </label>
												<div class="col-sm-7" >	
													<select id="x_salida" name="x_salida" class="chosen-select form-control " data-placeholder="Seleccione un Cliente...">
														<?php
															if ($x_salida == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_salida as $k => $v) {
																$ls_cadenasel =($k == $x_salida)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>							
													</select>
												</div>													
											</div>
											
																					
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right">Fecha</label>
												<div class="col-sm-4">	
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
														<input class="input-sm form-control" id="reportrange" name="x_fecha" value="<?php echo $x_fecha;?>"  type="text"  />
													</div>
												</div>
											</div>
											
																				
											<div class="form-group center ">
												
												<button type="button" class="btn btn-sm btn-primary"  onclick="Buscar('ALL')">
													<i class="ace-icon fa fa-search align-top bigger-125 "></i>
													Todos
												</button>
												
												<button type="button" class="btn btn-sm btn-info"  onclick="Buscar('NO_ALL')">
													<i class="ace-icon fa fa-search align-top bigger-125 "></i>
													Deben
												</button>			
												
												<button type="button" class="btn btn-sm btn-info"  onClick="Limpiar('<?php echo "tarea=X"; ?>')">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Reset
												</button>				
											</div>											
											
											<input type="hidden" name="pk_animal" value="<?php echo $pk_animal;?>">
											<input type="hidden" name="tarea" 		 value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" 		 value="<?php echo $modo;?>">
											<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">											
											<input type="hidden" id = "input_filtro" name="input_filtro" value="<?php echo $input_filtro;?>">		
										</form>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- / ROW BUSQUEDA AVANZADA -->



					
					<div class="row">
						<div class="col-xs-12">	
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Animales
							</div>

							<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr>
										<th class="hidden-480">Numero</th>
										<th class="">Nombre</th>
										<th class="">Sexo</th>
										<th class="hidden-480">Fecha Nacimiento</th>
										<th class="hidden-480">Edad</th>
										<th class="hidden-480">Raza</th>
										<th class="hidden-480">Grupo</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										$li_numcampo = 0; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_ANIMAL'); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
										
									?>
								</tbody>
							</table>
						</div>
					</div> <!-- /.row tabla principal -->		
					
					
			
			
			
				</div>
			</div>
				
						
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->


		<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>	
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/moment.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>
	<script src="../../assets/js/chosen.jquery.min.js"></script>
	
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>


	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical			
			
			$('#dynamic-table').dataTable( {
				"lengthChange": false,
				"pageLength": 50,
				"aaSorting": [ [0,'desc'] ],
				"oLanguage": {
					"sInfo": "Mostrando (_START_ hasta _END_) de un total _TOTAL_",
					"sSearch": "Buscar:",						
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
				
				"columns": [
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					{ "orderable": false }
				  ]
			} );
	
			//or change it into a date range picker
			$('.input-daterange').datepicker({
				
				autoclose:true,
				format: "dd/mm/yyyy"
				
			});	
			
						 				
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

	<script type="text/javascript"> 
		// BOTONES GENERALES
		function Agregar(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_animal_add.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}	
		
		function Control_Sanitario(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_sanidad.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Asignar_Potrero(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_movimiento.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		
		// BOTONES DE LA TABLA			
		function Eliminar_Animal(identificador){
			if (confirm('Desea Eliminar este Registro?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.pk_animal.value = identificador;
				document.formulario.action = "gan_animal_view.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
		
		function Editar_Animal(identificador){
			alert('paso');
			document.formulario.tarea.value = "M";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_animal_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Animal_Sanidad(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_animal_sanidad.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Animal_Movimiento(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_animal_movimiento.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Animal_Salida(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_animal_salida.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		
		function Agregar_Imagen(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_animal_img.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}	
		
		function Produccion_Leche(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_animal_leche.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
			
						
		function Buscar(filtro){
			document.formulario.filtro.value = filtro;	
			document.formulario.tarea.value = "B";
			document.formulario.action = "adm_venta_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Limpiar(parametros){	
			location.href='adm_venta_view.php'
		}
		
	</script>
</body>
</html>