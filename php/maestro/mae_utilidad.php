<?php
/*-------------------------------------------------------------------------------------------
	Nombre: mae_utilidad.php                                                     
	Descripcion: Contiene Funciones de Utilidad para el modulo << MAESTROS >>
--------------------------------------------------------------------------------------------*/
include("../../clases/clspostgres.php");
include("../../config/config.php");

/*-------------------------------------------------------------------------------------------
	FUNCIÓN : Dibuja el resultado de una Consulta SQL y coloca botones de accion
--------------------------------------------------------------------------------------------*/
function fun_dibujar_tabla($rs,$li_columnas,$li_indice, $operacion){
	$sw = 0; 
    $j=0;
	while ($row = pg_fetch_row($rs->li_idconsult)){
		// CAMPOS CLAVES PARA LOS BOTONES DE ENLACE - Deben Estar al final de la clausula select
     	$ls_cod = $row[$li_indice];     // Campo que identifica el registro clave - co_inscripcion
		$ls_cod1 = $row[$li_indice-1];  // Campo que identifica el registro clave - co_alumno

		// SE ESTABLECE EL COLOR DE LINEA POR DEFECTO
		$color_linea = ($sw==0)?"<tr class='Tabla_fila_claro'>":"<tr class='Tabla_fila_blanco'>"; 
		$sw = ($sw==0)?1:0; 
		
	
		// SE ESCRIBEN LOS VALORES EN LAS COLUMNAS
		echo "<tr>";
		for ($i = 0; $i < $li_columnas; $i++){
			$temp = is_numeric($row[$i])?number_format($row[$i],2,",","."):$row[$i];
			echo "<td>" .$temp."</td>";
		}
		
		if(strtoupper($operacion)=='LISTAR_PROVEEDOR'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
		
			echo '<td>
					<div class="hidden-sm hidden-xs action-buttons">						
						<a class="blue tooltip-info open-event" href="#" title="Detalles" onClick=\'DetalleProveedor("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-file-text-o bigger-130"></i>
						</a>
						
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Detalles" onClick=\'DetalleProveedor("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-file-text-o bigger-120"></i>
										</span>
									</a>
								</li>
								
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
				
		}	
		
			if(strtoupper($operacion)=='LISTAR_ARTICULO'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
		
			echo '<td>
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
				
		}	
		
		
			//echo "<td  onClick=\"DetalleProveedor('".$ls_cod."');\"><div align=\"center\" title=\"Ver Detalles \"><img src=\"../../img/iconos_pagina/ficha.png\" style=\"CURSOR: pointer\" width=\"25\" border=\"0\" ></div></td>";
			//echo "<td  onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"25\" border=\"0\" ></div></td>";
			//echo "<td  onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar datos \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";

		
		if(strtoupper($operacion)=='LISTAR_MARCAS'){ 
			echo "<td  onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			echo "<td  onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar datos \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";
		}
		
		if(strtoupper($operacion)=='LISTAR_CLASE'){ 
			echo "<td class='blue'>" . $ls_cod . "</td>"; 
			echo "<td class='blue'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
	
			echo '<td>
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
		}
		
		if(strtoupper($operacion)=='LISTAR_TIPO_CLASE'){ 
			echo "<td  onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			echo "<td  onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar datos \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";
		}
		
		if(strtoupper($operacion)=='LISTAR_TIPO_RUBRO'){ 
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

		if(strtoupper($operacion)=='LISTAR_BANCO'){ 
			echo "<td  onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			echo "<td  onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar datos \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";
		}

		echo "</tr>";
    } 

} 

?>
