SELECT nu_cedula, UPPER(tx_nombre), UPPER(tx_apellido), nu_salario, tx_nro_contrato, 
to_char(fe_inicio, 'dd/mm/yyyy'), to_char(fe_fin, 'dd/mm/yyyy'), tx_descripcion, 
extract(days from now()- fe_inicio)+2  as DiasTrabajados, 
(case when (now() >  t12_contrato.fe_fin) then 'Vencido' else 'Activo' end) AS 	EstadoContrato,
pk_contrato
 
FROM t12_contrato INNER JOIN s01_persona ON s01_persona.co_persona = t12_contrato.fk_trabajador 
WHERE t12_contrato.pk_contrato= 1