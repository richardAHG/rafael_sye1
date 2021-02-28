<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class GestionPeligrosG
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function insertar($tipo_ats_id, $peligroG_id, $riesgoG_id, $medidaG_id, $respuesta)
    {
        $sql = "INSERT INTO gestion_peligros_generales (peligro_id, riesgo_id, medida_id, respuesta, tipo_ats_id)
         VALUES ('$peligroG_id', '$riesgoG_id', '$medidaG_id', '$respuesta','$tipo_ats_id')";
        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id,$tipo_ats_id, $peligroG_id, $riesgoG_id, $medidaG_id, $respuesta)
    {
        $sql = "UPDATE gestion_peligros_generales SET peligro_id='$peligroG_id',riesgo_id='$riesgoG_id',
        medida_id='$medidaG_id',respuesta='$respuesta',tipo_ats_id='$tipo_ats_id' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT  g.id, peligro_id, riesgo_id, medida_id, respuesta, tipo_ats_id, g.estado,
                 p.nombre as peligro, r.nombre as riesgo, mc.nombre as medida, pa.nombre as tipo_ats
                 FROM gestion_peligros_generales g
                 inner join peligro p on g.peligro_id=p.id
                 inner join riesgo r on g.riesgo_id=r.id 
                 inner join medida_control mc on g.medida_id=mc.id 
                 inner join parametros pa on g.tipo_ats_id=pa.valor and pa.grupo='tipo_ats' 
                 WHERE g.id='$id' and g.estado=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT  g.id, peligro_id, riesgo_id, medida_id, respuesta, tipo_ats_id, g.estado,
                p.nombre as peligro, r.nombre as riesgo, mc.nombre as medida, pa.nombre as tipo_ats
                FROM gestion_peligros_generales g
                inner join peligro p on g.peligro_id=p.id
                inner join riesgo r on g.riesgo_id=r.id 
                inner join medida_control mc on g.medida_id=mc.id 
                inner join parametros pa on g.tipo_ats_id=pa.valor and pa.grupo='tipo_ats'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($id)
    {
        $sql = "UPDATE gestion_peligros_generales SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar categorías
    public function activar($id)
    {
        $sql = "UPDATE gestion_peligros_generales SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    
//select Peligro general por tipo de ats
    public function selectPGByAtsId($tipo_ats_id)
    {
        $sql = "SELECT DISTINCT peligro_id,p.nombre FROM gestion_peligros_generales g
                inner join peligro p on g.peligro_id=p.id and p.tipo_id=1
                WHERE tipo_ats_id='$tipo_ats_id' and g.estado=1";
        return ejecutarConsulta($sql);
    }

    //select Riesgo general por tipo de ats
    public function selectRGByAtsId($tipo_ats_id,$peligroG_id)
    {
        $sql = "SELECT DISTINCT riesgo_id,r.nombre FROM gestion_peligros_generales g
                inner join riesgo r on g.riesgo_id=r.id and r.tipo_id=1
                WHERE tipo_ats_id='$tipo_ats_id' and peligro_id='$peligroG_id' and g.estado=1";
        return ejecutarConsulta($sql);
    }

    //select medida de control general por tipo de ats
    public function selectMCByAtsId($tipo_ats_id,$riesgoG_id,$peligroG_id)
    {
        $sql = "SELECT DISTINCT medida_id,m.nombre FROM gestion_peligros_generales g
                inner join medida_control m on g.medida_id=m.id and m.tipo_id=1
                WHERE tipo_ats_id='$tipo_ats_id' and riesgo_id='$riesgoG_id' and peligro_id='$peligroG_id' and g.estado=1";
        return ejecutarConsulta($sql);
    }

    //select respuesta general por tipo de ats
    public function selectRByAtsId($tipo_ats_id,$medidaG_id,$riesgoG_id,$peligroG_id)
    {
        $sql = "SELECT distinct respuesta,case respuesta when 1 then 'Correcta'else 'incorrecta' end as nombre 
                FROM gestion_peligros_generales
                WHERE tipo_ats_id='$tipo_ats_id' and medida_id='$medidaG_id' and riesgo_id='$riesgoG_id' and peligro_id='$peligroG_id'";
        return ejecutarConsulta($sql);
    }




}
