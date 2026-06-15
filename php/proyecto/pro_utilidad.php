<?php

include("../../clases/clspostgres.php");
include("../../config/config.php");

/*----------------------------------------------------------------------------------------
 FUNCIÓN fun_conexion: esta función se encarga de realizar la conexión al bd.
-----------------------------------------------------------------------------------------*/
function fun_dibujar_tabla($rs,$li_columnas,$li_indice, $operacion){
	$sw = 0; 
    $j=0;
	while ($row = pg_fetch_row($rs->li_idconsult)){
		// CAMPOS CLAVES PARA LOS BOTONES DE ENLACE - Deben Estar al final de la clausula select
     	$ls_cod = $row[$li_indice];     // Campo que identifica el registro clave
		$ls_cod1 = $row[$li_indice-1];  // Campo que identifica el registro clave - opcional
		
		// SE E// SE ESTABLECE EL COLOR DE LINEA POR DEFECTO
												
		/*$color_linea = ($sw==0)?"<tr class='Tabla_fila_claro' onmouseout='cambiacolor_out(this,\"#EAFBF4\")' onmouseover='cambiacolor_over(this,\"#CEE3F6\")'>":
								"<tr class='Tabla_fila_blanco' onmouseout='cambiacolor_out(this,\"#FFFFFF\")' onmouseover='cambiacolor_over(this,\"#CEE3F6\")'>";*/
		$sw = ($sw==0)?1:0; 
		
				
		// SE ESCRIBEN LOS VALORES EN LAS COLUMNAS
		echo "<tr>";
		for ($i = 0; $i < $li_columnas; $i++){
			$temp = is_numeric($row[$i])?number_format($row[$i],2,",",""):$row[$i];
			echo "<td>" .$temp."</td>";
		}
		
		
			
		if(strtoupper($operacion)=='LISTAR_PROYECTO'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			
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
						<a class="green tooltip-success open-event" href="#" title="Finanzas" onClick=\'Detalle_Finanza("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-bar-chart  bigger-130"></i>
						</a>
						
						<a class="blue tooltip-info open-event" href="#" title="Notas" onClick=\'Evento("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-comment-o bigger-130"></i>
						</a>
						
						<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Proyecto("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>

											
					
						<a class="green tooltip-error open-event" href="#" title="Ventas" onClick=\'Venta_Proyecto("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-money bigger-130"></i>
						</a>
						
						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Proyecto("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Pagar" onClick=\'Detalle_Finanza("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-bar-chart  bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Ver" onClick=\'Evento("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-search-plus bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-success" data-rel="tooltip" title="Editar" onClick=\'Editar_Proyecto("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'Eliminar_Proyecto("'.$ls_cod.'");return false;\'>
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
		
			if(strtoupper($operacion)=='LISTAR_TIPO_PROYECTO'){ 
				echo "<td class=''>" . $row[$i++] . "</td>"; 
				echo "<td class=''>" . $row[$i++] . "</td>"; 
				
				echo '<td aling="center">
					<div class="hidden-sm hidden-xs action-buttons">						
											
						<a class="orange tooltip-warning open-event" href="#" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-success" data-rel="tooltip" title="Borrar" onClick=\'Eliminar("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-trash-o bigger-120"></i>
										</span>
									</a>
								</li>								
							</ul>
						</div>
					</div>
				</td>';

		
			//echo "<td  onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			//echo "<td  onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar datos \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";
		}
		
		if(strtoupper($operacion)=='LISTAR_SEGUIMIENTO'){ 
			
			echo "<td class='blue'>" .$row[$i++]."</td>"; 
			
			echo "<td class='hidden-480'>" .$row[$i++]."</td>"; 
			$ventas = $row[$i++];
			$gastos = $row[$i++];
			
			echo "<td class='hidden'>" .$ventas."</td>";  // Ventas  -- esta columna nunca se muestra			
			echo "<td>" .number_format($ventas,2,",",".")."</td>";  // Ventas
			
			echo "<td class='hidden'>" .$gastos ."</td>";  // Gastos -- esta columna nunca se muestra
			echo "<td>" .number_format($gastos,2,",",".")."</td>";  // Gastos
			
			echo "<td class='hidden-480'>" .number_format($row[$i++],2,",",".")."</td>";  // Ganancia
			
			$porc_gan = ($gastos == 0)? 0: (($ventas - $gastos)*100)/$gastos;
			
			echo "<td class=''>" .number_format($porc_gan,2,",",".")."%</td>";         //Ganancia en Porcentaje =    ventas - gastos)*100)/gastos
			
			$ls_cod1 = $row[$i++];  // Identifica el estado del proyecto
			$ls_cod =  $row[$i++];   // Identifica el campo clave
			
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
						<a class="green tooltip-success open-event" href="#" title="Finanzas" onClick=\'Detalle_Finanza("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-bar-chart  bigger-130"></i>
						</a>
						
						<a class="blue tooltip-info open-event" href="#" title="Notas" onClick=\'Evento("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-comment-o bigger-130"></i>
						</a>
						
						<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Proyecto("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>

						
						<a class="green tooltip-error open-event" href="#" title="Ventas" onClick=\'Venta_Proyecto("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-money bigger-130"></i>
						</a>
						
						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Proyecto("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Pagar" onClick=\'Detalle_Finanza("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-bar-chart  bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Ver" onClick=\'Evento("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-search-plus bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-success" data-rel="tooltip" title="Editar" onClick=\'Editar_Proyecto("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'Eliminar_Proyecto("'.$ls_cod.'");return false;\'>
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
		
		
		if(strtoupper($operacion)=='LISTAR_GASTO'){ 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td > <span class='blue '>".number_format($row[$i++],2,",",".")."</span></td>";  
		}
		
		if(strtoupper($operacion)=='LISTAR_VENTA_CLIENTE'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td > <span class='blue '>".number_format($row[$i++],2,",",".")."</span></td>";  
		}
			
		
		if(strtoupper($operacion)=='LISTAR_VENTA_VENDEDOR'){ 
			echo "<td class='details-control' id='".$row[$i++]."'></td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td > <span class='blue '>".number_format($row[$i++],2,",",".")."</span></td>";  
		}
		
		if(strtoupper($operacion)=='LISTAR_VENTA_RUBRO'){ 

			$categoria = $row[$i++];
			$articulo = $row[$i++];
			$catidad = $row[$i++];
			$total = $row[$i++];

			$promedio = $catidad >0 ? $total / $catidad: 0;


			echo "<td class=''>" .$categoria . "</td>"; 
			echo "<td class=''>" .$articulo . "</td>"; 
			echo "<td class=''>" . number_format($promedio,2,",",".") . "</td>"; 
			echo "<td class=''>" . number_format($catidad,0,",",".") . "</td>"; 			
			echo "<td class=''>" . number_format($total,2,",",".") . "</td>"; 			 
		}
		
		if(strtoupper($operacion)=='LISTAR_VENTA_PROYECTO'){ 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>";  
			echo "<td class=''>" . $row[$i++] . "</td>";  
			echo "<td class=''>" . $row[$i++] . "</td>"; // item
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// cantidad -- ESTA COLUMNA NO SE MUESTRA NUCA 
			echo "<td class=''>" .number_format($row[$i++],2,",","."). "</td>";    //cantidad 				
			echo "<td class=''>" .number_format($row[$i++],2,",","."). "</td>"; //precio
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// total -- ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td > <span class='blue '>".number_format($row[$i++],2,",",".")."</span></td>";  //total
		}
		
			
		echo "</tr>";
    }
		
} 
?>