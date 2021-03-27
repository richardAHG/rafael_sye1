<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Usuario
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	//Implementamos un método para insertar registros
	public function insertar($nombre, $ape_pat, $ape_mat, $email, $cargo_id, $regimen_id, $direccion, $cell, $tipo_documento, $numero_documento, $area_id, $subarea_id, $fecha_ingreso, $fecha_cese, $login, $clave, $imagen, $permisos)
	{
		if (empty($permisos)) {
			$permisos = [];
		}
		$isCorrect = 0;
		$mensaje = 'Usuario Registrado';
		desableCommitAutomatic();
		try {
			$sql = "INSERT INTO personal ( nombre, ape_pat, ape_mat, email, cargo_id, regimen_id, direccion, cell, tipo_documento, numero_documento, area_id, subarea_id, fecha_ingreso, login, clave, imagen)
		VALUES ('$nombre', '$ape_pat', '$ape_mat', '$email', '$cargo_id', '$regimen_id', '$direccion', '$cell', '$tipo_documento', '$numero_documento', '$area_id', '$subarea_id', '$fecha_ingreso', '$login', '$clave', '$imagen')";
			//return ejecutarConsulta($sql);

			$idusuarionew = ejecutarConsulta_retornarID($sql);
			if ($idusuarionew == 0) {
				throw new Exception('Error al registrar al personal');
			}
			$num_elementos = 0;
			$sw = true;

			while ($num_elementos < count($permisos)) {
				$sql_detalle = "INSERT INTO usuario_permiso(idpersonal, idpermiso) VALUES('$idusuarionew', '$permisos[$num_elementos]')";
				$rspta = ejecutarConsulta($sql_detalle);
				$num_elementos = $num_elementos + 1;
				if ($rspta == 0) {
					throw new Exception('Error al registrar los permisos');
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

	//Implementamos un método para editar registros

	public function editar($id, $nombre, $ape_pat, $ape_mat, $email, $cargo_id, $regimen_id, $direccion, $cell, $tipo_documento, $numero_documento, $area_id, $subarea_id, $fecha_ingreso, $fecha_cese, $login, $clave, $imagen, $permisos)
	{
		if (empty($permisos)) {
			$permisos = [];
		}
		// print_r($clave); die();
		$isCorrect = 0;
		$mensaje = 'Usuario Actualizado';
		desableCommitAutomatic();
		try {
			$sql = "UPDATE
					personal
				SET
					nombre = '$nombre',
					ape_pat = '$ape_pat',
					ape_mat = '$ape_mat',
					email = '$email',
					cargo_id = '$cargo_id',
					regimen_id = '$regimen_id',
					direccion = '$direccion',
					cell = '$cell',
					tipo_documento = '$tipo_documento',
					numero_documento = '$numero_documento',
					area_id = '$area_id',
					subarea_id = '$subarea_id',
					fecha_ingreso = '$fecha_ingreso',
					login = '$login',
					imagen = '$imagen'
				WHERE
					id = '$id'";

			$result = ejecutarConsulta($sql);
			if ($result == 0) {
				throw new Exception('Error al editar al personal');
			}
			//Eliminamos todos los permisos asignados para volverlos a registrar
			$sqldel = "DELETE FROM usuario_permiso WHERE idpersonal='$id'";
			$result =  ejecutarConsulta($sqldel);
			if ($result == 0) {
				throw new Exception('Error al eliminar los permisos');
			}
			$num_elementos = 0;
			$sw = true;

			while ($num_elementos < count($permisos)) {
				$sql_detalle = "INSERT INTO usuario_permiso(idpersonal, idpermiso) VALUES('$id', '$permisos[$num_elementos]')";
				$result =  ejecutarConsulta($sql_detalle);
				$num_elementos = $num_elementos + 1;
				if ($result == 0) {
					$sw = false;
					throw new Exception('Error al registrar nuevos permisos');
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

	//Implementamos un método para desactivar categorías
	public function desactivar($idusuario)
	{
		$sql = "UPDATE personal SET estado=0 WHERE id='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idusuario)
	{
		$sql = "UPDATE personal SET estado=1 WHERE id='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function bloquerATS($idusuario)
	{
		$sql = "UPDATE personal SET ats=0 WHERE id='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function habilitarATS($idusuario)
	{
		$sql = "UPDATE personal SET ats=1 WHERE id='$idusuario'";
		return ejecutarConsulta($sql);
	}
	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idusuario)
	{
		$sql = "SELECT p.id, p.nombre, ape_pat, ape_mat, email, cargo_id, regimen_id, direccion, cell, tipo_documento, numero_documento, area_id, subarea_id, fecha_ingreso, fecha_cese, login, clave, imagen, p.estado, 
					c.nombre as cargo, pa.nombre as regimen,pa2.nombre as tipoDocumento,a.nombre as area, sa.nombre as subarea
				FROM `personal` p 
				inner join cargo c on p.cargo_id=c.id
				inner join parametros pa on p.regimen_id=pa.valor and pa.grupo='regimen_laboral'
				inner join parametros pa2 on p.tipo_documento=pa2.valor and pa2.grupo='tipo_documento'
				inner join area a on p.area_id=a.id and a.tipo_id=1
				inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2 
				WHERE p.id='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql = "SELECT p.id, p.nombre, ape_pat, ape_mat, email, cargo_id, regimen_id, direccion, cell, tipo_documento, numero_documento, area_id, subarea_id, fecha_ingreso, fecha_cese, login, clave, imagen, p.estado,p.ats, 
					c.nombre as cargo, pa.nombre as regimen,pa2.nombre as tipoDocumento,a.nombre as area, sa.nombre as subarea,pa3.nombre as grupo_sanguineo 
				FROM `personal` p 
				inner join cargo c on p.cargo_id=c.id
				inner join parametros pa on p.regimen_id=pa.valor and pa.grupo='regimen_laboral'
				inner join parametros pa2 on p.tipo_documento=pa2.valor and pa2.grupo='tipo_documento'
				left join parametros pa3 on p.grupo_sanguineo =pa3.valor and pa3.grupo='grupo_sanguineo'
				inner join area a on p.area_id=a.id and a.tipo_id=1
				inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2";
		return ejecutarConsulta($sql);
	}
	//Implementar un método para listar los permisos marcados
	public function listarmarcados($idusuario)
	{
		$sql = "SELECT * FROM usuario_permiso WHERE idpersonal='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Función para verificar el acceso al sistema
	public function verificar($login, $clave)
	{
		$sql = "SELECT id, nombre, ape_pat, ape_mat, email, cargo_id, regimen_id, direccion, cell, 
		tipo_documento, numero_documento, area_id, subarea_id, 
		fecha_ingreso, fecha_cese, login, clave, imagen 
		FROM personal WHERE login='$login' AND clave='$clave' AND estado=1";

		return ejecutarConsulta($sql);
	}

	//Función para verificar el acceso al sistema desde la app
	public function verificarApp($clave)
	{
		$sql = "SELECT p.id, p.nombre, ape_pat, ape_mat, email, cargo_id,c.nombre AS cargo,
		regimen_id, direccion, cell, 
		tipo_documento, numero_documento, area_id, subarea_id, 
		fecha_ingreso, fecha_cese, login, clave, imagen 
		FROM personal p
		INNER JOIN cargo c ON
    	p.cargo_id = c.id AND c.estado = 1
		WHERE login='$clave' AND p.estado=1 and ats=1";

		return ejecutarConsulta($sql);
	}

	public function editClave($claveactual, $nuevaclave, $id_usuario)
	{
		$sql = "SELECT * from personal  WHERE clave='$claveactual' and id='$id_usuario'";
		$rpta = ejecutarConsultaSimpleFila($sql);
		if (isset($rpta['id'])) {
			$sql = "UPDATE personal SET CLAVE='$nuevaclave' WHERE  clave='$claveactual' and id='$id_usuario'";
			return ejecutarConsulta($sql);
		}
		return false;
	}

	//carga masiva

	public function insertarPersonalMasivo($nombre, $ape_pat, $ape_mat, $email, $cargo_id, $regimen_id, $direccion, $cell, $tipo_documento, $numero_documento, $area_id, $subarea_id, $fecha_ingreso, $fecha_cese, $login, $clave, $imagen, $grupoSanguineo = null)
	{
		$sql = "SELECT id from personal  WHERE numero_documento='$numero_documento' and estado=1";
		$rpta = ejecutarConsultaSimpleFila($sql);
		// if (!$rpta) {
		// 	throw new Exception('Error al verificar la existencia del Numero de Doc: '.$numero_documento);
		// }
		if (!isset($rpta['id'])) {
			$sql = "INSERT INTO personal ( nombre, ape_pat, ape_mat, email, cargo_id, regimen_id, direccion, cell, tipo_documento, numero_documento, area_id, subarea_id, fecha_ingreso, login, clave, imagen,grupo_sanguineo)
			VALUES ('$nombre', '$ape_pat', '$ape_mat', '$email', '$cargo_id', '$regimen_id', '$direccion', '$cell', '$tipo_documento', '$numero_documento', '$area_id', '$subarea_id', '$fecha_ingreso', '$login', '$clave', '$imagen','$grupoSanguineo')";
			$idusuarionew = ejecutarConsulta_retornarID($sql);
			if (!$idusuarionew) {
				throw new Exception('Error al guardar al personal. Numero Doc: '.$numero_documento);
			}
			return $idusuarionew;
			// $idusuarionew=1;
			// return $idusuarionew;
		}
		return 0;
	}

	public function insertarPersonalDetalle($data, $personal_id)
	{
		// print_r($data); die();
		$sql = "INSERT INTO personal_detalle(ESTADO_EMPRESA, REMUNERACION_BASICA, ASIG_FAMILIAR, 
		CENTRO_COSTO, SEXO, NACIONALIDAD, FECHA_NACIMIENTO, ESTADO_CIVIL, TELEFONO_EMERGENCIA, 
		DEPARTAMENTO, PROVINCIA, DISTRITO, NIVEL_EDUCATIVO, SISTEMA_PENSIÓN, CUSPP, TIPO_COMISION, 
		FEHCA_SPP, BANCO_SUELDO, CUENTA_SUELDO, INTERBANCARIO_SUELDO, BANCO_CTS, CUENTA_CTS, 
		CUENTA_INTERBANCARIA_CTS, TIPO_CONTRATO, HIJOS_MENORES, HIJOS_MAYORES, ACTIVIDAD, 
		TALLA_ZAPATOS, TALLA_CAMISA, TALLA_PANTALÓN, SCTR_SALUD, SCTR_PENSIÓN, 
		PLANILLA, EPS_PLAN, personal_id) values('$data[0]','$data[1]','$data[2]','$data[3]',
		'$data[4]','$data[5]','$data[6]','$data[7]',
		'$data[8]','$data[9]','$data[10]','$data[11]',
		'$data[12]','$data[13]','$data[14]','$data[15]',
		'$data[16]','$data[17]','$data[18]','$data[19]',
		'$data[20]','$data[21]','$data[22]','$data[23]',
		'$data[24]','$data[25]','$data[26]','$data[27]',
		'$data[28]','$data[29]','$data[30]','$data[31]',
		'$data[32]','$data[33]',$personal_id)";

		$rpta= ejecutarConsulta($sql);
		if (!$rpta) {
			throw new Exception('Error al guardar los datos complementarios del personal con Numero de Doc: '.$numero_documento);
		}
		return true;
	}
}
