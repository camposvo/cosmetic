<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: adm_venta.php                                                    
	Descripcion: 
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/

	
	
	$ls_sql = "SELECT to_char(t16_mensaje.fe_registro, 'dd/mm/yyyy'), 
		UPPER(Emisor.tx_indicador), 
		t16_mensaje.tx_mensaje,
		UPPER(Destinatario.tx_nombre||' '|| Destinatario.tx_apellido) as NombreDestino, t17_mensaje_persona.fe_fecha_leido, 
		in_leido, t16_mensaje.pk_mensaje, Emisor.co_persona,
		UPPER(Emisor.tx_nombre||' '||Emisor.tx_apellido)
		FROM t17_mensaje_persona
		INNER JOIN s01_persona AS Destinatario ON t17_mensaje_persona.fk_destinatario = Destinatario.co_persona 
		INNER JOIN t16_mensaje ON t17_mensaje_persona.fk_mensaje = t16_mensaje.pk_mensaje
		INNER JOIN s01_persona AS Emisor ON t16_mensaje.fk_emisor = Emisor.co_persona
		WHERE in_leido = 'N' and Destinatario.co_persona = 5
		ORDER BY t16_mensaje.fe_registro DESC
		";

	
	echo $ls_sql;



?>
