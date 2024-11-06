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
		<link rel="stylesheet" href="../../assets/css/chosen.min.css" /> 
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="../../assets/js/ace-extra.min.js"></script>	

		<style>
	.chosen-container, [class*="chosen-container"] {
	width: 100% !important;

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
	//	$a_item		= $_POST['det_Item'];
		$a_cantidad	= $_POST['det_Cantidad'];
		$a_articulo	= $_POST['det_Articulo'];		
	}


	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);	
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$arr_articulo	=  	Combo_Articulo_Venta();
	$arr_precios	=  	Combo_Articulo_Precio();

	
	$x_cant_item =  isset($x_cant_item)?$x_cant_item:0; 
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una  Factura
-------------------------------------------------------------------------------------------*/
	$x_fecha_actual = date('Y/m/d');	
	if ($tarea == "I"){
		$error_sql = false;
		$x_total = empty($x_total)?0:number_format($x_total,2,".","");
		$o_precio = number_format($o_precio,2,".","");
		$x_cant_item= $x_cant_item==''?0:$x_cant_item;
		
		$ls_sql = "INSERT INTO t20_factura(
			fk_responsable, 
			fe_fecha_factura, 
			fe_fecha_registro, 
            tx_nota, 
			tx_tipo, 
			fk_cliente, 
            nu_total 			
			)
		VALUES (
			$co_usuario, 
			'$o_fecha', 
			now(), 
			'$x_referencia', 
			'VENTA',	
			$o_cliente,
			$x_total			
		);";
						
		//echo $ls_sql;
						
		if($obj_miconexion->fun_consult("BEGIN TRANSACTION; ".$ls_sql) == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}else{
			
			$ls_sql ="SELECT CURRVAL('t20s_pk_factura')	"; //Obtiene el ID de la factura
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				$row = pg_fetch_row($ls_resultado,0);
				$id_factura       = $row[0];			
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
			}			
			
			echo count($a_articulo);
			if(count($a_articulo)){
				foreach($a_precio as $k=>$valor){ // Ingresa todo el detalle de la factura
					$ls_sql = "INSERT INTO t01_detalle(
						  fk_factura, 
						  fk_articulo,	
						  fk_rubro, 
						  nu_cantidad, 
						  nu_precio, 
						  tx_unidad, 
						  nu_cant_item, 
						  fe_fecha_registro 						
						)
					VALUES (
						$id_factura,
						$a_articulo[$k],
						$a_proyecto[$k],
						$a_cantidad[$k],
						$a_precio[$k],
						'$a_und[$k]',
						0,
						'$o_fecha' 				
						);";								
					//echo $ls_sql;
									
					if($obj_miconexion->fun_consult($ls_sql)){
						
					}else{
						$error_sql = true;
						//fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
					}	
				}	
			}
			
			if($error_sql){
			  $ls_sql_FIN = " ROLLBACK; ";
			  $msg = "¡Error en la Consulta !";
			  
			}else{
				 $ls_sql_FIN = " COMMIT; ";
				 $msg = "¡Factura Agregada Satisfactoriamente !";                     
			}

			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql_FIN);
			$parametros = "tarea=A&x_vendedor=$x_vendedor&x_cliente=$x_cliente&filtro=$filtro";							
			echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_venta_add.php?$parametros';</script>";
		}
	}

/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra los Datos para Actualizar un Registro
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$id_factura = $x_movimiento;
		
		$ls_sql ="UPDATE t20_factura SET 
			fe_fecha_registro =now(),
			tx_nota        ='$x_referencia', 
			tx_tipo           ='VENTA', 
			fk_cliente        = $o_cliente,
			fe_fecha_factura  ='$o_fecha', 
			nu_total		  = $x_total	
		WHERE pk_factura   	  = $id_factura;";
		// NO SE ACTUALIZA EL RESPONSABLE 

								
		if($obj_miconexion->fun_consult($ls_sql) == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}else{
			
			$ls_sql ="DELETE FROM t01_detalle WHERE fk_factura = $id_factura; "; //
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado){
			
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
			}			
			
			foreach($a_precio as $k=>$valor){
				$ls_sql = "INSERT INTO t01_detalle(
					  fk_factura, 
					  fk_articulo,
					  fk_rubro, 
					  nu_cantidad, 
					  nu_precio, 
					  tx_unidad, 
					  nu_cant_item, 
					  fe_fecha_registro 						
					)
				VALUES (
					$id_factura,
					$a_articulo[$k],
					$a_proyecto[$k],
					$a_cantidad[$k],
					$a_precio[$k],
					'$a_und[$k]',
					0,
					'$o_fecha'				
					);";								
				//echo $ls_sql;
								
				if($obj_miconexion->fun_consult($ls_sql)){
					
				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
				}	
			}	
			 $msg = "¡Factura Actualizada Satisfactoriamente !";           
			$parametros = "tarea=A&x_vendedor=$x_vendedor&x_cliente=$x_cliente&filtro=$filtro";							
			echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_venta_view.php?$parametros';</script>";	
			
		}

		$tarea = 'M';
	}
/*-------------------------------------------------------------------------------------------
	RUTINAS: MOSTRAR DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){		
		$ls_sql ="SELECT pk_factura, fk_responsable, fk_cliente, to_char(fe_fecha_factura, 'dd/mm/yyyy'),  tx_nota,
					tx_concepto,  nu_total, nu_abono
					FROM t20_factura
					WHERE pk_factura = $x_movimiento";

					
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$id_factura      = $row[0];
			$co_usuario	    = $row[1];
			$o_cliente  	= $row[2];	
			$o_fecha        = $row[3];
			$x_referencia      = $row[4];
			$x_observacion  = $row[5];
			$x_total        = $row[6];
			$x_abono        = $row[7];
			
			// Extrae el detalle de la factura
			$ls_sql ="SELECT fk_rubro, fk_articulo, nu_cantidad, nu_precio,  
				  nu_cantidad * nu_precio as total
				  FROM t01_detalle
				  WHERE fk_factura = $id_factura ;";
			
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
		$modo= 'Ingresar Nueva Venta';
	}
	
	$x_fecha_registro = date('d/m/Y H:i');
/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>
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
												<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha Fact.</label>
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
												<label class="col-sm-3 control-label no-padding-right" >Cliente</label>
												<div class="col-sm-7" >	
													<select name="o_cliente" class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Selecciona un Cliente..." >
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
												<label  class="col-sm-3 control-label no-padding-right"  for="x_referencia">Ref</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="x_referencia" value="<?php echo $x_referencia;?>" id="x_referencia" type="text"  placeholder="Referencia">
												</div>
											</div>
																										
										
										</div>	
										
										<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
										<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
										<input type="hidden" name="modo" value="<?php echo $modo;?>"> 
										<input type="hidden" name="x_vendedor" value="<?php echo $x_vendedor;?>">
										<input type="hidden" name="x_cliente" value="<?php echo $x_cliente;?>">   
										<input type="hidden" name="x_fecha" value="<?php echo $x_fecha;?>">
										<input type="hidden" name="input_filtro" 	value="<?php echo $input_filtro;?>">
										<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">											
									
									</div>
								</div>
						</div>
					</div>
					
					<div class="space-4"></div>
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 ">
							<button type="button" onClick="nuevoArticulo();"  class="btn-success btn-xs pull-left ">
								<i class="ace-icon fa fa-plus align-top bigger-100 "></i>
								Add
							</button>	
						</div> 
					</div>
					
					<div class="space-4"></div>
		
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr class="bg-primary" >
										<th width="30%">Proyecto</th>
										<th width="30%">Articulo</th>
										<th width="10%">Cantidad</th>
										<th width="10%">Precio</th>
										<th width="10%">SubTotal</th>
										<th width="10%"></th>
									</tr>
								</thead>
								<tbody id="tblDetalle">	
								<?php
									if($mostrar_rs){
										$li_numcampo = $obj_miconexion->fun_numcampos()-6; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_VENTA_ADD', 0); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);										
									}
								?>
								</tbody>
							</table>
						</div> 
					</div>
						
			
					<div class="space-4"></div>
					
					
			
			
					<div class="row">
						
						<div class="col-xs-12 col-sm-8 ">
								<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn-sm  btn-danger">
									<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
									Regresar
								</button>
								
								<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn-sm btn-success">
									<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
									Guardar
								</button>																								

						</div>
							
												
						<div class="col-xs-12 col-sm-4 widget-container-col">
							<div class="widget-box ">
								<div class="widget-body">
									<div class="widget-main ">
									
										<div class="form-group">
											<label  class="col-sm-3 control-label no-padding-right">Total</label>
											<div class="col-sm-7" >
												<input readonly = "readonly" class="col-xs-10 col-sm-7" name="x_total" value="<?php echo $x_total;?>" id="x_total" type="text"  >
											</div>
										</div>
								
									</div>	
								</div>
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
				todayHighlight: true,
				
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
						 $this.next().css({'width': '100%'});
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
/*-------------------------------------------------------------------------------------------
Se invoka en tiempo de ejecucion para activar la clase select multiple
--------------------------------------------------------------------------------------------*/		
		function chosen(id_sele){

			var precios = <?php echo json_encode($arr_precios); ?>;
			
			if(!ace.vars['touch']) {

				 $("#"+id_sele).chosen().change(function() {
					//Get Input Price
					const fila = this.parentNode.parentNode;
					const price = fila.cells[3].firstChild;	
		
					var valorSeleccionado = $(this).val();	

					price.value = precios[valorSeleccionado];
					
					calcular();		
				}); 

				$(".chosen-select").chosen({width: "100%"}); 

				$('.chosen-select').chosen({allow_single_deselect:true}); 
			
				//resize chosen on sidebar collapse/expand
				
				$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
					if(event_name != 'sidebar_collapsed') return;
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});

						 console.log("$this.parent().width()");

						console.log($this.parent().width());
					})
				});	

				//$(".chosen-select").chosen({width: "100%"}); 
		
				$('#chosen-multiple-style .btn').on('click', function(e){
						var target = $(this).find('input[type=radio]');
					var which = parseInt(target.val());
					if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
					 else $('#form-field-select-4').removeClass('tag-input-style');
				});
			}
			
		}				
/*-------------------------------------------------------------------------------------------
crea un campo dinamicamente
--------------------------------------------------------------------------------------------*/	
		function crearCampo(nombre,readonly, evento){
			td= document.createElement('td');
			txt = document.createElement('input');
			txt.type = 'text';
			txt.setAttribute('name',nombre);
			txt.setAttribute('size',7);
			txt.setAttribute('onkeyup',evento); 
			txt.setAttribute('onkeypress','return validardec(event);');
			
			if(readonly){
				txt.setAttribute('readonly','readonly')
			}
			td.appendChild(txt);
			return td;		
		}	


		function crearPrecio(nombre,readonly, evento, valor){
			td= document.createElement('td');
			txt = document.createElement('input');
			txt.type = 'text';
			txt.setAttribute('name',nombre);
			txt.setAttribute('size',7);
			txt.setAttribute('onkeyup',evento); 
			txt.setAttribute('onkeypress','return validardec(event);');
			
			if(readonly){
				txt.setAttribute('readonly','readonly')
			}
			td.appendChild(txt);
			return td;		
		}	
			
/*-------------------------------------------------------------------------------------------
Calcula el total de todos los item de la factura
--------------------------------------------------------------------------------------------*/	
		function calcular(){		
			cantidad = document.getElementsByName('det_Cantidad[]');
			precios =  document.getElementsByName('det_Precio[]');
			subtotal = document.getElementsByName('det_Subtotal[]');	

			var_subtotal =0;
			for(x=0; x < cantidad.length; x++){
				sub = redondeo2decimales(cantidad[x].value * precios[x].value);
				subtotal[x].value = sub;
				var_subtotal += sub;			
			}
			document.getElementById('x_total').value = redondeo2decimales(var_subtotal);		
		}	
/*-------------------------------------------------------------------------------------------
crea dinamicamente un fila para un nuevo articulo
--------------------------------------------------------------------------------------------*/	
		function nuevoArticulo(){
			var proy = <?php echo json_encode($arr_rubro); ?>;
			var articulo = <?php echo json_encode($arr_articulo); ?>;

			// Mostramos los valores del array

			destino = document.getElementById('tblDetalle');
			tr = document.createElement('tr');
			
			//Select de Proyectos
			td = document.createElement('td');
			sele = document.createElement('select');
			sele.name = 'det_Proyecto[]';
			sele.setAttribute("class","chosen-select chosen-select-width");

			opt = document.createElement('option');
			opt.value = '0';
			opt.setAttribute('data-placeholder','Selecciona un Proveedor');
			sele.appendChild(opt);

			for (var p in proy){
				opt = document.createElement('option');
				opt.value = p;
				opt.innerHTML = proy[p];
				sele.appendChild(opt);
			}	

			td.appendChild(sele);

			tr.appendChild(td);
			//tr.appendChild(crearCampo('det_Item[]',false,''));	 */

			// Select Articulo
			td = document.createElement('td');
			sele = document.createElement('select');
			sele.name = 'det_Articulo[]';
			
			sele.setAttribute("class","chosen-select  chosen-select-width");
			sele.setAttribute("id","form_field");
		
			
			opt = document.createElement('option');
			opt.value = '0';
			opt.setAttribute('data-placeholder','Selecciona un Articulo');
			sele.appendChild(opt);

			
			for (var p in articulo){
				opt = document.createElement('option');
				opt.value = p;
				opt.innerHTML = articulo[p];
				sele.appendChild(opt);
			}	

			td.appendChild(sele);
			tr.appendChild(td);


			// otros
			
			tr.appendChild(crearCampo('det_Cantidad[]',false,'calcular()'));
			
			tr.appendChild(crearPrecio('det_Precio[]',false,'calcular()'));
			tr.appendChild(crearCampo('det_Subtotal[]',true,''));
			td = document.createElement('td');
			x = document.createElement('button');
			x.type = 'button';
			x.innerHTML = 'X';
			x.setAttribute('onClick','eliminarfila(this)');
			td.appendChild(x);
			tr.appendChild(td);
			destino.appendChild(tr);
			
			const fila = sele.parentNode.parentNode.rowIndex;
			const id_sele = "articulo_"+fila;

			sele.setAttribute("id",id_sele);
			//console.log(padre);

			//LLama el JS que se requiere para el Select Multiple
			chosen(id_sele);

			
		}	
/*-------------------------------------------------------------------------------------------
Evento para ELIMINA una  fila de los registros DETALLE
--------------------------------------------------------------------------------------------*/	
		function eliminarfila(btn){
			fila = btn.parentNode.parentNode;
			fila.parentNode.removeChild(fila);
			calcular();
		}	
/*-------------------------------------------------------------------------------------------
GUARDAR un registro
--------------------------------------------------------------------------------------------*/				
		function Guardar(Identificador){
			
			if (document.getElementById('x_total').value > 0){ //Hay elementos en el detalle
				if(campos_blancos(document.formulario) == false){
					if (confirm('Esta conforme con los Datos Ingresados?') == true){	
						document.formulario.tarea.value = Identificador;						
						document.formulario.action = "adm_venta_add.php";
						document.formulario.method = "POST";
						document.formulario.submit();
					}
				}
			}else{
				alert("Debe cargar los Articulos")
			}
		}		
/*-------------------------------------------------------------------------------------------
EDITA una venta
--------------------------------------------------------------------------------------------*/			
		function Editar_Venta(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_cod_actividad.value = identificador;
			document.formulario.action = "mae_actividad.php"
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
/*-------------------------------------------------------------------------------------------
REGRESA a la pantalla anterior
--------------------------------------------------------------------------------------------*/			
		function Atras(parametros){
			document.formulario.tarea.value = "X";
			document.formulario.action = "adm_venta_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}			
		
	</script>
</html>