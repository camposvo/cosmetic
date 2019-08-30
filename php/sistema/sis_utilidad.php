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
		$ls_cod2 = $row[$li_indice-2];

		
		//PAGINA: no implementado
		if(strtoupper($operacion)=='ROLES'){ 
	    	echo "<td  onClick=\""."Editar_nombre_rol('".$ls_cod."');\"><div align=\"center\" title=\"Editar Rol \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
			echo "<td  onClick=\""."Editar_datos_rol('".$ls_cod."');\"><div align=\"center\" title=\"Editar Funcionalidades \"><img src=\"../../img/iconos_pagina/confi.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
			echo "<td  onClick=\""."Guardar_rol('".$ls_cod."');\"><div align=\"center\" title=\"Ver Usuario \"><img src=\"../../img/iconos_pagina/agregar_nuevo.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
			echo "<td  onClick=\""."Eliminar_rol('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Rol \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
		}
		
		//PAGINA: sis_usuario.php
		if(strtoupper($operacion)=='BUSCAR_USUARIO'){
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>";		
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>";		
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			
			if($ls_cod2=="S"){ // Esta en la  lista de mensajes
				echo '<td>
						<div>
						<a class="blue tooltip-info open-event" href="#" title="Grupo de Mensaje " onClick=\'Elimina_GMensaje("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  	fa-envelope bigger-130"></i>
						</a>
					</div>
				</td>';	
			}else{ // No esta  a lista de mensajes
			echo '<td>
					<div>
						<a class="blue tooltip-info open-event" href="#" title="Grupo de Mensaje " onClick=\'Agrega_GMensaje("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa   fa-exchange   bigger-130"></i>
						</a>
					</div>
				</td>';	
			}	
			
			if($ls_cod1==""){
				echo '<td>
						<div>
						<a class="green tooltip-success open-event" href="#" title="Fijar un Password " onClick=\'Password("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  	 fa-unlock  bigger-130"></i>
						</a>
					</div>
				</td>';	
			}else{ // No esta  a lista de mensajes
			echo '<td>
					<div>
						<a class="blue tooltip-info open-event" href="#" title="Cambiar Password " onClick=\'Password("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa   fa-lock    bigger-130"></i>
						</a>
					</div>
				</td>';	
			}
					
			echo '<td  align="center">
					<div class="hidden-sm hidden-xs action-buttons">
						
						
						<a class="blue tooltip-info open-event" href="#" title="Foto" onClick=\'Foto("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-camera bigger-130"></i>
						</a>
						
						<a class="orange tooltip-warning  open-event" href="#" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>
						
						<a class="green tooltip-success open-event" href="#" title="Privilegios"  onClick=\'Asignar_privilegios("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-bookmark  bigger-130"></i>
						</a>

						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Foto" onClick=\'Foto("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-camera bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-warning" data-rel="tooltip" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-success" data-rel="tooltip" title="Privilegios" onClick=\'Asignar_privilegios("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-bookmark  bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'Eliminar("'.$ls_cod.'");return false;\'>
										<span class="red">
											<i class="ace-icon fa fa-trash-o bigger-120"></i>
										</span>
									</a>
								</li>

								
							</ul>
						</div>
					</div>
				</td>';	
		
		//	echo "<td  onClick=\""."Foto('".$ls_cod."');\"><div align=\"center\" title=\"Cargar Foto \"><img src=\"../../img/iconos_pagina/camara4.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
		//	echo "<td  onClick=\""."Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos del Usuario \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
		//	echo "<td  onClick=\""."Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Usuario \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
		//	echo "<td  onClick=\""."Asignar_privilegios('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar Rol \"><img src=\"../../img/iconos_pagina/rol.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" ></div></td>";
			
				
		}
		
		//PAGINA: sis_rol.php
		if(strtoupper($operacion)=='LISTAR_ROL'){ 
			
			echo  '<td>
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="blue tooltip-info open-event" href="#" title="Editar" onClick=\'ir_a_pagina(2,'.$ls_cod.',"sis_rol_mtto.php");return false;\' >
							<i class="ace-icon fa  fa-pencil bigger-130"></i>
						</a>
						
						<a class="orange tooltip-warning  open-event" href="#" title="Funciones" onClick=\'ir_a_pagina(3,'.$ls_cod.',"sis_rol_privilegio.php");return false;\'>
							<i class="ace-icon fa fa-bookmark  bigger-130"></i>
						</a>
						
						<a class="green tooltip-success open-event" href="#" title="Privilegios"  onClick=\'ir_a_pagina(4,'.$ls_cod.',"sis_rol_usuarios.php");return false;\'>
							<i class="ace-icon fa fa-users  bigger-130"></i>
						</a>

						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'ir_a_pagina(5,'.$ls_cod.',"sis_rol.php");return false;\'>
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Editar" onClick=\'ir_a_pagina(2,'.$ls_cod.',"sis_rol_mtto.php");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-pencil bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-warning" data-rel="tooltip" title="Editar" onClick=\'ir_a_pagina(3,'.$ls_cod.',"sis_rol_privilegio.php");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-bookmark bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-success" data-rel="tooltip" title="Privilegios" onClick=\'ir_a_pagina(4,'.$ls_cod.',"sis_rol_usuarios.php");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-users  bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'ir_a_pagina(5,'.$ls_cod.',"sis_rol.php");return false;\'>
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

		//PAGINA: sis_rol.php
		if(strtoupper($operacion)=='LISTAR_PRIVILEGIO'){ 
			echo "<td  onClick=\""."Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Editar Rol \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";
			
		}			
		
		
		//PAGINA: sis_menu.php
		if(strtoupper($operacion)=='LISTAR_MENU_PADRE'){ 
			$max_orden = $row[$li_indice-2];
			$min_orden = $row[$li_indice-3];
			 
			//echo "<td  onClick=\"Submenu('".$ls_cod."');\"><div align=\"center\" title=\"SubMenu \"><img src=\"../../img/iconos_pagina/submenu.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			
			echo '<td align="center">
						<div>
						<a class="blue tooltip-info open-event" href="#" title="Sub-Menu" onClick=\'Submenu("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa fa-bars bigger-150"></i>
						</a>
					</div>
				</td>';	
			
			echo '<td align="center">
						<div>
						<a class="blue tooltip-info open-event" href="#" title="Editar" onClick=\'Editar("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa fa-pencil bigger-150"></i>
						</a>
					</div>
				</td>';	
			
			
			
			
			//echo "<td  onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			if($ls_cod1>$min_orden){
				//echo "<td  onClick=\"Mover('".$ls_cod."','".$ls_cod1."','U');\"><div align=\"center\" title=\"Subir \"><img src=\"../../img/iconos_pagina/menu_subir.gif\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";}
				echo '<td align="center">
					<div>
					<a class="green tooltip-success open-event" href="#" title="Subir" onClick=\'Mover("'.$ls_cod.'","'.$ls_cod1.'","U");return false;\' >
						<i class="ace-icon fa fa-arrow-up bigger-150"></i>
					</a>
					</div>
				</td>';
				
			}else{
				echo "<td></td>";
			}
			
			if($ls_cod1<$max_orden){
				//echo "<td  onClick=\"Mover('".$ls_cod."','".$ls_cod1."','D');\"><div align=\"center\" title=\"Bajar \"><img src=\"../../img/iconos_pagina/menu_bajar.gif\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
				echo '<td align="center">
					<div>
					<a class="green tooltip-success open-event" href="#" title="Bajar" onClick=\'Mover("'.$ls_cod.'","'.$ls_cod1.'","D");return false;\' >
						<i class="ace-icon fa fa-arrow-down bigger-150"></i>
					</a>
					</div>
				</td>';
			}else{
				echo "<td></td>";
			}
			
			echo '<td align="center">
						<div>
						<a class="red tooltip-error open-event" href="#" title="Eliminar" onClick=\'Eliminar("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa fa-trash-o bigger-150"></i>
						</a>
					</div>
				</td>';	
			
			//echo "<td  onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar datos \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";
		}	
		
		//PAGINA: sis_submenu.php
		if(strtoupper($operacion)=='LISTAR_MENU_HIJO'){
			$max_orden = $row[$li_indice-2]; 
			$min_orden = $row[$li_indice-3];
			$estado = $row[$li_indice-4];
			
			if($estado=='S'){echo "<td  onClick=\"Activar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/on.jpg\" style=\"CURSOR: pointer\" width=\"30\" border=\"0\" ></div></td>";}
			else{echo "<td  onClick=\"Activar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/off.jpg\" style=\"CURSOR: pointer\" width=\"30\" border=\"0\" ></div></td>";}
			
			echo "<td  onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Editar datos \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			
			if($ls_cod1>$min_orden){echo "<td  onClick=\"Mover('".$ls_cod."','".$ls_cod1."','U');\"><div align=\"center\" title=\"Subir \"><img src=\"../../img/iconos_pagina/menu_subir.gif\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";}
			else{echo "<td></td>";}
			
			if($ls_cod1<$max_orden){echo "<td  onClick=\"Mover('".$ls_cod."','".$ls_cod1."','D');\"><div align=\"center\" title=\"Bajar \"><img src=\"../../img/iconos_pagina/menu_bajar.gif\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";}
			else{echo "<td></td>";}
			echo "<td  onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar datos \"><img src=\"../../img/iconos_pagina/eliminar.png\" style=\"CURSOR: pointer\" width=\"20\" height=\"20\" border=\"0\" ></div></td>";
		}	
		
		echo "</tr>";
	}
	
} 
?>