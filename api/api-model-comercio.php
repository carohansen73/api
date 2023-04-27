<?php

include_once 'helpers/DB.helper.php';

class ApiModelComercio {
    
    /*** aca se gestionara las modificaciones hechas por usuarios para guardar un historico */

    private $db;
    private $dbHelper;

    function __construct() {
        
        $this->dbHelper = new DBHelper();
        // me conecto a la BD
        $this->db = $this->dbHelper->connect();
    }

    function getDatosComercio($comercio)
    {
        //convierte iso a utf en cada respuesta
        function convert(&$value, $key){
            $value = iconv('ISO-8859-1','UTF-8', $value);
        }

        //-------------------------------- 1: DATOS DEL COMERCIO ----------------------------//

        $consulta = "SELECT
            NRO_COMERCIO,
            NOMBRE,
            NOM_FANTASIA,
            RUBRO,
            COD_CALLE,
            CALLE,
            NRO,
            NRO_MED,
            PISO,
            DEPT,
            COD_POS,
            COD_POSTAL,
            COD_LOC,
            TELEFONOS,
            INGR_BRUTOS,
            CUIT,
            FECHA_APERTURA,
            FECHA_HABILITACION,
            VTO_HABILITACION,
            RESOL_NRO,
            RESOL_ANIO,
            EXPED_NRO,
            EXPED_ANIO,
            EXPED_CESE_NRO,
            EXPED_CESE_ANIO,
            FECHA_CESE,
            SUP_COMERCIAL,
            MTS_ESPACIO_P,
            COD_DELEG,
            OBSERVACIONES,
            FECHA_ALTA,
            FECHA_BAJA,
            MOT_BAJA,
            DETALLE,
            ZONA,
            ITEM,
            ACTIVIDAD_REAL,
            DECR_NRO_HABILITACION,
            DECR_ANIO_HABILITACION,
            TIPO_HABILITACION,
            DP_ESC
        FROM
            OWNER_RAFAM.ING_COMERCIOS
        where CUIT = :cuit AND FECHA_CESE is null ";
        //BUSCAR CON TIPO HABILITACION = 7 (?); FECHA CESE = "" NO ANDA
       
        $query = $this->db->prepare($consulta);
        $rs = $this->db->Execute($query, array('cuit'=>$comercio));
        $row = $rs->getAll();
        //convierto a UTF
        if(!empty($row)){
           
            //convierto a UTF iterando por si trae varias filas
            for($i=0; $i<count($row); $i++){
                array_walk($row[$i], 'convert');
            }
       
            $datos['DATOS'] = $row;

            //-------------------------------- 2: RUBROS -------------------------------------//

            $consulta2 = "SELECT
                r.ORDEN,
                r.RUBRO,
                r.ITEM,
                r.VIGENTE,
                r.VIGENCIA_DESDE,
                r.VIGENCIA_HASTA
            FROM
                  OWNER_RAFAM.ING_COMERCIOS_RUBROS r
            JOIN  OWNER_RAFAM.ING_COMERCIOS c ON r.NRO_COMERCIO = c.NRO_COMERCIO
            where c.CUIT = :cuit and r.VIGENTE = 'S' AND c.FECHA_CESE is null
            ORDER BY r.orden";

            $query = $this->db->prepare($consulta2);
            $rs = $this->db->Execute($query, array('cuit'=>$comercio));
            $rubros = $rs->getAll();

            if(empty($rubros)){
                $rubros = "No registra rubro.";
            }else{
                //convierto a UTF iterando por si trae varias filas
                for($i=0; $i<count($rubros); $i++){
                    array_walk($rubros[$i], 'convert');
                }
            }

            //agrego nueva respuesta a datos para retornar todo
            $datos['RUBROS'] = $rubros;


            //-------------------------------- 3: INMUEBLES -------------------------------------//

            $consulta3 = "SELECT i.NRO_INMUEBLE,
            i.FECHA_ALTA
            FROM ing_com_inmuebles i
            JOIN  OWNER_RAFAM.ING_COMERCIOS c ON i.NRO_COMERCIO = c.NRO_COMERCIO
            WHERE c.CUIT = :cuit 
                and i.fecha_baja is null
                AND c.FECHA_CESE  is null ";

            $query = $this->db->prepare($consulta3);
            $rs = $this->db->Execute($query, array('cuit'=>$comercio));
            $inmuebles = $rs->getAll();
        
            if(empty($inmuebles)){
                $inmuebles = "No registra inmuebles.";
            }else{
                //convierto a UTF iterando por si trae varias filas
                for($i=0; $i<count($inmuebles); $i++){
                    array_walk($inmuebles[$i], 'convert');
                }
            }
        
            //agrego nueva respuesta a datos para retornar todo
            $datos['INMUEBLES'] = $inmuebles;

             //-------------------------------- 4: DEUDAS -------------------------------------//
            $consulta4 = "SELECT 
                    R.DESCRIPCION, 
                    cc.anio, 
                    sum(cc.monto_original) AS monto
                FROM OWNER_RAFAM.ing_cc_com cc
                INNER JOIN OWNER_RAFAM.ING_COMERCIOS c ON cc.NRO_COMERCIO = c.NRO_COMERCIO 
                INNER JOIN ing_recursos r on cc.recurso = r.RECURSO 
                WHERE c.CUIT = :cuit 
                GROUP BY R.DESCRIPCION, cc.anio
                HAVING sum(cc.monto_original) > 0
                ORDER BY 1,2 DESC ,3";

            $query = $this->db->prepare($consulta4);
            $rs = $this->db->Execute($query, array('cuit'=>$comercio));
            $deudas = $rs->getAll();

            if(empty($deudas)){
                $deudas = "No registra deudas.";
            }else{
                //convierto a UTF iterando por si trae varias filas
                for($i=0; $i<count($deudas); $i++){
                    array_walk($deudas[$i], 'convert');
                }
            }

            //agrego nueva respuesta a datos para retornar todo
            $datos['DEUDAS'] = $deudas;

            //-------------------------------- 5: ANOTACIONES -------------------------------------//

            $consulta5 = "SELECT
                a.ORDEN,
                a.FECHA,
                a.TEMA,
                a.DETALLE,
                a.EXPED_NRO,
                a.EXPED_ANIO,
                a.FECHA_RESOL,
                a.FECHA_VTO,
                a.RECURSO
            FROM
                OWNER_RAFAM.ING_COM_ANOT a
            JOIN  OWNER_RAFAM.ING_COMERCIOS c ON a.NRO_COMERCIO = c.NRO_COMERCIO 
            WHERE c.CUIT = :cuit 
            AND c.FECHA_CESE  is null order by orden";

            $query = $this->db->prepare($consulta5);
            $rs = $this->db->Execute($query, array('cuit'=>$comercio));
            $anotaciones = $rs->getAll();
    
            if(empty($anotaciones)){
                $anotaciones = "No registra anotaciones.";
            }else{
                //convierto a UTF iterando por si trae varias filas
                for($i=0; $i<count($anotaciones); $i++){
                    array_walk($anotaciones[$i], 'convert');
                }
            }
            //agrego nueva respuesta a datos para retornar todo
            $datos['ANOTACIONES'] = $anotaciones;
        }
        return $datos;
    }
}