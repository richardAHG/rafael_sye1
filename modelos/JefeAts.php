<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class JefeAts
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function insertar($personal, $tipo_ats_id)
    {
        $sql = "INSERT INTO jefe_ats (personal_id, tipoats_id)
         VALUES ('$personal', '$tipo_ats_id')";
        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id, $personal, $tipo_ats_id)
    {
        $sql = "UPDATE jefe_ats SET personal_id='$personal',tipoats_id='$tipo_ats_id'
        WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT t.id, t.personal_id, t.tipoats_id, t.estado,
                CONCAT(p.nombre,' ',p.ape_pat,' ',p.ape_mat) as personal,pa.nombre as tipoats 
                FROM jefe_ats t
                inner join personal p on t.personal_id=p.id
                inner join parametros pa on t.tipoats_id=pa.valor and pa.grupo='tipo_ats'
                WHERE t.id='$id' and t.estado=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT t.id, t.personal_id, t.tipoats_id, t.estado,
                CONCAT(p.nombre,' ',p.ape_pat,' ',p.ape_mat) as personal,pa.nombre as tipoats 
                FROM jefe_ats t
                inner join personal p on t.personal_id=p.id
                inner join parametros pa on t.tipoats_id=pa.valor and pa.grupo='tipo_ats' ";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($id)
    {
        $sql = "UPDATE jefe_ats SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar categorías
    public function activar($id)
    {
        $sql = "UPDATE jefe_ats SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

   public function selectJefeByAts($tipo_ats_id)
    {
        $sql = "SELECT j.id, personal_id, tipoats_id, j.estado,concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre) as empleado
                from jefe_ats j 
                INNER JOIN personal p on j.personal_id=p.id and p.estado=1
                WHERE tipoats_id='$tipo_ats_id' and p.estado='1'";
        
        return ejecutarConsulta($sql);
    }
}
