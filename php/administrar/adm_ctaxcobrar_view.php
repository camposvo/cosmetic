<?php 
	session_start();
	include_once ("adm_utilidad.php");
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
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
	<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$o_cantidad  = 0;
	$o_cantidad2 = 0;
	$x_proyecto     = 0;
	
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nuevo Registro';
		$check = isset($check)?$check:0;
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
		$check = isset($check)?$check:0;
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_vendedor=  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$arr_abono   =  Combo_Abono();
	
	//$marcar_check = $check=='check'?'checked':'unchecked';
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para ELIMINAR una actividad 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT f_borrar_factura($x_movimiento )";		
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
		echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_ctaxcobrar_view.php'</script>";
		
	}

	$check = $tarea=="B"?1:$check ;  /* Permite filtrar entre todos los registros y solo las ventas que tienen deudas */

/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una actividad Factura
-------------------------------------------------------------------------------------------*/
	$i=0; /*Banderar para cantidad de reglas */
	if($x_unidad!='')$arr_criterio[$i++]=" UPPER(t20_factura.tx_unidad) = '".strtoupper($x_unidad)."' ";
	//if($check==0)$arr_criterio[$i++]=" (f_calcular_abono(pk_factura) < nu_total) "; /* solo los que deben*/
	if($x_cliente!=0)$arr_criterio[$i++]=" t20_factura.fk_cliente = ".$x_cliente;
	if($x_factura!=0)$arr_criterio[$i++]=" UPPER(t20_factura.tx_factura) = '".strtoupper($x_factura)."' ";
	if($x_fecha_ini !=0 and $x_fecha_fin !=0)$arr_criterio[$i++]=" t20_factura.fe_fecha_factura >= '".strtoupper($x_fecha_ini)."' and t20_factura.fe_fecha_factura <= '".strtoupper($x_fecha_fin)."' ";
	if($x_vendedor !=0)$arr_criterio[$i++]=" t20_factura.fk_responsable = ".$x_vendedor;
		
	for($j=0;$j<$i;$j++)$ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];
	
	$ls_criterio = $ls_criterio==""?"":" and ".$ls_criterio;
	
	
	/*-------------------------------------------------------------------------------------------
	RUTINAS: Consulta  datos resumen
	-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(f_calcular_factura(pk_factura)) - sum(f_calcular_abono(pk_factura)) as Debe 
				FROM t20_factura 
				INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente 
			WHERE t20_factura.tx_tipo='CTAXCOBRAR'  ".$ls_criterio;
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$SumaTotal    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
			

			
			
			
	/*-------------------------------------------------------------------------------------------
	RUTINAS: Consulta  de registros de la busqueda
	-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT to_char(pk_factura,'0000000'), UPPER(s01_persona.tx_nombre), tx_concepto,
				f_calcular_factura(pk_factura), f_calcular_abono(pk_factura) AS abono, (f_calcular_factura(pk_factura) - f_calcular_abono(pk_factura)) as debe,
				pk_factura
			FROM t20_factura
			INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente
			WHERE t20_factura.tx_tipo='CTAXCOBRAR' ".$ls_criterio;
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button class="btn-success btn-sm pull-left " onclick="Agregar()">
							<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
							Cuenta x Cobrar
						</button>
					</div>
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12 col-sm-12 widget-container-col">
					<div class="widget-box ">
						<div class="widget-body">
							<div class="widget-main no-padding">
								<table id="" class="table table-striped table-bordered ">
									<thead>
										<tr class="info">
											<th>Prestado</th>
											<th>Abono</th>
											<th>Debe</th>											
										</tr>
									</thead>
									<tbody>	
										<tr>
											<td >
												<input id= 'x_sum_prestado' class="input-sm form-control" name="x_sum_prestado"  type="text" readonly />
											</td>
											<td >
												<input id= 'x_sum_abono' class="input-sm form-control" name="x_sum_abono"  type="text" readonly />
											</td>											
											<td >
												<input id= 'x_sum_debe' class="input-sm form-control" name="x_sum_debe"  type="text" readonly />
											</td>
										</tr>				
									
																							
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>	
			</div>	<!-- /.row totales -->
			
			
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->				
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Cuentas por Cobrar
							</div>
							<form class="form-horizontal" name="formulario">

							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="hidden-480">id</th>
										<th>Deudor</th>
										<th class="hidden-480">Concepto</th>
										
										<th class="hidden">Prestado</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->	
										<th class="hidden-480">Prestado</th>
										
										<th class="hidden">Abono</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->	
										<th class="hidden-480">Abono</th>
										
										<th class="hidden">Debe</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->	
										<th class="">Debe</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										if($tarea == "M"){
											$li_hidden = 4; /* Cantidad de columnas que se van A OCULTAR cuando la pantalla es pequeña*/
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_CTAXCOBRAR',0); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
										}
									?>
								</tbody>
							</table>
							<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
							<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
							<input type="hidden" name="modo" value="<?php echo $modo;?>">
							<input type="hidden" name="check" value="<?php echo $check;?>">		
						</form>
						</div>
					</div> <!-- /.row tabla principal -->	

				
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->


		<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<script src="../../assets/js/bootstrap-datepicker.min.js"></script>				
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
					"bSort" : false,	
					"oLanguage": {
						"sInfo": "Mostrando (_START_ hasta _END_) de un total _TOTAL_",
						"sSearch": "Buscar:",						
						"spaginate": {
						  "next": "Próximo",
						  "previous": "Previo"
						}
					},
										
					
					// CALCULA LA SUMA RESUMEN POR FILTRO Y EL TOTAL  
					"fnFooterCallback": function ( row, data, start, end, display ) {					
						var api = this.api(), data;

						FiltroPrestado = api.column(3, { search: 'applied'} ).data()	.reduce( function (a, b) {
								 return parseFloat(a) + parseFloat(b); 
							}, 0 );
							
						FiltroAbono = api.column(5, { search: 'applied'} ).data()	.reduce( function (a, b) {
								 return parseFloat(a) + parseFloat(b); 
							}, 0 );	
						
						FiltroDebe = api.column(7, { search: 'applied'} ).data()	.reduce( function (a, b) {
								 return parseFloat(a) + parseFloat(b); 
							}, 0 );			
																
						FiltroPrestado = formato_numero(FiltroPrestado, 2, ',', '.');
						FiltroAbono = formato_numero(FiltroAbono, 2, ',', '.');
						FiltroDebe  = formato_numero(FiltroDebe, 2, ',', '.');
						
						$('#x_sum_prestado').val(FiltroPrestado);
						$('#x_sum_abono').val(FiltroAbono);
						$('#x_sum_debe').val(FiltroDebe);				
					}					
					  					  
				} );
		
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
function Cancelar(parametros){
		location.href = "inscripcion_ficha_fichas.php?" + parametros;
	}
	
	/*  Calcula el valor total de la factura*/
	function calcular_total(){
		document.formulario.o_total.value = document.formulario.o_cantidad.value * document.formulario.o_precio.value;
	}
	
	function Agregar(){
		document.formulario.tarea.value = "A";
		document.formulario.action = "adm_ctaxcobrar_add.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	
		
	function Ejecutar_Ctaxcobrar(identificador){
		document.formulario.tarea.value = "X";
		document.formulario.x_movimiento.value = identificador;
		document.formulario.action = "adm_ctaxcobrar_mtto.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function Eliminar_Ctaxcobrar(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_ctaxcobrar_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	function Editar_Ctaxcobrar(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_ctaxcobrar_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function Buscar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "adm_ctaxcobrar_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function Limpiar(parametros){
			document.formulario.x_cliente.value = 0;
			document.formulario.tarea.value = 'X';
			document.formulario.x_fecha_ini.value = '';
			document.formulario.x_fecha_fin.value = '';
			document.formulario.check.value = 0;
			document.formulario.action = "adm_ctaxcobrar_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
	
	
	
	</script>
</body>
</html>