<?php 
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("sis_utilidad.php");
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
	ELIMINA UNA IMAGEN
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "DELETE FROM gan_imagen
				WHERE pk_imagen = '$pk_imagen' ";
		
		$ls_sql= "UPDATE s01_persona
					SET tx_dir_foto = '' WHERE co_persona = '$co_usuario'";	
		
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}	
	
	
/*-------------------------------------------------------------------------------------------
	RUTINA: mostrar los datos del representante legal a editar en la interfaz.
-------------------------------------------------------------------------------------------*/	
	if($tarea == "F") { 
		$subio = false;
		if (is_uploaded_file ($HTTP_POST_FILES['archivo']['tmp_name'])) { 
		
			if($HTTP_POST_FILES['archivo']['size'] < 2000000) { 
						
				$destino = '../imagenes' ;
				$nombre_file =  ereg_replace( "([     ]+)", "_", $HTTP_POST_FILES['archivo']['name'] ); 
				$porciones = explode(".", $nombre_file);
				$extn = $porciones[1];
				
				//LEE LA SECUENCIA PARA EL NOMBRE DE LA PROXIMA IMAGEN 
				$ls_sql = "SELECT nextval('sec_img');";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if($ls_resultado != 0){	
					$row = pg_fetch_row($ls_resultado,0);
					$nro_img  = $row[0];
				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}		
				
				// COPIA LA IMAGEN EN EL SERVIDOR
				$ruta = $destino . '/' .'img_'.$nro_img.'.'.$extn;				
				copy($HTTP_POST_FILES['archivo']['tmp_name'], $ruta); 
								
						
				$ls_sql= "UPDATE s01_persona
                            SET tx_dir_foto = '".$ruta."' WHERE co_persona = '$co_usuario'";		
				
				//echo $ls_sql;
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);					
				if ($ls_resultado != 0){
					$parametros = "tarea=B";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Ingresados Satisfactoriamente!');</script>";

				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
				}
					
				$subio = true; 
											
  			} 
 		} 
		
		if (!$subio){
			$msg = "¡Falla en la carga del archivo !. El archivo no debe exceder de 2 MB";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";			
		}
	
	}
	
	/*-----------------------------------------------------------------------------------------
	LEE DATOS DEL USUARIO
------------------------------------------------------------------------------------------*/		
	
	$ls_sql = "SELECT tx_cedula, UPPER(tx_nombre), UPPER(tx_apellido), tx_dir_foto
				FROM s01_persona 	
				WHERE co_persona = '$co_usuario'";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_cedula           = $row[0];
			$x_nombre           = $row[1];
			$o_apellido         = $row[2];
			$ruta_archivo             = $row[3];
			$obj_miconexion->fun_closepg($li_id_conex); 
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
			Datos de la Persona
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">		
					
			<div class="row">
				<div class="col-xs-12 col-md-6">
				<form enctype="multipart/form-data" class="form-horizontal" name="formulario">					
					
					<div class="widget-body">
						<div class="widget-main">							
							<div class="form-group">
								<input name="archivo"  multiple="" type="file" id="id-input-file-3" />
							</div>
							
							<div class="profile-contact-info">
								<div class="profile-contact-links align-left">
									<a href="#" class="btn btn-link">
										<i class="ace-icon fa fa-paw  bigger-120 green"></i>
										<?php echo strtoupper($x_nombre);?> 
									</a>							
								</div>					
							</div>
						</div>
					</div>	
					
					<div class="space-4"></div>	
						
					<div class="form-group center ">												
						<button type="button" onClick="Cancelar('<?php echo "B"; ?>')" class="btn btn-sm  btn-danger">
							<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
							Regresar
						</button>
						
						<button type="button" onClick="Guardar();"class="btn btn-sm btn-success">
							<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
							Agregar
						</button>										
					</div>
						
								
					<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
					<input name="co_usuario" type="hidden" value="<?php echo $co_usuario;?>">
					<input type="hidden" name="pk_imagen" value="<?php echo $pk_imagen;?>">
					
				</form>
					</div>
			</div>
			
				
				
			<div class="row">
					<div class="col-xs-12">
						<!-- PAGE CONTENT BEGINS -->
						<div>
							<ul class="ace-thumbnails clearfix">
								
								<?php					
										if($ruta_archivo!=''){
												$texto_activo = '<div class="tags">
																			<span class="label-holder">
																				<span class="label label-info arrowed-in">Principal</span>
																			</span>
																		</div>';
												
												$Content.= '<li>
														<div>
															<img width="150" height="150" alt="150x150" src="'.$ruta_archivo.'" />'
															.$texto_activo.
															'<div class="text">
																<div class="inner">
																	<span>'.$x_nombre.'</span>

																	<br />
																	<a href="'.$ruta_archivo.'" data-rel="colorbox">
																		<i class="ace-icon fa fa-search-plus"></i>
																	</a>
																	
																	<a href="#" onClick=\'Activar_Imagen("'.$ls_cod.'");return false;\' >
																		<i class="ace-icon fa fa-paperclip"></i>
																	</a>
																	
																	<a href="#" onClick=\'Eliminar_Imagen("'.$ls_cod.'");return false;\' >
																		<i class="ace-icon fa fa-times red"></i>
																	</a>
																</div>
															</div>
														</div>
													</li>';						
											
										
											echo $Content;
										}
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
		location.href = "sis_usuario.php?" + parametros;
	}
	
	function Guardar() {
		document.formulario.action="sis_usuario_foto.php";
		document.formulario.tarea.value="F";
        document.formulario.method = "post";
        document.formulario.submit();
	}
	
	function Eliminar_Imagen(id) {
		document.formulario.action="sis_usuario_foto.php";
		document.formulario.tarea.value="E";
		document.formulario.pk_imagen.value=id;
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	
	
	</script>

</html>