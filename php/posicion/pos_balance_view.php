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
	$Disponible  = 0;
	$Banco       = 0;
	$x_rubro     = 0;
	
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
	
	$obj_miconexion_1 = fun_crear_objeto_conexion();
	$li_id_conex_1 = fun_conexion($obj_miconexion_1);
	
	$obj_miconexion_2 = fun_crear_objeto_conexion();
	$li_id_conex_2 = fun_conexion($obj_miconexion_2);
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	
/*-------------------------------------------------------------------------------------------
	LEE EL CAPITAL DEL BANCO
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(nu_capital) FROM t15_banco";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Banco    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
	
/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: CONSULTA DE CUENTAS POR COBRAR
**************************************************************	
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(abono) as Abono, 
				sum (detalle) as Deuda
				FROM v01_pago 
		WHERE v01_pago.tx_tipo='CTAXCOBRAR' ";
			
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$AbonoCtxCob    = $row[0];
		$TotalCtxCob    = $row[1];
		$CtxCob     = $row[1] - $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
/***************************************************************	
	RUTINAS: CONSULTA DE MONTO DISPONIBLE
***************************************************************/
	$ls_sql = "SELECT f_saldo_disponible() ";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado)>0){
			$row = pg_fetch_row($ls_resultado,0);
			$Disponible    = $row[0];
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
	$Caja = $Disponible;	

/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: CONSULTA DE CUENTAS POR COBRAR VENTAS
**************************************************************	
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(detalle) - sum(abono) as Debe 
		FROM v01_pago 
		WHERE v01_pago.tx_tipo='VENTA'";
	
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$TotalVenta    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}	
	
/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: CONSULTA DE CUENTAS POR PAGAR
**************************************************************	
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(abono) as Abono, sum (detalle) as Credito
				FROM v01_pago 
		WHERE v01_pago.tx_tipo='CTAXPAGAR' ";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$AbonoCtxPag    = $row[0];
		$TotalCtxPag    = $row[1];
		$DebeCtxPag     = $row[1] - $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}

	
/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: DETALLE DE CUENTAS POR COBRAR VENTAS
**************************************************************	
-----------------------------------------------------------------------------------------------*/	

	$ls_sql = "SELECT UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		sum(detalle) - sum(abono) as Debe 
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_cliente 		
		WHERE v01_pago.tx_tipo='VENTA' and (detalle - abono) > 0 
		GROUP BY s01_persona.tx_nombre, s01_persona.tx_apellido 	";
	
	//echo $ls_sql;
	$ls_resultado_1 =  $obj_miconexion_1->fun_consult($ls_sql);
	
		
	if($ls_resultado_1 != 0){
		$tarea = "N";
	}else{
		fun_error(1,$li_id_conex_1,$ls_sql,$_SERVER['PHP_SELF']);
	}
	
	/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: DETALLE DE CUENTAS POR COBRAR PRESTAMOS
**************************************************************	
-----------------------------------------------------------------------------------------------*/
		
	$ls_sql = "SELECT UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		sum(detalle) - sum(abono) as Debe 
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_cliente 		
		WHERE v01_pago.tx_tipo='CTAXCOBRAR' and (detalle - abono) > 0 
		GROUP BY s01_persona.tx_nombre, s01_persona.tx_apellido 	";
		
	
	//echo $ls_sql;
	$ls_resultado_2 =  $obj_miconexion_2->fun_consult($ls_sql);
	
		
	if($ls_resultado_2 != 0){
		$tarea = "N";
	}else{
		fun_error(1,$li_id_conex_2,$ls_sql,$_SERVER['PHP_SELF']);
	}
	
	
	
/*-------------------------------------------------------------------------------------------
**************************************************************	
	RUTINAS: DETALLE DE CUENTAS POR PAGAR
**************************************************************	
-----------------------------------------------------------------------------------------------*/	
	$i=0;
	
	$ls_sql = "SELECT UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		sum(detalle) - sum(abono) as Debe 
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_cliente 		
		WHERE v01_pago.tx_tipo='CTAXPAGAR' and (detalle - abono) > 0 
		GROUP BY s01_persona.tx_nombre, s01_persona.tx_apellido 	";
		
		//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	
	$activos_fijo = $Caja+$Banco;
	$pasivos = $DebeCtxPag;
	$total_activos = $activos_fijo + $TotalVenta + $CtxCob;
	$balance = ($activos_fijo + $TotalVenta + $CtxCob) - $pasivos;
	$color_font = $balance < 0?'<font color="red">':'';  

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
						<div class="col-xs-12">
							
							

							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="hidden-480">Caja</th>
										<th class="hidden-480">Ctas por Cobrar</th>
										<th >Total Activos</th>
										<th>Total Pasivos</th>
										<th>Balance</th>
									</tr>
								</thead>
								<tbody>	
									<td class="hidden-480"><?php echo number_format($activos_fijo,2,",","."); ?> </td>
									<td class="hidden-480"><?php echo number_format($TotalVenta + $CtxCob,2,",","."); ?> </td>
									<td >
										<!-- <span class="label label-info arrowed-in arrowed-in-right"> -->
										<strong>
											<?php echo number_format($total_activos,2,",","."); ?>
										</strong>
										<!-- </span> -->
									 </td>
									<td >
										<!-- <span class="label label-warning arrowed-in arrowed-in-right"> -->
											<strong>
											<?php echo number_format($pasivos,2,",","."); ?>
											</strong>
										<!-- </span> -->
									 </td>
									<td >
										<span class="label label-success ">
											<?php  echo number_format($balance  ,2,",","."); ?>
										</span>
									</td>
								</tbody>
							</table>
							
						</div>
					</div> <!-- /.row tabla principal -->
				
				
					<div class="row">
						<h5 class="header smaller lighter blue">							
							<strong>Cuentas por Cobrar</strong>
						</h5>
						
						<div class="col-xs-12 col-sm-6">
							
							<div class="row">
								<div class="col-xs-11 label label-lg label-success arrowed-in arrowed-right">
									<b>Ventas</b>
								</div>
							</div>
							
							<div class="space-6"></div>	

								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>Cliente</th>
											<th>Saldo</th>
										</tr>
									</thead>
									<tbody>	
										<?php   
										$li_numcampo = $obj_miconexion_1->fun_numcampos(); // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion_1->fun_numcampos(); // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion_1,$li_numcampo,$li_indicecampo, 'NO_LISTAR'); // Dibuja la Tabla de Datos
										$obj_miconexion_1->fun_closepg($li_id_conex_1,$ls_resultado_1);
										?>
									</tbody>
									<tfoot>
										<tr>
											<th>Total</th>
											<th>
												<span class="label  label-info arrowed"> 
													<?php echo number_format($TotalVenta,2,",",".").' Bs'; ?>
												</span> 
											</th>
										</tr>
									</tfoot>
								</table>
	
						</div>
					
						<div class="col-xs-12 col-sm-6">
							<div class="row">
								<div class="col-xs-11 label label-lg label-success arrowed-in arrowed-right">
									<b>Prestamos</b>
								</div>
							</div>
							
							<div class="space-6"></div>	
						
							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Cliente</th>
										<th>Saldo</th>
									</tr>
								</thead>
								<tbody>	
									<?php   
									$li_numcampo = $obj_miconexion_2->fun_numcampos(); // Columnas que se muestran en la Tabla
									$li_indicecampo = $obj_miconexion_2->fun_numcampos(); // Referencia al indice de la columna clave
									fun_dibujar_tabla($obj_miconexion_2,$li_numcampo,$li_indicecampo, 'NO_LISTAR'); // Dibuja la Tabla de Datos
									$obj_miconexion_2->fun_closepg($li_id_conex_2,$ls_resultado_2);
									?>
								</tbody>
								<tfoot>
									<tr>
										<th>Total</th>
										<th>
											<span class="label  label-info arrowed"> 
												<?php echo number_format($CtxCob,2,",",".").' Bs'; ?>
											</span> 
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					
					</div> <!-- /.row tabla principal -->
					<div class="row">
						
						
						<div class="col-xs-12">
						<h5 class="header smaller lighter blue">							
							<strong>Cuentas por Pagar</strong>
						</h5>
							<div class="col-xs-12 col-sm-6">
									<div class="row">
										<div class="col-xs-11 label label-lg arrowed-in arrowed-right">
											<b>Pasivos</b>
										</div>
									</div>
									
									<div class="space-6"></div>	
									

									<table id="dynamic-table" class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Acreedor</th>
												<th>Monto</th>
											</tr>
										</thead>
										<tbody>	
											<?php   
											if($tarea == "M"){
												$li_numcampo = $obj_miconexion->fun_numcampos(); // Columnas que se muestran en la Tabla
												$li_indicecampo = $obj_miconexion->fun_numcampos(); // Referencia al indice de la columna clave
												fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'NO_LISTAR'); // Dibuja la Tabla de Datos
												$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
											}
										?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th>
												<th>
													<span class="label label-info arrowed"> 
														<?php echo number_format($pasivos,2,",",".").' Bs'; ?>
													</span>
												</th>

											</tr>
										</tfoot>
									</table>
								</div>
							</div>
				
						
				
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- main-content Principal -->

	<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
	<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
	<input type="hidden" name="modo" value="<?php echo $modo;?>">    

	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			
			$(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
			} );
			
		</script>

	<script src="../../js/funciones.js"></script>
  


</body>
</html>























