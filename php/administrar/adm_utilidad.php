<?php

include("../../clases/clspostgres.php");
include("../../config/config.php");

/*----------------------------------------------------------------------------------------
 FUNCIÓN fun_conexion: esta función se encarga de realizar la conexión al bd.
-----------------------------------------------------------------------------------------*/
function fun_dibujar_tabla($rs,$li_columnas,$li_indice, $operacion, $variable){
	$sw = 0; 
    $j=0;
	$bandera = true;
	
	while ($row = pg_fetch_row($rs->li_idconsult)){
		// CAMPOS CLAVES PARA LOS BOTONES DE ENLACE - Deben Estar al final de la clausula select
     	$ls_cod = $row[$li_indice];     // Campo que identifica el registro clave
		$ls_cod1 = $row[$li_indice-1];  // Campo que identifica el registro clave - opcional
		$arr_rubro   =  Combo_Rubro();
		$arr_articulo 	=  	Combo_Articulo();
		$arr_articulo_venta 	=  	Combo_Articulo_Venta();
		
		// SE E// SE ESTABLECE EL COLOR DE LINEA POR DEFECTO							
		
		$sw = ($sw==0)?1:0; 	
				
		// SE ESCRIBEN LOS VALORES EN LAS COLUMNAS Y OCULTA LAS COLUMNAS POR RESPONSIVE
		echo "<tr>";
		for ($i = 0; $i < $li_columnas; $i++){
			$temp = is_numeric($row[$i])?number_format($row[$i],2,",",""):$row[$i];			
			$cols = ($i >= ($li_columnas))?"<td class=\"hidden-480\">" .$temp."</td>":"<td>" .$temp."</td>";		
			echo $cols ;		 
		}
	
		if(strtoupper($operacion)=='LISTAR_VENTA'){   
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class=''  >" . $row[$i++] . "</td>"; 
			$cell = $row[$i++];
			
			echo "<td class='blue'>
					<a class='blue' href='#' data-rel='popover'  data-trigger='hover'  data-placement='top'  data-content='".$cell."' >". 
					$row[$i++] . "</a>
				</td>"; 
				
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td class=''>" .  number_format($row[$i++],2,",",".") . "</td>"; 
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";  					 // ESTA COLUMNA NO SE MUESTRA NUNCA 
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>"; 
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";                    // ESTA COLUMNA NO SE MUESTRA NUNCA 
			echo "<td>" . number_format($row[$i++],2,",",".") . "</td>";		
		
			echo '<td  align="center">
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="blue tooltip-info open-event" href="#" title="Ver" onClick=\'Mostrar_Info("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-search-plus bigger-130"></i>
						</a>
					
						<a class="green tooltip-success  open-event" href="#" title="Pagar" onClick=\'Pagar_Venta("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-dollar bigger-130"></i>
						</a>
						
						<a class="tooltip-info open-event" href="#" title="Editar"  onClick=\'Editar_Venta("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>

						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Venta("'.$ls_cod.'");return false;\'>
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
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Ver" onClick=\'Mostrar_Info("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-search-plus bigger-120"></i>
										</span>
									</a>
								</li>
								
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Pagar" onClick=\'Pagar_Venta("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-dollar bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Editar" onClick=\'Editar_Venta("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-error" data-rel="tooltip" title="Borrar" onClick=\'Eliminar_Venta("'.$ls_cod.'");return false;\'>
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
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>";
			echo "<td class=''>" . $row[$i++] . "</td>";					
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			
			echo "<td>" . number_format($row[$i++],0,",",".") . "</td>";
			echo "<td>" . number_format($row[$i++],2,",",".") . "</td>";
			echo "<td class='hidden'>" . $row[$i] . "</td>"; 			
			echo "<td>" . number_format($row[$i++],2,",",".") . "</td>";			
			
			echo '<td  align="center">
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="tooltip-info open-event" href="#" title="Editar" onClick=\'Editar_Gasto("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>
						
						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Gasto("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-trash-o bigger-120"></i>
						</a>
					</div>

					<div class="hidden-md hidden-lg">
						<div class="inline pos-rel">
							<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
								<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
							</button>

							<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Ver" onClick=\'Editar_Gasto("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-pencil bigger-130"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Pagar" onClick=\'Eliminar_Gasto("'.$ls_cod.'");return false;\'>
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
			
		if(strtoupper($operacion)=='LISTAR_GASTO_ADD'){ 
			echo "<td><select name='det_Proyecto[]'  class='' >";
				$o_rubro = $row[$i++];
				if ($o_rubro == ""){
					echo "<option value='0' selected>Seleccionar -&gt;</option>";
				}else{
					echo "<option value='0'>Seleccionar -&gt;</option>";
				}
				foreach($arr_rubro as $k => $v) {
					$ls_cadenasel =($k == $o_rubro)?'selected':'';
					echo "<option value='$k' $ls_cadenasel>$v</option>";                
				}
				echo "</select></td>";	
				
			echo "<td><select name='det_Articulo[]'  class='chosen-select' >";
				$o_articulo = $row[$i++];
				if ($o_articulo == ""){
					echo "<option value='0' selected></option>";
				}else{
					echo "<option value='0'></option>";
				}
				foreach($arr_articulo as $k => $v) {
					$ls_cadenasel =($k == $o_articulo)?'selected':'';
					echo "<option value='$k' $ls_cadenasel>$v</option>";                
				}
				echo "</select></td>";		
			
			
			
			echo "<td><input name='det_Cantidad[]' onkeyup='calcular();' onkeypress = 'return validardec(event);' class='' size='7' value='".$row[$i++]."' type='text'></td>";			
			
			echo "<td><input name='det_Precio[]' onkeyup='calcular();' onkeypress = 'return validardec(event);'  class='' size='7' value='".$row[$i++]."' type='text'></td>";
			echo "<td><input name='det_Subtotal[]' class='' size='7' value='".$row[$i++]."' type='text'></td>";	
			
			$chk = $row[$i++] =='on'?'checked':'unchecked';
			
			echo "<td><input name='det_CheckAlmacen[]' class='' ".$chk." value='' type='checkbox'></td>";					
			echo "<td><button type='button' onClick='eliminarfila(this);' >X</button></td>";	
		}	
					
		if(strtoupper($operacion)=='LISTAR_VENTA_ADD'){ 
			echo "<td><select name='det_Proyecto[]'  class='chosen-select' >";
				$o_rubro = $row[$i++];
				if ($o_rubro == ""){
					echo "<option value='0' selected></option>";
				}else{
					echo "<option value='0'></option>";
				}
				foreach($arr_rubro as $k => $v) {
					$ls_cadenasel =($k == $o_rubro)?'selected':'';
					echo "<option value='$k' $ls_cadenasel>$v</option>";                
				}
				echo "</select></td>";	
			
			echo "<td><input name='det_Item[]' onkeypress = 'return validardec(event);'  class='' size='7' value='".$row[$i++]."' type='text'></td>";
			
			echo "<td><select name='det_Articulo[]'  class='chosen-select' >";
				$o_articulo = $row[$i++];
				if ($o_articulo == ""){
					echo "<option value='0' selected></option>";
				}else{
					echo "<option value='0'></option>";
				}
				foreach($arr_articulo_venta as $k => $v) {
					$ls_cadenasel =($k == $o_articulo)?'selected':'';
					echo "<option value='$k' $ls_cadenasel>$v</option>";                
				}
				echo "</select></td>";		
			
			echo "<td><input name='det_Cantidad[]' onkeyup='calcular();' onkeypress = 'return validardec(event);' class='' size='7' value='".$row[$i++]."' type='text'></td>";
			echo "<td><input name='det_Precio[]' onkeyup='calcular();' onkeypress = 'return validardec(event);'  class='' size='7' value='".$row[$i++]."' type='text'></td>";
			echo "<td><input name='det_Subtotal[]' class='' size='7' value='".$row[$i++]."' type='text'></td>";			
			echo "<td><button type='button' onClick='eliminarfila(this);' >X</button></td>";	
				
		}	
				
		if(strtoupper($operacion)=='LISTAR_CTAXCOBRAR'){ 
			echo "<td class='hidden-480' >" . $row[$i++] . "</td>"; 
			echo "<td >" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>";	
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>";

			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA	
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>"; 
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td>" . number_format($row[$i++],2,",",".") . "</td>";		
		
			
			echo '<td  align="center">
					<div class="hidden-sm hidden-xs action-buttons">
						
						<a class="green tooltip-success open-event" href="#" title="Prestar/Cobrar" onClick=\'Ejecutar_Ctaxcobrar("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-wrench  bigger-130"></i>
						</a>
						
												
						<a class="blue tooltip-info open-event" href="#" title="Editar" onClick=\'Editar_Ctaxcobrar("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-120"></i>
						</a>
						
						<a class="red tooltip-error open-event" href="#" title="Borrar" onClick=\'Eliminar_Ctaxcobrar("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-trash-o bigger-120"></i>
						</a>
					</div>

					<div class="hidden-md hidden-lg">
						<div class="inline pos-rel">
							<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
								<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
							</button>

							<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
								<li>
									<a href="#" class="blue tooltip-info open-event" data-rel="tooltip" title="Solicitar" onClick=\'Solicitar_Ctaxcobrar("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-plus bigger-130"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="blue tooltip-info open-event" data-rel="tooltip" title="Pagar" onClick=\'Pagar_Ctaxcobrar("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-dollar bigger-130"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="orange tooltip-warning open-event" data-rel="tooltip" title="Cobrar" onClick=\'Editar_Ctaxcobrar("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-pencil bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="red tooltip-error open-event" data-rel="tooltip" title="Borrar" onClick=\'Eliminar_Ctaxcobrar("'.$ls_cod.'");return false;\'>
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
		
		if(strtoupper($operacion)=='LISTAR_CTAXPAGAR'){ 
			echo "<td >" . $row[$i++] . "</td>"; 
			$persona = $row[$i];
			echo "<td >" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>";	
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>"; 
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>"; 
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>"; 
			
			echo "<td class='hidden'>" . $row[$i] . "</td>";     				// ESTA COLUMNA NO SE MUESTRA NUCA
			echo "<td>" . number_format($row[$i++],2,",",".") . "</td>";	

			$comentario   = $row[$i++];
			$tipo_persona = $row[$i++];
			$titular      = ($tipo_persona =='on')?$row[$i++]:$persona;

			echo '<td  align="center">
					<div class="hidden-sm hidden-xs action-buttons">
										
						
						<a class="blue" href="#" data-rel="popover"  data-trigger="hover"  data-placement="left"  data-content="'.$comentario.'" title="Titular: '.$titular.'"  >
							<i class="ace-icon fa fa-info-circle bigger-130"></i>
						</a>						
						
										
						<a class="green tooltip-success open-event" href="#" title="Credito/Pagar" onClick=\'Pagar_Ctaxpagar("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-wrench  bigger-130"></i>
						</a>
						
						
						
						<a class="blue tooltip-info open-event" href="#" title="Editar" onClick=\'Editar_Ctaxpagar("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-pencil bigger-130"></i>
						</a>
						
						<a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar_Ctaxpagar("'.$ls_cod.'");return false;\'>
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
									<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="Titular">?</span>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Solicitar" onClick=\'Solicitar_Ctaxpagar("'.$ls_cod.'");return false;\'>
										<span class="orange">
											<i class="ace-icon fa fa-plus bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Pagar" onClick=\'Pagar_Ctaxpagar("'.$ls_cod.'");return false;\'>
										<span class="blue">
											<i class="ace-icon fa fa-dollar bigger-120"></i>
										</span>
									</a>
								</li>
								
								<li>
									<a href="#" class="tooltip-info" data-rel="tooltip" title="Editar" onClick=\'Editar_Ctaxpagar("'.$ls_cod.'");return false;\'>
										<span class="green">
											<i class="ace-icon fa fa-pencil bigger-120"></i>
										</span>
									</a>
								</li>

								<li>
									<a href="#" class="tooltip-success" data-rel="tooltip" title="Borrar" onClick=\'Eliminar_Ctaxpagar("'.$ls_cod.'");return false;\'>
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
				
		if(strtoupper($operacion)=='LISTAR_RUBRO'){ 
			echo "<td  onClick=\"Editar_Rubro('".$ls_cod."');\"><div align=\"center\" title=\"Editar \"><img src=\"../../img/iconos_pagina/editar.png\" style=\"CURSOR: pointer\" width=\"21\" border=\"0\" ></div></td>";
			echo "<td  onClick=\"Eliminar_Rubro('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"21\" border=\"0\" ></div></td>";
		}
		
		if(strtoupper($operacion)=='LISTAR_ABONO'){ 
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 			
			echo "<td class=''>" . number_format($row[$i++],2,",",".") . "</td>"; 
			echo "<td class=''>" . number_format($row[$i++],2,",",".") . "</td>"; 
			echo "<td class=''>" . number_format($row[$i++],2,",",".") . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			
				echo '<td class="center"><a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar_Pago("'.$ls_cod.'");return false;\'>
					<i class="ace-icon fa fa-trash-o bigger-130"></i>
				</a>
				</td>'; 
			
		}	
				
		if(strtoupper($operacion)=='LISTAR_PRESTAR_ABONO'){ 
			
			$debe = $bandera?$variable: $debe;			
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 			
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 	
			
			$abono   = $row[$i++];
			$egreso  = $row[$i++];
			
			if($ls_cod1 == 'A' ){
				echo "<td class=''>" . number_format($abono,2,",",".") . "</td>"; //Abono
				echo "<td class=''></td>"; // Egreso
			}else{
				echo "<td class=''></td>"; //Abono
				echo "<td class=''>" . number_format($egreso,2,",",".") . "</td>"; // Egreso
				
			}
			
			echo "<td class=''>" . number_format($debe,2,",",".") . "</td>"; // Debe
			
			echo '<td class="center"><a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar_Pago("'.$ls_cod.'","'.$ls_cod1.'");return false;\'>
					<i class="ace-icon fa fa-trash-o bigger-130"></i>
				</a>
				</td>'; 
			
			$bandera = false;	
			$debe = $debe + $abono  - $egreso;	//Calcula la deuda actual en base el valor de deuda presente
		}	
					
		if(strtoupper($operacion)=='LISTAR_SOLICTAR_CREDITO'){ 
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 			
			echo "<td class=''>" . number_format($row[$i++],2,",",".") . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
				echo '<td class="center"><a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar_Pago("'.$ls_cod.'");return false;\'>
											<i class="ace-icon fa fa-trash-o bigger-130"></i>
										</a>
					</td>'; 		
		}	
						
		if(strtoupper($operacion)=='LISTAR_PAGAR'){ 
		
			$debe = $bandera?$variable: $debe;			
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 			
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 	
			$interes   = $row[$i++];
			$abono   = $row[$i++];
			$egreso  = $row[$i++];
			
			if($ls_cod1 == 'A' ){
				echo "<td class=''>" . number_format($interes,2,",",".") . "</td>"; //Abono
				echo "<td class=''>" . number_format($abono,2,",",".") . "</td>"; //Abono
				echo "<td class=''></td>"; // Egreso
			}else{
				echo "<td class=''></td>"; //Abono	
				echo "<td class=''></td>"; //Abono
				echo "<td class=''>" . number_format($egreso,2,",",".") . "</td>"; // Egreso
				
			}
			
			echo "<td class=''>" . number_format($debe,2,",",".") . "</td>"; // Debe
			
			echo '<td class="center"><a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar_Pago("'.$ls_cod.'","'.$ls_cod1.'");return false;\'>
					<i class="ace-icon fa fa-trash-o bigger-130"></i>
				</a>
				</td>'; 
			
			$bandera = false;	
			$debe = $debe + ($abono+$interes)  - $egreso;	//Calcula la deuda actual en base el valor de deuda presente
		
						
			
		}	
			
		if(strtoupper($operacion)=='LISTAR_COBRAR'){ 
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 			
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			
			echo '<td class="center"><a class="red tooltip-error open-event" href="#" title="Borrar"  onClick=\'Eliminar_Pago("'.$ls_cod.'");return false;\'>
							<i class="ace-icon fa fa-trash-o bigger-130"></i>
						</a>
				</td>'; 
						
		}	
		
		if(strtoupper($operacion)=='LISTAR_BANCO'){ 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>";				
			echo "<td >" . $row[$i++] . "</td>"; 			
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td>" . number_format($row[$i++],2,",",".")  . "</td>";			
			echo '<td  align="center">
					<div class="action-buttons">
						
						<a class="green tooltip-success open-event" href="#" title="Actualizar" onClick=\'Editar_Capital("'.$ls_cod.'");return false;\' >
							<i class="ace-icon fa  fa-refresh  bigger-130"></i>
						</a>
						
					</div>
					
				</td>';		
		}	
		
		if(strtoupper($operacion)=='VER_FACTURA'){ 
			echo "<td class=''>" . $row[$i++] . "</td>";				
			echo "<td class=''>" . $row[$i++] . "</td>"; 			
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>"; 
			echo "<td class='hidden-480'>" . number_format($row[$i++],2,",",".") . "</td>"; 
			echo "<td class=''>" . number_format($row[$i++],2,",",".") . "</td>";
		
		}					
		
		echo "</tr>";
    }
		
	
} 
?>