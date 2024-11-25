<?php 
/*---	DESCRIPCION : Agregar una Venta nuevo a la Base de Datos       --*/
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
		<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
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
	$mostrar_rs = false;
	
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
	
	//var_dump($_POST['det_Proyecto']);
	//var_dump($_POST['det_Precio']);
	
	if ($_POST['det_Proyecto'])	{ // Recibe el detalle de la Factura
		$a_proyecto	= $_POST['det_Proyecto'];
		$a_precio 	= $_POST['det_Precio'];
		$a_item		= $_POST['det_Item'];
		$a_cantidad	= $_POST['det_Cantidad'];
		$a_und		= $_POST['det_Und'];			
	}
	
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$x_cant_item =  isset($x_cant_item)?$x_cant_item:0; 
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una  Factura
-------------------------------------------------------------------------------------------*/
	$x_fecha_actual = date('Y/m/d');
	
	if ($tarea == "I"){
		$error_sql = false;
		$o_total = number_format($o_total,2,".","");
		$o_precio = number_format($o_precio,2,".","");
		$x_cant_item= $x_cant_item==''?0:$x_cant_item;
		
		$ls_sql = "INSERT INTO t20_factura(
			fk_responsable, 
			fe_fecha_factura, 
			fe_fecha_registro, 
			tx_tipo, 
			tx_concepto, 
			fk_cliente 
			)
		VALUES (
			$co_usuario, 
			'$o_fecha', 
			now(), 
			'CTAXCOBRAR',	
			'$x_observacion', 
			$o_cliente
		);";
						
		//echo $ls_sql;
						
		if($obj_miconexion->fun_consult($ls_sql) == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}else{
			$msg = "Cuenta Agregada Satisfactoriamente";
			$parametros = "tarea=A";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_ctaxcobrar_add.php?$parametros';</script>";
		}
	}


/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra los Datos para Actualizar un Registro
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$id_factura = $x_movimiento;
		
		$ls_sql ="UPDATE t20_factura SET 
			fk_responsable    = $co_usuario,
			fe_fecha_registro =now(),
			tx_tipo           ='CTAXCOBRAR', 
			tx_concepto       ='$x_observacion', 
			fk_cliente        = $o_cliente,
			fe_fecha_factura  ='$o_fecha'
		WHERE pk_factura   	  = $id_factura;";
								
		if($obj_miconexion->fun_consult($ls_sql) == 0){
			
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		
		}else{	
			
			echo "<script language='javascript' type='text/javascript'>alert('Cuenta Actualizada Exitosamente'); location.href='adm_ctaxcobrar_view.php'</script>";
		}

		$tarea = 'M';
	}

	
/*-------------------------------------------------------------------------------------------
	RUTINAS: MOSTRAR DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){		
		$ls_sql ="SELECT pk_factura, fk_responsable, fk_cliente, fe_fecha_factura,  
					tx_concepto,  nu_total, nu_subtotal, nu_abono
					FROM t20_factura
					WHERE pk_factura = $x_movimiento";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$id_factura      = $row[0];
			$co_usuario	    = $row[1];
			$o_cliente  	= $row[2];	
			$o_fecha        = $row[3];
			$x_observacion  = $row[4];
			$x_total        = $row[5];
			$x_subtotal     = $row[6];
			$x_abono    	= $row[7];
			
			// Extrae el detalle de la factura
			$ls_sql ="SELECT fk_rubro, nu_cant_item,nu_cantidad, tx_unidad, nu_precio,  
				  nu_cantidad * nu_precio as total
				  FROM t01_detalle
				  WHERE fk_factura = $id_factura ;";
			//echo $ls_sql;
			
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado){
				$mostrar_rs = true;
				// Consulta exitosa					
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
			}
			
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
		}
		$tarea = 'U';
		$modo= 'Actualizar Datos';
	}
	
	/*Se prepara para Insertar un Registro*/
	
	if ($tarea == "A"){
		$tarea = 'I';
		$modo= 'Crear Cuenta de Prestamo';
	}
	
	

	$x_fecha_registro = date('d/m/Y H:i');
/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					<?php echo $modo;?>
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
				<form class="form-horizontal" name="formulario">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-body">
									<div class="widget-main">
																					
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha</label>
												<div class="col-sm-4" >	
													<div class="input-group">
														<input name="o_fecha" value="<?php echo $o_fecha;?>" class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd/mm/yyyy" readonly />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Deudor</label>
												<div class="col-sm-7" >	
													<select name="o_cliente" class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Selecciona un Cliente...">
														<?php
															if ($o_cliente == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_cliente as $k => $v) {
																$ls_cadenasel =($k == $o_cliente)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>							
													</select>
												</div>													
											</div>
																
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" for="x_observacion" >Concepto</label>
												<div class="col-sm-9" >
													<textarea name="x_observacion" cols="2" id="x_observacion" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_observacion;?></textarea>
												</div>
											</div>	
										</div>	
										
										<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
										<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
										<input type="hidden" name="modo" value="<?php echo $modo;?>"> 
										<input type="hidden" name="x_vendedor" value="<?php echo $x_vendedor;?>">
										<input type="hidden" name="x_cliente" value="<?php echo $x_cliente;?>">   
										<input type="hidden" name="x_factura" value="<?php echo $x_factura;?>">
										<input type="hidden" name="x_proyecto" value="<?php echo $x_proyecto;?>">
										<input type="hidden" name="x_fecha_ini" value="<?php echo $x_fecha_ini;?>">
										<input type="hidden" name="x_fecha_fin" value="<?php echo $x_fecha_fin;?>">
										<input type="hidden" name="check" value="<?php echo $check;?>">	
											
									
									</div>
								</div>
						</div>
					</div>
					
					
					<div class="space-4"></div>
		
									
					
			
			
					<div class="row">						
						<div class="col-xs-12 col-sm-8 ">
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
					</div> 
				</form>
			</div> <!-- /.row tabla principal -->
		</div> <!-- /.page-content -->

</body>

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/chosen.jquery.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
	
		$('.date-picker').datepicker({
			autoclose: true,
			todayHighlight: true
		})
		//show datepicker when clicking on the icon
		.next().on(ace.click_event, function(){
			$(this).prev().focus();
		});
		
		if(!ace.vars['touch']) {
			$('.chosen-select').chosen({allow_single_deselect:true}); 
						
			//resize chosen on sidebar collapse/expand
			$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
				if(event_name != 'sidebar_collapsed') return;
				$('.chosen-select').each(function() {
					 var $this = $(this);
					 $this.next().css({'width': $this.parent().width()});
				})
			});	
	
			$('#chosen-multiple-style .btn').on('click', function(e){
				var target = $(this).find('input[type=radio]');
				var which = parseInt(target.val());
				if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
				 else $('#form-field-select-4').removeClass('tag-input-style');
			});
		}
		
		window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
	
	} );	
</script>
				
				

<script type="text/javascript">
	    
	
		    
	function Cancelar(parametros){
		location.href = "inscripcion_ficha_fichas.php?" + parametros;
	}
	
	function Guardar(Identificador){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				document.formulario.tarea.value = Identificador;
				
				document.formulario.action = "adm_ctaxcobrar_add.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}
	
	function calcular_total(){
		document.formulario.o_total.value = document.formulario.o_cantidad.value * document.formulario.o_precio.value;
	}
	
	function Editar_Venta(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_cod_actividad.value = identificador;
			document.formulario.action = "adm_ctaxcobrar_add.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
/*-------------------------------------------------------------------------------------------|
|	Funcin: 'Atras'															 	  		 |
|	Descripcin: Permite Regresar A La Pgina De Maestro De Almacenes.						 |
|-------------------------------------------------------------------------------------------*/
	function Atras(parametros){
		location.href = "adm_ctaxcobrar_view.php?" + parametros;
	}	

</script>
</html>