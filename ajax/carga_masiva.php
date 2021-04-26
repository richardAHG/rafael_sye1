<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['Administrar'] == 1) {
		require_once "../modelos/CargaMasiva.php";
		require_once "../helper/response/Response.php";
		require_once "../utils/CsvUtil.php";
		$cargaMasiva = new CargaMasiva();
		$usuario = new Usuario();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$archivo = isset($_POST["archivo"]) ? limpiarCadena($_POST["archivo"]) : "";

		switch ($_GET["op"]) {
			case 'validar':
				// print_r($_FILES['archivo']['type']); die();
				if (!file_exists($_FILES['archivo']['tmp_name']) || !is_uploaded_file($_FILES['archivo']['tmp_name'])) {
					return false;
				} else {
					$ext = explode(".", $_FILES["archivo"]["name"]);
					if (
						$_FILES['archivo']['type'] == "application/vnd.ms-excel" ||
						$_FILES['archivo']['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
					) {
						$name_archivo = round(microtime(true)) . '.' . end($ext);
						move_uploaded_file($_FILES["archivo"]["tmp_name"], "../files/temp/" . $name_archivo);
					}
				}
				// print_r($name_archivo); die();
				$xls = CsvUtil::LoadFileCreateJson($name_archivo);
				//validar archivo
				$path = CsvUtil::RUTA_FINAL . $xls . '.json';
				$file = file_get_contents($path);
				$data = json_decode($file, true);
				// print_r($data);die();
				//validar numero de columnas
				$rpta = CsvUtil::validateTotalHeaders($data['headers']);
				if (!$rpta['estado']) {
					CsvUtil::destroyFileTemp();
					$error = $rpta;
					echo json_encode($error);
					return;
				}

				//obtenemos los parametros
				$rspta = $cargaMasiva->getParams();
				$params = [];
				while ($reg = $rspta->fetch_object()) {
					$params[$reg->grupo][$reg->valor] = $reg->nombre;
				}

				// validamos que los datos enviados existan en la base de datos
				$rpta = CsvUtil::validateIdparams($data['data'], $params);
				if (!$rpta['estado']) {
					CsvUtil::destroyFileTemp();
					$error = $rpta;
					echo json_encode($error);
					return;
				}

				$rpta = CsvUtil::validateTypeDate($name_archivo);
				if (!$rpta['estado']) {
					CsvUtil::destroyFileTemp();
					$error = $rpta;
					echo json_encode($error);
					return;
				}
				CsvUtil::setFormatterDateInFileJson($name_archivo);

				$path = CsvUtil::RUTA_FINAL . $xls . '.json';
				$file = file_get_contents($path);
				$data = json_decode($file, true);

				//validar campos obligatorios, y asignamos id correspondientes
				$rpta = CsvUtil::validateRequiredFields($data['data'], $xls, $params);
				if (!$rpta['estado']) {
					CsvUtil::destroyFileTemp();
					$error = $rpta;
					echo json_encode($error);
					return;
				}

				$result = [
					'archivo' => $xls,
					'mensaje' => 'Validación finalizada con exito',
					'estado' => true
				];

				echo json_encode($result);

				break;
			case 'guardar':
				$archivo = $_POST['nombreArchivo'];
				//validar archivo
				$path = CsvUtil::RUTA_FINAL . $archivo . '.json';
				$file = file_get_contents($path);
				$data = json_decode($file, true);

				$rspta = $cargaMasiva->insertarCM($data);
				CsvUtil::destroyFileTemp();
				if ($rspta['correcto']) {
					Response::JSON(200, $rspta['mensaje'], $rspta);
				} else {
					Response::JSON(400, $rspta['mensaje'], $rspta);
				}
				// echo json_encode($rspta);
				break;
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $cargaMasiva->insertar($nombre, $tipo_id, $parent_id);
					// echo $rspta ? "Area registrada" : "Area no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Area registrada');
					} else {
						Response::JSON(400, 'Area no se pudo registrar');
					}
				} else {
					$rspta = $cargaMasiva->editar($id, $nombre, $tipo_id, $parent_id);
					// echo $rspta ? "Area actualizada" : "Area no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Area actualizada');
					} else {
						Response::JSON(400, 'Area no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $cargaMasiva->desactivar($id);
				//  echo $rspta ? "Area Desactivada" : "Area no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Area Desactivada');
				} else {
					Response::JSON(400, 'Area no se pudo desactivar');
				}
				break;



			case 'listar':
				$rspta = $cargaMasiva->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'nombre' => $reg->nombre,
						'tipo_id' => $reg->tipo_id,
						'estado' => $reg->estado,
						'tipo' => $reg->tipo,
					];
				}
				echo json_encode($data);

				break;
			case 'descarga':
				$data = [];
				$fechas = [];
				$data[0] = [
					// 'VINCULO',
					'ESTADO_EMPRESA',
					'REGIMEN_LABORAL',
					'TIPO_DOCUMENTO',
					'NRO_DOC',
					'APEPAT',
					'APEMAT',
					'NOMBRES',
					'REMUNERACION_BASICA',
					'ASIG_FAMILIAR',
					'CARGO',
					'SUB_AREA',
					'AREA',
					'CENTRO_COSTO',
					'FECHA_INGRESO',
					'FECHA_CESE',
					// 'MOTIVO_CESE',
					'SEXO',
					'NACIONALIDAD',
					'FECHA_NACIMIENTO',
					'ESTADO_CIVIL',
					'CELULAR',
					'TELEFONO_EMERGENCIA',
					'EMAIL',
					'DIRECCION',
					'DEPARTAMENTO',
					'PROVINCIA',
					'DISTRITO',
					'NIVEL_EDUCATIVO',
					'SISTEMA_PENSION',
					'CUSPP',
					// 'TIPO_COMISION',
					// 'FEHCA_SPP',
					'BANCO_SUELDO',
					'CUENTA_SUELDO',
					'INTERBANCARIO_SUELDO',
					'BANCO_CTS',
					'CUENTA_CTS',
					'CUENTA_INTERBANCARIA_CTS',
					'TIPO_CONTRATO',
					'HIJOS_MENORES',
					'HIJOS_MAYORES',
					'ACTIVIDAD',
					'GRUPO_SANGUINEO',
					'TALLA_ZAPATOS',
					'TALLA_CAMISA',
					'TALLA_PANTALON',
					'SCTR_SALUD',
					'SCTR_PENSION',
					'PLANILLA',
					'EPS_PLAN',
					'EPS',
					'JEFE_CARGO'

				];

				$rspta = $usuario->listarMasivo();
				//Vamos a declarar un array
				$cont = 1;
				while ($reg = $rspta->fetch_object()) {
					print_r($reg); die();
					$data[$cont] = [
					// 'VINCULO'=>$reg->,
					'ESTADO_EMPRESA'=>$reg->estado_empresa,
					'REGIMEN_LABORAL'=>$reg->regimen,
					'TIPO_DOCUMENTO'=>$reg->tipoDocumento,
					'NRO_DOC'=>$reg->numero_documento,
					'APEPAT'=>$reg->ape_pat,
					'APEMAT'=>$reg->ape_mat,
					'NOMBRES'=>$reg->nombre,
					'REMUNERACION_BASICA'=>$reg->REMUNERACION_BASICA,
					'ASIG_FAMILIAR'=>$reg->ASIG_FAMILIAR,
					'CARGO'=>$reg->cargo,
					'SUB_AREA'=>$reg->subarea,
					'AREA'=>$reg->area,
					'CENTRO_COSTO'=>$reg->CENTRO_COSTO,
					'FECHA_INGRESO'=>$reg->fecha_ingreso,
					'FECHA_CESE'=>$reg->fecha_cese,
					// 'MOTIVO_CESE'=>$reg->,
					'SEXO'=>$reg->SEXO,
					'NACIONALIDAD'=>$reg->NACIONALIDAD,
					'FECHA_NACIMIENTO'=>$reg->FECHA_NACIMIENTO,
					'ESTADO_CIVIL'=>$reg->ESTADO_CIVIL,
					'CELULAR'=>$reg->cell,
					'TELEFONO_EMERGENCIA'=>$reg->TELEFONO_EMERGENCIA,
					'EMAIL'=>$reg->email,
					'DIRECCION'=>$reg->direccion,
					'DEPARTAMENTO'=>$reg->DEPARTAMENTO,
					'PROVINCIA'=>$reg->PROVINCIA,
					'DISTRITO'=>$reg->DISTRITO,
					'NIVEL_EDUCATIVO'=>$reg->NIVEL_EDUCATIVO,
					'SISTEMA_PENSION'=>$reg->SISTEMA_PENSION,
					'CUSPP'=>$reg->CUSPP,
					'TIPO_COMISION'=>$reg->TIPO_COMISION,
					'FEHCA_SPP'=>$reg->FEHCA_SPP,
					'BANCO_SUELDO'=>$reg->BANCO_SUELDO,
					'CUENTA_SUELDO'=>$reg->CUENTA_SUELDO,
					'INTERBANCARIO_SUELDO'=>$reg->INTERBANCARIO_SUELDO,
					'BANCO_CTS'=>$reg->BANCO_CTS,
					'CUENTA_CTS'=>$reg->CUENTA_CTS,
					'CUENTA_INTERBANCARIA_CTS'=>$reg->CUENTA_INTERBANCARIA_CTS,
					'TIPO_CONTRATO'=>$reg->TIPO_CONTRATO,
					'HIJOS_MENORES'=>$reg->HIJOS_MENORES,
					'HIJOS_MAYORES'=>$reg->HIJOS_MAYORES,
					'ACTIVIDAD'=>$reg->ACTIVIDAD,
					'GRUPO_SANGUINEO'=>$reg->grupo_sanguineo,
					'TALLA_ZAPATOS'=>$reg->TALLA_ZAPATOS,
					'TALLA_CAMISA'=>$reg->TALLA_CAMISA,
					'TALLA_PANTALON'=>$reg->TALLA_PANTALON,
					'SCTR_SALUD'=>$reg->SCTR_SALUD,
					'SCTR_PENSION'=>$reg->SCTR_PENSION,
					'PLANILLA'=>$reg->PLANILLA,
					'EPS_PLAN'=>$reg->EPS_PLAN,
					'EPS'=>$reg->EPS,
					'JEFE_CARGO'=>$reg->jefe_cargo
					];
					$cont++;
				}

				try {

					$worksheet = CsvUtil::createSheet();
					$hoja = $worksheet->getActiveSheet();
					CsvUtil::writeSheet($hoja, $data);

					$sheet = CsvUtil::saveSheet($worksheet, 'descarga_masiva');
				} catch (Exception $ex) {
					throw $ex->getMessage();
				}


				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
