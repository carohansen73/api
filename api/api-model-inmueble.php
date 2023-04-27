<?php

include_once 'helpers/DB.helper.php';

class ApiModelInmueble {
    
    /*** aca se gestionara las modificaciones hechas por usuarios para guardar un historico */

    private $db;
    private $dbHelper;

    function __construct() {
        
        $this->dbHelper = new DBHelper();
        // me conecto a la BD
        $this->db = $this->dbHelper->connect();
    }


    
    function getDatosInmueble($inmueble)
    {
        //convierte iso a utf en cada respuesta
        function convert(&$value, $key){
            $value = iconv('ISO-8859-1','UTF-8', $value);
            // print_r($value);
        }

        //-------------------------------- 1: DATOS INMUEBLE ----------------------------//

        $consulta = 'SELECT
            NRO_INMUEBLE,
            TIPO,
            USO,
            CARACTERISTICA,
            SUP_TERRENO,
            SUP_CUBIERTA,
            SUP_SEMICUBIERTA,
            MTS_FONDO,
            ZONA,
            OBSERVACIONES,
            FECHA_ALTA,
            FECHA_BAJA,
            MOT_BAJA,
            DP_COD_CALLE,
            DP_CALLE,
            DP_NRO,
            DP_PISO,
            DP_DEPT,
            DP_COD_POS,
            DP_COD_POSTAL,
            DP_COD_LOC,
            DP_COD_PRO,
            DP_DETALLE,
            DETALLE,
            ID_GIS,
            ESC
        FROM
            OWNER_RAFAM.ING_INMUEBLES
        WHERE NRO_INMUEBLE  =:nroInmueble';
       
        $query = $this->db->prepare($consulta);
        $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
        $row = $rs->FetchRow();
        //convierto a UTF
        array_walk($row, 'convert');
       

        if($row){
            $datos['DATOS'] = $row;

            
            //-------------------------------- 2: FRENTES -------------------------------------//

            $consulta2 = "SELECT
                F.ORDEN,
                F.CATEGORIA,
                G.DESCRIPCION, 
                F.METROS
                FROM
                OWNER_RAFAM.ING_INM_FRENTES F
                INNER JOIN ING_INM_FRENTES_CATEG G ON F.CATEGORIA = G.CODIGO 
                WHERE F.NRO_INMUEBLE =:nroInmueble ORDER BY F.ORDEN ";

            $query = $this->db->prepare($consulta2);
            $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
        
            $frentes = $rs->getAll();
        
        
            for($i=0; $i<count($frentes); $i++){
                array_walk( $frentes[$i], 'convert');
            }

            $datos['FRENTES'] =$frentes;
        
            //convierto a UTF
            // array_walk($frentes, 'convert');
            //agrego nueva respuesta a datos para retornar todo
        


            //---------------------------------  3: TITULARES  ----------------------------------//

            $consulta3 = "SELECT
                i.NRO_CONTRIB,
                i.TITULAR,
                i.VINC_TIPO,
                i.VINC_PORC,
                i.VINC_ALTA,
                c.APYNOM AS NOMBRE
                    
            FROM OWNER_RAFAM.ING_CON_INMUEBLES i
            JOIN OWNER_RAFAM.ING_CONTRIBUYENTES c ON i.NRO_CONTRIB = c.NRO_CONTRIB
            WHERE i.NRO_INMUEBLE =:nroInmueble 
            and i.VINC_BAJA is null
            and i.MOT_BAJA IS NULL";

            $query = $this->db->prepare($consulta3);
            $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
            $titulares = $rs->getAll();

            //convierto a UTF iterando por si trae varias filas
            for($i=0; $i<count($titulares); $i++){
                array_walk($titulares[$i], 'convert');
            }
            //agrego nueva respuesta a datos para retornar todo
            $datos['TITULARES'] = $titulares;


            //--------------------------- 4: TASAS Y DEUDAS ------------------------------------//
            $consulta4 = "SELECT
                r.DESCRIPCION, 
                cc.anio, 
                sum(monto_original) AS monto
            FROM ing_cc_inm cc 
            INNER JOIN ing_recursos r on cc.recurso = r.RECURSO 
            WHERE cc.nro_inmueble =:nroInmueble
            GROUP BY r.DESCRIPCION, cc.anio
            HAVING sum(monto_original) > 0
            ORDER BY 1,2 DESC ,3";

            $query = $this->db->prepare($consulta4);
            $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
            
            $tasas_y_deudas = $rs->getAll();
            if(empty($tasas_y_deudas)){
                $tasas_y_deudas = "No existen deudas registradas.";
            }else{
                //convierto a UTF c/fila
                for($i=0; $i<count($tasas_y_deudas); $i++){
                    array_walk($tasas_y_deudas[$i], 'convert');
                }
            }
            //agrego nueva respuesta a datos para retornar todo
            $datos['TASAS_Y_DEUDAS'] = $tasas_y_deudas;
                

            //-------------------------- 5: COMERCIOS EN INMUEBLE -----------------------------//

            $consulta5 = "SELECT 
                i.NRO_COMERCIO,
                i.FECHA_ALTA,
                c.CUIT
            FROM ing_com_inmuebles i
            JOIN ING_COMERCIOS c ON i.NRO_COMERCIO = c.NRO_COMERCIO
            WHERE i.NRO_INMUEBLE = :nroInmueble  
            AND i.fecha_baja is null
            AND i.MOT_BAJA IS NULL";

            $query = $this->db->prepare($consulta5);
            $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
            $comercios_en_inmueble = $rs->getAll();

            if(empty($comercios_en_inmueble)){
                $comercios_en_inmueble = "No registra comercio.";
            }else{
                //convierto a UTF
                for($i=0; $i<count($comercios_en_inmueble); $i++){
                    array_walk($comercios_en_inmueble[$i], 'convert');
                }
            }
            //agrego nueva respuesta a datos para retornar todo
            $datos['COMERCIOS_EN_INMUEBLE'] = $comercios_en_inmueble;


            //-------------------------------- 6: ANOTACIONES ------------------------------//

            $consulta6 = "SELECT
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
            where nro_inmueble = :nroInmueble order by orden";

            $query = $this->db->prepare($consulta6);
            $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
            $anotaciones = $rs->getAll();

            if(empty($anotaciones)){
                $anotaciones = "No registra anotaciones.";
            }else{
                //convierto a UTF
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