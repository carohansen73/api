-- datos inmueble
SELECT
	NRO_INMUEBLE,
	/*
	CIRCUNS,
	SECCION,
	CHACRA_NRO,
	CHACRA_LET,
	QUINTA_NRO,
	QUINTA_LET,
	FRACCION_NRO,
	FRACCION_LET,
	MANZANA_NRO,
	MANZANA_LET,
	PARCELA_NRO,
	PARCELA_LET,
	SUBPARCELA,
	UNI_FUNCIONAL,
	POLIGONO,
	PORC_DOMINIO, */
	TIPO,
	USO,
	CARACTERISTICA,
	/*COD_CALLE,
	CALLE,
	NRO,
	NRO_MED,
	PISO,
	DEPT,
	COD_POS,
	COD_POSTAL,
	COD_LOC,
	FECHA_EDIFICACION,
	FECHA_INSCRIPCION,
	ORIGEN,
	NRO_INSCRIPCION,
	NRO_PLANO,*/
	SUP_TERRENO,
	SUP_CUBIERTA,
	SUP_SEMICUBIERTA,
	MTS_FONDO,
	/*VAL_TERRENO,
	VAL_EDIFICACION,
	VAL_MEJORAS,
	VAL_BASICA,
	VAL_FISCAL,
	PORC_LAGUNA,
	PAR_ORIGINAL,
	PAR_PROVINCIAL,*/
	ZONA,
	-- COD_DELEG,
	OBSERVACIONES,
	FECHA_ALTA,
	FECHA_BAJA,
	MOT_BAJA,
	/*MULTIPR1,
	MULTIPR2,
	MULTIPR3,
	RESP_PAGO,*/
	DP_COD_CALLE,
	DP_CALLE,
	DP_NRO,
	-- DP_NRO_MED,
	DP_PISO,
	DP_DEPT,
	DP_COD_POS,
	DP_COD_POSTAL,
	DP_COD_LOC,
	DP_COD_PRO,
	DP_DETALLE,
	DETALLE,
	/*MEDIDOR_AGUA,
	MEDIDOR_GAS,
	MEDIDOR_LUZ,
	VAL_ANTERIOR,
	NRO_PLANO_OBRA,
	COD_BARRIO,
	DP_COD_BARRIO,
	TOMO,
	FOLIO,
	BIEN_FAMILIA,
	INST_DEPOR,
	COCHERA,
	ASOC_CIVIL_SFL,
	PREPARTIDA,
	BASE_IMPONIBLE,*/
	ID_GIS,
	/*DP_LATERAL1,
	DP_LATERAL2,
	LATERAL1,
	LATERAL2,
	DP_MZA,
	DP_BLK,
	DP_ESC,
	MZA,
	BLK,*/
	ESC
FROM
	OWNER_RAFAM.ING_INMUEBLES
WHERE NRO_INMUEBLE = 5274500;
/*
se puede unir la anterior con:
ing_inm_caracteristicas
ing_inm_tipo
ing_inm_uso
ing_inm_zonas
*/

-- array datos frentes:
SELECT
	F.NRO_INMUEBLE,
	F.ORDEN,
	F.CATEGORIA,
	G.DESCRIPCION, 
	F.METROS,
	F.ZONA,
	F.CARACT
FROM
	OWNER_RAFAM.ING_INM_FRENTES F
INNER JOIN ING_INM_FRENTES_CATEG G ON F.CATEGORIA = G.CODIGO 
WHERE F.NRO_INMUEBLE = 5274500 ORDER BY F.ORDEN

-- array titulares 
SELECT
	NRO_INMUEBLE,
	NRO_CONTRIB,
	TITULAR,
	VINC_TIPO,
	VINC_PORC,
	VINC_ALTA,
	VINC_BAJA,
	MOT_BAJA
FROM
	OWNER_RAFAM.ING_CON_INMUEBLES
WHERE NRO_INMUEBLE = 5274500 and VINC_BAJA is null

-- array tasas y deudas:
select r.DESCRIPCION, cc.anio, cc.cuota, sum(monto_original) 
from ing_cc_inm cc 
inner join ing_recursos r on cc.recurso = r.RECURSO 
where cc.nro_inmueble = 5274500 /*151600*/
group by r.DESCRIPCION, cc.anio, cc.cuota
order by 1,2 desc ,3

-- array comercios en inmueble:
select * from ing_com_inmuebles WHERE NRO_INMUEBLE = 5274400 and fecha_baja is null

-- array anotaciones
SELECT
	NRO_INMUEBLE,
	ORDEN,
	FECHA,
	TEMA,
	DETALLE,
	EXPED_NRO,
	EXPED_ANIO,
	FECHA_RESOL,
	FECHA_VTO,
	RECURSO
FROM
	OWNER_RAFAM.ING_INM_ANOT
where nro_inmueble = 5274400 order by orden 

