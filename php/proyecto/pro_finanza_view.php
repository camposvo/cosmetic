<?php 
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
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
<title>BellinghieriCosmetic</title>
	
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 	
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />	
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/jquery.gritter.min.css" />
	
	
	
	<script src="../../assets/js/ace-extra.min.js"></script>
	<script src="../../clases/Highstock-5.0.2/code/highstock.js"></script>
	<script src="../../clases/Highstock-5.0.2/code/modules/exporting.js"></script>
	<script src="../../clases/Highstock-5.0.2/code/highcharts-3d.js"></script>
	
	
	<style type="text/css">
		td.details-control {
			background: url('../../img/details_open.png') no-repeat center center;
			cursor: pointer;
		}
		tr.shown td.details-control {
			background: url('../../img/details_close.png') no-repeat center center;
		}
	</style>
			
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$o_cantidad  = 0;
	$o_cantidad2 = 0;
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
	
	$obj_miconexion_venta = fun_crear_objeto_conexion();
	$li_id_conex_venta = fun_conexion($obj_miconexion_venta);
	
	$obj_miconexion_gasto = fun_crear_objeto_conexion();
	$li_id_conex_gasto = fun_conexion($obj_miconexion_gasto);
	
	$obj_miconexion_vendedor = fun_crear_objeto_conexion();
	$li_id_conex_vendedor = fun_conexion($obj_miconexion_vendedor);
	
	$obj_miconexion_proyecto = fun_crear_objeto_conexion();
	$li_id_conex_proyecto = fun_conexion($obj_miconexion_proyecto);
	
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$arr_abono   =  Combo_Abono();
	
/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 GRAFICA 1 - VENTAS POR VENDEDOR GRAFICAS
---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT  UPPER(VENDEDOR.tx_nombre)||' '||UPPER(VENDEDOR.tx_apellido) as Vendedor,
						SUM(t01_detalle.nu_cantidad * t01_detalle.nu_precio) as monto						
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
				LEFT JOIN s01_persona AS VENDEDOR ON t20_factura.fk_responsable = VENDEDOR.co_persona					
			WHERE t20_factura.tx_tipo='VENTA' AND 
			t20_factura.in_pedido='N' AND 
			t20_factura.fk_proyecto = ".$pk_proyecto.  
			" GROUP BY VENDEDOR.co_persona, VENDEDOR.tx_nombre,VENDEDOR.tx_apellido ORDER BY VENDEDOR.tx_nombre ASC";
	
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);			
	if($ls_resultado != 0){		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$data_1[] ="{ name: '$row[0]',y: $row[1]}";
		}		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}		
	
	
/*-------------------------------------------------------------------------------------------
	GRAFICAS 2 VENTAS POR RUBRO
-------------------------------------------------------------------------------------------*/
	$i=0;
	$ls_sql = "SELECT   UPPER(ARTICULO.nb_articulo) as Articulo,
						SUM(t01_detalle.nu_cantidad * t01_detalle.nu_precio) as monto						
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
				LEFT JOIN t13_articulo AS ARTICULO ON t01_detalle.fk_articulo = ARTICULO.pk_articulo					
			WHERE t20_factura.tx_tipo='VENTA' AND 
			t20_factura.in_pedido='N' AND 
			t01_detalle.fk_rubro = ".$pk_proyecto.  
			" GROUP BY ARTICULO.nb_articulo ORDER BY ARTICULO.nb_articulo ASC";
	
	//echo $ls_sql;
		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);			
	if($ls_resultado != 0){		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$data_2[] ="{ name: '$row[0]',y: $row[1]}";
		}		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}	

/*-------------------------------------------------------------------------------------------
	GRAFICA 3 DE GASTOS
-------------------------------------------------------------------------------------------*/

	$ls_sql = "SELECT   UPPER(nb_clase) as tipo, 
						SUM(t01_detalle.nu_cantidad * t01_detalle.nu_precio) as monto	
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura				
				LEFT JOIN t13_articulo ON t01_detalle.fk_articulo = t13_articulo.pk_articulo 
				INNER JOIN t05_clase ON t13_articulo.fk_clase = t05_clase.pk_clase		
			WHERE t20_factura.tx_tipo='GASTO' AND t01_detalle.fk_rubro = ".$pk_proyecto.  
			" GROUP BY nb_clase ORDER BY nb_clase ASC ";
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);			
	if($ls_resultado != 0){		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$data_3[] ="{ name: '$row[0]',y: $row[1]}";
		}		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: permite MOSTRAR LOS DATOS
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT UPPER(tx_nombre), to_char(fe_inicial, 'dd/mm/yyyy') , nu_cantidad, fk_responsable, 
				t02_proyecto.tx_descripcion, fk_tipo_rubro, nu_muerte, t08_tipo_proyecto.nb_tipo_rubro
		FROM t02_proyecto
		LEFT JOIN t08_tipo_proyecto ON t02_proyecto.fk_tipo_rubro = t08_tipo_proyecto.pk_tipo_rubro
		WHERE pk_proyecto = $pk_proyecto";
		
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$o_nombre        = $row[0];
		$o_fecha	     = $row[1];
		$o_cantidad  	 = $row[2];	
		$x_responsable   = $row[3];
		$o_descripcion   = strtoupper($row[4]);
		$o_tipo_rubro    = $row[5];
		$x_muerte        = $row[6];
		$x_tipo_rubro  = $row[7];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
/*-------------------------------------------------------------------------------------------
	RUTINAS: RESUMEN DE GASTOS
-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum (t01_detalle.nu_cantidad * nu_precio) as TotalGasto 
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura	
			WHERE t20_factura.tx_tipo='GASTO' AND t01_detalle.fk_rubro = ".$pk_proyecto;
	

	//echo $ls_sql;
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$TotalGasto    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}	
		
		
	/*-------------------------------------------------------------------------------------------
	RUTINAS: RESUMEN DE VENTAS
	-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum (t01_detalle.nu_cantidad * nu_precio) as Total,
					sum (t01_detalle.nu_cant_item) as TotalItem,
					sum (t01_detalle.nu_cantidad) as TotalCantidad
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura	
			WHERE t20_factura.tx_tipo='VENTA' AND 
			t20_factura.in_pedido='N' AND 
			t20_factura.fk_proyecto = ".$pk_proyecto;
	
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$SumaTotal      = $row[0];
		$TotalItem      = $row[1];
		$TotalCantidad  = $row[2];
		$PromItem       = $TotalItem ==0? 0:($SumaTotal / $TotalItem); 
		$PromCant       = $TotalCantidad ==0? 0:($SumaTotal / $TotalCantidad); 
		$PromPeso       = $TotalItem ==0? 0:($TotalCantidad / $TotalItem); 
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}

/*-------------------------------------------------------------------------------------------
RUTINAS: LISTADO DE VENTAS POR RUBRO
-------------------------------------------------------------------------------------------*/
	$i=0;
	$ls_sql = "SELECT   UPPER(t21_categoria.nb_categoria) as Categoria,
						UPPER(ARTICULO.nb_articulo) as Articulo,
						SUM(t01_detalle.nu_cantidad) AS Cantidad,
						SUM(t01_detalle.nu_cantidad * t01_detalle.nu_precio) as monto						
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
				LEFT JOIN t13_articulo AS ARTICULO ON t01_detalle.fk_articulo = ARTICULO.pk_articulo
				LEFT JOIN t05_clase ON t05_clase.pk_clase = ARTICULO.fk_clase	
				LEFT JOIN t21_categoria ON t05_clase.fk_categoria = t21_categoria.pk_categoria				
			WHERE t20_factura.tx_tipo='VENTA' AND 
			t20_factura.in_pedido='N' AND
			t20_factura.fk_proyecto = ".$pk_proyecto.  
			" GROUP BY t21_categoria.nb_categoria, ARTICULO.nb_articulo ORDER BY t21_categoria.nb_categoria";
	
	//echo $ls_sql;
	$ls_resultado_proyecto =  $obj_miconexion_proyecto->fun_consult($ls_sql);
		
	if($ls_resultado_proyecto != 0){
		$tarea = "V";
	}else{
		fun_error(1,$li_id_conex_proyecto,$ls_sql,$_SERVER['PHP_SELF']);
	}		
	
	
/*-------------------------------------------------------------------------------------------
RUTINAS: LISTADO DE VENTAS POR VENDEDOR
-------------------------------------------------------------------------------------------*/
	$i=0;
	$ls_sql = "SELECT   VENDEDOR.co_persona, UPPER(VENDEDOR.tx_nombre)||' '||UPPER(VENDEDOR.tx_apellido) as Vendedor,
						SUM(t01_detalle.nu_cantidad * t01_detalle.nu_precio) as monto						
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
				LEFT JOIN s01_persona AS VENDEDOR ON t20_factura.fk_responsable = VENDEDOR.co_persona					
			WHERE t20_factura.tx_tipo='VENTA' AND 
			t20_factura.in_pedido='N' AND 
			t20_factura.fk_proyecto = ".$pk_proyecto.  
			" GROUP BY VENDEDOR.co_persona, VENDEDOR.tx_nombre,VENDEDOR.tx_apellido ORDER BY VENDEDOR.tx_nombre ASC";
	
	//echo $ls_sql;
	$ls_resultado_vendedor =  $obj_miconexion_vendedor->fun_consult($ls_sql);
		
	if($ls_resultado_vendedor != 0){
		$tarea = "V";
	}else{
		fun_error(1,$li_id_conex_venta,$ls_sql,$_SERVER['PHP_SELF']);
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: LISTA DE GASTOS
-------------------------------------------------------------------------------------------*/
	$i=0;
	$ls_sql = "SELECT   UPPER(nb_articulo) as tipo, 
						SUM(t01_detalle.nu_cantidad) AS Cantidad, 
						SUM(t01_detalle.nu_cantidad * t01_detalle.nu_precio) as monto	
				FROM t01_detalle
			    INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
				LEFT JOIN t13_articulo ON t01_detalle.fk_articulo = t13_articulo.pk_articulo 				
			WHERE t20_factura.tx_tipo='GASTO' AND t01_detalle.fk_rubro = ".$pk_proyecto.  
			" GROUP BY nb_articulo ORDER BY nb_articulo ASC ";
	//echo $ls_sql;
	$ls_resultado_gasto =  $obj_miconexion_gasto->fun_consult($ls_sql);

	if($ls_resultado_gasto != 0){
		$tarea_gasto = "G";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}	
	
	$Ganancia = $SumaTotal - $TotalGasto;	
	$Utilidad = $TotalGasto==0?0:($Ganancia * 100)/$TotalGasto;	
	
	if (!(isset($data3))) {
		$data3[]= 0;		
	}
	
	//print_r ($data3); // Imprime un arreglo por pantalla	
	
?>

<div class="container-fluid">
	<div class="page-header">
		<h1>
			<?php echo  "Finanzas del Proyecto";?>
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
		
			<div class="row">			
				<div class="col-xs-12 col-sm-6">
					<div class="profile-user-info profile-user-info-striped">						
						
						<div class="profile-info-row">
							<div class="profile-info-name"> Proyecto </div>

							<div class="profile-info-value">
								<span class="blue" style="font-weight: bold;" ><?php echo $o_nombre;?></span>
							</div>
						</div>

						<div class="profile-info-row">
							<div class="profile-info-name"> Tipo </div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $x_tipo_rubro;?></span>
							</div>
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name"> Fecha </div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $o_fecha;?></span>
							</div>								
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name">Descripcion</div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $o_descripcion;?></span>
							</div>
						</div>
						
					</div>					
				</div>
			
				<div class="col-sm-6 infobox-container">
					
					<div class="infobox infobox-blue">
						<div class="infobox-icon">
							<i class="ace-icon fa fa-dollar"></i>
						</div>

						<div class="infobox-data">
							<span class="infobox-data-number"><?php echo number_format($SumaTotal,2,",",".");  ?></span>
							<div class="infobox-content">Ventas</div>
						</div>
						
					</div>

					<div class="infobox infobox-red">
						<div class="infobox-icon">
							<i class="ace-icon fa fa-shopping-cart "></i>
						</div>

						<div class="infobox-data">
							<span class="infobox-data-number"><?php echo number_format($TotalGasto,2,",","."); ?></span>
							<div class="infobox-content">Gastos</div>
						</div>
					</div>
					
					
					<div class="infobox infobox-green">
						<div class="infobox-icon">
							<i class="ace-icon fa fa-dollar"></i>
						</div>

						<div class="infobox-data">
							<span class="infobox-data-number"><?php echo number_format($Ganancia,2,",","."); ?></span>
							<div class="infobox-content">Ganancias</div>
						</div>
						
					</div>

					<div class="infobox infobox-orange">
						<div class="infobox-icon">
							<i class="ace-icon fa fa-bolt "></i>
						</div>

						<div class="infobox-data">
							<span class="infobox-data-number"><?php echo number_format($Utilidad,2,",",".")."%"; ?></span>
							<div class="infobox-content">Utilidad</div>
						</div>
					</div>					
				</div>
			</div> <!-- /.row Datos  -->
	
			<div class="space-6"></div>
			<div class="row">
				<div class="col-xs-12 col-sm-12">
					<div class="form-group left">												
						<button type="button" onClick="Atras()" class="btn btn-sm  btn-danger">
							<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
							Regresar
						</button>																						
					</div>
				</div>
			</div>
			
			<!--  **********************  VENTAS POR VENDEDOR  ******************   -->
			
			<h3 class="header smaller  blue">
				<i class="ace-icon fa fa-bullhorn"></i>
				Ventas 
			</h3>
			
			<div class="row">								
				<div class="col-sm-12">
				
					<div class="col-sm-6">
						<table id="example" class="table table-bordered table-striped ">
							<div class="table-header ">									
								Ventas por Vendedor
							</div>		
							<thead>
								<tr>
									<th width="40px"></th>
									<th>Vendedor</th>
									<th>Monto Ventas</th>
								</tr>
							</thead>
							<tbody>
								<?php   
									$li_numcampo = 0;//$obj_miconexion_vendedor->fun_numcampos(); // Columnas que se muestran en la Tabla
									$li_indicecampo = $obj_miconexion_vendedor->fun_numcampos()-1; // Referencia al indice de la columna clave
									fun_dibujar_tabla($obj_miconexion_vendedor,$li_numcampo,$li_indicecampo, 'LISTAR_VENTA_VENDEDOR'); // Dibuja la Tabla de Datos
									$obj_miconexion_vendedor->fun_closepg($li_id_conex_vendedor,$ls_resultado_vendedor);
								?>				
							</tbody>
							 <tfoot>
								<tr>
									<td colspan="2" align="right"><strong>Total </strong></td>
									<td><strong><span class="blue "><?php echo number_format($SumaTotal,2,",","."); ?></span></strong></td>
								</tr>
							  </tfoot>	
						</table>
					</div><!-- /.col -->	
						
	
					<div class="col-sm-6 padding">
						<div id="container_1" style="padding: 10px; border-color:#E0E0E0; border-style: solid; border-width: 0.5px; margin: 0 height:300px; min-width: 310px"></div>
					</div><!-- /.widget-main -->
							
				</div><!-- /.col -->
			</div><!-- /.row -->
			<!--  **********************   VENTAS POR RUBRO   ******************   -->
			<h3 class="header smaller  blue">
				<i class="ace-icon fa fa-bullhorn"></i>
				Ventas 
			</h3>
			
			<div class="row">								
				<div class="col-sm-12">
				
						<table  id="dynamic-table" class="table table-striped table-bordered table-hover">
							<div class="table-header ">									
								Ventas por Rubro
							</div>								
							<thead>
								<tr>
									<th>Clase</th>
									<th>Articulo</th>
									<th>Precio Prom.</th>
									<th>Cantidad</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<?php   
									$li_numcampo = 0;// $obj_miconexion_proyecto->fun_numcampos(); // Columnas que se muestran en la Tabla
									$li_indicecampo = $obj_miconexion_proyecto->fun_numcampos()-1; // Referencia al indice de la columna clave
									fun_dibujar_tabla($obj_miconexion_proyecto,$li_numcampo,$li_indicecampo, 'LISTAR_VENTA_RUBRO'); // Dibuja la Tabla de Datos
									$obj_miconexion_proyecto->fun_closepg($li_id_conex_proyecto,$ls_resultado_proyecto);
								?>
								 <tr>
									<td align="right" colspan="3"><strong>Total</strong></td>
									<td><strong><span class="blue "><?php echo number_format($TotalCantidad,2,",","."); ?></span></strong></td>
									<td><strong><span class="blue "><?php echo number_format($SumaTotal,0,",","."); ?></span></strong></td>
								</tr> 
							</tbody>
						</table>								
					</div><!-- /.col -->	
							
			</div><!-- /.row -->
			
			<!-- <div class="row">								
					<div class="col-sm-12 padding">
						<div id="container_2" style="padding: 10px; border-color:#E0E0E0; border-style: solid; border-width: 0.5px; margin: 0 height: 300px; min-width: 310px"></div>
					</div>
			</div> -->
			
			<!--  **********************   GASTOS  ******************   -->
		
			
	<!-- 		<h3 class="header smaller blue">
				<i class="ace-icon fa fa-bullhorn"></i>
				Gastos
			</h3>
			
			<div class="row">								
				<div class="col-sm-12">
				
					<div class="col-sm-6">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<div class="table-header ">									
								Gastos
							</div>		
							<thead>
								<tr>
									<th>Concepto</th>
									<th>Cantidad</th>
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
								<?php   
										/* $li_numcampo = 0; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion_gasto->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion_gasto,$li_numcampo,$li_indicecampo, 'LISTAR_GASTO'); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex_gasto,$ls_resultado_gasto);
 */								?>
								<tr>
									<td colspan="2" align="right"><strong>Total</strong></td>
									<td><strong><span class="blue "><?php echo number_format($TotalGasto,2,",","."); ?></span></strong></td>
								</tr>
								
							</tbody>
						</table>								
					</div>
						
	
					<div class="col-sm-6 padding">
						<div id="container_3" style="padding: 10px; border-color:#E0E0E0; border-style: solid; border-width: 0.5px; margin: 0 height: 300px; min-width: 310px"></div>
					</div>
							
				</div>
			</div>
		 -->

	</div> <!-- /.row tabla principal -->
</div> <!-- /.page-content -->

</body>
	<script src="../../js/funciones.js"></script> 
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>

	<script type="text/javascript">
		
		function Atras(){
			location.href = "pro_proyecto_view.php?tarea=B";
		}	
		
		function calcular_total(){
			document.formulario.o_total.value = document.formulario.o_cantidad.value * document.formulario.o_precio.value;
		}
		
		function Limpiar(){
			document.formulario.reset();
		}	

	</script>
	
	

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			var table = $('#example').DataTable( {
				 "paging":   false,
				"ordering": false,
				"info":     false,
				 "searching": false,
				"columns": [
					{
						//"className":      'details-control',
						"orderable":      false,
						 "data":           null,
                		"defaultContent": ''
					},
					{ "orderable": false },
					{ "orderable": false }
				
				],
				"order": [[1, 'asc']]
			} ); 
		
					
			 // Add event listener for opening and closing details
			$('#example tbody').on('click', 'td.details-control', function () {
				var tr = $(this).closest('tr');				
				var row = table.row( tr);
				
				var id_vendedor = $(this).attr("id");	
				var id_proyecto = <?php echo $pk_proyecto; ?>;
				
				
					$.post("ajax_proy_finanzas.php", { id_vendedor: id_vendedor, id_proyecto: id_proyecto }, function(data){
						//alert( "Data Loaded: " + data );
						if ( row.child.isShown() ) {
							// This row is already open - close it
							row.child.hide();
							tr.removeClass('shown');
						}
						else {
							// Open this row
							row.child( data).show();
							tr.addClass('shown');
						}
						//tabla_hijo = data;
					}); 
				
			} );
			
				/*********************** VENTAS POR VENDEDOR *******************************/
				
				/* Highcharts.chart('container_1', {
					 chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},		
					  title: {
						text: 'Vendedores'
					},	
					
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false
							},
							showInLegend: true
						}
					},
					series: [{
						name: 'Vendedor',
						colorByPoint: true,
						data: [<?php //echo join($data_1, ','); ?>]
					}]
				}); */
				
				/*********************** VENTAS  POR RUBRO*******************************/
				
				/* Highcharts.chart('container_2', {
					 chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},		
					  title: {
						text: 'Ventas Rubros'
					},	
					
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false
							},
							showInLegend: true
						}
					},
					series: [{
						name: 'Vendedor',
						colorByPoint: true,
						data: [<?php //echo join($data_2, ','); ?>]
					}]
				}); */
				
				/*********************** GASTOS *******************************/
			/* 	
				Highcharts.chart('container_3', {
					 chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},		
					  title: {
						text: 'Clase Gastos'
					},	
					
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false
							},
							showInLegend: true
						}
					},
					series: [{
						name: 'Vendedor',
						colorByPoint: true,
						data: [<?php //echo join($data_3, ','); ?>]
					}]
				}); */
				
					
			
			
			
		} );	
				
	
	</script>
	
				
 


</html>