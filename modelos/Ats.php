<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Ats
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	//Implementamos un método para insertar registros
	public function insertar(
		$personal_id,
		$tipo_ats_id,
		$jefe_id,
		$horaFin,
		$distrito_id,
		$lugar,
		$equipoP,
		$equipoE,
		$equipoPC,
		$peligrosG,
		$peligrosE,
		$trabajadores,
		$firma,
		$ubicacion,
		$idusuario,
		$otros_peligros,
		$otros_riesgos,
		$otras_medidas,
		$fecha_login
	) {

		$isCorrect = 0;
		$mensaje = 'AtS Registrada con exito';
		/* Desactivar la autoconsigna */
		desableCommitAutomatic();
		try {
			$fecha = new DateTime('now');
			$fechaCreacion = $fecha->format('Y-m-d H:i:s');
			$fechaAts = $fecha->format('Y-m-d H:i:s');
			$fechalogin = new DateTime($fecha_login);
			$fecha_login = $fechalogin->format('Y-m-d H:i:s');
			$horaFin = substr($horaFin,11,8);
			
			$sql = "INSERT INTO ats(personal_id, distrito_id, direccion, ubicacion, 
					fecha_hora_inicio, hora_fin, firma_ruta, jefe_id,tipo_ats_id, 
					fecha_creacion, usuario_creacion,otros_peligros,otros_riesgos,otras_medidas) 
					VALUES ('$personal_id','$distrito_id','$lugar','$ubicacion',REPLACE('$fecha_login',': ',':'),
					REPLACE('$horaFin',': ',':'),'$firma','$jefe_id',$tipo_ats_id,REPLACE('$fecha_login',': ',':'),'$personal_id',
					'$otros_peligros','$otros_riesgos','$otras_medidas')";
			//return ejecutarConsulta($sql);
		
			$idats = ejecutarConsulta_retornarID($sql);
			if ($idats == 0) {
				throw new Exception('Error al guardar al personal');
			}

			// $peligrosG=json_decode($peligrosG,true);
			foreach ($peligrosG as $key => $value) {
				// print_r($value); die();
				$pId = $value['p_id'];
				$rId = $value['r_id'];
				$mId = $value['m_id'];
				$sql = "INSERT INTO ats_peligro_general(ats_id, peligro_id,riesgo_id,medida_id) 
					VALUES('$idats','$pId','$rId','$mId')";
				$result = ejecutarConsulta($sql);
				if (!$result) {
					throw new Exception('Error al guardar peligro general');
				}
			}

			// $peligrosE=json_decode($peligrosE,true);
			foreach ($peligrosE as $key => $value) {
				// print_r($value); die();
				$sId = $value['s_id'];
				$pId = $value['p_id'];
				$rId = $value['r_id'];
				$mId = $value['m_id'];
				$sql = "INSERT INTO ats_peligro_especifico(ats_id, subactividad_id,peligro_id,riesgo_id,medida_id)
			 VALUES('$idats','$sId','$pId','$rId','$mId')";
				$result = ejecutarConsulta($sql);
				if (!$result) {
					throw new Exception('Error al guardar peligro especifico');
				}
			}

			// $equipoE=json_decode($equipoE,true);
			foreach ($equipoE as $key => $value) {
				// $id = $value['id'];
				$sql = "INSERT INTO ats_equipo_emergencia(ats_id, equipo_emergencia_id) VALUES('$idats','$value')";
				$result = ejecutarConsulta($sql);
				if (!$result) {
					throw new Exception('Error al guardar equipo de emergencia');
				}
			}

			// $equipoP=json_decode($equipoP,true);
			foreach ($equipoP as $key => $value) {
				// $id = $value['id'];
				$sql = "INSERT INTO ats_equipo_proteccion(ats_id, equipo_proteccion_id) VALUES('$idats','$value')";
				$result = ejecutarConsulta($sql);
				if (!$result) {
					throw new Exception('Error al guardar equipo de proteccion');
				}
			}

			// $equipoPC=json_decode($equipoPC,true);
			foreach ($equipoPC as $key => $value) {
				// $id = $value['id'];
				$sql = "INSERT INTO ats_equipo_dispositivos_protecion(ats_id, equipo_dispositivo_proteccion_id) VALUES('$idats','$value')";
				$result = ejecutarConsulta($sql);
				if (!$result) {
					throw new Exception('Error al guardar dispositivo de protección');
				}
			}

			// $trabajadores=json_decode($trabajadores,true);
			foreach ($trabajadores as $key => $value) {
				// $id = $value['id'];
				$sql = "INSERT INTO ats_trabajadores(ats_id, personal_id) VALUES('$idats','$value')";
				$result = ejecutarConsulta($sql);
				if (!$result) {
					throw new Exception('Error al guardar los trabajadores');
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
			'mensaje' => $mensaje
		];
	}


	//Implementamos un método para anular categorías
	// public function anular($idingreso)
	// {
	// 	$sql="UPDATE ingreso SET estado='Anulado' WHERE idingreso='$idingreso'";
	// 	return ejecutarConsulta($sql);
	// }


	//Implementar un método para mostrar los datos de un registro a modificar
	// public function mostrar($idingreso)
	// {
	// 	$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE i.idingreso='$idingreso'";
	// 	return ejecutarConsultaSimpleFila($sql);
	// }

	// public function listarDetalle($idingreso)
	// {
	// 	$sql="SELECT di.idingreso,di.idarticulo,a.nombre,di.cantidad,di.precio_compra,di.precio_venta FROM detalle_ingreso di inner join articulo a on di.idarticulo=a.idarticulo where di.idingreso='$idingreso'";
	// 	return ejecutarConsulta($sql);
	// }

	//Implementar un método para listar los registros
	// public function listar()
	// {
	// 	$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario ORDER BY i.idingreso desc";
	// 	return ejecutarConsulta($sql);		
	// }

	// public function ingresocabecera($idingreso){
	// 	$sql="SELECT i.idingreso,i.idproveedor,p.nombre as proveedor,p.direccion,p.tipo_documento,p.num_documento,p.email,p.telefono,i.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,date(i.fecha_hora) as fecha,i.impuesto,i.total_compra FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE i.idingreso='$idingreso'";
	// 	return ejecutarConsulta($sql);
	// }

	// public function ingresodetalle($idingreso){
	// 	$sql="SELECT a.nombre as articulo,a.codigo,d.cantidad,d.precio_compra,d.precio_venta,(d.cantidad*d.precio_compra) as subtotal FROM detalle_ingreso d INNER JOIN articulo a ON d.idarticulo=a.idarticulo WHERE d.idingreso='$idingreso'";
	// 	return ejecutarConsulta($sql);
	// }

	public function listar()
	{
		// $sql = "SELECT personal_id,concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre),p.numero_documento,firma_ruta,hora_fin, fecha_creacion
		// 		FROM `ats` a
		// 		inner join personal p on p.id=a.personal_id
		// 		where ((DATE_FORMAT(a.fecha_creacion,'%Y%m%d') like '%01%') 
		// 		and (p.ape_pat like '%%' ) 
		// 		and (p.ape_mat like '%%' ) 
		// 		and (p.nombre like '%%' ))";
		$sql = "SELECT personal_id,concat(p.ape_pat,' ',p.ape_mat,' ',p.nombre),p.numero_documento,firma_ruta,hora_fin, fecha_creacion, 
				concat(p.ape_pat,p.ape_mat,p.nombre,DATE_FORMAT(a.fecha_creacion,'%Y%m%d'),p.numero_documento) 
				FROM `ats` a 
				inner join personal p on p.id=a.personal_id 
				where concat(p.ape_pat,p.ape_mat,p.nombre,
				DATE_FORMAT(a.fecha_creacion,'%Y%m%d'),p.numero_documento) like '%2021%'";
		return ejecutarConsulta($sql);
	}

	public function reporte($ats_id)
	{
		$sql = "SELECT a.id, a.personal_id, distrito_id, a.direccion, ubicacion, 
				fecha_hora_inicio, hora_fin, firma_ruta, jefe_id, tipo_ats_id, 
				date(fecha_creacion) as fecha_creacion, a.estado,
				concat(p.nombre,' ',p.ape_pat,' ',p.ape_mat) as personal,p.numero_documento,c.nombre as cargo,
				d.nombre as distrito,
				concat(pj.nombre,' ',pj.ape_pat,' ',pj.ape_mat) as jefe,pa.nombre as tipo_ats ,
				av.codigo,av.version,av.actividad,a.otros_peligros,a.otros_riesgos,a.otras_medidas
				FROM ats a 
				inner join personal p on p.id=a.personal_id and p.estado=1
				inner join cargo c on p.cargo_id=c.id and c.estado=1
				inner join distritos d on d.id=a.distrito_id and d.estado=1
				inner join jefe_ats j on j.id=a.jefe_id and j.estado=1
				inner join personal pj on pj.id=j.personal_id and pj.estado=1
				inner join parametros pa on pa.valor=a.tipo_ats_id and pa.estado=1 and grupo='tipo_ats'
				inner join ats_version av on a.tipo_ats_id=av.tipo_ats 
				where a.estado=1 and a.id=$ats_id";
		$data = ejecutarConsultaSimpleFila($sql);
		// if (empty($data)) {
		// 	echo '<h2>Id de ATS no procesado</h2>
		// 	<b>Nota: Verifique que el personal, cargo, distrito, jefe_ats este activo</b>';
		// }
		$sqlEquipo = "SELECT e.nombre
						FROM ats_equipo_proteccion aeq
						inner join equipo e on aeq.equipo_proteccion_id=e.id and e.estado=1
						WHERE ats_id = $ats_id";
		$dataEquipo = '';
		$rspta = ejecutarConsulta($sqlEquipo);
		while ($reg = $rspta->fetch_object()) {
			$dataEquipo .= $reg->nombre . ', ';
		}
		$dataEquipo = substr($dataEquipo, 0, -2);

		$sqlEquipoE = "SELECT e.nombre FROM `ats_equipo_emergencia` aee
						INNER JOIN equipo e ON aee.`equipo_emergencia_id` = e.id and e.estado=1
						WHERE ats_id = $ats_id";
		$dataEquipoE = '';
		$rspta = ejecutarConsulta($sqlEquipoE);
		while ($reg = $rspta->fetch_object()) {
			$dataEquipoE .= $reg->nombre . ', ';
		}
		$dataEquipoE = substr($dataEquipoE, 0, -2);

		$sqlEquipoPC = "SELECT e.nombre FROM `ats_equipo_dispositivos_protecion`aedp
						INNER JOIN equipo e ON aedp.`equipo_dispositivo_proteccion_id` = e.id and e.estado=1
						WHERE ats_id = $ats_id";
		$dataEquipoPC = '';
		$rspta = ejecutarConsulta($sqlEquipoPC);
		while ($reg = $rspta->fetch_object()) {
			$dataEquipoPC .= $reg->nombre . ', ';
		}
		$dataEquipoPC = substr($dataEquipoPC, 0, -2);

		$sqlPeligroG = "SELECT a.id,p.nombre as peligro,r.nombre as riesgo,m.nombre as medida 
						FROM `ats_peligro_general` a
						left join peligro p on a.peligro_id = p.id and p.estado=1
						left join riesgo r on a.riesgo_id = r.id and r.estado=1
						left join medida_control m on a.peligro_id = m.id and m.estado=1
						WHERE ats_id = $ats_id";

		$rsptaPG = ejecutarConsulta($sqlPeligroG);

		$sqlPeligroE = "SELECT a.id,s.nombre as subactividad, p.nombre as peligro,r.nombre as riesgo,m.nombre as medida 
						FROM `ats_peligro_especifico` a
						left join subactividad s on a.subactividad_id = s.id and s.estado=1
						left join peligro p on a.peligro_id = p.id and p.estado=1
						left join riesgo r on a.riesgo_id = r.id and r.estado=1
						left join medida_control m on a.peligro_id = m.id and m.estado=1
						WHERE ats_id= $ats_id";

		$rsptaPE = ejecutarConsulta($sqlPeligroE);

		$sqlTrabajadores = "SELECT
							a.`id`,
							a.`ats_id`,
							p.numero_documento,c.nombre as cargo,concat(p.nombre,' ',p.ape_pat,' ',p.ape_mat)as nombre,
							ar.nombre as area
						FROM
							`ats_trabajadores` a 
							inner join personal p on a.personal_id = p.id and p.estado=1
							INNER JOIN cargo c on p.cargo_id=c.id and c.estado=1
							INNER JOIN area ar on p.area_id=ar.id and ar.estado=1
						WHERE
							a.ats_id= $ats_id";

		$rsptatrb = ejecutarConsulta($sqlTrabajadores);

		$reporte = [
			'ats' => $data,
			'ep' => $dataEquipo,
			'ee' => $dataEquipoE,
			'pc' => $dataEquipoPC,
			'peligroG' => $rsptaPG,
			'peligroE' => $rsptaPE,
			'trabajador' => $rsptatrb
		];

		return $reporte;
	}

	public function getTipoAts()
	{
		$sql = "SELECT p.*,a.especifico from parametros p inner join ats_version a on p.valor=a.tipo_ats and grupo='TIPO_ATS' and p.estado=1";
		return ejecutarConsulta($sql);
	}
	public function selectParticipantes()
	{
		$sql = "SELECT p.id, concat(numero_documento,' ',nombre,' ',ape_pat,' ',ape_mat,' ')as nombre FROM `ats_trabajadores` t 
				inner join personal p on t.personal_id=p.id
				group by p.id";
		return ejecutarConsulta($sql);
	}
}
