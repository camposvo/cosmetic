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
	$li_id_conex    = fun_conexion($obj_miconexion);
	
	
	$co_usuario        =  $_SESSION["li_cod_usuario"];
	$arr_proveedor     =  Combo_Proveedor();
	$arr_rubro         =  Combo_Rubro();
	$arr_clasificacion =  Combo_Clasificacion();
	$arr_almacen       =  Combo_Almacen();
	$x_fecha_actual    =  date('Y/m/d');
	

/*-------------------------------------------------------------------------------------------
	RUTINAS: MOSTRAR DATOS
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT 	tx_tipo,  
				UPPER(CLIENTE.tx_nombre)||' '||UPPER(CLIENTE.tx_apellido) as Clien, 
				UPPER(RESPONSABLE.tx_indicador) as Respon, 
				UPPER(nb_proveedor), 
				to_char(fe_fecha_factura, 'dd/mm/yyyy'),
				tx_factura, 
				f_calcular_factura(pk_factura) as Total,
				f_calcular_abono(pk_factura) as Abono,	
				tx_concepto	,
				to_char(pk_factura,'0000000')
			  FROM t20_factura
				LEFT JOIN s01_persona as CLIENTE ON t20_factura.fk_cliente = CLIENTE.co_persona
				INNER JOIN s01_persona as RESPONSABLE ON t20_factura.fk_responsable = RESPONSABLE.co_persona
				LEFT JOIN t03_proveedor ON t20_factura.fk_proveedor = t03_proveedor.pk_proveedor
		WHERE pk_factura = $x_movimiento";
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$o_tipo          = $row[0];
		$o_cliente       = $row[1];
		$x_responsable	 = $row[2];
		$x_proveedor	 = $row[3];
		$o_fecha         = $row[4];
		$x_factura       = $row[5];
		$o_total         = $row[6];
		$o_abono         = $row[7];
		$o_observacion   = $row[8];
		$x_id			   = $row[9];
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}

	// Extrae el detalle de la factura
	$ls_sql ="SELECT t02_proyecto.tx_nombre, nb_articulo, t01_detalle.nu_cantidad, nu_precio,  
		  t01_detalle.nu_cantidad * nu_precio as total, fk_articulo
		  FROM t01_detalle
		  LEFT JOIN t02_proyecto ON t01_detalle.fk_rubro = t02_proyecto.pk_proyecto		
		  LEFT JOIN t13_articulo ON t01_detalle.fk_articulo = t13_articulo.pk_articulo	
		  WHERE fk_factura = $x_movimiento ;";
	//echo $ls_sql;
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado){
		$mostrar_rs = true;
		// Consulta exitosa					
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
	
	$Titulo = $o_tipo=='GASTO'?'Proveedor':'Cliente';
	$Valor  = $o_tipo=='GASTO'?$x_proveedor:$o_cliente;
	
?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Detalle de Movimiento
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">					
						<div class="col-xs-12 col-sm-6">							
							<div class="profile-user-info profile-user-info-striped">								
								<div class="profile-info-row">
									<div class="profile-info-name"> ID </div>
										<div class="profile-info-value">
										<span class="blue" style="font-weight: bold;" ><?php echo $x_id;?></span> 

									</div>
								</div>
								
								<div class="profile-info-row">
									<div class="profile-info-name"> Tipo </div>
										<div class="profile-info-value">
										<span class="blue" style="font-weight: bold;" ><?php echo $o_tipo;?></span> 

									</div>
								</div>
								
								<div class="profile-info-row">
									<div class="profile-info-name"><?php echo $Titulo;?> </div>

									<div class="profile-info-value">
										<span class="blue" id="age"><?php echo $Valor;?></span>
									</div>
								</div>
								
								<div class="profile-info-row">
									<div class="profile-info-name">Responsable </div>

									<div class="profile-info-value">
										<span class="blue" id="age"><?php echo $x_responsable;?></span>
									</div>
								</div>
								
								<div class="profile-info-row">
									<div class="profile-info-name"> Fecha </div>

									<div class="profile-info-value">
										<span class="blue" id="age"><?php echo $o_fecha;?></span>
									</div>
									
								</div>
								
														
								<div class="profile-info-row">
									<div class="profile-info-name">Factura</div>
									<div class="profile-info-value">
										<span class="blue" id="age"><?php echo $x_factura;?></span>
									</div>
								</div>
								
								<div class="profile-info-row">
									<div class="profile-info-name">Total</div>
									<div class="profile-info-value">
										<span class="blue" id="age"><?php echo number_format($o_total,2,",","."); ?></span>
									</div>
								</div>
								
								<div class="profile-info-row">
									<div class="profile-info-name">Abono</div>
									<div class="profile-info-value">
										<span class="blue" id="age"><?php echo number_format($o_abono,2,",","."); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>

<br>
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr class="bg-primary" >
										<th>Proyecto</th>
										<th>Articulo</th>
										<th>Cantidad</th>
										<th>Precio</th>
										<th>SubTotal</th>
									</tr>
								</thead>
								<tbody id="tblDetalle">	
								<?php
									if($mostrar_rs){
										$li_numcampo = $obj_miconexion->fun_numcampos()-1; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, '', $li_hidden); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);										
									}
								?>
								</tbody>
							</table>
						</div> 
					</div>
						
					
								
					
					
					<div class="row">
					
						<h3 class="header blue lighter smaller">
								<i class="ace-icon fa"></i>
						</h3>
						
						<div class="col-xs-12">
							<button class="btn btn-danger btn-sm pull-left" onclick="Atras()">
								<i class="ace-icon fa fa-reply align-top bigger-125 "></i>
								Regresar
							</button>
						</div>
							
					</div>
				
				
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- main-content Principal -->





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
			
			} );
			
			
			
			
			
		</script>


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript"> 

	function Atras(){
		location.href = "pos_estado_cta_view.php";
	}	
	
	</script>

</body>
</html>


