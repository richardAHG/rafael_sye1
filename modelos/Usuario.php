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
	public function insertar($nombre, $ape_pat, $ape_mat, $email, $cargo_id, $regimen_id, $direccion, $cell, $tipo_documento, $numero_documento, $area_id, $subarea_id, $fecha_ingreso, $fecha_cese, $login, $clave, $imagen, $permisos, $estado_empresa, $jefe_cargo)
	{
		if (empty($permisos)) {
			$permisos = [];
		}
		$isCorrect = 0;
		$mensaje = 'Usuario Registrado';
		desableCommitAutomatic();
		try {
			$null = "NULL";
			$fecha_ingreso = empty($fecha_ingreso) ? $null : "'$fecha_ingreso'";
			$jefe_cargo = empty($jefe_cargo) ? 0 : $jefe_cargo;
			$sql = "INSERT INTO personal ( nombre, ape_pat, ape_mat, email, cargo_id, regimen_id, direccion, cell, tipo_documento, numero_documento, area_id, subarea_id, fecha_ingreso, login, clave, imagen,estado_empresa,jefe_cargo)
			VALUES ('$nombre', '$ape_pat', '$ape_mat', '$email', '$cargo_id', '$regimen_id', '$direccion', '$cell', '$tipo_documento', '$numero_documento', '$area_id', '$subarea_id', $fecha_ingreso, '$login', '$clave', '$imagen','$estado_empresa',$jefe_cargo)";
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

	public function editar($id, $nombre, $ape_pat, $ape_mat, $email, $cargo_id, $regimen_id, $direccion, $cell, $tipo_documento, $numero_documento, $area_id, $subarea_id, $fecha_ingreso, $fecha_cese, $login, $clave, $imagen, $permisos, $estado_empresa, $jefe_cargo)
	{
		if (empty($permisos)) {
			$permisos = [];
		}
		// print_r($clave); die();
		$isCorrect = 0;
		$mensaje = 'Usuario Actualizado';
		desableCommitAutomatic();
		try {
			$null = "NULL";
			$fecha_ingreso = empty($fecha_ingreso) ? $null : "'$fecha_ingreso'";
			$jefe_cargo = empty($jefe_cargo) ? 0 : $jefe_cargo;
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
					fecha_ingreso = $fecha_ingreso,
					login = '$login',
					imagen = '$imagen',
					estado_empresa = '$estado_empresa',
					jefe_cargo = $jefe_cargo
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
					c.nombre as cargo, pa.nombre as regimen,pa2.nombre as tipoDocumento,a.nombre as area, sa.nombre as subarea,p2.nombre as estado_empresa,p.jefe_cargo
				FROM `personal` p 
				inner join cargo c on p.cargo_id=c.id
				inner join parametros pa on p.regimen_id=pa.valor and pa.grupo='regimen_laboral'
				inner join parametros pa2 on p.tipo_documento=pa2.valor and pa2.grupo='tipo_documento'
				inner join area a on p.area_id=a.id and a.tipo_id=1
				inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2 
				left join parametros p2 on p.estado_empresa =p2.valor and p2.grupo ='ESTADO_EMPRESA'
				WHERE p.id='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql = "SELECT p.id, p.nombre, p.ape_pat, p.ape_mat, p.email, p.cargo_id, p.regimen_id, p.direccion, p.cell, p.tipo_documento, p.numero_documento, p.area_id, p.subarea_id, p.fecha_ingreso, p.fecha_cese, p.login, p.clave, p.imagen, p.estado,p.ats, 
				c.nombre as cargo, pa.nombre as regimen,pa2.nombre as tipoDocumento,a.nombre as area, sa.nombre as subarea,pa3.nombre as grupo_sanguineo, p.estado_empresa as estado_empresa_id, p2.nombre as estado_empresa,
				p.jefe_cargo, CONCAT(p3.nombre,' ',p3.ape_pat,' ',p3.ape_mat)as nombre_jefe
				FROM `personal` p 
				inner join cargo c on p.cargo_id=c.id
				inner join parametros pa on p.regimen_id=pa.valor and pa.grupo='regimen_laboral'
				inner join parametros pa2 on p.tipo_documento=pa2.valor and pa2.grupo='tipo_documento'
				left join parametros pa3 on p.grupo_sanguineo =pa3.valor and pa3.grupo='grupo_sanguineo'
				left join personal p3 on p3.id =p.jefe_cargo 
				inner join area a on p.area_id=a.id and a.tipo_id=1
				inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2
				left join parametros p2 on p.estado_empresa =p2.valor and p2.grupo ='ESTADO_EMPRESA'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listarMasivo()
	{
		$sql = "SELECT p.id as idpersonal, p.nombre, p.ape_pat, p.ape_mat, p.email, p.cargo_id, p.regimen_id, p.direccion, p.cell, p.tipo_documento, p.numero_documento, p.area_id, p.subarea_id, p.fecha_ingreso, p.fecha_cese, p.login, p.clave, p.imagen, p.estado,p.ats, 
		c.nombre as cargo, pa.nombre as regimen,pa2.nombre as tipoDocumento,a.nombre as area, sa.nombre as subarea,pa3.nombre as grupo_sanguineo, p.estado_empresa as estado_empresa_id, p2.nombre as estado_empresa,
		p.jefe_cargo, CONCAT(p3.nombre,' ',p3.ape_pat,' ',p3.ape_mat)as nombre_jefe,
		pa4.nombre as pasig_familiar,pa5.nombre as pcentro_costo,pa6.nombre as psexo,pa7.nombre as pnacionalidad,
		pa8.nombre as pestado_civil,pa9.nombre as pnivel_educativo,pa10.nombre as psistema_pension,pa11.nombre as pbanco_sueldo,
		pa12.nombre as pbanco_cts,pa13.nombre as ptipo_contrato,pa14.nombre as psctr_salud,pa15.nombre as psctr_pension,
		pa16.nombre as pplanilla,pa17.nombre as peps,pd.*
		FROM `personal` p 
		inner join cargo c on p.cargo_id=c.id
		inner join parametros pa on p.regimen_id=pa.valor and pa.grupo='regimen_laboral'
		inner join parametros pa2 on p.tipo_documento=pa2.valor and pa2.grupo='tipo_documento'
		left join personal p3 on p3.id =p.jefe_cargo 
		left join parametros pa3 on p.grupo_sanguineo =pa3.valor and pa3.grupo='grupo_sanguineo'
		inner join area a on p.area_id=a.id and a.tipo_id=1
		inner join area sa on p.subarea_id=sa.id and sa.tipo_id=2
		left join parametros p2 on p.estado_empresa =p2.valor and p2.grupo ='ESTADO_EMPRESA' 
		left join personal_detalle pd on pd.personal_id =p.id
		left join parametros pa4 on pd.ASIG_FAMILIAR =pa4.valor and pa4.grupo='ASIG_FAMILIAR'
		left join parametros pa5 on pd.CENTRO_COSTO =pa5.valor and pa5.grupo='CENTRO_COSTO'
		left join parametros pa6 on pd.SEXO =pa6.valor and pa6.grupo='SEXO'
		left join parametros pa7 on pd.NACIONALIDAD =pa7.valor and pa7.grupo='NACIONALIDAD'
		left join parametros pa8 on pd.ESTADO_CIVIL =pa8.valor and pa8.grupo='ESTADO_CIVIL'
		left join parametros pa9 on pd.NIVEL_EDUCATIVO =pa9.valor and pa9.grupo='NIVEL_EDUCATIVO'
		left join parametros pa10 on pd.SISTEMA_PENSION =pa10.valor and pa10.grupo='SISTEMA_PENSION'
		left join parametros pa11 on pd.BANCO_SUELDO =pa11.valor and pa11.grupo='BANCO_SUELDO'
		left join parametros pa12 on pd.BANCO_CTS =pa12.valor and pa12.grupo='BANCO_CTS'
		left join parametros pa13 on pd.TIPO_CONTRATO =pa13.valor and pa13.grupo='TIPO_CONTRATO'
		left join parametros pa14 on pd.SCTR_SALUD =pa14.valor and pa14.grupo='SCTR_SALUD'
		left join parametros pa15 on pd.SCTR_PENSION =pa15.valor and pa15.grupo='SCTR_PENSION'
		left join parametros pa16 on pd.PLANILLA =pa16.valor and pa16.grupo='PLANILLA'
		left join parametros pa17 on pd.EPS =pa17.valor and pa17.grupo='EPS'";
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

	public function insertarPersonalMasivo($nombre, $ape_pat, $ape_mat, $email, $cargo_id, $regimen_id, $direccion, $cell, $tipo_documento, $numero_documento, $area_id, $subarea_id, $fecha_ingreso, $fecha_cese, $login, $clave, $imagen, $grupoSanguineo = null, $estado_empresa, $jefe_cargo)
	{
		$sql = "SELECT id from personal  WHERE numero_documento='$numero_documento' and estado=1";
		$rpta = ejecutarConsultaSimpleFila($sql);
		// if (!$rpta) {
		// 	throw new Exception('Error al verificar la existencia del Numero de Doc: '.$numero_documento);
		// }
		if (!isset($rpta['id'])) {
			$sql = "INSERT INTO personal ( nombre, ape_pat, ape_mat, email, cargo_id, regimen_id, direccion, cell, tipo_documento, numero_documento, area_id, subarea_id, fecha_ingreso, login, clave, imagen,grupo_sanguineo,estado_empresa,nombre_jefecargo)
			VALUES ('$nombre', '$ape_pat', '$ape_mat', '$email', '$cargo_id', '$regimen_id', '$direccion', '$cell', '$tipo_documento', '$numero_documento', '$area_id', '$subarea_id', '$fecha_ingreso', '$login', '$clave', '$imagen','$grupoSanguineo','$estado_empresa','$jefe_cargo')";

			$idusuarionew = ejecutarConsulta_retornarID($sql);
			if (!$idusuarionew) {
				throw new Exception('Error al guardar al personal. Numero Doc: ' . $numero_documento);
			}
			return $idusuarionew;
			// $idusuarionew=1;
			// return $idusuarionew;
		}
		return 0;
	}

	public function insertarPersonalDetalle($data, $personal_id, $numero_documento)
	{
		// print_r($data); die();
		//TIPO_COMISION, FEHCA_SPP,
		//'$data[15]','$data[16]'
		$null = "NULL";
		$fecha_ingreso = ($data[6] == 'NULL') ? $null : "'$data[6]'";
		$sql = "INSERT INTO personal_detalle(EPS, REMUNERACION_BASICA, ASIG_FAMILIAR, 
		CENTRO_COSTO, SEXO, NACIONALIDAD, FECHA_NACIMIENTO, ESTADO_CIVIL, TELEFONO_EMERGENCIA, 
		DEPARTAMENTO, PROVINCIA, DISTRITO, NIVEL_EDUCATIVO, SISTEMA_PENSION, CUSPP, 
		BANCO_SUELDO, CUENTA_SUELDO, INTERBANCARIO_SUELDO, BANCO_CTS, 
		CUENTA_CTS, CUENTA_INTERBANCARIA_CTS, TIPO_CONTRATO, HIJOS_MENORES, HIJOS_MAYORES, ACTIVIDAD, 
		TALLA_ZAPATOS, TALLA_CAMISA, TALLA_PANTALON, SCTR_SALUD, SCTR_PENSION, PLANILLA, 
		EPS_PLAN, personal_id) values('$data[0]','$data[1]','$data[2]',
		'$data[3]','$data[4]','$data[5]',$fecha_ingreso,'$data[7]','$data[8]',
		'$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]',
		'$data[15]','$data[16]','$data[17]','$data[18]',
		'$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]',
		'$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]',
		'$data[31]',$personal_id)";

		$rpta = ejecutarConsulta($sql);
		if (!$rpta) {
			throw new Exception('Error al guardar los datos complementarios del personal con Numero de Doc: ' . $numero_documento);
		}
		return true;
	}

	public function mostrarDetails($idusuario)
	{
		$sql = "SELECT id, EPS, REMUNERACION_BASICA, ASIG_FAMILIAR, CENTRO_COSTO, SEXO, NACIONALIDAD, FECHA_NACIMIENTO, ESTADO_CIVIL, 
				TELEFONO_EMERGENCIA, DEPARTAMENTO, PROVINCIA, DISTRITO, NIVEL_EDUCATIVO, SISTEMA_PENSION, CUSPP, TIPO_COMISION, FEHCA_SPP, BANCO_SUELDO, 
				CUENTA_SUELDO, INTERBANCARIO_SUELDO, BANCO_CTS, CUENTA_CTS, CUENTA_INTERBANCARIA_CTS, TIPO_CONTRATO, HIJOS_MENORES, HIJOS_MAYORES, ACTIVIDAD, 
				TALLA_ZAPATOS, TALLA_CAMISA, TALLA_PANTALON, SCTR_SALUD, SCTR_PENSION, PLANILLA, EPS_PLAN,personal_id
				FROM personal_detalle p
				WHERE p.personal_id='$idusuario'";

		return ejecutarConsultaSimpleFila($sql);
	}

	public function editarPersonalDetail(
		$EPS,
		$REMUNERACION_BASICA,
		$ASIG_FAMILIAR,
		$CENTRO_COSTO,
		$SEXO,
		$NACIONALIDAD,
		$FECHA_NACIMIENTO,
		$ESTADO_CIVIL,
		$TELEFONO_EMERGENCIA,
		$DEPARTAMENTO,
		$PROVINCIA,
		$DISTRITO,
		$NIVEL_EDUCATIVO,
		$SISTEMA_PENSION,
		$CUSPP,
		$TIPO_COMISION,
		$FEHCA_SPP,
		$BANCO_SUELDO,
		$CUENTA_SUELDO,
		$INTERBANCARIO_SUELDO,
		$BANCO_CTS,
		$CUENTA_CTS,
		$CUENTA_INTERBANCARIA_CTS,
		$TIPO_CONTRATO,
		$HIJOS_MENORES,
		$HIJOS_MAYORES,
		$ACTIVIDAD,
		$TALLA_ZAPATOS,
		$TALLA_CAMISA,
		$TALLA_PANTALON,
		$SCTR_SALUD,
		$SCTR_PENSION,
		$PLANILLA,
		$EPS_PLAN,
		$personal_id,
		$id_personal_detalle
	) {

		$null = "NULL";
		$FEHCA_SPP = ($FEHCA_SPP == '') ? $null : "'$FEHCA_SPP'";

		$sql = "UPDATE personal_detalle
				SET EPS=$EPS, REMUNERACION_BASICA='$REMUNERACION_BASICA', ASIG_FAMILIAR=$ASIG_FAMILIAR, CENTRO_COSTO=$CENTRO_COSTO, SEXO=$SEXO, NACIONALIDAD=$NACIONALIDAD, FECHA_NACIMIENTO='$FECHA_NACIMIENTO', 
				ESTADO_CIVIL=$ESTADO_CIVIL, TELEFONO_EMERGENCIA='$TELEFONO_EMERGENCIA', DEPARTAMENTO='$DEPARTAMENTO', PROVINCIA='$PROVINCIA', DISTRITO='$DISTRITO', NIVEL_EDUCATIVO=$NIVEL_EDUCATIVO, SISTEMA_PENSION=$SISTEMA_PENSION, 
				CUSPP='$CUSPP', TIPO_COMISION=$TIPO_COMISION, FEHCA_SPP=$FEHCA_SPP, BANCO_SUELDO=$BANCO_SUELDO, CUENTA_SUELDO='$CUENTA_SUELDO', INTERBANCARIO_SUELDO='$INTERBANCARIO_SUELDO', BANCO_CTS=$BANCO_CTS, CUENTA_CTS='$CUENTA_CTS', 
				CUENTA_INTERBANCARIA_CTS='$CUENTA_INTERBANCARIA_CTS', TIPO_CONTRATO=$TIPO_CONTRATO, HIJOS_MENORES=$HIJOS_MENORES, HIJOS_MAYORES=$HIJOS_MAYORES, ACTIVIDAD='$ACTIVIDAD', TALLA_ZAPATOS='$TALLA_ZAPATOS', TALLA_CAMISA='$TALLA_CAMISA', 
				TALLA_PANTALON='$TALLA_PANTALON', SCTR_SALUD=$SCTR_SALUD, SCTR_PENSION=$SCTR_PENSION, PLANILLA=$PLANILLA, EPS_PLAN='$EPS_PLAN'
				WHERE personal_id=$personal_id and id=$id_personal_detalle";
		
		$result = ejecutarConsulta($sql);

		if ($result == 0) {
			$mensaje = 'Datos no se pudierón actualizar';
		} else {
			$mensaje = 'Datos actualizados con exito';
		}
		return [
			'correcto' => $result,
			'mensaje' => $mensaje
		];
	}

	public function insertarPersonalDetail(
		$EPS,
		$REMUNERACION_BASICA,
		$ASIG_FAMILIAR,
		$CENTRO_COSTO,
		$SEXO,
		$NACIONALIDAD,
		$FECHA_NACIMIENTO,
		$ESTADO_CIVIL,
		$TELEFONO_EMERGENCIA,
		$DEPARTAMENTO,
		$PROVINCIA,
		$DISTRITO,
		$NIVEL_EDUCATIVO,
		$SISTEMA_PENSION,
		$CUSPP,
		$TIPO_COMISION,
		$FEHCA_SPP,
		$BANCO_SUELDO,
		$CUENTA_SUELDO,
		$INTERBANCARIO_SUELDO,
		$BANCO_CTS,
		$CUENTA_CTS,
		$CUENTA_INTERBANCARIA_CTS,
		$TIPO_CONTRATO,
		$HIJOS_MENORES,
		$HIJOS_MAYORES,
		$ACTIVIDAD,
		$TALLA_ZAPATOS,
		$TALLA_CAMISA,
		$TALLA_PANTALON,
		$SCTR_SALUD,
		$SCTR_PENSION,
		$PLANILLA,
		$EPS_PLAN,
		$personal_id
	) {

		$sql = "INSERT INTO personal_detalle
		(EPS, REMUNERACION_BASICA, ASIG_FAMILIAR, CENTRO_COSTO, SEXO, NACIONALIDAD, FECHA_NACIMIENTO, ESTADO_CIVIL, TELEFONO_EMERGENCIA, 
		DEPARTAMENTO, PROVINCIA, DISTRITO, NIVEL_EDUCATIVO, SISTEMA_PENSION, CUSPP, TIPO_COMISION, FEHCA_SPP, BANCO_SUELDO, CUENTA_SUELDO, 
		INTERBANCARIO_SUELDO, BANCO_CTS, CUENTA_CTS, CUENTA_INTERBANCARIA_CTS, TIPO_CONTRATO, HIJOS_MENORES, HIJOS_MAYORES, ACTIVIDAD, 
		TALLA_ZAPATOS, TALLA_CAMISA, TALLA_PANTALON, SCTR_SALUD, SCTR_PENSION, PLANILLA, EPS_PLAN, personal_id)
		VALUES($EPS,
		'$REMUNERACION_BASICA',
		$ASIG_FAMILIAR,
		$CENTRO_COSTO,
		$SEXO,
		$NACIONALIDAD,
		'$FECHA_NACIMIENTO',
		$ESTADO_CIVIL,
		'$TELEFONO_EMERGENCIA',
		'$DEPARTAMENTO',
		'$PROVINCIA',
		'$DISTRITO',
		$NIVEL_EDUCATIVO,
		$SISTEMA_PENSION,
		'$CUSPP',
		$TIPO_COMISION,
		'$FEHCA_SPP',
		$BANCO_SUELDO,
		'$CUENTA_SUELDO',
		'$INTERBANCARIO_SUELDO',
		$BANCO_CTS,
		'$CUENTA_CTS',
		'$CUENTA_INTERBANCARIA_CTS',
		$TIPO_CONTRATO,
		$HIJOS_MENORES,
		$HIJOS_MAYORES,
		'$ACTIVIDAD',
		'$TALLA_ZAPATOS',
		'$TALLA_CAMISA',
		'$TALLA_PANTALON',
		$SCTR_SALUD,
		$SCTR_PENSION,
		$PLANILLA,
		'$EPS_PLAN',
		$personal_id);";
		$result = ejecutarConsulta($sql);
		if ($result == 0) {
			$mensaje = 'Datos no se pudierón registrar';
		} else {
			$mensaje = 'Datos Registrados con exito';
		}
		return [
			'correcto' => $result,
			'mensaje' => $mensaje
		];
	}
}
