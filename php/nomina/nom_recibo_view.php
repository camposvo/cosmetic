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
<title>BellinghieriCosmetic</title>
		
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>
				
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$encontrado = false;
	if (!$_GET){
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

/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para buscar y filtrar USUARIOS
--------------------------------------------------------------------------------------------*/	
		$i=0; $j=0;
		$ls_sql = "SELECT to_char(co_nomina,'0000000') , to_char(t20_factura.pk_factura,'0000000'), UPPER(s01_persona.tx_nombre), 
					to_char(fe_pago, 'dd-TMMon-yyyy'), to_char(fe_inicial, 'dd-TMMon-yyyy') ,to_char(fe_final, 'dd-TMMon-yyyy') , co_nomina 
			FROM t22_nomina 
			INNER JOIN t12_contrato ON t12_contrato.pk_contrato = t22_nomina.fk_contrato
			LEFT JOIN t20_factura ON t20_factura.fk_contrato =  t12_contrato.pk_contrato
			INNER JOIN s01_persona  ON t12_contrato.fk_trabajador = 	s01_persona.co_persona 	
			WHERE t12_contrato.in_activo ='S'
			ORDER BY co_nomina desc  ";
		
					
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		//echo $ls_sql;
		
		if($ls_resultado != 0){
			$tarea = "M";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
		
/*-------------------------------------------------------------------------------------------
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.             
|------------------------------------------------------------------------------------------*/
?>
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Trabajdores
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Trabajador
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th class='hidden-480'>Recibo</th>
											<th class='hidden-480'>Contrato</th>
											<th class=''>Empleado</th>
											<th class=''>Fecha Pago</th>
											<th class='hidden-480'>Fecha Inicial</th>
											<th class='hidden-480'>Fecha Final</th>
											<th ></th>
										</tr>
									</thead>
									<tbody>	
										<?php
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'RECIBO_PAGO'); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);		
										?> 
									</tbody>
								</table>
							<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
							<input name="co_usuario" type="hidden" value="<?php echo $co_usuario;?>">
							<input name="co_recibo" type="hidden" value="<?php echo $co_recibo;?>">
						</form>
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->

	<script src="../../js/funciones.js"></script>
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>	
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

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
					{ "orderable": false },
					{ "orderable": false },
					{ "orderable": false },
					{ "orderable": false }
				  ]
			} );
	
			//or change it into a date range picker
			$('.input-daterange').datepicker({
				
				autoclose:true,
				format: "dd/mm/yyyy"
				
			});
			
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

		function Buscar(){
			document.formulario.action = "nom_trabajador_view.php";
			document.formulario.tarea.value = "B";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		function ImprimirPago(identificador){
			pagina = "rep_recibo_pago.php?co_recibo="+identificador;
			window.open(pagina,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');

		}
		
		function Permiso(identificador){
			document.formulario.action = "nom_permiso_add.php";
			document.formulario.co_contrato.value = identificador;
			document.formulario.tarea.value = "M";
			document.formulario.method = "post";
			document.formulario.submit();
		}	
						
		function cambio(){
			document.formulario.o_parametro.value = "";
			document.formulario.o_parametro.focus();
		}
		
	
	</script>

</body>
</html>



