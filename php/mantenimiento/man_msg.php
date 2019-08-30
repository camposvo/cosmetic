<?php 
	include_once ("man_utilidad.php");

	if (!$_GET)	{
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
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	$ls_sql = "SELECT to_char(t16_mensaje.fe_registro, 'dd/mm/yyyy'), 
		UPPER(substring(Emisor.tx_nombre from 1 for 1))||LOWER(substring(Emisor.tx_nombre from 2 for char_length(Emisor.tx_nombre))), 
		t16_mensaje.tx_mensaje,
		UPPER(Destinatario.tx_nombre||' '|| Destinatario.tx_apellido) as NombreDestino, t17_mensaje_persona.fe_fecha_leido, 
		in_leido, t16_mensaje.pk_mensaje, Emisor.co_persona,
		UPPER(Emisor.tx_nombre||' '||Emisor.tx_apellido)
		FROM t17_mensaje_persona
		INNER JOIN s01_persona AS Destinatario ON t17_mensaje_persona.fk_destinatario = Destinatario.co_persona 
		INNER JOIN t16_mensaje ON t17_mensaje_persona.fk_mensaje = t16_mensaje.pk_mensaje
		INNER JOIN s01_persona AS Emisor ON t16_mensaje.fk_emisor = Emisor.co_persona
		WHERE in_leido = 'N' and Destinatario.co_persona = ".$co_usuario." 
		ORDER BY t16_mensaje.fe_registro DESC
		";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	$num_reg = $obj_miconexion->fun_numregistros($ls_resultado);
	
	//echo $ls_sql;
	while($row = pg_fetch_row($ls_resultado)){
		$i=0;
			$fecha_registro   = $row[$i++];
			$emisor       = $row[$i++];
			$mensaje      = $row[$i++];
			$destinatario = $row[$i++];
			
			$result .= '<li>
					<a href="#" class="clearfix">
						<img src="assets/avatars/avatar.png" class="msg-photo" alt="" />
							<span class="msg-body">
								<span class="msg-title">
									<span class="blue">'.$emisor .':</span>
									'.$mensaje.'
								</span>
							<span class="msg-time">
								<i class="ace-icon fa fa-clock-o"></i>
									<span>'.$fecha_registro.'</span>
							</span>
						</span>
					</a>
				</li>';	
	}
	
	//$a = array ('id'=>1,'datos'=>2);
	$a['num_reg']=$num_reg;
	$a['datos']=$result;
	
	$json = json_encode($a);
	echo $json ;


?>
