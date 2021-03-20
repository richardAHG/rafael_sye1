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
				echo json_encode($rspta);
				break;
			case 'guardaryeditar':
				print_r($_FILES['archivo']);
				die();
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
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
