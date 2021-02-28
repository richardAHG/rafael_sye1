<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Area
{
    //Implementamos nuestro constructor
    public function __construct()
    {
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
    public function selectTipo()
    {
        $sql = "SELECT * FROM parametros where estado=1 and grupo='area'";
        return ejecutarConsulta($sql);
    }

    public function selectArea()
    {
        $sql = "SELECT id,nombre FROM area where estado=1 and tipo_id=1";
        return ejecutarConsulta($sql);
    }

    public function selectSubArea($idArea)
    {
        $sql = "SELECT id,nombre FROM area where estado=1 and tipo_id=2 and parent_id=$idArea";
        return ejecutarConsulta($sql);
    }
    
    public function selectAllByTipo($tipo)
    {
        $sql = "SELECT id,nombre FROM area where tipo_id=$tipo";
        return ejecutarConsulta($sql);
    }
}
