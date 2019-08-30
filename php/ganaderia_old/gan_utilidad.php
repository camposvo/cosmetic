<?php

	include("../../clases/clspostgres.php");
	include("../../config/config.php");
	
/*-------------------------------------------------------------------------------------------|
|	Función: Dibuja El Resultado De Una Consulta SQL Y Coloca Botones De Acción.         |
|-------------------------------------------------------------------------------------------*/
	function fun_dibujar_tabla($rs,$li_totcampos,$li_indice,$operacion){
		$sw = 0; 
		$j=0;
		while ($row = pg_fetch_row($rs->li_idconsult)){
			
			// Botones De Enlace. - - - Campo Que Identifica El Registro Clave. (Código De La Tabla)
			$ls_cod =  $row[$li_indice]; // Tipo  Clase
			$ls_cod1 = $row[$li_indice-1]; // Marca
			
			// Se Establece El Color De Linea Por Defecto.
			$color_linea = ($sw==0)?"<tr>":"<tr>";
			$sw = ($sw==0)?1:0;
			
			
			//Se Escriben Los Valores En Las Columnas.
			echo $color_linea;
			for ($i = 0; $i < $li_totcampos; $i++){
				echo "<td >" . $row[$i] . "</td>";
			}
			
			
			// Página: 'ma_almacen.php'
			if(strtoupper($operacion)=='LISTAR_ANIMAL'){ 
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";	
				$sexo = $row[$i++];
				echo "<td>" .$sexo."</td>";	
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>";	
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>";	
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
				
					
				
				echo '<td  align="center">
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="green tooltip-success open-event" href="#" title="Salida"  onClick=\'Animal_Salida("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-sign-out   bigger-130"></i>
						</a>
												
						<a class="red tooltip-error open-event" href="#" title="Sanidad"  onClick=\'Animal_Sanidad("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-ambulance  bigger-130"></i>
						</a>
						
						<a class="green tooltip-success open-event" href="#" title="Movimientos"  onClick=\'Animal_Movimiento("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-cubes  bigger-130"></i>
						</a>
						
											
						<a class="orange tooltip-warning open-event" href="#" title="Editar" onClick=\'Editar_Animal("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>
						
						<a class="blue tooltip-info open-event" href="#" title="Imagen"  onClick=\'Agregar_Imagen("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-picture-o bigger-130"></i>
						</a>
						
						<a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar_Animal("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-success open-event" data-rel="tooltip" title="Salida" onClick=\'Animal_Salida("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-sign-out bigger-120"></i>
										</span>
									</a>
								</li>
							
								<li>
									<a href="#" class="tooltip-error open-event" data-rel="tooltip" title="Sanidad" onClick=\'Animal_Sanidad("'.$ls_cod.'");return false;\'>
										<span class="red">
											<i class="ace-icon fa fa-ambulance bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-success open-event" data-rel="tooltip" title="Movimiento" onClick=\'Animal_Movimiento("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-cubes bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-warning open-event" data-rel="tooltip" title="Peso" onClick=\'Peso_Animal("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa  fa-bullseye  bigger-130"></i>
										</span>
									</a>
								</li>
								
								
								<li>
									<a href="#" class="tooltip-info open-event" data-rel="tooltip" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-pencil bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="blue tooltip-info open-event" data-rel="tooltip" title="Imagen" onClick=\'Agregar_Imagen("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-picture-o bigger-130"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-error open-event" data-rel="tooltip" title="Eliminar" onClick=\'Eliminar_Animal("'.$ls_cod.'");return false;\'>
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

				
			if(strtoupper($operacion)=='LISTAR_DIAGNOSTICO'){ 
				
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				
				echo '<td>
						<div>
							
							<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Vacuna("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-pencil bigger-130"></i>
							</a>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Vacuna("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		

			}
								
			if(strtoupper($operacion)=='LISTAR_PALPACION'){ 
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
		
				
				echo '<td>
						<div>
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Parto("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
						</div>
					</td>';
			
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}			
			
			if(strtoupper($operacion)=='LISTAR_PARTO'){ 
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";					
				
				echo '<td>
						<div>
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Parto("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
						</div>
					</td>';
			
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
									
			// Página: 'ma_almacen.php'
			if(strtoupper($operacion)=='LISTAR_PRODUCCION'){ 
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
				
								
				echo '<td>
					<div class="hidden-sm hidden-xs action-buttons">
						<a class="blue tooltip-info open-event" href="#" title="Secar"  onClick=\'Secar_Animal("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-calendar  bigger-130"></i>
						</a>
						
						<a class="blue tooltip-info open-event" href="#" title="Leche"  onClick=\'Produccion_Leche("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-flask   bigger-130"></i>
						</a>  
						
						<a class="blue tooltip-info open-event" href="#" title="Peso" onClick=\'Peso_Animal("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-bullseye  bigger-130"></i>
						</a>						
						
						<a class="green tooltip-success open-event" href="#" title="Partos"  onClick=\'Parto_Animal("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-code-fork   bigger-130"></i>
						</a>
						
						<a class="red tooltip-warning open-event" href="#" title="Palpacion"  onClick=\'Palpalcion_Animal("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-star-half-o   bigger-130"></i>
						</a>
						
					</div>

					<div class="hidden-md hidden-lg">
						<div class="inline pos-rel">
							<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
								<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
							</button>

							<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
								<li>
									<a href="#" class="tooltip-info open-event" data-rel="tooltip" title="Peso" onClick=\'Secar_Animal("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-home bigger-120"></i>
										</span>
									</a>
								</li>
								
																
								<li>
									<a href="#" class="tooltip-success open-event" data-rel="tooltip" title="Leche" onClick=\'Produccion_Leche("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-trash-o bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info open-event" data-rel="tooltip" title="Peso" onClick=\'Parto_Animal("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-home bigger-120"></i>
										</span>
									</a>
								</li>
																								
								<li>
									<a href="#" class="tooltip-success open-event" data-rel="tooltip" title="Palpacion" onClick=\'Palpalcion_Animal("'.$ls_cod.'");return false;\'>
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
									
			if(strtoupper($operacion)=='LISTAR_RAZA'){ 
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";					
				
				echo '<td>
						<div>
							<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Raza("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-pencil bigger-130"></i>
							</a>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Raza("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
						</div>
					</td>';
			
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			
			
			if(strtoupper($operacion)=='LISTAR_GRUPO_ETAREO'){ 
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";					
				
				echo '<td>
						<div>
							<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Grupo("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-pencil bigger-130"></i>
							</a>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Grupo("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			
			if(strtoupper($operacion)=='LISTAR_SECTOR'){ 
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";					
				echo "<td>" . $row[$i++] . "</td>";					
				
				echo '<td>
						<div>
							<a class="blue tooltip-info open-event" href="#" title="Ubicaciones" onClick=\'Ver_Sector_Potrero("'.$ls_cod.'");return false;\' >
								<i class="ace-icon fa  fa-home bigger-130"></i>
							</a>
							
							<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Sector("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-pencil bigger-130"></i>
							</a>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Sector("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			
			if(strtoupper($operacion)=='VER_SECTOR_POTRERO'){ 
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
			}
			
			
			
			if(strtoupper($operacion)=='LISTAR_POTRERO'){
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td class='blue'>" . $row[$i++] . "</td>";		
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>";	
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>";
				
				$capacidad = $row[$i++]; //capacidad					
				$cantidad  = $row[$i++];  // cantidad que hay				
				$por_ocupacion = ($capacidad == 0)? 0: ($cantidad *100)/$capacidad;
				$color_bar = $por_ocupacion > 80 ?'progress-bar-warning':  'progress-bar-blue';
				
								
				echo '<td><div class="progress pos-rel  progress-striped" data-percent="'.number_format($por_ocupacion,2,",",".").'%">			
						<div class="progress-bar '.$color_bar.' " style="width: '.$por_ocupacion.'%;"></div>
					</div></td>';
				
				
				echo '<td>
						<div class="hidden-sm hidden-xs action-buttons">							
							<a class="blue tooltip-info open-event" href="#" title="Editar"  onClick=\'Editar_Potrero("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-pencil bigger-130"></i>
							</a>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Potrero("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Editar" onClick=\'Editar_Potrero("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-pencil bigger-130"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'Eliminar_Potrero("'.$ls_cod.'");return false;\'>
										<span class="red">
											<i class="ace-icon fa fa-trash-o bigger-130"></i>
										</span>
									</a>
								</li>
							</ul>
							
						</div>
					</div>
						
						
					</td>';		
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			
						
			if(strtoupper($operacion)=='LISTAR_LOTE'){ 
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";					
				
				echo '<td>
						<div>
							
							<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Lote("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-pencil bigger-130"></i>
							</a>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Lote("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
					
			if(strtoupper($operacion)=='LISTAR_ORIGEN'){ 
				echo '<td class="center">
						<label class="pos-rel">
							<input name="origen[]" value="'.$ls_cod.'" type="checkbox" class="ace checkbox-tr" />
							<span class="lbl"></span>
						</label>
					</td>';
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";			
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";					
				
			}
			
			if(strtoupper($operacion)=='LISTAR_ANIMAL_SANIDAD'){ 				
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";		
				echo '<td>
						<div>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Vacuna("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			}
			
			if(strtoupper($operacion)=='LISTAR_ANIMAL_SECAR'){ 				
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo '<td>
						<div>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Secar("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			}
			
			if(strtoupper($operacion)=='LISTAR_ANIMAL_POTRERO'){ 				
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo '<td>
						<div>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Animal_Move("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			}
			
			if(strtoupper($operacion)=='LISTAR_ANIMAL_PESO'){ 				
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo '<td>
						<div>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Animal_Peso("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			}
			
			if(strtoupper($operacion)=='LISTAR_ANIMAL_LECHE'){ 				
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				echo "<td>" . $row[$i++] . "</td>";	
				echo '<td>
						<div>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Animal_Leche("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		
			}
			
			if(strtoupper($operacion)=='LISTAR_VACUNA'){ 
				
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";		
				echo "<td>" . $row[$i++] . "</td>";	
				
				echo '<td>
						<div>
							
							<a class="orange tooltip-warning open-event" href="#" title="Editar"  onClick=\'Editar_Vacuna("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-pencil bigger-130"></i>
							</a>
							
							<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Vacuna("'.$ls_cod.'");return false;\'>
								<i class="ace-icon fa fa-trash-o bigger-130"></i>
							</a>
							
						</div>
					</td>';		

			}
			
					
			echo "</tr>";
		}
		
		
	} 
?>