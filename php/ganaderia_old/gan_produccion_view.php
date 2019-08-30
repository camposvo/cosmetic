<?php 
	session_start();
	include_once ("gan_utilidad.php");
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
	
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
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
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$co_usuario   =  $_SESSION["li_cod_usuario"];
	$arr_cliente  =  Combo_Cliente();
	$arr_vendedor =  Combo_Cliente();
	$arr_rubro    =  Combo_Rubro();
	$arr_abono    =  Combo_Abono();
	
/*-------------------------------------------------------------------------------------------
	ELIMINA UN REGISTRO DE LA TABLA GAN_GANADO
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT f_borrar_animal_temp($pk_animal )";		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);		
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$Result    = $row[0];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}
		
		if ($Result==0){
			$msg = "¡La factura existe pero no puede ser borrada !";    			
		}elseif($Result==1){
			$msg = "¡La factura fue borrada Satisfactoriamente !";			
		}elseif($Result==2){
			$msg = "¡La factura No existe !";
		}		
		echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='gan_animal_view.php'</script>";		
	}

/*-------------------------------------------------------------------------------------------
RUTINAS: Consulta  datos resumen
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT sum (f_calcular_factura(pk_factura)) as SumaTotal, 
				sum(f_calcular_abono(pk_factura)) as SumaAbono
				FROM t20_factura 
				INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente 
			WHERE t20_factura.tx_tipo='VENTA'  ".$ls_criterio;			

	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$SumaTotal    = $row[0];
		$SumaAbono    = $row[1];
		$SumaDebe     = $SumaTotal - $SumaAbono ;
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
			
/*-------------------------------------------------------------------------------------------
	CONSULTA LISTA DE ANIMALES
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT id_numero, UPPER(nb_nombre_animal), to_char(fe_nacimiento, 'dd-TMMon-yyyy'), 222,
				UPPER(gan_raza.nb_raza), 
				pk_ganado
			FROM gan_ganado
			LEFT JOIN gan_raza ON gan_raza.pk_raza = gan_ganado.fk_raza 
			WHERE in_sexo = 'HEMBRA'";
		
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
					Produccion Ganadera
				</h1>
			</div><!-- /.page-header -->
			
																				
					<div class="row">
						<div class="col-xs-12">	
							<form class="form-horizontal" name="formulario">	
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Animales
							</div>

							<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr>
										<th class="">Numero</th>
										<th class="">Nombre</th>
										<th class="">Fecha Nacimiento</th>
										<th class="">Edad</th>
										<th class="">Raza</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										$li_numcampo = 0; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_PRODUCCION'); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
										
									?>
								</tbody>
							</table>
							<input type="hidden" name="pk_animal" value="<?php echo $pk_animal;?>">
								<input type="hidden" name="tarea" 		 value="<?php echo $tarea;?>">
								<input type="hidden" name="modo" 		 value="<?php echo $modo;?>">
								<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">		
							</form>
						</div>
					</div> <!-- /.row tabla principal -->	
						
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->


	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	
	<script src="../../assets/js/daterangepicker.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>	
	
 	<!-- <script src="../../assets/js/jquery-ui.custom.min.js"></script>  -->			
	<!-- <script src="../../assets/js/ace-elements.min.js"></script> -->
	
	<script src="../../assets/js/ace.min.js"></script> 
	<script src="../../assets/js/chosen.jquery.min.js"></script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical			
			
			$('#dynamic-table').dataTable( {
				"lengthChange": false,
				"pageLength": 50,
				"aaSorting": [ [0,'desc'] ],
				"oLanguage": {
					"sInfo": "Mostrando (_START_ hasta _END_) de un total _TOTAL_",
					"sSearch": "Buscar:",						
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
				
				"columns": [
					null,
					null,
					null,
					null,
					null,
					{ "orderable": false }
				  ]
			} );
	
			//or change it into a date range picker
			$('.input-daterange').datepicker({
				
				autoclose:true,
				format: "dd/mm/yyyy"
				
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
			
						
								
			//  Tooltip
			$( ".open-event" ).tooltip({
				show: null,
				position: {
					my: "left top",
					at: "left bottom"
				},
				open: function( event, ui ) {
					ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
				}
			});
			
		} );
	</script>

	<script type="text/javascript"> 
						
		function Secar_Animal(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_produccion_secar.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
						
		function Produccion_Leche(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_produccion_leche.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		function Parto_Animal(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_produccion_parto.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		function Palpalcion_Animal(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_produccion_palpacion.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		function Peso_Animal(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.pk_animal.value = identificador;
			document.formulario.action = "gan_produccion_peso.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		
				
		
				
		function Limpiar(parametros){	
			location.href='adm_venta_view.php'
		}
		
	</script>
</body>
</html>