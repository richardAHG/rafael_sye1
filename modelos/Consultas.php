<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Consultas
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	public function atsfecha($fecha_inicio,$fecha_fin)
	{
		$sql="SELECT
					a.id,
					personal_id,
					CONCAT(
						p.ape_pat,
						' ',
						p.ape_mat,
						' ',
						p.nombre
					) AS empleado,
					p.numero_documento,
					hora_fin,
					DATE(fecha_creacion) AS fecha_creacion,
					time(fecha_creacion) AS hora_creacion
				FROM
					`ats` a
				INNER JOIN personal p ON
					p.id = a.personal_id
				WHERE DATE(a.fecha_creacion)>='$fecha_inicio' AND DATE(a.fecha_creacion)<='$fecha_fin'";
				// print_r($sql); die();
		return ejecutarConsulta($sql);		
	}

    public function consultasATS($fecha_inicio, $fecha_fin, $usuarioId)
	{
		$sql = "SELECT a.id,fecha_creacion,tipo_ats_id,p.nombre as tipo_ats FROM `ats` a
				inner join parametros p on a.tipo_ats_id=p.valor and p.grupo='TIPO_ATS'
				WHERE personal_id=$usuarioId and CAST(fecha_creacion AS DATE) 
				BETWEEN '$fecha_inicio' and '$fecha_fin'  and a.estado=1
				order by fecha_creacion desc";
		return ejecutarConsulta($sql);
	}

	public function ubicarmeInAts($fecha_inicio, $fecha_fin, $usuarioId)
	{
		$sql = "SELECT a.id,fecha_creacion,tipo_ats_id,p.nombre as tipo_ats FROM `ats` a
				inner join parametros p on a.tipo_ats_id=p.valor and p.grupo='TIPO_ATS'
				inner join ats_trabajadores t on a.id = t.ats_id
				WHERE t.personal_id=$usuarioId and CAST(fecha_creacion AS DATE) 
				BETWEEN '$fecha_inicio' and '$fecha_fin'  and a.estado=1
				order by fecha_creacion desc";
		return ejecutarConsulta($sql);
	}
	
	public function atsParticipantes($fecha_inicio, $fecha_fin,$participanteId)
	{
		$sql = "SELECT
					distinct  a.id,
					a.personal_id,
					CONCAT(
						p.ape_pat,
						' ',
						p.ape_mat,
						' ',
						p.nombre
					) AS empleado,
					p.numero_documento,
					firma_ruta,
					hora_fin,
					DATE(fecha_creacion) AS fecha_creacion
				FROM
					`ats` a
				INNER JOIN personal p ON
					p.id = a.personal_id
				INNER JOIN ats_trabajadores t ON  a.id=t.ats_id
				WHERE CAST(fecha_creacion AS DATE) 
				BETWEEN '$fecha_inicio' and '$fecha_fin' 
				and t.personal_id=$participanteId ";
		return ejecutarConsulta($sql);
	}

public function rptCumplimiento($fecha_inicio, $fecha_fin)
	{
		// $sql="SELECT numero_documento,area,subarea,cargo,nombre,GROUP_CONCAT(fecha) as fechas,jefe from 
		// 		(SELECT  distinct numero_documento,ar.nombre as area,sa.nombre as subarea,c.nombre as cargo, concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre)as nombre,ats.fecha,jefe FROM `personal` p 
		// 		inner join area ar on p.area_id=ar.id and ar.tipo_id=1 and ar.estado=1 
		// 		inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2 and sa.estado=1 
		// 		inner join cargo c on p.cargo_id=c.id and c.estado=1 
		// 		left join(
		// 			SELECT a.id,personal_id,jefe_id,
 		// 		concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre)as jefe,
		// 		CAST(fecha_creacion AS DATE)as fecha FROM `ats` a
		// 		inner join personal p on a.jefe_id =p.id
		// 		WHERE CAST(fecha_creacion AS DATE) BETWEEN '$fecha_inicio' and '$fecha_fin'
		// 		) ats  on ats.personal_id=p.id 
		// 		where p.ats=1
		// 		union
		// 		SELECT  distinct numero_documento,ar.nombre as area,sa.nombre as subarea,c.nombre as cargo, concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre)as nombre,atr.fecha,jefe FROM `personal` p 
		// 		inner join area ar on p.area_id=ar.id and ar.tipo_id=1 and ar.estado=1 
		// 		inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2 and sa.estado=1 
		// 		inner join cargo c on p.cargo_id=c.id and c.estado=1 
		// 		left join(
		// 			SELECT distinct atr.personal_id,CAST(fecha_creacion AS DATE)as fecha,concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre)as jefe FROM ats_trabajadores atr
		// 		left join ats a on atr.ats_id=a.id
		// 		inner join personal p on a.jefe_id =p.id
		// 		WHERE CAST(fecha_creacion AS DATE) BETWEEN '$fecha_inicio' and '$fecha_fin'
		// 		) atr  on atr.personal_id=p.id
		// 		where p.ats=1
		// 		)as t
		// 		group by numero_documento,area,subarea,cargo,nombre,jefe ";
		$sql="SELECT numero_documento,area,subarea,cargo,nombre,GROUP_CONCAT(fecha) as fechas,jefeCargo,estadoEmpresa from 
				(SELECT  distinct p.numero_documento,ar.nombre as area,sa.nombre as subarea,c.nombre as cargo, 
				concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre)as nombre,ats.fecha,
				concat(p2.ape_pat,' ',p2.ape_mat,' ',p2.nombre)as jefeCargo,p3.nombre as estadoEmpresa
				FROM `personal` p 
				inner join area ar on p.area_id=ar.id and ar.tipo_id=1 and ar.estado=1 
				inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2 and sa.estado=1 
				inner join cargo c on p.cargo_id=c.id and c.estado=1 
				left join personal p2 on p.jefe_cargo =p2.id and p2.estado =1
				left join parametros p3 on p.estado_empresa =p3.valor and p3.grupo ='ESTADO_EMPRESA'
				left join(
					SELECT a.id,personal_id,jefe_id,
				CAST(fecha_creacion AS DATE)as fecha FROM `ats` a
				WHERE CAST(fecha_creacion AS DATE) BETWEEN '$fecha_inicio' and '$fecha_fin'
				) ats  on ats.personal_id=p.id 
				where p.ats=1
				union
				SELECT  distinct p.numero_documento,ar.nombre as area,sa.nombre as subarea,c.nombre as cargo, 
				concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre)as nombre,atr.fecha,
				concat(p2.ape_pat,' ',p2.ape_mat,' ',p2.nombre)as jefeCargo,p3.nombre as estadoEmpresa
				 FROM `personal` p 
				inner join area ar on p.area_id=ar.id and ar.tipo_id=1 and ar.estado=1 
				inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2 and sa.estado=1 
				inner join cargo c on p.cargo_id=c.id and c.estado=1 
				left join personal p2 on p.jefe_cargo =p2.id and p2.estado =1
				left join parametros p3 on p.estado_empresa =p3.valor and p3.grupo ='ESTADO_EMPRESA'
				left join(
					SELECT distinct atr.personal_id,CAST(fecha_creacion AS DATE)as fecha
					FROM ats_trabajadores atr
				left join ats a on atr.ats_id=a.id
				WHERE CAST(fecha_creacion AS DATE) BETWEEN '$fecha_inicio' and '$fecha_fin'
				) atr  on atr.personal_id=p.id
				where p.ats=1
				)as t
				group by numero_documento,area,subarea,cargo,nombre,jefeCargo,estadoEmpresa ";
			// echo $sql; die();
		return ejecutarConsulta($sql);
	}
}

?>