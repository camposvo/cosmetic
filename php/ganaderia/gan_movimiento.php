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

	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
				
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
	
	//var_dump($_POST['origen']);
	$datos = $_POST['origen'];
	
/*-------------------------------------------------------------------------------------------
	RUTINA: variables tipo arreglo para rellenar los combos en la interfaz. (VER CONFIG.PHP)
---------------------------------------------------------------------------------------------*/
	$arr_potrero         = Combo_Potrero();
	$arr_lote            = Combo_Lote();

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

	/*-----------------------------------------------------------------------------------------------------------------------
	ACTUALIZA EL POTRERO ASIGNADO AL GANADO
------------------------------------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
					
		$error_sql = false;		
		$sw= true;
		
		if($x_lote!=0 and  $x_potrero!=0){
			$criterio = "UPDATE gan_ganado SET fk_potrero	= '$x_potrero',  fk_lote = '$x_lote'";
						
		}else if($x_lote==0 and  $x_potrero!=0){
			$criterio = "UPDATE gan_ganado SET fk_potrero	= '$x_potrero'";			
			
		}else if($x_lote!=0 and  $x_potrero==0){
			$criterio = "UPDATE gan_ganado SET  fk_lote = '$x_lote'";
			
		}else{
			$sw = false;
		}
		
		echo $criterio;
		
		if($sw and count($datos)<>0){
		
			if( $obj_miconexion->fun_consult(" BEGIN TRANSACTION ") == 0)  $error_sql = true;		
			
			foreach($datos as $k => $id_ganado){ 
				$ls_sql = $criterio." WHERE pk_ganado = '$id_ganado'";	
				if ($obj_miconexion->fun_consult(strtoupper($ls_sql))== 0)	{
					$error_sql = true;
				}
				
				
				$ls_sql = " INSERT INTO gan_movimiento(fk_animal, fk_potrero, fe_traslado, tx_comentario) 
								VALUES ($id_ganado, $x_potrero, '$o_fecha', '$x_observacion');";	
				if ($obj_miconexion->fun_consult(strtoupper($ls_sql))== 0)	{
					$error_sql = true;
				}
							
			}			
			
					
			if(!$error_sql){
				$ls_resultado =  $obj_miconexion->fun_consult(" COMMIT ");

				echo "<script language='JavaScript' type='text/JavaScript'>alert('Datos Actualizados Satisfactoriamente');</script>";
			}else{
				$ls_resultado =  $obj_miconexion->fun_consult(" ROLLBACK ");
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
			}
		}
			
		
		$tarea == "X";  //Vuelve a colocar la Tarea 
	}
/*-------------------------------------------------------------------------------------------
	CONSULTA LISTA DE ANIMALES
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT id_numero, UPPER(nb_nombre_animal), in_sexo, to_char(fe_nacimiento, 'yyyy/mm/dd'), nb_potrero, nb_lote,
				f_grupo_etareo(fe_nacimiento,in_sexo), UPPER(gan_raza.nb_raza), pk_ganado
			FROM gan_ganado
			LEFT JOIN gan_raza ON gan_raza.pk_raza = gan_ganado.fk_raza 
			LEFT JOIN gan_potrero ON gan_potrero.pk_potrero = gan_ganado.fk_potrero 
			LEFT JOIN gan_lote ON gan_lote.pk_lote = gan_ganado.fk_lote 
			";
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
/*-------------------------------------------------------------------------------------------
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.           
|------------------------------------------------------------------------------------------*/
?>
<!-- Content Header (Page header) -->
	<div class="container-fluid">
			<form class="form-horizontal" name="formulario"  method="post">
				
			<div class="row">				
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="widget-box ">								
				
						<div class="widget-header  widget-header-small">
							<h5 class="widget-title"> Reasignar </h5>								
						</div>
						
						<div class="widget-body">
							<div class="widget-main">	
									
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha</label>
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
									<label class="col-sm-3 control-label no-padding-right" >Potrero</label>
									<div class="col-sm-7" >	
										<select name="x_potrero" class=" chosen-select form-control" data-placeholder="Seleccione el Potrero...">
											<?php
												if ($x_potrero == ""){
													echo "<option value='0' selected></option>";
												}else{
													echo "<option value='0'></option>";
												}
												foreach($arr_potrero as $k => $v) {
													$ls_cadenasel =($k == $x_potrero)?'selected':'';
													echo "<option value='$k' $ls_cadenasel>$v</option>";                
												}
											?>							
										</select>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" >Lote</label>
									<div class="col-sm-7" >	
										<select name="x_lote" class=" chosen-select form-control" data-placeholder="Seleccione un Lote...">
											<?php
												if ($x_lote == ""){
													echo "<option value='0' selected></option>";
												}else{
													echo "<option value='0'></option>";
												}
												foreach($arr_lote as $k => $v) {
													$ls_cadenasel =($k == $x_lote)?'selected':'';
													echo "<option value='$k' $ls_cadenasel>$v</option>";                
												}
											?>							
										</select>
									</div>
								</div>
								
								<div class="form-group">
									<label  class="col-sm-3 control-label no-padding-right" for="x_observacion" >Comentario</label>
									<div class="col-sm-9" >
										<textarea name="x_observacion" cols="2" id="x_observacion" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_observacion;?></textarea>
									</div>
								</div>	
								
								<div class="form-group center ">
									<button type="button" onClick="Cancelar()" class="btn btn-sm  btn-danger">
										<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
										Regresar
									</button>				
									<button type="submit" class="btn btn-sm btn-info" >
										<i class="ace-icon fa fa-exchange  align-top bigger-125 "></i>
										Reubicar
									</button>				
								</div>						
								
								
							</div>	
						</div>
					</div>
				</div>
			</div>
			
			
			
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-12">										
					<div class="table-header">
						Rebaño
					</div>

					<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
						<thead>
							<tr>
								<th class="center">
									<label class="pos-rel">
										<input type="checkbox" class="ace select_all" name="select_all" id="example-select-all" />
										<span class="lbl"></span>
									</label>
								</th>
								<th class="">Numero</th>
								<th class="">Nombre</th>
								<th class="">Sexo</th>
								<th class="">Fecha Nac.</th>
								<th class="">Potrero</th>
								<th class="">Lote</th>
								<th class="">Grupo</th>
								<th class="">Raza</th>
							</tr>
						</thead>
						<tbody>	
							<?php   										
								$li_numcampo = 0;
								$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
								fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_ORIGEN'); // Dibuja la Tabla de Datos
								$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
							?>
						</tbody>
					</table>
				</div>
			</div>
			
			<input type="hidden" name="tarea" 		 value="<?php echo $tarea;?>">
			<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">	
			</form>		

				
				
	</div>
			
					

</body>

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
<script src="../../assets/js/bootstrap-datepicker.min.js"></script>		

	<script src="../../assets/js/chosen.jquery.min.js"></script>
	<script src="../../assets/js/dataTables.tableTools.min.js"></script>
	<script src="../../assets/js/dataTables.colVis.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function (){
		   	
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			
			.next().on(ace.click_event, function(){//show datepicker when clicking on the icon
				$(this).prev().focus();
			})
			
			var table = $('#dynamic-table').DataTable( {
				"lengthChange": false,
				"pageLength": 10,
				"aaSorting": [ [1,'desc'] ],
				"oLanguage": {
					"sInfo": "De _START_ hasta _END_ de un total de _TOTAL_",
					"sInfoFiltered": " ( filtrado de _MAX_ registros )",
					"sSearch": "Filtro:",						
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
				"select": true,
								
				"columns": [
					{ "orderable": false },
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null
				  ]				  				  
			} );
			
			var tableTools_obj = new $.fn.dataTable.TableTools( table, {					
				"sRowSelect": "multi",
				"fnRowSelected": function(row) {
					//check checkbox when row is selected
					try { $(row).find('input[type=checkbox]').get(0).checked = true}
					catch(e) {}
				},
				"fnRowDeselected": function(row) {
					//uncheck checkbox
					try { $(row).find('input[type=checkbox]').get(0).checked = false }
					catch(e) {}
				},
				"sSelectedClass": "success"			       
			 } );
				
						
		   // Handle click on "Select all" control - Check/uncheck checkboxes for all rows in the table
		   $('#example-select-all').on('click', function(){	  // Get all rows with search applied
			  			
			  var rows = table.rows({ 'search': 'applied' }).nodes();
				if(this.checked) tableTools_obj.fnSelect(rows);
				else tableTools_obj.fnDeselect(rows);
			  $('input[type="checkbox"]', rows).prop('checked', this.checked);
			 
		   });
		   
		   //select/deselect a row when the checkbox is checked/unchecked
			$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){

				var row = $(this).closest('tr').get(0);
				if(!this.checked) {
					tableTools_obj.fnSelect(row);
					 $('input[type="checkbox"]', row).prop('checked',true);
				}	 
				else{
					 tableTools_obj.fnDeselect($(this).closest('tr').get(0));
					  $('input[type="checkbox"]', row).prop('checked',false);
				}	 
				
			});

			

			$('form[name="formulario"]').on('submit', function(e){
				var form = this;
				$( "input[name=tarea]" ).val('U'); // Indico la TAREA A EJECUTAR

				//e.preventDefault();
				// Iterate over all checkboxes in the table
				table.$('input[type="checkbox"]').each(function(){
					// If checkbox doesn't exist in DOM
					if(!$.contains(document, this)){
						// If checkbox is checked
						if(this.checked){
						   // Create a hidden element 
						   $(form).append(
							  $('<input>')
								 .attr('type', 'hidden')
								 .attr('name', this.name)
								 .val(this.value)
						   );
						}
					 } 
				  });
			   });
			  
		});
	
	</script>
				

<script language="javascript" type="text/javascript">

	/*function Asignar(){
			if (confirm('Desea Reasignar el Ganado Seleccionado?') == true){
				document.formulario.tarea.value = "U";
				document.formulario.action = "gan_animal_potrero.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}*/
	
	function Cancelar(){
		location.href = "gan_animal_view.php";
	}
			
</script>

</html>