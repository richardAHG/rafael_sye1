<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class JefeCargo
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function insertar($personal_id, $area_id, $subarea_id)
    {
        $sql = "INSERT INTO jefe_cargo (personal_id, area_id, subarea_id)
         VALUES ('$personal_id', '$area_id','$subarea_id')";

        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id, $personal_id, $area_id, $subarea_id)
    {
        $sql = "UPDATE jefe_cargo
         SET personal_id='$personal_id',area_id='$area_id',subarea_id='$subarea_id' WHERE id='$id' and estado=1";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT jc.id,jc.personal_id,jc.area_id,jc.subarea_id,jc.estado,a2.nombre as area,a.nombre as subarea FROM jefe_cargo jc
                inner join area a2 on jc.area_id = a2.id 
                inner join area a on jc.subarea_id = a.id
                WHERE jc.id='$id' and jc.estado=1";
                
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT jc.*,a2.nombre as area,a.nombre as subarea,
                CONCAT(p.nombre,' ',p.ape_pat,' ',p.ape_mat)as personal 
                FROM jefe_cargo jc
                inner join area a2 on jc.area_id = a2.id 
                inner join area a on jc.subarea_id = a.id
                inner join personal p on jc.personal_id=p.id  ";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($id)
    {
        $sql = "UPDATE jefe_cargo SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar categorías
    public function activar($id)
    {
        $sql = "UPDATE jefe_cargo SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementar un método para listar los registros y mostrar en el select
  
    public function selectJefeACargo()
    {
        $sql = "SELECT id,nombre FROM jefe_cargo where estado=1 ";
        return ejecutarConsulta($sql);
    }

    public function selectSubJefeACargo($idArea)
    {
        $sql = "SELECT id,nombre FROM jefe_cargo where estado=1";
        return ejecutarConsulta($sql);
    }
    
}
