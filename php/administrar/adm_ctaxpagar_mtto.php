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
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
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
	ELIMINA UN REGISTRO DE LA TABLA ABONO
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
	if ($tarea == "A_A"){
		//if($o_PagMonto <= 	$x_debe ){
			$o_total = number_format($o_total,2,".","");
			$x_PagInteres = $x_PagInteres == ''?0:$x_PagInteres;
		
			$ls_sql = "INSERT INTO t04_abono(			
				fk_factura, 
				fe_fecha, 
				nu_monto,
				nu_interes,	
				tx_observacion, 
				fk_indicador)
			VALUES (
				$x_movimiento, 
				now(),					
				$o_PagMonto, 
				$x_PagInteres,
				'$x_observacion', 
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
		/*}else{
			echo "<script language='javascript' type='text/javascript'>alert('Monto supera el Debe');</script>";
		}*/
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Agrega un Abono a una factura
-------------------------------------------------------------------------------------------*/
	if ($tarea == "A_B"){
			$o_total = number_format($o_total,2,".","");
		
			$ls_sql = "INSERT INTO t01_detalle(
					  fk_factura, 
					  nu_cantidad, 
					  nu_precio, 
					  tx_unidad,
					  tx_observacion,					  
					  fe_fecha_registro ,
					  fk_responsable
					)
				VALUES (
					$x_movimiento,
					1,
					$o_PagMonto,
					'UND',
					'$x_observacion',
					now(),
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
		
	}
	

/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra los datos de la Factura
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT t20_factura.fk_responsable, UPPER(s01_persona.tx_nombre||' '||s01_persona.tx_apellido),
			to_char(fe_fecha_factura,'DD/MM/YYYY'), 
			tx_concepto, f_calcular_factura($x_movimiento),
			UPPER(tx_concepto)
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
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}

/*-------------------------------------------------------------------------------------------
	LEE DATOS DE ABONO A CAPITAL Y ABONO A INTERES
-------------------------------------------------------------------------------------------*/	
	$ls_sql ="	SELECT sum(nu_monto), sum(nu_interes) FROM t04_abono WHERE fk_factura = $x_movimiento";
	//echo $ls_sql;		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$SumaAbono	    = $row[0];
		$SumaInteres    = $row[1];	
		$x_debe			= $o_total - $SumaAbono;
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}	

/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra la lista de ABONOS realizados
-------------------------------------------------------------------------------------------*/
  	$i=0;
	/*$ls_sql = "SELECT to_char(fe_fecha,'dd-TMMon-yyyy'), nu_monto, nu_interes, nu_monto + nu_interes, tx_observacion, UPPER(s01_persona.tx_nombre),  pk_abono
					FROM t04_abono
					INNER JOIN s01_persona ON s01_persona.co_persona = t04_abono.fk_indicador
					WHERE fk_factura= $x_movimiento
					order by fe_fecha desc";
		*/			
	$ls_sql = "SELECT nu_referencia, fecha,  nombre, tx_observacion, nu_interes, monto_a , monto_b, tipo, id  
				FROM v07_mov_abono_detalle
				WHERE fk_factura= $x_movimiento";
				
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);	
		
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
?>

<div class="container-fluid">
			<div class="page-header">
				<h1>
					Creditos
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
										Punto de Cuenta
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
													
													<th class="hidden-480">
														<i class="ace-icon fa fa-user"></i>
														Cliente
													</th>
													<th>
														<i class="ace-icon fa 	fa-credit-card  "></i>
														Concepto
													</th>
													
													<th>
														<i class="ace-icon fa fa-dollar "></i>
														Total
													</th>
													
													<th class="hidden-480">
														<i class="ace-icon fa fa-download "></i>
														Capital
													</th>
													
													<th class="hidden-480">
														<i class="ace-icon fa fa-download "></i>
														Interes
													</th>
													
													<th>
														<i class="ace-icon fa fa-clock-o "></i>
														Saldo Pendiente
													</th>
													
																									
												</tr>
											</thead>

											<tbody>
												<tr>	
													<td class="hidden-480"><?php echo $o_fecha;?></td>													
													<td class="hidden-480"><?php echo $o_cliente;?></td>													
													<td class=""><?php echo $o_factura;?></td>												
													<td class=""><?php echo number_format($o_total,2,",","."); ?></td>													
													<td class="hidden-480"><?php echo number_format($SumaAbono,2,",","."); ?></td>	
													<td class="hidden-480"><?php echo number_format($SumaInteres,2,",","."); ?></td>			
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
												<label  class="col-sm-3 control-label no-padding-right"  for="o_factura">Monto</label>
												<div class="col-sm-7" >
													<input name="o_PagMonto" value="<?php echo $o_PagMonto;?>" id="factura" type="text" class="form-control" onkeypress = "return validardec(event)" placeholder="Monto">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_factura">Interes</label>
												<div class="col-sm-7" >
													<input name="x_PagInteres" value="<?php echo $x_PagInteres;?>" id="factura" type="text" class="form-control" onkeypress = "return validardec(event)" placeholder="Aplica para Pagos">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="x_observacion">Concepto</label>
												<div class="col-sm-7" >
													<input name="x_observacion" value="<?php echo $x_observacion;?>" type="text" class="form-control" onKeyPress="return validarAlfa(event)" placeholder="Descripcion">
												</div>
											</div>
																															
											<div class="form-group center">												
												<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-prev">
													<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
													Regresar
												</button>
												
												<button type="button" onClick="Guardar_sol();" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-plus  icon-on-right bigger-110"></i>
													Solicitar
												</button>		
												
												<button type="button" onClick="Guardar_pag();" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-minus  icon-on-right bigger-110"></i>
													Pagar
												</button>
											</div>
										
										</div>
										
											<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
											<input type="hidden" name="x_pagar" value="<?php echo $x_pagar;?>">
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
											<input type="hidden" name="o_total" value="<?php echo $o_total;?>">
											<input type="hidden" name="x_debe" value="<?php echo $x_debe;?>">
											<input type="hidden" name="x_fecha_ini" value="<?php echo $x_fecha_ini;?>">
											<input type="hidden" name="x_fecha_fin" value="<?php echo $x_fecha_fin;?>">
											<input type="hidden" id = "input_filtro" name="input_filtro" value="<?php echo $input_filtro;?>">	
											
										</form>
									</div>
								</div>
							</div>
						</div>	<!-- /.row cargar abono -->
							
					<div class="space-4"></div>
							
						
					<div class="row">
						<div class="col-xs-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr class="bg-primary" > 
										<th>Ref</th>
										<th>Fecha</th>
										<th>Nombre</th>
										<th>Concepto</th>
										<th>Abono Interes</th>
										<th>Abono Capital</th>
										<th>Ingreso</th>	
										<th>Debe</th>			
										<th>Borrar</th>
								</tr>
								</thead>
								<tbody>	
									<?php   
										if($tarea == "M"){
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_PAGAR',$x_debe); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
										}
									?>
								</tbody>
							</table>
						</div> 
					</div> <!-- /.row tabla principal -->

				
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
       
	   
	 function Guardar_sol(){		
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				document.formulario.tarea.value = 'A_B';
				document.formulario.action = "adm_ctaxpagar_mtto.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
		
	} 
	
	 function Guardar_pag(){		
		resul =  parseFloat(document.formulario.o_total.value);
		if( resul > 0){
			if(campos_blancos(document.formulario) == false){
				if (confirm('Esta conforme con los Datos Ingresados?') == true){	
					document.formulario.tarea.value = 'A_A';
					document.formulario.action = "adm_ctaxpagar_mtto.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		}else{
			alert("Operacion Invalida")
		}	
	} 
	 
	/*function Guardar(operacion){		
		resul =  parseFloat(document.formulario.o_total.value);
		if( resul > 0){
			if(campos_blancos(document.formulario) == false){
				if (confirm('Esta conforme con los Datos Ingresados?') == true){	
					document.formulario.tarea.value = operacion;
					document.formulario.action = "adm_ctaxpagar_mtto.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		}else{
			alert("Operacion Invalida")
		}	
	}*/
	
	function Eliminar_Pago(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.x_pagar.value = identificador;
			document.formulario.action = "adm_ctaxpagar_mtto.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}

	function Atras(parametros){
		document.formulario.tarea.value = "X";
		document.formulario.action = "adm_ctaxpagar_view.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}	
	
</script>
 

</html>

