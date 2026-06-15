<?php
/*------------------------------------------------------------------------------------------------|
|  			Nombre: 'ma_utilidad.php'          	                       							  |
|  			Descripción: Contiene Funciones De Utilidad Para El Modulo << MAESTROS >>			  |
|------------------------------------------------------------------------------------------------*/ 

	include("../../clases/clspostgres.php");
	include("../../config/config.php");
	
/*-------------------------------------------------------------------------------------------|
|		Función: Dibuja El Resultado De Una Consulta SQL Y Coloca Botones De Acción.         |
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
			
			// Página: 'ma_proveedor.php'
			if(strtoupper($operacion)=='BUSCAR_PROVEEDOR'){ 
				echo "<td style=\"CURSOR: pointer\" onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar Datos Del Proveedor \"><img src=\"../../img/iconos_pagina/editar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: pointer\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Proveedor\"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			// Página: 'ma_clasificaciones.php' 
			if(strtoupper($operacion)=='LISTAR_TIPO_CLASIFICACION'){ 
				echo "<td style=\"CURSOR: pointer\" onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar Clasificación\"><img src=\"../../img/iconos_pagina/editar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: pointer\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Clasificación\"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			// Página: 'ma_almacen.php'
			if(strtoupper($operacion)=='LISTAR_ALMACEN'){ 
				echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
				
				echo '<td>
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="blue tooltip-info open-event" href="#" title="Ubicaciones" onClick=\'Agregar_ubicacion("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-home bigger-130"></i>
						</a>
						
						<a class="green tooltip-success open-event" href="#" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>
						
						<a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-info open-event" data-rel="tooltip" title="Ubicacion" onClick=\'Agregar_ubicacion("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-home bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info open-event" data-rel="tooltip" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-pencil bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-success open-event" data-rel="tooltip" title="Eliminar" onClick=\'Eliminar("'.$ls_cod.'");return false;\'>
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
				
			// Página: 'ma_marcas.php'
			if(strtoupper($operacion)=='LISTAR_MARCAS'){ 
				echo "<td style=\"CURSOR: pointer\" onClick=\"Agregar_modelos('".$ls_cod."');\"><div align=\"center\" title=\"Agregar Modelos\"><img src=\"../../img/iconos_pagina/producto.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: pointer\" onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar Marca\"><img src=\"../../img/iconos_pagina/editar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: pointer\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Marca\"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			// Página: 'ma_modelos.php'
			if(strtoupper($operacion)=='LISTAR_MODELOS'){ 
				echo "<td style=\"CURSOR: pointer\" onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar Modelo\"><img src=\"../../img/iconos_pagina/editar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: pointer\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Modelo\"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			if(strtoupper($operacion)=='LISTAR_UBICACIONES'){ 
				echo "<td style=\"CURSOR: pointer\" onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar Modelo\"><img src=\"../../img/iconos_pagina/editar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: pointer\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Modelo\"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			
			
			
			if(strtoupper($operacion)=='LISTAR_INVENTARIO'){ 
				$articulo = $row[$i++];
				$presentacion = $row[$i++];
				$compras = $row[$i++];
				$ventas = $row[$i++];
				$inventario = $row[$i++];

				echo "<td class=''>" . $articulo . "</td>"; 
				echo "<td class='hidden-480'>" . $presentacion . "</td>"; 
				echo "<td class=''>" . $compras . "</td>";	
				echo "<td class=''>" . $ventas . "</td>";		
				echo "<td class='hidden-480'>" . $inventario . "</td>"; 

			
						
			
				//echo "<td onClick=\"Inventario('".$ls_cod."');\"><div align=\"center\" title=\"Inventariar\"><img src=\"../../img/iconos_pagina/almacen.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
			
			if(strtoupper($operacion)=='LISTAR_CATALOGO'){ 
				echo "<td style=\"CURSOR: pointer\" onClick=\"Detalle('".$ls_cod."','".$ls_cod1."');\"><div align=\"center\" title=\"Editar Modelo\"><img src=\"../../img/iconos_pagina/editar.png\" width=\"22\" height=\"22\" border=\"0\" ></div></td>";
			}
		
			echo "</tr>";
		}
		
		
	} 
?>