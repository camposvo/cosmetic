<?php 
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("gan_utilidad.php");
	$usu_autentico = isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI"){
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
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="../../assets/js/ace-extra.min.js"></script>	
		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/colorbox.min.css" />
				
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
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
	
	
	//echo $tarea;
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	LEE LOS DATOS DEL ANIMAL 
-------------------------------------------------------------------------------------------*/
	/*$ls_sql = "SELECT id_numero, id_lomo, id_arete, 
					   to_char(fe_nacimiento, 'dd/mm/yyyy'),  in_sexo, 
					   nb_nombre_animal, fk_madre, fk_padre, 
					   fk_raza, in_tipo
				FROM gan_ganado 									
				WHERE pk_ganado = '$pk_animal'";
				

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
								
		$row = pg_fetch_row($ls_resultado,0);
		$o_numero           = $row[0];
		$x_lomo           	= $row[1];
		$x_oreja         	= $row[2];
		$x_nacimiento		= $row[3];
		$o_sexo    			= $row[4];
		$x_nombre       	= $row[5];
		$x_madre       		= $row[6];
		$x_padre        	= $row[7];
		$x_raza    			= $row[8];
		$x_tipo				= $row[9];
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}		*/
	
/*-------------------------------------------------------------------------------------------
	LEE LAS RUTA DE ARCHIVO DE TODAS LA IMAGENES ASOCIADAS AL ANIMAL
-------------------------------------------------------------------------------------------*/
	
	$ls_sql = "SELECT nb_nombre_animal, tx_ruta_archivo, gan_imagen.in_activo,pk_imagen
			FROM gan_imagen	
			INNER JOIN gan_ganado ON gan_imagen.fk_animal = gan_ganado.pk_ganado
			WHERE gan_imagen.in_activo = 'S'
			ORDER BY pk_imagen";
				
	//echo $ls_sql;

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//$row = pg_fetch_row($ls_resultado,0);
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}		
	
/*-------------------------------------------------------------------------------------------
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.           
|------------------------------------------------------------------------------------------*/
?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			Galeria de Imagenes
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">		
					
						
				
			<div class="row">
					<div class="col-xs-12">
						<!-- PAGE CONTENT BEGINS -->
						<div>
							<ul class="ace-thumbnails clearfix">
								
								<?php					
									while($row = pg_fetch_row($ls_resultado)){
									$i=0;
										$title_img   = strtoupper($row[$i++]);
										$ruta_archivo= $row[$i++];
										$activo= $row[$i++];
										$ls_cod= $row[$i++];
										
										/*$texto_activo = $activo =='S'?'<div class="tags">
																	<span class="label-holder">
																		<span class="label label-info arrowed-in">Principal</span>
																	</span>
																</div>':'';*/
										
										$Content.= '<li>
												<div>
													<img width="150" height="150" alt="150x150" src="'.$ruta_archivo.'" />'
													.$texto_activo.
													'<div class="text">
														<div class="inner">
															<span>'.$title_img.'</span>

															<br />
															<a href="'.$ruta_archivo.'" data-rel="colorbox">
																<i class="ace-icon fa fa-search-plus"></i>
															</a>
														</div>
													</div>
												</div>
											</li>';						
									
									}	
									echo $Content;
									
								?>	
							
							
							</ul>
						</div><!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->
							
					
			</div>
		</div> <!-- /.row tabla principal -->
	</div> <!-- /.page-content -->

</body>

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
		<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery.colorbox.min.js"></script>

	<script type="text/javascript">
		 $(document).ready(function() {
		
				$('#id-input-file-3').ace_file_input({
					style:'well',
					btn_choose:'Haga click para Seleccionar',
					btn_change:null,
					no_icon:'ace-icon fa fa-picture-o',
					droppable:true,
					thumbnail:'fit'//'small'//large | fit
					//,icon_remove:null//set null, to hide remove/reset button
					/**,before_change:function(files, dropped) {
						//Check an example below
						//or examples/file-upload.html
						return true;
					}*/
					/**,before_remove : function() {
						return true;
					}*/
					,
					preview_error : function(filename, error_code) {
						//name of the file that failed
						//error_code values
						//1 = 'FILE_LOAD_FAILED',
						//2 = 'IMAGE_LOAD_FAILED',
						//3 = 'THUMBNAIL_FAILED'
						//alert(error_code);
					}
			
				}).on('change', function(){
					//console.log($(this).data('ace_input_files'));
					//console.log($(this).data('ace_input_method'));
				});
				
				
				var $overflow = '';
				var colorbox_params = {
					rel: 'colorbox',
					reposition:true,
					scalePhotos:true,
					scrolling:false,
					opacity:'0.65',
					previous:'<i class="ace-icon fa fa-arrow-left"></i>',
					next:'<i class="ace-icon fa fa-arrow-right"></i>',
					//close:'&times;',
					closeButton: 'false',
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
				
			
			
		} );
		
		
		</script>
		
	</script>
				
				

<script language="javascript" type="text/javascript">
	function Cancelar(parametros){
		location.href = "gan_animal_view.php?" + parametros;
	}
	
	function Guardar() {
		document.formulario.action="gan_animal_img.php";
		document.formulario.tarea.value="F";
        document.formulario.method = "post";
        document.formulario.submit();
	}
	
	function Eliminar_Imagen(id) {
		document.formulario.action="gan_animal_img.php";
		document.formulario.tarea.value="E";
		document.formulario.pk_imagen.value=id;
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function Activar_Imagen(id) {
		document.formulario.action="gan_animal_img.php";
		document.formulario.tarea.value="U";
		document.formulario.pk_imagen.value=id;
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	</script>

</html>