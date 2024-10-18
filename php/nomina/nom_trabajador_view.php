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
		if($cedula!='')$arr_criterio[$i++] = "tx_cedula = ".$cedula;
		if($nombre!='')$arr_criterio[$i++] = "tx_nombre like '".strtoupper($nombre)."%' ";
		if($apellido!='')$arr_criterio[$i++] = "tx_apellido like '".strtoupper($apellido)."%'";
		for($j=0;$j<$i;$j++){
			$ls_criterio = $ls_criterio.(($ls_criterio=='')?$arr_criterio[$j]:" and ".$arr_criterio[$j]);
		}
		$ls_criterio = $ls_criterio==''?'':" and ".$ls_criterio;
		$li_tampag = 100;
		$ls_sql = "SELECT to_char(t20_factura.pk_factura,'0000000'), UPPER(tx_nombre)||' '||UPPER(tx_apellido), nu_salario, to_char(fe_inicio, 'dd-TMMon-yyyy'), 
						tx_descripcion, pk_contrato
					FROM t12_contrato 
					INNER JOIN s01_persona ON s01_persona.co_persona = t12_contrato.fk_trabajador
					LEFT JOIN t20_factura ON t20_factura.fk_contrato =  t12_contrato.pk_contrato
					WHERE t12_contrato.in_activo ='S' ".$ls_criterio." ORDER BY fe_inicio";
		
					
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
											<th class=''>Contrato</th>
											<th class=''>Nombre</th>
											<th class='hidden-480'>Salario</th>
											<th class='hidden-480'>Fecha Ingreso</th>
											<th ></th>
										</tr>
									</thead>
									<tbody>	
										<?php
											if($tarea == "M"){
												$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-6;
												$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1;
												fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"TRABAJADOR");
											}
										?> 
									</tbody>
								</table>
							<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
							<input name="co_usuario" type="hidden" value="<?php echo $co_usuario;?>">
							<input name="co_contrato" type="hidden" value="<?php echo $co_contrato;?>">
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
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			
				$('#dynamic-table').dataTable( {
					"lengthChange": false,
					"pageLength": 50,
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
						{ "orderable": false }
					  ]
				} );
		
							
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
		
		function PagarNomina(identificador){
			document.formulario.action = "nom_nomina_pagar.php";
			document.formulario.co_contrato.value = identificador;
			document.formulario.tarea.value = "M";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		function Permiso(identificador){
			document.formulario.action = "nom_permiso_add.php";
			document.formulario.co_contrato.value = identificador;
			document.formulario.tarea.value = "M";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		
		
		function validar(e){
			tecla = (document.all) ? e.keyCode : e.which;
			if (tecla == 13){
				Buscar();
			}
			if (document.formulario.op_buscar[0].checked == true){
				return validarNum(e);
			}else{
				return validarLetras(e);
			}
		}
		
		function cambio(){
			document.formulario.o_parametro.value = "";
			document.formulario.o_parametro.focus();
		}
		
	
	</script>

</body>
</html>



