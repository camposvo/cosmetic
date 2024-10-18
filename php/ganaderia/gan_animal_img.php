<?php 

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
<title>BellinghieriCosmetic</title>
		
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
	
/*-------------------------------------------------------------------------------------------
	RUTINA: variables tipo arreglo para rellenar los combos en la interfaz. (VER CONFIG.PHP)
---------------------------------------------------------------------------------------------*/
	$array_profesion = Combo_profesion();
	$array_sexo      = Combo_Sexo_Animal();
	$array_raza      = Combo_Raza();
	$array_madre      = Combo_Gan_Madre();
	$array_padre      = Combo_Gan_Padre();
	$array_tipo_gan   = Combo_Tipo_Ganado();
	
	
	//echo $tarea;
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	LEE LOS DATOS DEL ANIMAL 
-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT id_numero, id_lomo, id_arete, 
					   to_char(fe_nacimiento, 'dd/mm/yyyy'),  in_sexo, 
					   nb_nombre_animal, fk_madre, fk_padre, 
					   fk_raza, in_tipo
				FROM gan_ganado 									
				WHERE pk_ganado = '$pk_animal'";
				
	//echo $ls_sql;

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
	}		
	
/*-------------------------------------------------------------------------------------------
	ELIMINA UNA IMAGEN
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "DELETE FROM gan_imagen
				WHERE pk_imagen = '$pk_imagen' ";
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}	
	
/*-------------------------------------------------------------------------------------------
	ESTABLECE LA IMAGEN COMO PRINCIPAL
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$ls_sql = "UPDATE gan_imagen SET in_activo='N' WHERE fk_animal = '$pk_animal'; 
					UPDATE gan_imagen SET in_activo='S'	WHERE pk_imagen = '$pk_imagen' ";
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
		$file= $_FILES['archivo']['tmp_name'];
		
		
		if ( is_uploaded_file ($file) ) { 
		
			$tam_file =  $_FILES['archivo']['size'];
			if($_FILES['archivo']['size'] < 2000000) { 
						
				$destino = '../imagenes' ;
				$nombre_file =  ereg_replace( "([     ]+)", "_", $_FILES['archivo']['name'] ); 
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
				
				//copy($HTTP_POST_FILES['archivo']['tmp_name'], $ruta); 
				
				if(move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta)){				
				
									
					//INSERTA LA RUTA DE LA IMAGEN EN LA TABLE gan_imagen
					$ls_sql = "INSERT INTO gan_imagen(nb_img_ganado, tx_ruta_archivo, fk_animal)
							VALUES('prueba','$ruta' ,$pk_animal )";
					
					//echo $ls_sql;
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);					
					if ($ls_resultado != 0){
						$parametros = "tarea=B";
						echo "El archivo: ".$file." de tamaño ".$tam_file." ruta ".$ruta;
						echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Ingresados Satisfactoriamente!');</script>";

					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
						
					$subio = true; 
				}else{
					$msg = "Error al mover el Archivo al Servidor";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
				}	
										
  			} else{
				$msg = "¡Falla en la carga del archivo !. El archivo no debe exceder de 2 MB";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";			
			}
			
 		} else{
			$msg = $file." no pudo ser cargado";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";	
		}
		
			
	}
	
/*-------------------------------------------------------------------------------------------
	LEE LAS RUTA DE ARCHIVO DE TODAS LA IMAGENES ASOCIADAS AL ANIMAL
-------------------------------------------------------------------------------------------*/
	
	$ls_sql = "SELECT nb_img_ganado, tx_ruta_archivo, in_activo,pk_imagen
			FROM gan_imagen							
			WHERE fk_animal = '$pk_animal'
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
			Datos del Animal
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
										<?php echo strtoupper($x_nombre.' ('.$o_numero.')');?> 
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
					<input type="hidden" name="pk_animal" value="<?php echo $pk_animal;?>">
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
									while($row = pg_fetch_row($ls_resultado)){
									$i=0;
										$title_img   = $row[$i++];
										$ruta_archivo= $row[$i++];
										$activo= $row[$i++];
										$ls_cod= $row[$i++];
										
										$texto_activo = $activo =='S'?'<div class="tags">
																	<span class="label-holder">
																		<span class="label label-info arrowed-in">Principal</span>
																	</span>
																</div>':'';
										
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