<?php
/*-------------------------------------------------------------------------------------------
	Nombre: sis_utilidad.php                                                     
	Descripcion: Contiene Funciones de Utilidad para el modulo << SISTEMAS >>
--------------------------------------------------------------------------------------------*/
	include("../../clases/clspostgres.php");
	include("../../config/config.php");


/*-------------------------------------------------------------------------------------------
	FUNCIÓN : Dibuja el resultado de una Consulta SQL y coloca botones de accion
--------------------------------------------------------------------------------------------*/
function fun_dibujar_tabla($rs,$li_totcampos,$li_indice,$operacion){
	$sw = 0; 
    $j=0;
	while ($row = pg_fetch_row($rs->li_idconsult)){
 		$color_linea = ($sw==0)?"<tr class='Tabla_fila_claro'>":"<tr class='Tabla_fila_blanco'>";
		$sw = ($sw==0)?1:0;
		echo $color_linea;
		for ($i = 0; $i < $li_totcampos; $i++){echo "<td align='left' class='font_fila'><div align=\"left\">" . $row[$i] . "</div></td>";}
		
		// BOTONES DE ENLACE 
     	$ls_cod = $row[$li_indice]; // Campo que identifica el registro clave
		$ls_cod1 = $row[$li_indice-1];
		
		//PAGINA: no implementado
		if(strtoupper($operacion)=='TRABAJADOR'){ 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>";	
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 				
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
		
			echo '<td>
					<div class="hidden-sm hidden-xs action-buttons">
					
											
						<a class="blue tooltip-info open-event" href="#" title="Solicitar Permiso" onClick=\'Permiso("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-taxi   bigger-130"></i>
						</a>
						
						<a class="green tooltip-success  open-event" href="#" title="Pagar Nomina" onClick=\'PagarNomina("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-dollar bigger-130"></i>
						</a>
					</div>

					<div class="hidden-md hidden-lg">
						<div class="inline pos-rel">
							<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
								<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
							</button>

							<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Permiso" onClick=\'Permiso("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-search-plus bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Pagar" onClick=\'PagarNomina("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-dollar bigger-120"></i>
										</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</td>';
			
		}
	
	//PAGINA: no implementado
		if(strtoupper($operacion)=='LISTA_CONTRATO'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			
			$ls_cod1 = $row[$i++];
			if($ls_cod1=='S'){
				echo '<td>
						<div>
						<a class="green tooltip-success open-event" href="#" title="Habilitado" onClick=\'Actualizar_Estatus("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-check-square-o bigger-130"></i>
						</a>
					</div>
				</td>';	
			}else{ // No esta  a lista de mensajes
			echo '<td>
					<div>
						<a class="red tooltip-error open-event" href="#" title="Deshabilitado" onClick=\'Actualizar_Estatus("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa  fa-times  bigger-130"></i>
						</a>
					</div>
				</td>';	
			}	
			
			echo '<td>
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="tooltip-info open-event" href="#" title="Editar"  onClick=\'EditarContrato("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>
						
						<a class="red tooltip-error  open-event" href="#" title="Borrar"  onClick=\'Eliminar("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-trash-o bigger-130"></i>
						</a>	
						
					</div>

					<div class="hidden-md hidden-lg">
						<div class="inline pos-rel">
							<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
								<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
							</button>

							<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Editar" onClick=\'EditarContrato("'.$ls_cod.'");return false;\'>
										<span class="orange">											
											<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'Eliminar("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-trash-o bigger-120"></i>
										</span>
									</a>
								</li>		
								
							</ul>
						</div>
					</div>
				</td>';
				
		}
		
		//PAGINA: no implementado
		if(strtoupper($operacion)=='LISTAR_PERMISO'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" .mes_letras($ls_cod1). "</td>";
			echo "<td style=\"CURSOR: hand\" onClick=\""."EliminarPermiso('".$ls_cod."');\"><div align=\"center\" title=\"Editar \"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"25\" height=\"25\" border=\"0\" ></div></td>";
		}
		
		//PAGINA: no implementado
		if(strtoupper($operacion)=='RECIBO_PAGO'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			
			echo '<td>
					<div class="action-buttons">
						
						<a class="blue  tooltip-info open-event" href="#" title="Imprimir"  onClick=\'ImprimirPago("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa  fa-print bigger-130"></i>
						</a>						
					</div>
					
					

				</td>';		}
				
		//PAGINA: no implementado
		if(strtoupper($operacion)=='LISTAR_PAGO'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			
			echo '<td>
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="blue  tooltip-info open-event" href="#" title="Ver"  onClick=\'VerPago("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa  fa-search-plus bigger-130"></i>
						</a>

						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'EliminarPago("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-trash-o bigger-130"></i>
						</a>
						
					</div>

					<div class="hidden-md hidden-lg">
						<div class="inline pos-rel">
							<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
								<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
							</button>

							<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
								<li>
									<a href="#" class="tooltip-success" data-rel="tooltip" title="Editar" onClick=\'VerPago("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'EliminarPago("'.$ls_cod.'");return false;\'>
										<span class="red">
											<i class="ace-icon fa fa-trash-o bigger-120"></i>
										</span>
									</a>
								</li>
								
							</ul>
						</div>
					</div>
				</td>';
		}
		
		echo "</tr>";
	}
} 
?>