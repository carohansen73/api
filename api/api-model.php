<?php

include_once 'helpers/DB.helper.php';

class ApiModel {
    
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
            $datos['datos'] = $row;

            
            //-------------------------------- 2: FRENTES -------------------------------------//

            $consulta2 = "SELECT
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
                WHERE F.NRO_INMUEBLE =:nroInmueble ORDER BY F.ORDEN ";

            $query = $this->db->prepare($consulta2);
            $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
        
            $frentes = $rs->getAll();
        
        
            for($i=0; $i<count($frentes); $i++){
                array_walk( $frentes[$i], 'convert');
            }

            $datos['frentes'] =$frentes;
        
            //convierto a UTF
            // array_walk($frentes, 'convert');
            //agrego nueva respuesta a datos para retornar todo
        


            //---------------------------------  3: TITULARES  ----------------------------------//

            $consulta3 = "SELECT
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
            WHERE NRO_INMUEBLE =:nroInmueble and VINC_BAJA is null";

            $query = $this->db->prepare($consulta3);
            $rs = $this->db->Execute($query, array('nroInmueble'=>$inmueble));
            $titulares = $rs->getAll();

            //convierto a UTF iterando por si trae varias filas
            for($i=0; $i<count($titulares); $i++){
                array_walk($titulares[$i], 'convert');
            }
            //agrego nueva respuesta a datos para retornar todo
            $datos['titulares'] = $titulares;


            //--------------------------- 4: TASAS Y DEUDAS ------------------------------------//
            $consulta4 = "SELECT
                r.DESCRIPCION, cc.anio, cc.cuota, sum(monto_original) 
            FROM ing_cc_inm cc 
            INNER JOIN ing_recursos r on cc.recurso = r.RECURSO 
            WHERE cc.nro_inmueble =:nroInmueble
            GROUP BY r.DESCRIPCION, cc.anio, cc.cuota
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
            $datos['tasas y deudas'] = $tasas_y_deudas;
                

            //-------------------------- 5: COMERCIOS EN INMUEBLE -----------------------------//

            $consulta5 = "SELECT * 
            FROM ing_com_inmuebles 
            WHERE NRO_INMUEBLE = :nroInmueble 
                AND fecha_baja is null";

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
            $datos['comercios en inmueble'] = $comercios_en_inmueble;


            //-------------------------------- 6: ANOTACIONES ------------------------------//

            $consulta6 = "SELECT
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
            $datos['anotaciones'] = $anotaciones;

        }
       

       
        return $datos;
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
       

       
            $datos['datos'] = $row;


            //-------------------------------- 2: RUBROS -------------------------------------//

            $consulta2 = "SELECT
                r.NRO_COMERCIO,
                r.ORDEN,
                r.RUBRO,
                r.ITEM,
                r.VIGENTE,
                r.VIGENCIA_DESDE,
                r.VIGENCIA_HASTA
            FROM
                  OWNER_RAFAM.ING_COMERCIOS_RUBROS r
            JOIN  OWNER_RAFAM.ING_COMERCIOS c ON r.NRO_COMERCIO = c.NRO_COMERCIO
            where c.CUIT = :cuit and r.VIGENTE = 'S' AND c.FECHA_CESE is null";

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
            $datos['rubros'] = $rubros;


            //-------------------------------- 3: INMUEBLES -------------------------------------//

            $consulta3 = "SELECT i.*
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
            $datos['inmuebles'] = $inmuebles;
            
            //-------------------------------- 3: ANOTACIONES -------------------------------------//

            $consulta3 = "SELECT
                a.NRO_COMERCIO,
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

            $query = $this->db->prepare($consulta3);
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
            $datos['anotaciones'] = $anotaciones;
        }
        return $datos;
    }



}