<?php
/*-------------------------------------------------------------------------------------------
	Nombre: adm_utilidad.php                                                     
	Descripcion: Contiene Funciones de Utilidad para el modulo << ADMINISTRAR >>
--------------------------------------------------------------------------------------------*/
include("../../clases/clspostgres.php");
include("../../config/config.php");

/*----------------------------------------------------------------------------------------
 FUNCIÓN fun_dibujar_tabla: Ahora procesa arrays puros en lugar de recursos activos de BD
-----------------------------------------------------------------------------------------*/
function fun_dibujar_tabla($rows, $operacion) {
	if (empty($rows) || !is_array($rows)) {
		return;
	}

	$sw = 0; 
	foreach ($rows as $row) {
		$sw = ($sw == 0) ? 1 : 0; 
		echo "<tr>";
		
		// Inicializamos el iterador de columnas dinámicas
		$i = 0;
		
		if (strtoupper($operacion) == 'LISTAR_DEUDAS_VENDEDOR') { 
			// $row[0]: fk_responsable, $row[1]: tx_nombre, $row[2]: Debe
			echo "<td class='details-control' id='" . $row[0] . "'></td>"; 
			echo "<td class=''>" . $row[1] . "</td>"; 
			echo "<td class='blue'>" . number_format($row[2], 2, ",", ".") . "</td>"; 			
		}
		
		if (strtoupper($operacion) == 'LISTAR_PRESTADO') { 
			// $row[0]: tx_nombre, $row[1]: Debe
			echo "<td class=''>" . $row[0] . "</td>"; 
			echo "<td class='blue'>" . number_format($row[1], 2, ",", ".") . "</td>"; 			
		}
		
		if (strtoupper($operacion) == 'LISTAR_CREDITOS') { 
			// $row[0]: tx_nombre, $row[1]: Debe
			echo "<td class=''>" . $row[0] . "</td>"; 
			echo "<td class='red'><b>" . number_format($row[1], 2, ",", ".") . "</b></td>"; 			
		}
				
		if (strtoupper($operacion) == 'LISTAR_GASTO_PERIODO') { 
			echo "<td class=''>" . $row[0] . "</td>"; 
			echo "<td class='blue'>" . number_format($row[1], 2, ",", ".") . "</td>"; 			
		}

		if (strtoupper($operacion) == 'LISTAR_VENTAS_PERIODO') { 
			echo "<td class=''>" . $row[0] . "</td>"; 
			echo "<td class=''>" . $row[1] . "</td>"; 
			echo "<td class=''>" . $row[2] . "</td>"; 
			echo "<td class='blue'>" . number_format($row[3], 2, ",", ".") . "</td>"; 			
		}
				
		if (strtoupper($operacion) == 'LISTAR_SEGUIMIENTO_1') { 
			$proyecto      = $row[0];
			$ventaNeta     = floatval($row[1]);
			$gastoNeto     = floatval($row[2]);
			$gananciaNeta  = $ventaNeta - $gastoNeto; 
			$porc_gan      = ($gastoNeto == 0) ? 0 : (($ventaNeta - $gastoNeto) * 100) / $gastoNeto;		
			
			echo "<td class='blue'>" . $proyecto . "</td>";	
			echo "<td>" . number_format($ventaNeta, 2, ",", ".") . "</td>";  
			echo "<td>" . number_format($gastoNeto, 2, ",", ".") . "</td>";  
			echo "<td>" . number_format(floatval($gananciaNeta), 2, ",", ".") . "</td>";  
			echo "<td class=''>" . number_format($porc_gan, 2, ",", ".") . "%</td>"; 
		}

		if (strtoupper($operacion) == 'LISTAR_SEGUIMIENTO_2') { 
			$proyecto   = $row[0];
			$ingreso    = floatval($row[3]);
			$egreso     = floatval($row[4]);
			$ganancia   = $ingreso - $egreso; 
			$porc_gan   = ($egreso == 0) ? 0 : (($ingreso - $egreso) * 100) / $egreso;		
			
			echo "<td class='blue'>" . $proyecto . "</td>";	
			echo "<td>" . number_format($ingreso, 2, ",", ".") . "</td>";  
			echo "<td>" . number_format($egreso, 2, ",", ".") . "</td>";  
			echo "<td>" . number_format(floatval($ganancia), 2, ",", ".") . "</td>";  
			echo "<td class=''>" . number_format($porc_gan, 2, ",", ".") . "%</td>"; 
		}
		
		echo "</tr>";
	}
} 
?>