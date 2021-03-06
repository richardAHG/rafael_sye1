<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";
require "Usuario.php";

class CargaMasiva
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function insertarCM($data)
    {
        $obj = new Usuario();
        $userNoregistrado = [];
        $userRegistrado = [];
        $isCorrect = 0;
        $mensaje = 'Proceso Finalizado con exito';
        /* Desactivar la autoconsigna */
        desableCommitAutomatic();
        try {
            foreach ($data as $key => $value) {
                //insertar en tabla personal
                $JEFE_CARGO = empty($value['JEFE_CARGO']) ? 0 : $value['JEFE_CARGO'];
                $usuario_id = $obj->insertarPersonalMasivo(
                    $value['NOMBRES'],
                    $value['APEPAT'],
                    $value['APEMAT'],
                    $value['EMAIL'],
                    $value['CARGO'],
                    $value['REGIMEN_LABORAL'],
                    $value['DIRECCION'],
                    $value['CELULAR'],
                    $value['TIPO_DOCUMENTO'],
                    $value['NRO_DOC'],
                    $value['AREA'],
                    $value['SUB_AREA'],
                    $value['FECHA_INGRESO'],
                    $value['FECHA_CESE'],
                    $value['NRO_DOC'],
                    hash("SHA256", $value['NRO_DOC']),
                    null,
                    $value['GRUPO_SANGUINEO'],
                    $value['ESTADO_EMPRESA'],
                    $JEFE_CARGO
                );

                //insertar en tabla personal_detalle
                if ($usuario_id != 0) {
                    //$value['TIPO_COMISION'],$value['FEHCA_SPP'],
                    $hmenores = empty($value['HIJOS_MENORES']) ? 0 : $value['HIJOS_MENORES'];
                    $hmayores = empty($value['HIJOS_MAYORES']) ? 0 : $value['HIJOS_MAYORES'];
                    $paramDetalle = [
                        $value['EPS'], $value['REMUNERACION_BASICA'], $value['ASIG_FAMILIAR'], $value['CENTRO_COSTO'],
                        $value['SEXO'], $value['NACIONALIDAD'], $value['FECHA_NACIMIENTO'], $value['ESTADO_CIVIL'],
                        $value['TELEFONO_EMERGENCIA'], $value['DEPARTAMENTO'],
                        $value['PROVINCIA'],  $value['DISTRITO'],   $value['NIVEL_EDUCATIVO'],  $value['SISTEMA_PENSION'],  $value['CUSPP'], 
                        $value['BANCO_SUELDO'],  $value['CUENTA_SUELDO'], $value['INTERBANCARIO_SUELDO'],  $value['BANCO_CTS'],
                        $value['CUENTA_CTS'], $value['CUENTA_INTERBANCARIA_CTS'], $value['TIPO_CONTRATO'],  $hmenores, $hmayores,
                        $value['ACTIVIDAD'], $value['TALLA_ZAPATOS'],  $value['TALLA_CAMISA'], $value['TALLA_PANTALON'],  $value['SCTR_SALUD'],
                        $value['SCTR_PENSION'],   $value['PLANILLA'], $value['EPS_PLAN']
                    ];
                    $obj->insertarPersonalDetalle($paramDetalle, $usuario_id, $value['NRO_DOC']);
                    $userRegistrado[] = $usuario_id;
                } else {
                    $userNoregistrado[] = $value['NRO_DOC'];
                }
            }

            commit();
            enableCommitAutomatic();
            $isCorrect = 1;
        } catch (Exception $e) {
            rollback();
            enableCommitAutomatic();
            // echo "Fallo: " . $e->getMessage();
            $isCorrect = 0;
            $mensaje = $e->getMessage();
        }

        return [
            'correcto' => $isCorrect,
            'mensaje' => $mensaje,
            'data' => $userNoregistrado,
            'totalReg' => count($data),
            'noInsertado' => count($userNoregistrado),
            'totalInsertado' => count($userRegistrado),
        ];
    }


    public function insertar($nombre, $tipo_id, $parent_id)
    {
        $sql = "INSERT INTO area (nombre, tipo_id, parent_id)
         VALUES ('$nombre', '$tipo_id','$parent_id')";
        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id, $nombre, $tipo_id, $parent_id)
    {
        $sql = "UPDATE area
         SET nombre='$nombre',tipo_id='$tipo_id',parent_id='$parent_id' WHERE id='$id' and estado=1";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT t.id, t.nombre, tipo_id, parent_id, t.estado,p.nombre as tipo FROM area t
                inner join parametros p on t.tipo_id=p.valor and p.grupo='area' 
                WHERE t.id='$id' and t.estado=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT t.id, t.nombre, tipo_id, parent_id, t.estado,p.nombre as tipo FROM area t
                inner join parametros p on t.tipo_id=p.valor and p.grupo='area' ";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($id)
    {
        $sql = "UPDATE area SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar categorías
    public function activar($id)
    {
        $sql = "UPDATE area SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementar un método para listar los registros y mostrar en el select
    public function getParams()
    {
        $sql = "SELECT * FROM parametros where estado=1";
        return ejecutarConsulta($sql);
    }
}
