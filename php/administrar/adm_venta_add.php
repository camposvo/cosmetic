<?php

session_start();
include_once("adm_utilidad.php");
$usu_autentico = isset($_SESSION['autentificado']) ? $_SESSION['autentificado'] : '';
if ($usu_autentico != "SI") {
	session_destroy();
	echo "<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
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
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>

	<style>
		.chosen-container,
		[class*="chosen-container"] {
			width: 100% !important;
		}


		.title-items {
			background: #438eb9 !important;
			color: white !important;
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
	$o_cliente = "";
	$o_proyecto = "";
	$x_pedido = "S";
	$x_descuento = 0;

	if (!$_GET) {
		foreach ($_POST as $nombre_campo => $valor) {
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo']) ? $_POST['modo'] : 'Insertar Nuevo Registro';
	} else {
		foreach ($_GET as $nombre_campo => $valor) {
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo']) ? $_GET['modo'] : 'Insertar Nuevo Registro';
	}

	if ($_POST['det_Articulo']) { // Recibe el detalle de la Factura
		$a_precio 	= $_POST['det_Precio'];
		$a_cantidad	= $_POST['det_Cantidad'];
		$a_articulo	= $_POST['det_Articulo'];
	}


	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$arr_articulo	=  	Combo_Articulo_Venta();
	$arr_precios	=  	Combo_Articulo_Precio_Venta();



	$x_cant_item =  isset($x_cant_item) ? $x_cant_item : 0;

	/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una  Factura
-------------------------------------------------------------------------------------------*/
	$x_fecha_actual = date('Y/m/d');
	if ($tarea == "I") {
		$error_sql = false;
		$x_total = empty($x_total) ? 0 : number_format($x_total, 2, ".", "");
		$o_precio = number_format($o_precio, 2, ".", "");
		$x_cant_item = $x_cant_item == '' ? 0 : $x_cant_item;

		$ls_sql = "INSERT INTO t20_factura(
			fk_responsable, 
			fe_fecha_factura, 
			fe_fecha_registro, 
            tx_nota, 
			tx_tipo, 
			fk_cliente, 
            nu_total,
			fk_proyecto,
			in_pedido,
			nu_descuento	
			)
		VALUES (
			$co_usuario, 
			'$o_fecha', 
			now(), 
			'$x_referencia', 
			'VENTA',	
			$o_cliente,
			$x_total,
			$o_proyecto,
			'$x_pedido',
			$x_descuento
		);";

		if ($obj_miconexion->fun_consult("BEGIN TRANSACTION; " . $ls_sql) == 0) {
			fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
		} else {

			$ls_sql = "SELECT CURRVAL('t20s_pk_factura')	"; //Obtiene el ID de la factura
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if ($ls_resultado != 0) {
				$row = pg_fetch_row($ls_resultado, 0);
				$id_factura       = $row[0];
				$x_movimiento = $id_factura;
			} else {
				fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
			}


			if (count($a_articulo)) {
				foreach ($a_precio as $k => $valor) { // Ingresa todo el detalle de la factura
					$ls_sql = "INSERT INTO t01_detalle(
						  fk_factura, 
						  fk_articulo,	
						  nu_cantidad, 
						  nu_precio, 
						  tx_unidad, 
						  nu_cant_item, 
						  fe_fecha_registro 						
						)
					VALUES (
						$id_factura,
						$a_articulo[$k],
						$a_cantidad[$k],
						$a_precio[$k],
						'$a_und[$k]',
						0,
						'$o_fecha' 				
						);";
					//echo $ls_sql;

					if ($obj_miconexion->fun_consult($ls_sql)) {
					} else {
						$error_sql = true;
						fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
					}
				}
			}

			if ($error_sql) {
				$ls_sql_FIN = " ROLLBACK; ";
				$msg = "¡Error en la Consulta !";
			} else {
				$ls_sql_FIN = " COMMIT; ";
				$msg = "¡Factura Agregada Satisfactoriamente !";
				$tarea = 'M';
			}

			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql_FIN);
			$parametros = "tarea=A&x_vendedor=$x_vendedor&x_cliente=$x_cliente&filtro=$filtro";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}
	}

	/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra los Datos para Actualizar un Registro
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U") {
		$id_factura = $x_movimiento;

		$ls_sql = "UPDATE t20_factura SET 
			fe_fecha_registro =now(),
			tx_nota        ='$x_referencia', 
			tx_tipo           ='VENTA', 
			fk_cliente        = $o_cliente,
			fe_fecha_factura  ='$o_fecha', 
			nu_total		  = $x_total,
			fk_proyecto		  = $o_proyecto,
			in_pedido		  = '$x_pedido',	
			nu_descuento      = $x_descuento
		WHERE pk_factura   	  = $id_factura;";
		// NO SE ACTUALIZA EL RESPONSABLE 


		if ($obj_miconexion->fun_consult($ls_sql) == 0) {
			fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
		} else {

			$ls_sql = "DELETE FROM t01_detalle WHERE fk_factura = $id_factura; "; //
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if ($ls_resultado) {
			} else {
				fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
			}

			foreach ($a_precio as $k => $valor) {
				$ls_sql = "INSERT INTO t01_detalle(
					  fk_factura, 
					  fk_articulo,
					  nu_cantidad, 
					  nu_precio, 
					  tx_unidad, 
					  nu_cant_item, 
					  fe_fecha_registro 						
					)
				VALUES (
					$id_factura,
					$a_articulo[$k],
					$a_cantidad[$k],
					$a_precio[$k],
					'$a_und[$k]',
					0,
					'$o_fecha'				
					);";
				//echo $ls_sql;

				if ($obj_miconexion->fun_consult($ls_sql)) {
				} else {
					fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
				}
			}
			$msg = "¡Factura Actualizada Satisfactoriamente !";
			$parametros = "tarea=A&x_vendedor=$x_vendedor&x_cliente=$x_cliente&filtro=$filtro";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}

		$tarea = 'M';
	}

	/*-------------------------------------------------------------------------------------------
	RUTINAS: MOSTRAR DATOS
	-------------------------------------------------------------------------------------------*/
	if ($tarea == "M") {
		$ls_sql = "SELECT pk_factura, fk_responsable, fk_cliente, to_char(fe_fecha_factura, 'dd/mm/yyyy'),  tx_nota,
					tx_concepto,  nu_total, nu_abono, fk_proyecto, in_pedido, nu_descuento
					FROM t20_factura
					WHERE pk_factura = $x_movimiento";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0) {
			$row = pg_fetch_row($ls_resultado, 0);
			$id_factura      = $row[0];
			$co_usuario	    = $row[1];
			$o_cliente  	= $row[2];
			$o_fecha        = $row[3];
			$x_referencia      = $row[4];
			$x_observacion  = $row[5];
			$x_total        = $row[6];
			$x_abono        = $row[7];
			$o_proyecto        = $row[8];
			$x_pedido       = $row[9];
			$x_descuento      = $row[10];

			// Extrae el detalle de la factura
			$ls_sql = "SELECT fk_articulo, nu_cantidad, nu_precio,  
				  nu_cantidad * nu_precio as total
				  FROM t01_detalle
				  WHERE fk_factura = $id_factura ;";

			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if ($ls_resultado) {
				$mostrar_rs = true;
				// Consulta exitosa					
			} else {
				fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
			}
		} else {
			fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
		}
		$tarea = 'U';
		$modo = 'Actualizar Datos';
	}

	/*Se prepara para Insertar un Registro*/

	if ($tarea == "A") {
		$tarea = 'I';
		$modo = 'Ingresar Nueva Venta';
	}

	$x_fecha_registro = date('d/m/Y H:i');
	/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
	|------------------------------------------------------------------------------------------*/
	?>
	<div class="container-fluid">
		<div class="page-header">
			<h1>
				<?php echo $modo; ?>
			</h1>
		</div><!-- /.page-header -->
		<div class="row">
			<!-- ROW CONTENT BEGINS -->
			<div class="col-xs-12">

				<form class="form-horizontal" name="formulario">

					<div class="row">
						<div class="col-xs-12 col-sm-6 widget-container-col">
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1">Fecha
									Fact.</label>
								<div class="col-sm-4">
									<div class="input-group">
										<input name="o_fecha" value="<?php echo $o_fecha; ?>"
											class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1"
											type="text" data-date-format="dd/mm/yyyy" readonly />
										<span class="input-group-addon">
											<i class="fa fa-calendar bigger-110"></i>
										</span>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right">Cliente</label>
								<div class="col-sm-7">
									<select type="select-one" name="o_cliente" class="col-xs-10 col-sm-7 chosen-select "
										data-placeholder="Seleccionar">
										<?php
										if ($o_cliente == "") {
											echo "<option value='0' selected>Seleccionar</option>";
										} else {
											echo "<option value='0'></option>";
										}
										foreach ($arr_cliente as $k => $v) {
											$ls_cadenasel = ($k == $o_cliente) ? 'selected' : '';
											echo "<option value='$k' $ls_cadenasel>$v</option>";
										}
										?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right">Proyecto</label>
								<div class="col-sm-7">
									<select type="select-one" name="o_proyecto"
										class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Seleccionar">
										<?php
										if ($o_proyecto == "") {
											echo "<option value='0' selected>Seleccionar</option>";
										} else {
											echo "<option value='0'></option>";
										}
										foreach ($arr_rubro as $k => $v) {
											$ls_cadenasel = ($k == $o_proyecto) ? 'selected' : '';
											echo "<option value='$k' $ls_cadenasel>$v</option>";
										}
										?>
									</select>
								</div>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6 widget-container-col">
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right"
									for="x_descuento">Descuento</label>
								<div class="col-sm-7">
									<input class="col-xs-10 col-sm-7" name="x_descuento"
										value="<?php echo $x_descuento; ?>" id="x_descuento"
										onkeypress="return validardec(event)" onblur="calcular()" type="text"
										placeholder="Descuento">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="x_subtotal">Subtotal</label>
								<div class="col-sm-7">
									<input readonly="readonly" class="col-xs-10 col-sm-7" id="x_subtotal" name="x_subtotal"
										value="<?php echo $x_subtotal; ?>" id="x_subtotal" type="text"
										>
								</div>
							</div>


							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right">Total</label>
								<div class="col-sm-7">
									<input readonly="readonly" class="col-xs-10 col-sm-7" name="x_total"
										value="<?php echo $x_total; ?>" id="x_total" type="text">
								</div>
							</div>

						</div>

					</div>

					<div class="row">
						<div class="col-xs-12  text-center">
							<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')"
								class="btn-sm  btn-danger">
								<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
								Regresar
							</button>

							<button type="button"
								onClick="Guardar('<?php echo $tarea; ?>', '<?php echo $x_pedido; ?>');"
								class="btn-sm btn-success">
								<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
								Guardar
							</button>

							<button type="button" onClick="Guardar('<?php echo $tarea; ?>','N');"
								class="btn-sm btn-success">
								<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
								Cerrar Pedido
							</button>

						</div>
					</div>

					<div class="space-4"></div>

					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<table id="simple-table-new" class="table table-striped table-bordered">
								<thead>
									<tr class="bg-primary">
										<th width="50%">Articulo</th>
										<th width="10%">Cantidad</th>
										<th width="15%">Precio</th>
										<th width="15%">SubTotal</th>
										<th width="10%"></th>
									</tr>
								</thead>
								<tbody>
									<?php
									echo "<td><select  id='id_articulo' name='articulo'  class='chosen-select' onchange='findPrice()' >";
									$o_articulo = '';
									if ($o_articulo == "") {
										echo "<option value='0' selected></option>";
									} else {
										echo "<option value='0'></option>";
									}
									foreach ($arr_articulo as $k => $v) {
										$ls_cadenasel = ($k == $o_articulo) ? 'selected' : '';
										echo "<option value='$k' $ls_cadenasel>$v</option>";
									}
									echo "</select></td>";
									?>

									<td><input id="id_cantidad" name="cantidad" onkeyup="calcularSubtotal();" onkeypress="return validardec(event);" class="" size="7" type="text"></td>
									<td><input id="id_precio" n name="precio" onkeyup="calcularSubtotal();" onkeypress="return validardec(event);" class="" size="7" type="text"></td>
									<td><input id="id_subtotal" name="subtotal" class="" size="7" type="text"></td>
									<td><button type="button" onClick="nuevoArticulo();" class="btn-success btn-xs pull-left ">Agregar</button></td>

								</tbody>
							</table>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<table id="simple-table" class="table table-striped table-bordered">
								<thead class="">
									<tr class="title-items ">
										<th width="50%">Articulo</th>
										<th width="10%">Cantidad</th>
										<th width="15%">Precio</th>
										<th width="15%">SubTotal</th>
										<th width="10%"></th>
									</tr>
								</thead>
								<tbody id="tblDetalle">
									<?php
									if ($mostrar_rs) {
										$li_numcampo = $obj_miconexion->fun_numcampos() - 5; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos() - 1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion, $li_numcampo, $li_indicecampo, 'LISTAR_VENTA_ADD', 0); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex, $ls_resultado);
									}
									?>
								</tbody>
							</table>
						</div>
					</div>


					<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento; ?>">
					<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
					<input type="hidden" name="modo" value="<?php echo $modo; ?>">
					<input type="hidden" name="x_vendedor" value="<?php echo $x_vendedor; ?>">
					<input type="hidden" name="x_cliente" value="<?php echo $x_cliente; ?>">
					<input type="hidden" name="x_fecha" value="<?php echo $x_fecha; ?>">
					<input type="hidden" name="input_filtro" value="<?php echo $input_filtro; ?>">
					<input type="hidden" name="filtro" value="<?php echo $filtro; ?>">
					<input type="hidden" name="x_pedido" value="<?php echo $x_pedido; ?>">
					<input type="hidden" name="x_referencia" value="<?php echo $x_referencia; ?>">

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
			.next().on(ace.click_event, function() {
				$(this).prev().focus();
			});

		if (!ace.vars['touch']) {
			$('.chosen-select').chosen({
				allow_single_deselect: true
			});

			//resize chosen on sidebar collapse/expand

			$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
				if (event_name != 'sidebar_collapsed') return;
				$('.chosen-select').each(function() {
					var $this = $(this);
					$this.next().css({
						'width': '100%'
					});
				})
			});

			$('#chosen-multiple-style .btn').on('click', function(e) {
				var target = $(this).find('input[type=radio]');
				var which = parseInt(target.val());
				if (which == 2) $('#form-field-select-4').addClass('tag-input-style');
				else $('#form-field-select-4').removeClass('tag-input-style');
			});
		}


		window.parent
			.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
	});
</script>


<script type="text/javascript">
	/*-------------------------------------------------------------------------------------------
Se invoka en tiempo de ejecucion para activar la clase select multiple
--------------------------------------------------------------------------------------------*/
	function chosen(id_sele) {

		var precios = <?php echo json_encode($arr_precios); ?>;

		if (!ace.vars['touch']) {

			$("#" + id_sele).chosen().change(function() {
				//Get Input Price
				const fila = this.parentNode.parentNode;
				const price = fila.cells[2].firstChild;

				var valorSeleccionado = $(this).val();

				price.value = precios[valorSeleccionado];

				calcular();
			});

			$(".chosen-select").chosen({
				width: "100%"
			});

			$('.chosen-select').chosen({
				allow_single_deselect: true
			});

			//resize chosen on sidebar collapse/expand

			$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
				if (event_name != 'sidebar_collapsed') return;
				$('.chosen-select').each(function() {
					var $this = $(this);
					$this.next().css({
						'width': $this.parent().width()
					});
				})
			});

			//$(".chosen-select").chosen({width: "100%"}); 

			$('#chosen-multiple-style .btn').on('click', function(e) {
				var target = $(this).find('input[type=radio]');
				var which = parseInt(target.val());
				if (which == 2) $('#form-field-select-4').addClass('tag-input-style');
				else $('#form-field-select-4').removeClass('tag-input-style');
			});
		}

	}
	/*-------------------------------------------------------------------------------------------
	crea un campo dinamicamente
	--------------------------------------------------------------------------------------------*/
	function crearCampo(nombre, readonly, evento, valor) {
		td = document.createElement('td');
		txt = document.createElement('input');
		txt.type = 'text';
		txt.setAttribute('name', nombre);
		txt.setAttribute('value', valor);
		txt.setAttribute('size', 7);
		txt.setAttribute('onkeyup', evento);
		txt.setAttribute('onkeypress', 'return validardec(event);');

		if (readonly) {
			txt.setAttribute('readonly', 'readonly')
		}
		td.appendChild(txt);
		return td;
	}


	function crearPrecio(nombre, readonly, evento, valor) {
		td = document.createElement('td');
		txt = document.createElement('input');
		txt.type = 'text';
		txt.setAttribute('name', nombre);
		txt.setAttribute('value', valor);
		txt.setAttribute('size', 7);
		txt.setAttribute('onkeyup', evento);
		txt.setAttribute('onkeypress', 'return validardec(event);');

		if (readonly) {
			txt.setAttribute('readonly', 'readonly')
		}
		td.appendChild(txt);
		return td;
	}

	/*-------------------------------------------------------------------------------------------
	Calcula el total de todos los item de la factura
	--------------------------------------------------------------------------------------------*/
	function calcular() {
		cantidad = document.getElementsByName('det_Cantidad[]');
		precios = document.getElementsByName('det_Precio[]');
		subtotal = document.getElementsByName('det_Subtotal[]');
		descuento = document.getElementById('x_descuento').value;

		var_subtotal = 0;
		for (x = 0; x < cantidad.length; x++) {
			sub = redondeo2decimales(cantidad[x].value * precios[x].value);
			subtotal[x].value = sub;
			var_subtotal += sub;
		}

		if (descuento > var_subtotal) {
			alert("Descuento Invalido")
		} else {

			document.getElementById('x_subtotal').value = redondeo2decimales(var_subtotal);
			document.getElementById('x_total').value = redondeo2decimales(var_subtotal - descuento);
		}

	}

	function calcularSubtotal() {
		cantidad = document.getElementById('id_cantidad').value;
		precio = document.getElementById('id_precio').value;
		var sub = cantidad * precio;
		document.getElementById('id_subtotal').value = redondeo2decimales(sub);
	}

	function findPrice() {
		var precios = <?php echo json_encode($arr_precios); ?>;
		const selectElement = document.getElementById('id_articulo');
		precio = document.getElementById('id_precio');
		const valorSeleccionado = selectElement.value;

		if (valorSeleccionado == 0) {
			precio.value = "";
			return
		}

		precio.value = precios[valorSeleccionado];

	}

	/*-------------------------------------------------------------------------------------------
	crea dinamicamente un fila para un nuevo articulo
	--------------------------------------------------------------------------------------------*/
	function nuevoArticulo() {

		var articulo = <?php echo json_encode($arr_articulo); ?>;


		cantidad = document.getElementById('id_cantidad').value;
		precio = document.getElementById('id_precio').value;
		subtotal = document.getElementById('id_subtotal').value;
		item = document.getElementById('id_articulo').value;

		if (item === 0) return;
		if (cantidad === "") return;
		if (precio === "") return;
		if (subtotal === "") return;

		console.log(item)
		console.log(cantidad)
		console.log(precio)
		console.log(subtotal)





		destino = document.getElementById('tblDetalle');
		tr = document.createElement('tr');

		// Select Articulo
		td = document.createElement('td');
		sele = document.createElement('select');
		sele.name = 'det_Articulo[]';

		sele.setAttribute("class", "chosen-select  chosen-select-width");
		//sele.setAttribute("id", "form_field");


		for (var p in articulo) {
			opt = document.createElement('option');
			opt.value = p;

			if (p === item) {
				opt.selected = true;
			}
			opt.innerHTML = articulo[p];
			sele.appendChild(opt);
		}
		td.appendChild(sele);
		tr.appendChild(td);


		tr.appendChild(crearCampo('det_Cantidad[]', false, 'calcular()', cantidad));
		tr.appendChild(crearPrecio('det_Precio[]', false, 'calcular()', precio));
		tr.appendChild(crearCampo('det_Subtotal[]', true, '', subtotal));
		td = document.createElement('td');
		x = document.createElement('button');
		x.type = 'button';
		x.innerHTML = 'X';
		x.setAttribute('onClick', 'eliminarfila(this)');
		td.appendChild(x);
		tr.appendChild(td);
		destino.appendChild(tr);

		const fila = sele.parentNode.parentNode.rowIndex;
		const id_sele = "articulo_" + fila;

		sele.setAttribute("id", id_sele);
		chosen(id_sele);

		calcular();

		// Limpia los Inputs de Agregar
		document.getElementById('id_cantidad').value = "";
		document.getElementById('id_precio').value = "";
		document.getElementById('id_subtotal').value = "";
		const selectElement = document.getElementById('id_articulo');
		for (let i = 0; i < selectElement.options.length; i++) {
			selectElement.options[i].selected = false;
		}

		$('#id_articulo').trigger("chosen:updated")


	}
	/*-------------------------------------------------------------------------------------------
	Evento para ELIMINA una  fila de los registros DETALLE
	--------------------------------------------------------------------------------------------*/
	function eliminarfila(btn) {
		fila = btn.parentNode.parentNode;
		fila.parentNode.removeChild(fila);
		calcular();
	}
	/*-------------------------------------------------------------------------------------------
	GUARDAR un registro
	--------------------------------------------------------------------------------------------*/
	function Guardar(Identificador, pedido) {

		if (document.getElementById('x_total').value > 0) { //Hay elementos en el detalle
			if (campos_blancos(document.formulario) == false) {
				if (confirm('Esta conforme con los Datos Ingresados?') == true) {
					document.formulario.tarea.value = Identificador;
					document.formulario.x_pedido.value = pedido;
					document.formulario.action = "adm_venta_add.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		} else {
			alert("Debe cargar los Articulos")
		}
	}
	/*-------------------------------------------------------------------------------------------
	EDITA una venta
	--------------------------------------------------------------------------------------------*/
	function Editar_Venta(identificador) {
		document.formulario.tarea.value = "M";
		document.formulario.x_cod_actividad.value = identificador;
		document.formulario.action = "mae_actividad.php"
		document.formulario.method = "POST";
		document.formulario.submit();
	}
	/*-------------------------------------------------------------------------------------------
	REGRESA a la pantalla anterior
	--------------------------------------------------------------------------------------------*/
	function Atras(parametros) {
		document.formulario.tarea.value = "X";
		document.formulario.action = "adm_venta_view.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}
</script>

</html>