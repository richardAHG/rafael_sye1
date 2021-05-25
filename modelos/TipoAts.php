<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class TipoAts
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function insertar($codigo, $version, $fecha, $actividad, $tipo_id,$especifico)
    {
        $sql = "INSERT INTO ats_version(tipo_ats, codigo, version, fecha, actividad,especifico) 
                VALUES ('$codigo', '$version', '$fecha', '$actividad', '$tipo_id','$especifico')";
        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id, $codigo, $version, $fecha, $actividad, $tipo_id,$especifico)
    {
        $sql = "UPDATE ats_version 
        SET tipo_ats='$tipo_id',codigo='$codigo',version='$version',fecha='$fecha',actividad='$actividad' ,especifico='$especifico'
        WHERE id='$id' and estado=1";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT a.id, tipo_ats, codigo, version, fecha, actividad,especifico,p.nombre as Ats FROM ats_version a 
                INNER JOIN parametros p on a.tipo_ats=p.valor and p.grupo='TIPO_ATS' and p.estado=1
                WHERE a.id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT a.id, tipo_ats, codigo, version, fecha, actividad,a.estado,especifico,p.nombre as ats FROM ats_version a 
                INNER JOIN parametros p on a.tipo_ats=p.valor and p.grupo='TIPO_ATS' and p.estado=1";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($id)
    {
        $sql = "UPDATE ats_version SET estado=0 WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar categorías
    public function activar($id)
    {
        $sql = "UPDATE ats_version SET estado=1 WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementar un método para listar los registros y mostrar en el select
    public function selectTipo()
    {
        $sql = "SELECT * FROM parametros where estado=1 and grupo='TIPO_ATS'";
        return ejecutarConsulta($sql);
    }

    public function selectP($tipo_id)
    {
        $sql = "SELECT * FROM ats_version where estado=1 and tipo_id='$tipo_id'";
        return ejecutarConsulta($sql);
    }
}
