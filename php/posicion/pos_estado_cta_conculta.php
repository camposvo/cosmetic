<?php 
	session_start();
	include_once ("utilidad.php");
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
	$x_rubro     = 0;
	$Disponible  = 0;
	$Banco       = 0;
	$SelectMes   = "checked";
	$SelectRango = "unchecked";
	
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
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$x_mes_actual = date('m');
	$x_ano_actual = date('Y');
	$x_mes = isset($x_mes)?$x_mes:$x_mes_actual;
	$x_ano = isset($x_ano)?$x_ano:$x_ano_actual;
	$x_fecha_actual    =  date('Y/m/d');
	
	$tipo =  isset($tipo)?$tipo:'mes';

	
		$ls_sql = "SELECT f_refresh();";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado != 0){
			
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}

	

/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: CONSULTA DE BANCO
**************************************************************	
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(nu_capital) 
       FROM t15_banco";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Banco    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	  
/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: CONSULTA DE CUENTAS POR COBRAR
**************************************************************	
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(f_calcular_abono(fk_factura)) as Abono, 
				sum (t01_detalle.nu_cantidad * nu_precio) as SumaTotal
				FROM t01_detalle 
				INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
		WHERE t20_factura.tx_tipo='CTAXCOBRAR' ";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$AbonoCtxCob    = $row[0];
		$TotalCtxCob    = $row[1];
		$DebeCtxCob     = $row[1] - $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: CONSULTA DE CUENTAS POR PAGAR
**************************************************************	
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(f_calcular_abono(fk_factura)) as Abono, 
				sum (t01_detalle.nu_cantidad * nu_precio) as SumaTotal
				FROM t01_detalle 
				INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
		WHERE t20_factura.tx_tipo='CTAXPAGAR' ";
     //echo $ls_sql;	
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$AbonoCtxPag    = $row[0];
		$TotalCtxPag    = $row[1];
		$DebeCtxPag     = $row[1] - $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
/***************************************************************	
	RUTINAS: CONSULTA DE MONTO DISPONIBLE
***************************************************************/
	$ls_sql = "SELECT f_cuenta
				FROM vm02_edo_cuenta
			WHERE ref = (SELECT Max(ref) from vm02_edo_cuenta)";
	//echo $ls_sql;		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado)>0){
			$row = pg_fetch_row($ls_resultado,0);
			$Disponible    = $row[0];
		}	
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
	$Caja = $Disponible - $Banco;
	

	
/*-------------------------------------------------------------------------------------------
	RUTINAS: RESUMEN DE VENTAS
-------------------------------------------------------------------------------------------*/
	
	if ($tipo =='mes'){
		$SelectMes   = "checked";
		$SelectRango = "unchecked";
		$nuevafecha = strtotime ( '-131 day' , strtotime ( $x_fecha_actual ) ) ;
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
		
		if($x_mes !=0){
			//$ls_criterio="WHERE EXTRACT(month FROM fecha) = ".$x_mes." AND EXTRACT (YEAR FROM fecha) = ".$x_ano;
			$ls_criterio ="WHERE fecha >= '".strtoupper($nuevafecha)."' and fecha <= '".strtoupper($x_fecha_actual)."' ";
			
		}else{
		// Debe especificar un mes
		}
	}elseif($tipo =='rango'){
		$SelectMes   = "unchecked";
		$SelectRango = "checked";
		if($x_fecha_ini !=0 and $x_fecha_fin !=0){
			$ls_criterio ="WHERE fecha >= '".strtoupper($x_fecha_ini)."' and fecha <= '".strtoupper($x_fecha_fin)."' ";
		}else{
			// debe especificar una fecha
		}	
	}

/***************************************************************	
	RUTINAS: INGRESOS Y EGRESOS
***************************************************************/
	$ls_sql = "SELECT SUM(ingreso) as Ingreso, SUM(egreso) as Egreso
				FROM vm02_edo_cuenta	".$ls_criterio;
	

	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Ingreso   = $row[0];
		$Egreso    = $row[1];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}		
	

/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULT DE MOVIMIENTOS
--------------------------------------------------------------------------------------------*/
	$i=0;
	$li_tampag = 80;
	$ls_sql = "SELECT to_char(fecha, 'dd/mm/yyyy'), operacion,				
			UPPER(vm02_edo_cuenta.cliente),  
			ingreso, egreso, f_cuenta, pk_factura
			FROM vm02_edo_cuenta
           ORDER BY ref DESC";
	
	//echo $ls_sql;

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	$li_inicio =    $obj_miconexion->fun_tampagina($li_pagina, $li_tampag); 
	$li_totreg = $obj_miconexion->fun_numregistros($ls_resultado);
		
	if ($li_totreg > 0){ // Reescribe la consulta para un tamao de pagina definido
		$ls_sql = $ls_sql.sprintf(" LIMIT %d OFFSET %d ", $li_tampag, $li_inicio);
		$ls_resultado= $obj_miconexion->fun_consult($ls_sql);
		
	}
	$li_totpag  = $obj_miconexion->fun_calcpag( $li_totreg, $li_tampag);
		
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
				<h1>
					Posicion Global
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
					
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> Estado de Cuenta </h4>
									<div class="widget-toolbar">
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-up"></i>
										</a>
									</div>
								</div>	
								
								<div class="widget-body">
									<div class="widget-main">
								
								
									<table id="simple-table" class="table table-striped table-bordered table-hover">
										<thead>
											<tr  class="success">
												<th class="hidden-480">Efectivo</th>
												<th class="hidden-480">Banco</th>
												<th>Saldo </th>
												<th>Ingreso</th>
												<th>Egreso</th>
											</tr>
										</thead>
										<tbody>	
											<tr>
												<td class="hidden-480"><?php echo number_format($Caja,2,",","."); ?> </td>
												<td class="hidden-480"><?php echo number_format($Banco,2,",","."); ?></td>
												<td><?php echo number_format($Disponible,2,",","."); ?></td>
												<td><?php echo number_format($Ingreso,2,",","."); ?></td>
												<td><?php echo number_format($Egreso*(-1),2,",","."); ?></td>
											</tr>
										</tbody>
									</table>
							
								</div>
								</div>
							</div>
						</div>
					</div>
		
				
			
					<div class="row">
						<div class="col-xs-12">
							<form name="formulario">  <!-- form start -->
								<div class="clearfix">
									<div class="pull-right tableTools-container"></div>
								</div>
								
								<div class="table-header">
									Movimientos
								</div>

								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th class='hidden-480'>Fecha</th>
											<th class='hidden-480'>Tipo</th>
											<th class='hidden-480'>Concepto</th>
											<th>Ingreso</th>
											<th>Egreso</th>
											<th>Monto</th>
											<th></th>
										</tr>
									</thead>
									<tbody>	
										<?php   
											if($tarea == "M"){
												$li_numcampo = $obj_miconexion->fun_numcampos() -7; // Columnas que se muestran en la Tabla
												$li_indicecampo = $obj_miconexion->fun_numcampos() - 1; // Referencia al indice de la columna clave
												fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_CUENTA'); // Dibuja la Tabla de Datos
												}
										?>
									</tbody>
								</table>
							
								<input type="hidden" name="ingreso1" value="<?php echo $ingreso1;?>">
								<input type="hidden" name="egreso1" value="<?php echo $egreso1;?>">
								<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
								<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
								<input type="hidden" name="SelectMes" value="<?php echo $SelectMes;?>">
								<input type="hidden" name="SelectRango" value="<?php echo $SelectRango;?>">
			
							</form>
							
						</div>
					</div> <!-- /.row tabla principal -->
				
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- main-content Principal -->


		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		
		<script src="../../assets/js/bootstrap.min.js"></script>
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
		
		
		<!-- ace scripts -->
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			} );
			
			
			
			
			
		</script>


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript"> 

		function Mostrar_Info(identificador,ingreso1,egreso1){

			document.formulario.tarea.value = "X";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.ingreso1.value = ingreso1;
			document.formulario.egreso1.value = egreso1;
			
			document.formulario.action = "pos_mov_inf.php";
			document.formulario.method = "POST";
			document.formulario.submit();
			
	
	}

		
	function Buscar(){
		if(document.formulario.tipo[1].checked == true) { //Seleccion de Mes
			if(document.formulario.x_mes.value == 0){	
				error = 1; 	
			}else{ 
				error = 0; 	
			}
		}else{                                             //Seleccion POR RANGO
			if(document.formulario.x_fecha_ini.value !=0 && document.formulario.x_fecha_fin.value !=0){
				error= 0;
			}else{
				error= 1;
			}
		}
		
		if(!error){
			document.formulario.tarea.value = "B";
			document.formulario.action = "pos_estado_cta_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
		}else{
			alert('Debe ingresar un Valor Valido');
		}
	}	
	
	function Refrescar(){
		document.formulario.tarea.value = "U";
		document.formulario.action = "pos_estado_cta_view.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}
	
	
	</script>

</body>
</html>

