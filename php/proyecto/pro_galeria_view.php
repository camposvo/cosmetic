<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: adm_venta.php                                                    
	Descripcion: 
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("pro_utilidad.php");
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
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="UTF-8" />
		<title>BellinghieriCosmetic</title>


		<meta name="description" content="responsive photo gallery using colorbox" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="../../assets/css/colorbox.min.css" />
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
	$x_rubro     = 0;
	
	
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
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: MOSTRAR DATOS
-------------------------------------------------------------------------------------------*/
		
	$ls_sql = "SELECT pk_galeria, nb_imagen, nb_ruta_img FROM t19_galeria;"; 
	
	//echo $ls_sql;
		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	
	
	if($ls_resultado != 0){
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}

	
	
?>


<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Galeria de Imagenes
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12">
							<ul class="ace-thumbnails clearfix">
								<?php 
									while($row = pg_fetch_row($ls_resultado)){
										$cod = $row[0];
										$nombre = $row[1];
										$ruta = $row[2];

										echo '
										<li>
											<div>
												<img width="150" height="150" alt="150x150" src="'.$ruta.'" />
												<div class="text">
													<div class="inner">
														<span>'.$nombre.'</span>

														<br />
														<a href="'.$ruta.'" data-rel="colorbox">
															<i class="ace-icon fa fa-search-plus"></i>
														</a>
													</div>
												</div>
											</div>
										</li>
										';                
									
									}										
								?>
																	
							</ul>
														
						</div>
					</div>	
					
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->

	
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	
	
	<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='../../assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="../../assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery.colorbox.min.js"></script>

		<!-- ace scripts -->
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			 jQuery(function($) {
				var $overflow = '';
				var colorbox_params = {
					rel: 'colorbox',
					reposition:true,
					scalePhotos:true,
					scrolling:false,
					previous:'<i class="ace-icon fa fa-arrow-left"></i>',
					next:'<i class="ace-icon fa fa-arrow-right"></i>',
					close:'&times;',
					current:'{current} of {total}',
					maxWidth:'100%',
					maxHeight:'100%',
					onOpen:function(){
						$overflow = document.body.style.overflow;
						document.body.style.overflow = 'hidden';
					},
					onClosed:function(){
						document.body.style.overflow = $overflow;
					},
					onComplete:function(){
						$.colorbox.resize();
					}
				};

				$('.ace-thumbnails [data-rel="colorbox"]').colorbox(colorbox_params);
				$("#cboxLoadingGraphic").html("<i class='ace-icon fa fa-spinner orange fa-spin'></i>");//let's add a custom loading icon
				
				
				$(document).one('ajaxloadstart.page', function(e) {
					$('#colorbox, #cboxOverlay').remove();
			   });
			})
			
		</script>


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript"> 

		function Cancelar(parametros){
		window.location.href = "inscripcion_ficha_fichas.php?" + parametros;
	}
	
	function Agregar(){
		document.formulario.tarea.value = "A";
		document.formulario.action = "pro_proyecto_add.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function Detalle_Proyecto(identificador){
		LoadAjaxContent("php/proyecto/pro_proyecto_detalle.php?pk_proyecto="+identificador+"&tarea=A");
	}
	
	function Eliminar_Proyecto(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.pk_proyecto.value = identificador;
			document.formulario.action = "pro_proyecto_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}

	function Editar_Proyecto(identificador){
		document.formulario.tarea.value = "M";
		document.formulario.pk_proyecto.value = identificador;
		document.formulario.action = "pro_proyecto_mod.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}

	function Detalle_Finanza(identificador){
		document.formulario.tarea.value = "M";
		document.formulario.pk_proyecto.value = identificador;
		document.formulario.action = "pro_finanza_view.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}
	
	function Evento(identificador){
		document.formulario.tarea.value = "M";
		document.formulario.pk_proyecto.value = identificador;
		document.formulario.action = "pro_evento_add.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}

	function Actualizar_Estatus(identificador){
		if (confirm('Desea Actualizar el Estado del Proyecto?') == true){
			document.formulario.tarea.value = "S";
			document.formulario.pk_proyecto.value = identificador;
			document.formulario.action = "pro_proyecto_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}	
	
	function Buscar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "php/proyecto/pro_proyecto_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	</script>

</body>
</html>
