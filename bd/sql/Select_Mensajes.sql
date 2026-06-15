SELECT t17_mensaje_persona.fe_fecha_leido, UPPER(Destinatario.tx_nombre||' '|| Destinatario.tx_apellido) as NombreDestino, 
	t16_mensaje.tx_mensaje,  t16_mensaje.fe_registro,
	UPPER(Emisor.tx_nombre||' '|| Emisor.tx_apellido), in_leido, t16_mensaje.pk_mensaje 
  FROM t17_mensaje_persona
  INNER JOIN s01_persona AS Destinatario ON t17_mensaje_persona.fk_destinatario = Destinatario.co_persona 
  INNER JOIN t16_mensaje ON t17_mensaje_persona.fk_mensaje = t16_mensaje.pk_mensaje
  INNER JOIN s01_persona AS Emisor ON t16_mensaje.fk_emisor = Emisor.co_persona
  where Destinatario.co_persona = 5
  ORDER BY t16_mensaje.fe_registro DESC
