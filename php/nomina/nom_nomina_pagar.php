<?php 
	session_start();
	include_once ("nom_utilidad.php");
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
<title>La Peperana</title>

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
	
	if ($_POST['input_asig'])	{ // Recibe el detalle de la Factura
		$input_asig	= $_POST['input_asig'];
		$input_deduc	= $_POST['input_deduc'];
	
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex    = fun_conexion($obj_miconexion);
	$array_asig  		= Combo_Asignacion();
	$array_deduc  		= Combo_Deduccion();

	$co_usuario     =  $_SESSION["li_cod_usuario"];
	$x_fecha_actual =  date('Y/m/d');
	
/*------------------------------------------------------------------------------------------------|
|	Rutina: Se Utiliza Para Eliminar Un Proveedor.							 	  
|------------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		// Verificar la tabla detalle
		$ls_sql = "DELETE FROM t22_nomina WHERE co_nomina = '$pk_nomina' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$msg = "¡Registro Eliminado Exitosamente!";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		
	}		
/*-------------------------------------------------------------------------------------------
	RUTINAS: AGREGAR DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){
		// Calcula el total de asignaciones y deducciones
		$asignaciones = 0;
		$deducciones = 0;
		
		for ($i = 0; $i <= 6; $i++) {	
	      $asignaciones = $asignaciones + $input_asig[$i];
		  $deducciones = $deducciones + $input_deduc[$i];
		}
		
		//Calcula Salario Basico
		/*switch ($x_tipo_nomina) {
		   case 'DIARIA':
				 $basico = $o_salario * $o_dias_lab;
				 break;
		   case 'SEMANAL':
				 $basico = $o_salario * 5;
				 break;
		   case 'QUINCENAL':
				 $basico = $o_salario * 15;
				 break;
		}*/	
		
		$pago = $o_salario + ($asignaciones - $deducciones);
		
		$ls_sql = "INSERT INTO t22_nomina(
			fk_contrato, 
			fe_pago, 
			fe_inicial,
			fe_final,
			nu_pago, 
			nu_dia_nlaborado,
			nu_sueldo,	
			nu_asig_1, 
			nu_asig_2, 
			nu_asig_3, 
			nu_asig_4,            
			nu_dedu_1, 
			nu_dedu_2, 
			nu_dedu_3, 
            nu_dedu_4, 			
			fk_indicador,
			tx_nota
			
			)
		VALUES (
			$co_contrato, 
			'$o_fecha_pago',
			'$o_fecha_ini',
			'$o_fecha_fin',
			$pago,
			0,
			$o_salario,
			$input_asig[0],
			$input_asig[1],
			$input_asig[2],
			$input_asig[3],			
			$input_deduc[0],
			$input_deduc[1],
			$input_deduc[2],
			$input_deduc[3],			
			$co_usuario,
			'$x_nota'
			
			
		);";
						
		//echo $ls_sql;
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);	
		if ($ls_resultado != 0){
			$parametros = "";
			echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Ingresados Satisfactoriamente!');</script>";

		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}		
	}	
/*-------------------------------------------------------------------------------------------
	RUTINAS: MOSTRAR DATOS
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT tx_cedula, UPPER(tx_nombre), UPPER(tx_apellido), nu_salario, tx_nro_contrato, 
					to_char(fe_inicio, 'dd/mm/yyyy'), to_char(fe_fin, 'dd/mm/yyyy'), tx_descripcion, 
					extract(days from now()- fe_inicio) +2 as DiasTrabajados,
					(case when (t12_contrato.fe_fin <= now() ) then 'Activo' else 'Vencido' end) AS 	EstadoContrato,
					tx_tipo_nomina, tx_telefono_hab, tx_direccion_hab, tx_email, fe_nacimiento
					FROM t12_contrato 
				INNER JOIN s01_persona ON s01_persona.co_persona = t12_contrato.fk_trabajador
				WHERE t12_contrato.pk_contrato=  $co_contrato";
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$x_cedula           = $row[0];
		$x_nombre	        =  ucwords(strtolower($row[1]));
		$x_apellido    	    =  ucwords(strtolower($row[2]));
		$o_salario			= $row[3];
		$x_nro_contrato     = $row[4];
		$x_fecha_ini        = $row[5];
		$x_fecha_fin        = $row[6];
		$x_descripcion      = $row[7];
		$dias_trabajados    = $row[8];
		$estatus_contrato   = $row[9];
		$x_tipo_nomina      = $row[10];
		$telefono           = $row[11];
		$direccion          = ucwords(strtolower($row[12]));
		$email              = strtolower($row[13]);
		$fe_nacimiento      = $row[14];
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
/*-------------------------------------------------------------------------------------------
	RUTINAS:Para Mostrar las ultimas Pagos
-------------------------------------------------------------------------------------------*/
	$i=0;		
	$ls_sql = "SELECT to_char(fe_pago, 'dd-TMMon-yyyy'),  
				to_char(fe_inicial, 'dd-TMMon-yyyy') ||' al '|| to_char(fe_final, 'dd-TMMon-yyyy'),
				  (nu_asig_1 + nu_asig_2 + nu_asig_3 + nu_asig_4  + nu_sueldo) AS TotalAsig, 
				  (nu_dedu_1 + nu_dedu_2 + nu_dedu_3 + nu_dedu_4 ) AS TotalDeduc,
				  (nu_asig_1 + nu_asig_2 + nu_asig_3 + nu_asig_4 + nu_sueldo) - (nu_dedu_1 + nu_dedu_2 + nu_dedu_3 + nu_dedu_4 ) as PAGO ,
				  co_nomina
			  FROM t22_nomina
			WHERE fk_contrato= $co_contrato ORDER BY co_nomina DESC";
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);

	$x_fecha_registro = date('d/m/Y H:i');
	$tarea = 'I';
	
	for ($i = 0; $i <= 6; $i++) {	
		$input_asig[$i]=0;
		$input_deduc[$i]=0;	
	}
/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>
<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			Pago Salario
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
			<div class="row">								
				<div class="col-xs-12 col-sm-12 widget-container-col">
					<div class="widget-box ">
						<div class="widget-header widget-header-small">
							<h4 class="widget-title"><?php echo $x_nombre.' '.$x_apellido; ?></h4>
						</div>
			
						<div class="widget-body">
							<div class="widget-main">
								<form class="form-horizontal" name="formulario">							
								
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha de Pago</label>
										<div class="col-sm-4" >	
											<div class="input-group">
												<input name="o_fecha_pago" value="<?php echo $o_fecha_pago;?>" class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd/mm/yyyy" readonly />
												<span class="input-group-addon">
													<i class="fa fa-calendar bigger-110"></i>
												</span>
											</div>
										</div>
									</div>	
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Lapso</label>
										<div class="col-sm-6">	
											<div class="input-daterange input-group">
												<input class="input-sm form-control" name="o_fecha_ini" value="<?php echo $o_fecha_ini;?>" placeholder="Desde" type="text" readonly onchange ="Calcular()"/>
												<span class="input-group-addon">
													<i class="fa fa-exchange"></i>
												</span>
												<input class="input-sm form-control" name="o_fecha_fin" value="<?php echo $o_fecha_fin;?>" placeholder="Hasta" type="text" readonly onchange ="Calcular()" />
											</div>
										</div>
									</div>
								
								<!-- 	<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Tipo de Nomina</label>
										<div class="col-sm-6">
											<input  readonly class="input-sm form-control" name="x_tipo_nomina"  value="<?php echo $x_tipo_nomina; ?>"  type="text" onKeyPress="return validardec(event)" />
										</div>
									</div> 
									 -->
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Salario</label>
										<div class="col-sm-6">
											<input  class="input-sm form-control" name="o_salario"  value="<?php echo $o_salario; ?>"  type="text" onKeyPress="return validardec(event)" onchange ="Calcular()"/>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Dias No Trabajados</label>
										<div class="col-sm-6">
											<input  class="input-sm form-control" name="x_dias_no_lab"  value="<?php echo $x_dias_no_lab; ?>"  type="text" onKeyPress="return validardec(event)" />
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Nota</label>
										<div class="col-sm-6">
											<input  class="input-sm form-control" name="x_nota"  value="<?php echo $x_nota; ?>"  type="text" onKeyPress="return validalfa(event)" />
										</div>
									</div>
									
									<div class="widget-body">
										<div class="widget-main">
											<div class="alert alert-info">
												<div id="x_total" ></div>												
											</div>
										</div>
									</div>
									
									<div class="col-xs-12 col-sm-6 widget-container-col">
										<div class="widget-box ">											
											<div class="widget-header widget-header-small">
												<h4 class="widget-title"> Asignaciones </h4>
											</div>																														
											
											<div class="widget-body">
												<div class="widget-main">	
												

												<!-- <div class="form-group">
													<label class="col-sm-8 control-label left "><?php echo $o_dias_lab;?> Dias Laborados.</label>
													<div class="col-sm-3">
														<input readonly class="input-sm form-control" name="o_dias_lab"  value="<?php echo $o_dias_lab*$o_pago; ?>"  type="text"  />
													</div>
												</div>  -->
														
												<?php	
													for ($i = 0; $i <= 3; $i++) {	
														echo '<div class="col-sm-8  checkbox">
																<label>
																	<input name="a_check[]" value="'.$i.'" class="ace ace-checkbox-2" '. $activo_asig[$i] .' type="checkbox" onClick="activar(this,'.$i.')"/>
																	<span class="lbl">'.$array_asig[$i].'</span>
																</label>															
															</div>														
															<div class="form-group">
																<div class="col-sm-3">														
																	<input  readonly class="input-sm form-control" name="input_asig[]"  value="'.$input_asig[$i].'"  type="text" onKeyPress="return validardec(event)" onKeyup ="Calcular()"/>															
																</div>
															</div>';
													}														
												?>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-xs-12 col-sm-6 widget-container-col">
										<div class="widget-box ">
											
											<div class="widget-header widget-header-small">
												<h4 class="widget-title"> Deducciones </h4>
											</div>
								
											<div class="widget-body">
												<div class="widget-main">
													
													<!-- <div class="form-group">
														<label class="col-sm-8 control-label left "><?php echo $o_dias_lab;?> Dias No Laborados</label>
														<div class="col-sm-3">
															<input readonly class="input-sm form-control" name="o_dias_lab"  value="<?php echo $o_dias_lab*$nu; ?>"  type="text" onKeyPress="return validardec(event)" />
														</div>
													</div> -->
														
													<?php	
														for ($i = 0; $i <= 3; $i++) {	
															echo '<div class="col-sm-8  checkbox">
																	<label>
																		<input  name="d_check[]" value="'.$i.'" class="ace ace-checkbox-2"'. $activo_deduc[$i] .' type="checkbox" onClick="activar(this,'.$i.')" />
																		<span class="lbl">'.$array_deduc[$i].'</span>
																	</label>															
																</div>														
																<div class="form-group">
																	<div class="col-sm-3">														
																		<input  readonly class="input-sm form-control" name="input_deduc[]"  value="'.$input_deduc[$i].'"  type="text" onKeyPress="return validardec(event)" onKeyup ="Calcular()" />															
																	</div>
																</div>';
														}														
													?>
												</div>
												
											</div>
										</div>
									</div>
									
									
								<div class="form-group center">												
									<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
										<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
										Regresar
									</button>
									
									<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
										<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
										Guardar
									</button>																								
								</div>
																
								<input name="co_contrato" type="hidden" value="<?php echo $co_contrato;?>">
								<input name="pk_nomina" type="hidden" value="<?php echo $pk_nomina;?>">
								<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
								<input type="hidden" name="modo" value="<?php echo $modo;?>">   
								<input name="co_trabajador" type="hidden" value="<?php echo $co_trabajador;?>">
								<input name="x_cedula" value="<?php echo $x_cedula;?>" id="x_cedula" type="hidden" ><!-- /.box-body -->
								<input name="x_tipo_nomina" value="<?php echo $x_tipo_nomina;?>" id="x_tipo_nomina" type="hidden" >
								</form>
							</div>
						</div>
					</div>
				</div>
			
			
			</div> <!-- /.Row datos -->
			
	
			<div class="row">
				<div class="col-xs-12">
					<table id="simple-table" class="table table-striped table-bordered table-hover">
						<thead>
							<tr class="bg-primary" >	
								<th>Fecha</th>
								<th>Periodo</th>
								<th>Asignaciones</th>
								<th>Deducciones</th>
								<th>Pago</th>
								<th></th>
							</tr>
						</thead>
						<tbody>	
							<?php   
								$li_numcampo = 0; // Columnas que se muestran en la Tabla
								$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
								fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_PAGO'); // Dibuja la Tabla de Datos
								$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
							?>
						</tbody>
					</table>
				</div> 
			</div>  <!-- /.row Tabla resumen pagos  -->
			
		</div> <!-- /.row Datos  -->
	</div> <!-- /.row tabla principal -->
	
		<!-- /.VENTANA MODAL A LA DERECHA CON DATOS DEL TRABAJADOR -->
	<div id="right-menu" class="modal aside" data-body-scroll="false" data-offset="true" data-placement="right" data-fixed="true" data-backdrop="false" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<div class="table-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							<span class="white">&times;</span>
						</button>
						Datos del Trabajador
					</div>
				</div>

				<div class="modal-body">
					<h6 class="blue"><b><?php echo $x_nombre.' '.$x_apellido; ?></b></h6>
					<h6><?php echo '<b class="blue"> C.I: </b>'.$x_cedula; ?></h6>	
					<h6><?php echo '<b class="blue"> Tef: </b>'.$telefono; ?></h6>	
					<h6><?php echo '<b class="blue"> Dir: </b>'.$direccion; ?></h6>	
					<h6><?php echo '<b class="blue"> Email: </b>'.$email; ?></h6>						
				</div>
			</div><!-- /.modal-content -->

			<button class="aside-trigger btn btn-info btn-app btn-xs ace-settings-btn" data-target="#right-menu" data-toggle="modal" type="button">
				<i data-icon1="fa-plus" data-icon2="fa-minus" class="ace-icon fa fa-plus bigger-110 icon-only"></i>
			</button>
		</div><!-- /.modal-dialog -->
	</div>	
		
		
	
	
	
</div> <!-- /.page-content -->
</body>

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>


<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
				
			// PRUEBAS CON VENTANA MODAL
			$('.modal.aside').ace_aside();
				
			$('#aside-inside-modal').addClass('aside').ace_aside({container: '#my-modal > .modal-dialog'});
			
			$(document).one('ajaxloadstart.page', function(e) {
				//in ajax mode, remove before leaving page
				$('.modal.aside').remove();
				$(window).off('.aside')
			});
				
				
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			
			.next().on(ace.click_event, function(){//show datepicker when clicking on the icon
				$(this).prev().focus();
			})
						
			$('.input-daterange').datepicker({//or change it into a date range picker				
				autoclose:true,
				format: "dd/mm/yyyy"				
			});
		
		} );
	</script>

	<script type="text/javascript">
		
		Calcular(); // Inicializa el calculo del total a pagar
		
		function Guardar(identificador){
			if(campos_blancos(document.formulario) == false){
				if (confirm('Esta conforme con los Datos Ingresados?') == true){	
					document.formulario.tarea.value = identificador;
					document.formulario.action = "nom_nomina_pagar.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		}			
				
		function Atras(parametros){
			location.href = "nom_trabajador_view.php?" + parametros;
		}	
		
		
		function VerPago(identificador){
			alert("mostrar pago")
			/*	document.formulario.tarea.value = "E";
				document.formulario.pk_nomina.value = identificador;
				document.formulario.action = "nom_nomina_pagar.php"
				document.formulario.method = "POST";
				document.formulario.submit();*/
		}	
		
		function EliminarPago(identificador){
			if (confirm('Esta seguro de Eliminar el registro?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.pk_nomina.value = identificador;
				document.formulario.action = "nom_nomina_pagar.php"
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}	
		
		// Calcula de forma dinamica el Pago total del trabajador
		function Calcular(){		
		
			fecha_inicio   =  document.formulario.o_fecha_ini.value; 
			fecha_fin      =  document.formulario.o_fecha_fin.value;
			
			if(isNaN(fecha_inicio) && isNaN(fecha_fin)){
				dias_labor = restaFechas(fecha_inicio,fecha_fin);
				
			}else{
				dias_labor = 0;
			} 
			
			
			//alert(dias_labor);
		
			asignaciones   =  document.getElementsByName('input_asig[]');
			deducciones    =  document.getElementsByName('input_deduc[]');
			salario_basico =  document.formulario.o_salario.value;	
			tipo_nomina    =  document.formulario.x_tipo_nomina.value;	
		
			
			//salario_basico = parseInt(salario_basico) * dias_labor;	
			
			/*if(tipo_nomina == "QUINCENAL"){
				salario_basico = parseInt(salario_basico) * 15;				
			}else if(tipo_nomina == "SEMANAL"){
				salario_basico = parseInt(salario_basico) * 5;		
				
			}else if(tipo_nomina == "DIARIO"){
					salario_basico = parseInt(salario_basico);		
			}else{
				salario_basico = parseInt(salario_basico);		
			}*/
	
			asig =0;
			dedu =0;
			for(x=0; x < asignaciones.length; x++){				
				asig = parseInt(asig) + parseInt(asignaciones[x].value);
				dedu = parseInt(dedu) + parseInt(deducciones[x].value);				
			}
			
			total = parseInt(salario_basico) + parseInt(asig) - parseInt(dedu);
			
			salario_basico = formato_numero(salario_basico, 2, ',', '.');
			asig           = formato_numero(asig, 2, ',', '.');
			dedu		   = formato_numero(dedu, 2, ',', '.'); 		
			total          = formato_numero(total, 2, ',', '.');  

			
			resul = "Salario Basico: "+salario_basico+ "  Asignaciones:"+asig+"  Deducciones:"+dedu+"<strong>  Total:"+ total + "</strong>";		
			    $("#x_total").html(resul);
			
						
		}

		// Activa el checkbox respectivo para habilitar el input
		function activar(check, i){		
	
			if(check.name.substring(0,1)=="d") 	texto =	document.getElementsByName('input_deduc[]');
			else 								texto =	document.getElementsByName('input_asig[]');
			
			if(check.checked) texto[i].removeAttribute("readonly");			
			else			  texto[i].setAttribute("readonly","readonly");
						
		}	
			
		
		
		
	</script>

</html>