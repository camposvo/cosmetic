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
<title>BellinghieriCosmetic</title>
	
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$tarea = isset($_POST['tarea'])?$_POST['tarea']:'X';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$tarea = isset($_GET['tarea'])?$_GET['tarea']:'X';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	
/*-------------------------------------------------------------------------------------------|
|	ACTUALIZA UN REGISTRO
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "B"){
		// Validar 
		$x_edad_ini1 = 0;
		$h_edad_ini1 = 0;
		$ERROR =false;
		
		if ($x_edad_ini1 === '') {
			echo 'entro 1';
			$ERROR = true;
		} elseif ($x_edad_fin1=='') {
			echo 'entro 2';
			$ERROR = true;
		} elseif ($x_edad_ini2=='') {
			echo 'entro 3';
			$ERROR = true;
		} elseif ($x_edad_fin2=='') {
			echo 'entro 4';
			$ERROR = true;
		} elseif ($x_edad_ini3=='') {
			echo 'entro 5';
			$ERROR = true;
		} elseif ($x_edad_fin3=='') {
			echo 'entro 6';
			$ERROR = true;
		} elseif ($x_edad_ini4=='') {
			echo 'entro 7';
			$ERROR = true;
		} elseif ($x_edad_fin4=='') {
			echo 'entro 8';
			$ERROR = true;
		}  		
		$x_edad_fin5 = 16000;	 // tiempo de vida de un toro
		$h_edad_fin5 = 16000;     // tiempo de vida de una vaca

       	if (!$ERROR){						
			$ls_sql = "UPDATE gan_grupo_etareo SET 
						nb_grupo_1              = '".strtoupper($x_grupo1)."',           
						tx_descripcion_grupo_1  = '".strtoupper($x_descripcion1)."',
						nu_edad_ini_1           = $x_edad_ini1,  
						nu_edad_fin_1           = $x_edad_fin1,
						nb_grupo_2              = '".strtoupper($x_grupo2)."',  
						tx_descripcion_grupo_2  = '".strtoupper($x_descripcion2)."',  
						nu_edad_ini_2           = $x_edad_ini2,   
						nu_edad_fin_2           = $x_edad_fin2,
						nb_grupo_3              = '".strtoupper($x_grupo3)."',  
						tx_descripcion_grupo_3  = '".strtoupper($x_descripcion3)."',  
						nu_edad_ini_3           = $x_edad_ini3,    
						nu_edad_fin_3           = $x_edad_fin3, 
						nb_grupo_4              = '".strtoupper($x_grupo4)."',  
						tx_descripcion_grupo_4  = '".strtoupper($x_descripcion4)."',  
						nu_edad_ini_4           = $x_edad_ini4,  
						nu_edad_fin_4	        = $x_edad_fin4,		
						nb_grupo_5              = '".strtoupper($x_grupo5)."',  
						tx_descripcion_grupo_5  = '".strtoupper($x_descripcion5)."',  
						nu_edad_ini_5           = $x_edad_ini5,  
						nu_edad_fin_5	        = $x_edad_fin5		
					WHERE pk_grupo_etareo = 1;";
					
			$ls_sql = $ls_sql."UPDATE gan_grupo_etareo SET 
						nb_grupo_1              = '".strtoupper($h_grupo1)."',           
						tx_descripcion_grupo_1  = '".strtoupper($h_descripcion1)."',
						nu_edad_ini_1           = $h_edad_ini1,  
						nu_edad_fin_1           = $h_edad_fin1,
						nb_grupo_2              = '".strtoupper($h_grupo2)."',  
						tx_descripcion_grupo_2  = '".strtoupper($h_descripcion2)."',  
						nu_edad_ini_2           = $h_edad_ini2,   
						nu_edad_fin_2           = $h_edad_fin2,
						nb_grupo_3              = '".strtoupper($h_grupo3)."',  
						tx_descripcion_grupo_3  = '".strtoupper($h_descripcion3)."',  
						nu_edad_ini_3           = $h_edad_ini3,    
						nu_edad_fin_3           = $h_edad_fin3, 
						nb_grupo_4              = '".strtoupper($h_grupo4)."',  
						tx_descripcion_grupo_4  = '".strtoupper($h_descripcion3)."',  
						nu_edad_ini_4           = $h_edad_ini4,  
						nu_edad_fin_4	        = $h_edad_fin4,
						nb_grupo_5              = '".strtoupper($h_grupo5)."',  
						tx_descripcion_grupo_5  = '".strtoupper($h_descripcion5)."',  
						nu_edad_ini_5           = $h_edad_ini5,  
						nu_edad_fin_5	        = $h_edad_fin5				
					WHERE pk_grupo_etareo = 2";		
					
			//echo $ls_sql;			
			
			if ($obj_miconexion->fun_consult($ls_sql)== 0)	{
				$error_sql = true;
			}

			$parametros = "tarea=X";
			//echo "<script language'JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Satisfactoriamente!');location.href='gan_grupoetareo_view.php?$parametros';</script>";
			echo "<script language'JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Satisfactoriamente!');</script>";
		}else{
			echo "<script language'JavaScript' type='text/JavaScript'>alert('¡Revise los Datos Ingresados!');</script>";
		}

	}
	
	
				
/*-------------------------------------------------------------------------------------------
RUTINAS: Consulta  de registros de la busqueda
-------------------------------------------------------------------------------------------*/
	if ($tarea == "X"){	
		
		$ls_sql = "SELECT nb_grupo_1, tx_descripcion_grupo_1, nu_edad_ini_1, nu_edad_fin_1,
					   nb_grupo_2, tx_descripcion_grupo_2, nu_edad_ini_2, nu_edad_fin_2,
					   nb_grupo_3, tx_descripcion_grupo_3, nu_edad_ini_3, nu_edad_fin_3,  
					   nb_grupo_4, tx_descripcion_grupo_4, nu_edad_ini_4, nu_edad_fin_4 ,
					   nb_grupo_5, tx_descripcion_grupo_5, nu_edad_ini_5, nu_edad_fin_5 ,
					pk_grupo_etareo
				FROM gan_grupo_etareo
				WHERE pk_grupo_etareo = 1	
				";
			
		//echo $ls_sql;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				
		if($ls_resultado != 0){
			$i=0;
			$row = pg_fetch_row($obj_miconexion->li_idconsult);
				$x_grupo1       = $row[0];
				$x_descripcion1 = $row[1];
				$x_edad_ini1    = $row[2];
				$x_edad_fin1    = $row[3];
				$x_grupo2       = $row[4];
				$x_descripcion2 = $row[5];
				$x_edad_ini2    = $row[6];
				$x_edad_fin2    = $row[7];
				$x_grupo3       = $row[8];
				$x_descripcion3 = $row[9];
				$x_edad_ini3    = $row[10];
				$x_edad_fin3    = $row[11];
				$x_grupo4       = $row[12];
				$x_descripcion4 = $row[13];
				$x_edad_ini4    = $row[14];
				$x_edad_fin4    = $row[15];
				$x_grupo5      = $row[16];
				$x_descripcion5 = $row[17];
				$x_edad_ini5    = $row[18];
				$x_edad_fin5    = $row[19];
				
			
			
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
		
		/*-------------------------------------------------------------------------------------------
		RUTINAS: Consulta  de registros de la busqueda
		-------------------------------------------------------------------------------------------*/	
		
	$ls_sql = "SELECT nb_grupo_1, tx_descripcion_grupo_1, nu_edad_ini_1, nu_edad_fin_1,
				   nb_grupo_2, tx_descripcion_grupo_2, nu_edad_ini_2, nu_edad_fin_2,
				   nb_grupo_3, tx_descripcion_grupo_3, nu_edad_ini_3, nu_edad_fin_3,  
				   nb_grupo_4, tx_descripcion_grupo_4, nu_edad_ini_4, nu_edad_fin_4 ,
				   nb_grupo_5, tx_descripcion_grupo_5, nu_edad_ini_5, nu_edad_fin_5 ,
				pk_grupo_etareo
			FROM gan_grupo_etareo
			WHERE pk_grupo_etareo = 2	
			";
		
		//echo $ls_sql;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				
		if($ls_resultado != 0){
			$i=0;
			$row = pg_fetch_row($obj_miconexion->li_idconsult);
				$h_grupo1       = $row[0];
				$h_descripcion1 = $row[1];
				$h_edad_ini1    = $row[2];
				$h_edad_fin1    = $row[3];
				$h_grupo2       = $row[4];
				$h_descripcion2 = $row[5];
				$h_edad_ini2    = $row[6];
				$h_edad_fin2    = $row[7];
				$h_grupo3       = $row[8];
				$h_descripcion3 = $row[9];
				$h_edad_ini3    = $row[10];
				$h_edad_fin3    = $row[11];
				$h_grupo4       = $row[12];
				$h_descripcion4 = $row[13];
				$h_edad_ini4    = $row[14];
				$h_edad_fin4    = $row[15];
				$h_grupo5       = $row[16];
				$h_descripcion5 = $row[17];
				$h_edad_ini5    = $row[18];
				$h_edad_fin5    = $row[19];
				
			
			
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}
	
//var_dump($grupo);

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						
					</div>
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> 
					<form class="form-horizontal" name="formulario">
					
					<div class="row">
						<h5 class="header smaller lighter blue">							
							<strong>Machos</strong>
						</h5>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-body">
									<div class="widget-main no-padding">
										<table id="" class="table table-striped table-bordered ">
											<thead>
												<tr class="info">
													<th>Grupo Etareo</th>
													<th>Edad Inicial (dias) </th>
													<th>Edad Final (dias)</th>
													
												</tr>
											</thead>
											<tbody>	
												<tr>
													<td >
														<input id= 'x_grupo1' class="input-sm form-control" name="x_grupo1" value="<?php echo $x_grupo1; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_ini1' readonly class="input-sm form-control" name="x_edad_ini1" value="<?php echo $x_edad_ini1; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_fin1' class="input-sm form-control" name="x_edad_fin1" value="<?php echo $x_edad_fin1; ?>" type="text"  onkeyup="chgrp1(1);" />
													</td>
												</tr>
												<tr>
													<td >
														<input id= 'x_grupo2' class="input-sm form-control" name="x_grupo2" value="<?php echo $x_grupo2; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_ini2' readonly class="input-sm form-control" name="x_edad_ini2" value="<?php echo $x_edad_ini2; ?>" type="text"   />
													</td>
													<td >
														<input id= 'x_edad_fin2' class="input-sm form-control" name="x_edad_fin2" value="<?php echo $x_edad_fin2; ?>" type="text" onkeyup="chgrp1(2);" />
													</td>
												</tr>	
												<tr>
													<td >
														<input id= 'x_grupo3' class="input-sm form-control" name="x_grupo3" value="<?php echo $x_grupo3; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_ini3' readonly class="input-sm form-control" name="x_edad_ini3" value="<?php echo $x_edad_ini3; ?>" type="text"   />
													</td>
													<td >
														<input id= 'x_edad_fin3' class="input-sm form-control" name="x_edad_fin3" value="<?php echo $x_edad_fin3; ?>" type="text" onkeyup="chgrp1(3);" />
													</td>
												</tr>
												<tr>
													<td >
														<input id= 'x_grupo4' class="input-sm form-control" name="x_grupo4" value="<?php echo $x_grupo4; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_ini4' readonly class="input-sm form-control" name="x_edad_ini4" value="<?php echo $x_edad_ini4; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_fin4' class="input-sm form-control" name="x_edad_fin4" value="<?php echo $x_edad_fin4; ?>" type="text" onkeyup="chgrp1(4);" />
													</td>
												</tr>
												<tr>
													<td >
														<input id= 'x_grupo5' class="input-sm form-control" name="x_grupo5" value="<?php echo $x_grupo5; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_ini5' readonly class="input-sm form-control" name="x_edad_ini5" value="<?php echo $x_edad_ini5; ?>" type="text"  />
													</td>
													<td >
														<input id= 'x_edad_fin5' readonly class="input-sm form-control" name="x_edad_fin5" value="<?php echo $x_edad_fin5; ?>" type="text"  />
													</td>
												</tr>				
											</tbody>
										</table>
									</div>
								</div>
							</div>
						
							
						
						</div>	
					</div>	<!-- /.row totales -->

					<div class="row">
						<h5 class="header smaller lighter blue">							
							<strong>Hembras</strong>
						</h5>
					</div>
							
						<div class="space-6"></div>			

										<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-body">
									<div class="widget-main no-padding">
										<table id="" class="table table-striped table-bordered ">
											<thead>
												<tr class="info">
													<th>Grupo Etareo</th>
													<th>Edad Inicial (dias) </th>
													<th>Edad Final (dias)</th>
													
												</tr>
											</thead>
											<tbody>	
												<tr>
													<td >
														<input id= 'h_grupo1' class="input-sm form-control" name="h_grupo1" value="<?php echo $h_grupo1; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_ini1' readonly class="input-sm form-control" name="h_edad_ini1" value="<?php echo $h_edad_ini1; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_fin1' class="input-sm form-control" name="h_edad_fin1" value="<?php echo $h_edad_fin1; ?>" type="text" onkeyup="chgrp2(1);" />
													</td>
												</tr>
												<tr>
													<td >
														<input id= 'h_grupo2' class="input-sm form-control" name="h_grupo2" value="<?php echo $h_grupo2; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_ini2' readonly class="input-sm form-control" name="h_edad_ini2" value="<?php echo $h_edad_ini2; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_fin2' class="input-sm form-control" name="h_edad_fin2" value="<?php echo $h_edad_fin2; ?>" type="text" onkeyup="chgrp2(2);" />
													</td>
												</tr>	
												<tr>
													<td >
														<input id= 'h_grupo3' class="input-sm form-control" name="h_grupo3" value="<?php echo $h_grupo3; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_ini3' readonly class="input-sm form-control" name="h_edad_ini3" value="<?php echo $h_edad_ini3; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_fin3' class="input-sm form-control" name="h_edad_fin3" value="<?php echo $h_edad_fin3; ?>" type="text" onkeyup="chgrp2(3);" />
													</td>
												</tr>
												<tr>
													<td >
														<input id= 'h_grupo4' class="input-sm form-control" name="h_grupo4" value="<?php echo $h_grupo4; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_ini4' readonly class="input-sm form-control" name="h_edad_ini4" value="<?php echo $h_edad_ini4; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_fin4' class="input-sm form-control" name="h_edad_fin4" value="<?php echo $h_edad_fin4; ?>" type="text" onkeyup="chgrp2(4);" />
													</td>
												</tr>
												<tr>
													<td >
														<input id= 'h_grupo5' class="input-sm form-control" name="h_grupo5" value="<?php echo $h_grupo5; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_ini5' readonly class="input-sm form-control" name="h_edad_ini5" value="<?php echo $h_edad_ini5; ?>" type="text"  />
													</td>
													<td >
														<input id= 'h_edad_fin5' readonly class="input-sm form-control" name="h_edad_fin5" value="<?php echo $h_edad_fin5; ?>" type="text"  />
													</td>
												</tr>					
											</tbody>
										</table>
									</div>
								</div>
							</div>
						
							<div class="form-group center">	
								<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
									<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
									Guardar
								</button>																								
							</div>
						
						</div>	
					</div>	
							


						
					
					<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
					<input type="hidden" name="pk_grupo_etareo" value="<?php echo $pk_grupo_etareo; ?>">	
					</form>								
							
						
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->


	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	
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
		
		function chgrp1(valor){	
			if(valor==1)
				document.formulario.x_edad_ini2.value = document.formulario.x_edad_fin1.value;
			else if(valor==2)
				document.formulario.x_edad_ini3.value = document.formulario.x_edad_fin2.value;
			else if(valor==3)
				document.formulario.x_edad_ini4.value = document.formulario.x_edad_fin3.value;
			else if(valor==4)
				document.formulario.x_edad_ini5.value = document.formulario.x_edad_fin4.value;
			
		}
		
		function chgrp2(valor){	
			if(valor==1)
				document.formulario.h_edad_ini2.value = document.formulario.h_edad_fin1.value;
			else if(valor==2)
				document.formulario.h_edad_ini3.value = document.formulario.h_edad_fin2.value;
			else if(valor==3)
				document.formulario.h_edad_ini4.value = document.formulario.h_edad_fin3.value;		
			else if(valor==4)
				document.formulario.h_edad_ini5.value = document.formulario.h_edad_fin4.value;	
		}


		
		function Guardar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "gan_grupoetareo_view.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}		
		
						
	</script>
</body>
</html>