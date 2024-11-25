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
	
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" /> 
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../clases/Highmaps-5.0.2/code/modules/exporting.js"></script>
	<script src="../../clases/Highmaps-5.0.2/code/highmaps.js"></script>
	<script src="../../clases/Highmaps-5.0.2/code/modules/data.js"></script>
	
	<style type="text/css">
#container {
    height: 600px; 
    min-width: 310px; 
    max-width: 800px; 
    margin: 0 auto; 
}
.loading {
    margin-top: 10em;
    text-align: center;
    color: gray;
}
</style> 

	
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$Disponible  = 0;
	$Banco       = 0;
	$sw = 0;
	
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
	$arr_tipo =     Combo_TipoMovimiento();
	
	// Carga variables con los ultimos 30 DIAS POR DEFECTO
	$x_fecha_actual        =  date('d/m/Y');
	$x_fecha_mespasado 	   =  date('d/m/Y', strtotime('-29 day')) ;   

	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE BANCO
----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(nu_capital) FROM t15_banco";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Banco    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	  
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE CUENTAS POR COBRAR
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
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE CUENTAS POR PAGAR
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
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE MONTO DISPONIBLE
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT f_cuenta	FROM vm02_edo_cuenta
			WHERE ref = (SELECT Max(ref) from vm02_edo_cuenta)";
	//echo $ls_sql;		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado)>0){
			$row = pg_fetch_row($ls_resultado,0);
			$Disponible    = $row[0];
		}	
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
	$Caja = $Disponible - $Banco;	
	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA LOS INGRESOS Y EGRESOS 
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT SUM(ingreso) as Ingreso, SUM(egreso) as Egreso
				FROM vm02_edo_cuenta	";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Ingreso   = $row[0];
		$Egreso    = $row[1];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}		


/*-------------------------------------------------------------------------------------------
	 GRAFICA 2 - INGRESOS VS EGRESOS
--------------------------------------------------------------------------------------------*/	
	for ($i = 1; $i <= 12; $i++) {
		$serie1_bar[$i] = "0"; //Crear un array ingreso
		$serie2_bar[$i] = "0"; //egreso
	}

	$total_ingreso=0;
	$total_egreso =0;
	
	$ls_sql = "SELECT SUM(ingreso), SUM(egreso), EXTRACT(month FROM fecha) AS MES 
			FROM vm02_edo_cuenta 
			WHERE EXTRACT(YEAR FROM fecha) = '2016'
			GROUP BY EXTRACT(month FROM fecha)  ORDER BY EXTRACT(month FROM fecha)  asc";
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			//$timestamp = strtotime($row[2])*1000; // Convierte la Fecha en Formato UNIX	en milisegundos
			$serie1_bar[$row[2]] = "$row[0]"; //Crear un array ingreso
			$serie2_bar[$row[2]] = "$row[1]"; //egreso
			$total_ingreso = $total_ingreso + $row[0];
			$total_egreso = $total_egreso + $row[1];
		}
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	

/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 GRAFICA 1 - INGRESOS VS EGRESOS
---------------------------------------------------------------------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT SUM(ingreso), SUM(egreso), to_char(fecha, 'mm/dd/yyyy')
			FROM vm02_edo_cuenta GROUP BY fecha ORDER BY fecha asc";	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);			
	if($ls_resultado != 0){		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$timestamp = strtotime($row[2])*1000; // Convierte la Fecha en Formato UNIX	
			//$timestamp = $timestamp * 1000;  // Convierte el tiempo en milisegundos
			$serie1[] = "[$timestamp, $row[0]]"; //Crear un array ingreso
			$serie2[] = "[$timestamp, $row[1]]"; //egreso
		}		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
		
	//print_r ($serie1_bar); // Imprime un arreglo por pantalla		
	$collaps = ($sw==1)?'':'collapsed';	
	
?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
					
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
				
								
				<div class="col-sm-12">
					<div id="container" style="min-width: 410px; max-width: 900px"></div>
				</div><!-- /.col -->
												
					
				</div> <!-- ROW CONTENT END -->
			</div>  <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- main-content Principal -->

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	




	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		$(function () {

    // Prepare random data
    var data = [
        ['DE.SH', 728],
        ['DE.BE', 710],
        ['DE.MV', 963],
        ['DE.HB', 541],
        ['DE.HH', 622],
        ['DE.RP', 866],
        ['DE.SL', 398],
        ['DE.BY', 785],
        ['DE.SN', 223],
        ['DE.ST', 605],
        ['DE.NW', 237],
        ['DE.BW', 157],
        ['DE.HE', 134],
        ['DE.NI', 136],
        ['DE.TH', 704],
        ['DE.', 361]
    ];

    $.getJSON('../../geojson/mapa5.geojson', function (geojson) {

        // Initiate the chart
        Highcharts.mapChart('container', {

            title: {
                text: 'Mapa BellinghieriCosmetic'
            },

            mapNavigation: {
                enabled: true,
                buttonOptions: {
                    verticalAlign: 'bottom'
                }
            },

            colorAxis: {
            },

            series: [{
                data: data,
                mapData: geojson,
                joinBy: ['code_hasc', 0],
                keys: ['code_hasc', 'value'],
                name: 'Random data',
                states: {
                    hover: {
                        color: '#BADA55'
                    }
                },
                dataLabels: {
                    enabled: true,
                    format: '{point.properties.postal}'
                }
            }]
        });
    });
});
					
	</script>	
  
	<script type="text/javascript"> 
			
		function Buscar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "pos_estado_cta.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
	
			
		function Limpiar(parametros){	
			location.href='pos_estado_cta.php'
		}
		
	</script>
</body>
</html>