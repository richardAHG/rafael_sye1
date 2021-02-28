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
		require_once "../modelos/TipoAts.php";
		require_once "../helper/response/Response.php";
		$tipoAts = new TipoAts();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
		$version = isset($_POST["version"]) ? limpiarCadena($_POST["version"]) : "";
		$fecha = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";
		$actividad = isset($_POST["actividad"]) ? limpiarCadena($_POST["actividad"]) : "";
		$tipo_id = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : "";
$especifico = isset($_POST["especifico"]) ? limpiarCadena($_POST["especifico"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $tipoAts->insertar($codigo, $version, $fecha, $actividad, $tipo_id,$especifico);
					// echo $rspta ? "Area registrada" : "Area no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Versión de ATS registrada');
					} else {
						Response::JSON(400, 'Versión de ATS no se pudo registrar');
					}
				} else {
					$rspta = $tipoAts->editar($id, $codigo, $version, $fecha, $actividad, $tipo_id,$especifico);
					// echo $rspta ? "Area actualizada" : "Area no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Versión de ATS actualizada');
					} else {
						Response::JSON(400, 'Versión de ATS no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $tipoAts->desactivar($id);
				//  echo $rspta ? "Area Desactivada" : "Area no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Versión de ATS Desactivada');
				} else {
					Response::JSON(400, 'Versión de ATS no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $tipoAts->activar($id);
				// echo $rspta ? "Area activada" : "Area no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Versión de ATS activada');
				} else {
					Response::JSON(400, 'Versión de ATS no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $tipoAts->mostrar($id);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de Versión de ATS', $rspta);
				break;

			case 'listar':
				$rspta = $tipoAts->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
                        'ats' => $reg->ats,
						'codigo' => $reg->codigo,
						'version' => $reg->version,
						'fecha' => $reg->fecha,
						'actividad' => $reg->actividad,
						'especifico' => $reg->especifico,
						'estado' => $reg->estado
					];
				}
				echo json_encode($data);

				break;
			case "selectTipo":
				$rspta = $tipoAts->selectTipo();
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->valor,
						'nombre' => $reg->nombre
					];
				}
				Response::JSON(200, 'Datos de tipo de Versión de ATS', $data);
				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
