<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class GestionPeligrosE
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function insertar($subactividad,$tipo_ats_id, $peligroG_id, $riesgoG_id, $medidaG_id, $respuesta)
    {
        $sql = "INSERT INTO gestion_peligros_especificos (subactividad_id,peligro_id, riesgo_id, medida_id, respuesta, tipo_ats_id)
         VALUES ('$subactividad','$peligroG_id', '$riesgoG_id', '$medidaG_id', '$respuesta','$tipo_ats_id')";
        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id,$subactividad,$tipo_ats_id, $peligroG_id, $riesgoG_id, $medidaG_id, $respuesta)
    {
        $sql = "UPDATE gestion_peligros_especificos SET subactividad_id='$subactividad',peligro_id='$peligroG_id',riesgo_id='$riesgoG_id',
        medida_id='$medidaG_id',respuesta='$respuesta',tipo_ats_id='$tipo_ats_id' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT  g.id, subactividad_id, peligro_id, riesgo_id, medida_id, respuesta, tipo_ats_id, g.estado,
                 s.nombre as subactividad,p.nombre as peligro, r.nombre as riesgo, mc.nombre as medida, pa.nombre as tipo_ats
                 FROM gestion_peligros_especificos g
                 inner join subactividad s on g.subactividad_id=s.id
                 inner join peligro p on g.peligro_id=p.id
                 inner join riesgo r on g.riesgo_id=r.id 
                 inner join medida_control mc on g.medida_id=mc.id 
                 inner join parametros pa on g.tipo_ats_id=pa.valor and pa.grupo='tipo_ats' 
                 WHERE g.id='$id' and g.estado=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT  g.id, subactividad_id, peligro_id, riesgo_id, medida_id, respuesta, tipo_ats_id, g.estado,
                 s.nombre as subactividad,p.nombre as peligro, r.nombre as riesgo, mc.nombre as medida, pa.nombre as tipo_ats
                 FROM gestion_peligros_especificos g
                 inner join subactividad s on g.subactividad_id=s.id
                 inner join peligro p on g.peligro_id=p.id
                 inner join riesgo r on g.riesgo_id=r.id 
                 inner join medida_control mc on g.medida_id=mc.id 
                 inner join parametros pa on g.tipo_ats_id=pa.valor and pa.grupo='tipo_ats'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($id)
    {
        $sql = "UPDATE gestion_peligros_especificos SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar categorías
    public function activar($id)
    {
        $sql = "UPDATE gestion_peligros_especificos SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

     //selectsubactividad por tipo de ats
    public function selectSAByAtsId($tipo_ats_id)
    {
        $sql = "SELECT DISTINCT subactividad_id,s.nombre FROM gestion_peligros_especificos g 
                INNER JOIN subactividad s on g.subactividad_id=s.id and s.estado=1
                WHERE tipo_ats_id='$tipo_ats_id' and g.estado=1";
        return ejecutarConsulta($sql);
    }

    //select Peligro especifico por tipo de ats
    public function selectPGByAtsId($tipo_ats_id,$subactividad_id)
    {
        $sql = "SELECT DISTINCT peligro_id,p.nombre FROM gestion_peligros_especificos g
                inner join peligro p on g.peligro_id=p.id and p.tipo_id=2 
                WHERE tipo_ats_id='$tipo_ats_id'  and subactividad_id='$subactividad_id' and g.estado=1";
        return ejecutarConsulta($sql);
    }

    //select Riesgo especifico por tipo de ats
    public function selectRGByAtsId($tipo_ats_id,$peligroG_id,$subactividad_id)
    {
        $sql = "SELECT DISTINCT riesgo_id,r.nombre FROM gestion_peligros_especificos g
                inner join riesgo r on g.riesgo_id=r.id and r.tipo_id=2 
                WHERE tipo_ats_id='$tipo_ats_id' and peligro_id='$peligroG_id'and subactividad_id='$subactividad_id' and g.estado=1";
        return ejecutarConsulta($sql);
    }

    //select medida de control especifico por tipo de ats
    public function selectMCByAtsId($tipo_ats_id,$riesgoG_id,$peligroG_id,$subactividad_id)
    {
        $sql = "SELECT DISTINCT medida_id,m.nombre FROM gestion_peligros_especificos g
                inner join medida_control m on g.medida_id=m.id and m.tipo_id=2
                WHERE tipo_ats_id='$tipo_ats_id' and riesgo_id='$riesgoG_id'and peligro_id='$peligroG_id'and subactividad_id='$subactividad_id' and g.estado=1";
        return ejecutarConsulta($sql);
    }

    //select respuesta especifico por tipo de ats
    public function selectRByAtsId($tipo_ats_id,$medidaG_id,$riesgoG_id,$peligroG_id,$subactividad_id)
    {
        $sql = "SELECT distinct respuesta,case respuesta when 1 then 'Correcta'else 'incorrecta' end as nombre 
                FROM gestion_peligros_especificos 
                WHERE tipo_ats_id='$tipo_ats_id' and medida_id='$medidaG_id'and riesgo_id='$riesgoG_id'and peligro_id='$peligroG_id'and subactividad_id='$subactividad_id' ";
        return ejecutarConsulta($sql);
    }



}
