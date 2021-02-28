<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Peligro
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function insertar($nombre, $tipo_id)
    {
        $sql = "INSERT INTO peligro (nombre, tipo_id)
         VALUES ('$nombre', '$tipo_id')";
        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id, $nombre, $tipo_id)
    {
        $sql = "UPDATE peligro
         SET nombre='$nombre',tipo_id='$tipo_id' WHERE id='$id' and estado=1";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT
                    t.id,
                    t.nombre,
                    tipo_id,
                    t.estado,
                    p.nombre AS tipo
                FROM
                    peligro t
                INNER JOIN parametros p ON
                    t.tipo_id = p.valor AND p.grupo = 'peligro' 
                WHERE t.id='$id' and t.estado=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT
                    t.id,
                    t.nombre,
                    tipo_id,
                    t.estado,
                    p.nombre AS tipo
                FROM
                    peligro t
                INNER JOIN parametros p ON
                    t.tipo_id = p.valor AND p.grupo = 'peligro' ";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($id)
    {
        $sql = "UPDATE peligro SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar categorías
    public function activar($id)
    {
        $sql = "UPDATE peligro SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementar un método para listar los registros y mostrar en el select
    public function selectTipo()
    {
        $sql = "SELECT * FROM parametros where estado=1 and grupo='peligro'";
        return ejecutarConsulta($sql);
    }

    public function selectP($tipo_id)
    {
        $sql = "SELECT * FROM peligro where estado=1 and tipo_id='$tipo_id'";
        return ejecutarConsulta($sql);
    }
}
