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
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>
		
</head>
<body>
<?php 


	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_vendedor=  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$arr_abono   =  Combo_Abono();
	
	$arr_fecha = explode('-',$x_fecha,2);
	$x_fecha_ini = $arr_fecha[0];
	$x_fecha_fin = $arr_fecha[1];	
	
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
		echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_ctaxpagar_view.php'</script>";
		
	}	

/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una actividad Factura
-------------------------------------------------------------------------------------------*/
	$i=0; /*Banderar para cantidad de reglas */
	$sw = 0; // Bandera para indicar si hay filtros
	if($filtro =='NO_ALL')$arr_criterio[$i++]=" (f_calcular_abono(pk_factura) < f_calcular_factura(pk_factura)) "; /* solo los que deben*/
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){ $arr_criterio[$i++]=" t20_factura.fe_fecha_factura >= '".strtoupper($x_fecha_ini)."' and t20_factura.fe_fecha_factura <= '".strtoupper($x_fecha_fin)."' "; $sw = 1;}
		
	for($j=0;$j<$i;$j++)$ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];
	
	$ls_criterio = $ls_criterio==""?"":" and ".$ls_criterio;	
	
	/*-------------------------------------------------------------------------------------------
	RUTINAS: Consulta  datos resumen
	-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(f_calcular_factura(pk_factura)) - sum(f_calcular_abono_capital(pk_factura)) as Debe 
				FROM t20_factura 
				INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente 
			WHERE t20_factura.tx_tipo='CTAXPAGAR'  ";
	
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
	$ls_sql = "SELECT to_char(pk_factura,'0000000'),UPPER(s01_persona.tx_nombre), tx_concepto,
				f_calcular_factura(pk_factura), f_calcular_abono_capital(pk_factura) AS Capital, 
				f_calcular_abono(pk_factura) - f_calcular_abono_capital(pk_factura) AS Interes,
				(f_calcular_factura(pk_factura) - f_calcular_abono_capital(pk_factura)) as debe,
				tx_nota, in_tipo_persona, tx_indicador,
				pk_factura
			FROM t20_factura
			INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente
			WHERE t20_factura.tx_tipo='CTAXPAGAR'
            ORDER BY debe DESC			";
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
	$collaps = ($sw==1)?'':'collapsed';	

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button class=" btn-success btn-sm pull-left " onclick="Agregar()">
							<i class="ace-icon fa fa-plus align-top bigger-125 ">
							</i>
							Punto de Cuenta
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
											<th>Credito</th>
											<th>Capital</th>
											<th>Interes</th>
											<th>Debe</th>											
										</tr>
									</thead>
									<tbody>	
										<tr>
											<td >
												<input id= 'x_sum_credito' class="input-sm form-control" name="x_sum_credito"  type="text" readonly />
											</td>
											<td >
												<input id= 'x_sum_capital' class="input-sm form-control" name="x_sum_capital"  type="text" readonly />
											</td>
											<td >
												<input id= 'x_sum_interes' class="input-sm form-control" name="x_sum_interes"  type="text" readonly />
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
								Resumen de Cuentas
								
							</div>
							<form class="form-horizontal" name="formulario">
							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>id</th>
										<th>Acreedor</th>
										<th class="hidden-480">Descripcion</th>
										
										<th class="hidden">Credito</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->	
										<th class="hidden-480">Credito</th>
										
										<th class="hidden">Capital</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->	
										<th class="hidden-480">Capital</th>
										
										<th class="hidden">Interes</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->	
										<th class="hidden-480">Interes</th>
										
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
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_CTAXPAGAR',0); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
										}
									?>
								</tbody>
							</table>
							<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
							<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
							<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">	
							<input type="hidden" id = "input_filtro" name="input_filtro" value="<?php echo $input_filtro;?>">		
						</form>
						</div>
					</div> <!-- /.row tabla principal -->
					
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
	
	
	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>	
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

	<script type="text/javascript">
		 
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			$('#dynamic-table').dataTable( {
				"search": {
					"search": $('#input_filtro').val()
				  },
				"lengthChange": false,
				"pageLength": 50,
				"bSort" : false,	
				"oLanguage": {
					"sInfo": "De _START_ hasta _END_ de un total de _TOTAL_",
					"sInfoFiltered": " ( filtrado de _MAX_ registros )",
					"sSearch": "Filtro:",						
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
								


					// CALCULA LA SUMA RESUMEN POR FILTRO Y EL TOTAL  
				"fnFooterCallback": function ( row, data, start, end, display ) {					
					var api = this.api(), data;

					FiltroCredito = api.column(3, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );
						
					FiltroCapital = api.column(5, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );	
					
					FiltroInteres = api.column(7, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );	
					
					FiltroDebe = api.column(9, { search: 'applied'} ).data()	.reduce( function (a, b) {
						 return parseFloat(a) + parseFloat(b); 
					}, 0 );			
							 								
					FiltroCredito = formato_numero(FiltroCredito, 2, ',', '.');
					FiltroCapital = formato_numero(FiltroCapital, 2, ',', '.');
					FiltroInteres  = formato_numero(FiltroInteres, 2, ',', '.');
					FiltroDebe  = formato_numero(FiltroDebe, 2, ',', '.');
					
					$('#x_sum_credito').val(FiltroCredito);
					$('#x_sum_capital').val(FiltroCapital);
					$('#x_sum_interes').val(FiltroInteres);
					$('#x_sum_debe').val(FiltroDebe);		
				}	
					

				  
			} );
			
					// Toma el valor del Filtro 
				var table = $('#dynamic-table').DataTable(); 
				table.on( 'search.dt', function () {
					$('#input_filtro').val(table.search());
				} );
				
				
				
				$('[data-rel=tooltip]').tooltip({container:'body'});
				$('[data-rel=popover]').popover({container:'body'});
			
				
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
				
		function Agregar(){
			document.formulario.tarea.value = "A";
			document.formulario.action = "adm_ctaxpagar_add.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}		
		
		function Pagar_Ctaxpagar(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_ctaxpagar_mtto.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
						
		function Eliminar_Ctaxpagar(identificador){
			if (confirm('Desea Eliminar este Registro?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.x_movimiento.value = identificador;
				document.formulario.action = "adm_ctaxpagar_view.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
		
		function Editar_Ctaxpagar(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_ctaxpagar_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	
	</script>
</body>
</html>