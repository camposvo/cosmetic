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

	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
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
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$x_fecha_actual = date('d/m/Y h:i');
	

/*-------------------------------------------------------------------------------------------
	RUTINAS: para ELIMINAR una actividad 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "DELETE FROM t04_abono WHERE pk_abono = '$x_pagar' ";
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}	
	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Agrega un Abono a una factura
-------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		if($o_PagMonto <= 	$x_debe ){
			$o_total = number_format($o_total,2,".","");
		
			$ls_sql = "INSERT INTO t04_abono(			
				fk_factura, 
				fe_fecha, 
				nu_monto, 
				tx_observacion, 
				fk_indicador)
			VALUES (
				$x_movimiento, 
				now(),					
				$o_PagMonto, 
				'$x_referencia',
				$co_usuario		
			);";
						
			//echo $ls_sql;
						
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado == 0){
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
			}else{
				echo "<script language='javascript' type='text/javascript'>alert('Transaccion Exitosa');</script>";
				$x_debe = $x_debe - $o_PagMonto;
			}
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Monto supera el Debe');</script>";
		}
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra los datos de la Factura
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT t20_factura.fk_responsable, UPPER(s01_persona.tx_nombre),
			 to_char(fe_fecha_factura,'DD/MM/YYYY'), 
			tx_factura, f_calcular_factura($x_movimiento),
			UPPER(tx_concepto),
			f_calcular_abono($x_movimiento),
			(f_calcular_factura($x_movimiento) - f_calcular_abono($x_movimiento)) as Debe
			FROM t20_factura
			INNER JOIN s01_persona ON t20_factura.fk_cliente = s01_persona.co_persona
			WHERE pk_factura = $x_movimiento";
	//echo $ls_sql;
		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$co_usuario	    = $row[0];
		$o_cliente	    = $row[1];	
		$o_fecha        = $row[2];
		$o_factura      = $row[3];
		$o_total        = $row[4];
		$o_observacion  = $row[5];
		$SumaAbono      = $row[6];
		$x_debe         = $row[7];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	//$x_debe = number_format(($o_total - $SumaAbono),2,",",".");

	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra la lista de ABONOS realizados
-------------------------------------------------------------------------------------------*/
  	$i=0;
	$ls_sql = "SELECT to_char(fe_fecha,'DD/MM/YYYY'), UPPER(s01_persona.tx_nombre),  nu_monto,
				f_abono_anterior(nu_referencia,fk_factura), $o_total - f_abono_anterior(nu_referencia,fk_factura) as Deuda , tx_observacion, pk_abono
					FROM t04_abono
					INNER JOIN s01_persona ON s01_persona.co_persona = t04_abono.fk_indicador
					WHERE fk_factura= $x_movimiento
					ORDER BY pk_abono DESC";
					
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	
		
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>


<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Ventas
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box widget-color-green">
								<div class="widget-header">
									<h5 class="widget-title bigger lighter">
										<i class="ace-icon fa fa-table"></i>
										Factura
									</h5>
								
								</div>

								<div class="widget-body">
									<div class="widget-main no-padding">
										<table class="table table-striped table-bordered table-hover">
											<thead class="thin-border-bottom">
												<tr>
													<th class="hidden-480">
														<i class="ace-icon fa fa-calendar "></i>
														Fecha
													</th>
													<th>
														<i class="ace-icon fa 	fa-credit-card  "></i>
														Factura
													</th>
													<th class="hidden-480">
														<i class="ace-icon fa fa-user"></i>
														Cliente
													</th>
													
													<th>
														<i class="ace-icon fa fa-dollar "></i>
														Total
													</th>
													
													<th class="hidden-480">
														<i class="ace-icon fa fa-download "></i>
														Abono
													</th>
													<th>
														<i class="ace-icon fa fa-clock-o "></i>
														Debe
													</th>
													
																									
												</tr>
											</thead>

											<tbody>
												<tr>	
													<td class="hidden-480"><?php echo $o_fecha;?></td>
													
													<td class=""><?php echo $o_factura;?></td>
													
													<td class="hidden-480"><?php echo $o_cliente;?></td>
												
													<td class=""><?php echo number_format($o_total,2,",","."); ?></td>
													
													<td class="hidden-480"><?php echo number_format($SumaAbono,2,",","."); ?></td>
													
													<td class="">
														<span class="label label-warning"> <?php echo number_format($x_debe,2,",",".");  ?></span>
													</td>
													
													
												</tr>

											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div><!-- /.span -->	
					</div>
					
					
					<div class="space-6"></div>	
								
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
																								
												
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Monto</label>
												<div class="col-sm-7" >
													<input name="o_PagMonto" value="<?php echo $o_PagMonto;?>" type="text" class="form-control" onkeypress = "return validardec(event)" placeholder="Monto a Pagar">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Ref</label>
												<div class="col-sm-7" >
													<input name="x_referencia" value="<?php echo $x_referencia;?>"  type="text" class="form-control" placeholder="Referencia">
												</div>
											</div>
											
											<div class="form-group center">												
												<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
													Regresar
												</button>
												
												<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
									
										
										</div>	
											
												<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
												<input type="hidden" name="x_pagar" value="<?php echo $x_pagar;?>">
												<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
												<input type="hidden" name="x_debe" value="<?php echo $x_debe;?>">
												<input type="hidden" name="x_vendedor" value="<?php echo $x_vendedor;?>">
												<input type="hidden" name="x_cliente" value="<?php echo $x_cliente;?>">   
												<input type="hidden" name="x_fecha" value="<?php echo $x_fecha;?>">
												<input type="hidden" name="input_filtro" 	value="<?php echo $input_filtro;?>">
												<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">
											
										</form>
									</div>
								</div>
							</div>
						</div>	
							
							<div class="space-4"></div>
							
						
					<div class="row">
						<div class="col-xs-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
								<tr class="bg-primary" >
									<th>Fecha</th>
									<th>Responsable</th>
									<th>Abono</th>
									<th>Total Abono</th>
									<th>Deuda</th>
									<th class="hidden-480">Referencia</th>
									<th>Borrar</th>
								</tr>
							</thead>
							<tbody>	
								<?php   
									if($tarea == "M"){
										$li_numcampo = $obj_miconexion->fun_numcampos()-8; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_ABONO',0); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
									}
								?>
							</tbody>
						</table>
					</div> 
					</div>

				
				</div>
			
		</div> <!-- /.page-content -->

</body>

	<!--  SISTEMA   -->	
	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
	    
   // When the document is ready
        
	$(document).ready(function () {
		
		window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
		$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
		})
		//show datepicker when clicking on the icon
		.next().on(ace.click_event, function(){
			$(this).prev().focus();
		});
	
	
	});
       
	    
	function Guardar(){
		num1 = parseFloat(document.formulario.o_PagMonto.value);
		num2 = parseFloat(document.formulario.x_debe.value);
		resul = num1 - num2;

		if( resul <= 0 && num1 > 0){
			if(campos_blancos(document.formulario) == false){
				if (confirm('Esta conforme con los Datos Ingresados?') == true){	
					document.formulario.tarea.value = "A";
					document.formulario.action = "adm_venta_cobrar.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		}else{
			alert("El Monto Invalido")
		}	
	}
	
	function Eliminar_Pago(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.x_pagar.value = identificador;
			document.formulario.action = "adm_venta_cobrar.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}

	function Atras(parametros){
		document.formulario.tarea.value = "X";
		document.formulario.action = "adm_venta_view.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}	
	
</script>
 

</html>

