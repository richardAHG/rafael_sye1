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
		require_once "../modelos/JefeAts.php";
		require_once "../helper/response/Response.php";
		$jefeAts = new JefeAts();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$personal = isset($_POST["personal"]) ? limpiarCadena($_POST["personal"]) : "";
		$tipo_ats_id = isset($_POST["tipo_ats_id"]) ? limpiarCadena($_POST["tipo_ats_id"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $jefeAts->insertar($personal, $tipo_ats_id);
					// echo $rspta ? "Area registrada" : "Area no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Jefe de ATS registrada');
					} else {
						Response::JSON(400, 'Jefe de ATS no se pudo registrar');
					}
				} else {
					$rspta = $jefeAts->editar($id, $personal, $tipo_ats_id);
					// echo $rspta ? "Area actualizada" : "Area no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Jefe de ATS actualizada');
					} else {
						Response::JSON(400, 'Jefe de ATS no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $jefeAts->desactivar($id);
				//  echo $rspta ? "Area Desactivada" : "Area no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Jefe de ATS Desactivada');
				} else {
					Response::JSON(400, 'Jefe de ATS no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $jefeAts->activar($id);
				// echo $rspta ? "Area activada" : "Area no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Jefe de ATS activada');
				} else {
					Response::JSON(400, 'Jefe de ATS no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $jefeAts->mostrar($id);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de area', $rspta);
				break;

			case 'listar':
				$rspta = $jefeAts->listar();
				//Vamos a declarar un array
				$data = array();
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'personal_id' => $reg->personal_id,
						'tipoats_id' => $reg->tipoats_id,
						'estado' => $reg->estado,
						'personal' => $reg->personal,
						'tipoats' => $reg->tipoats,
					];
				}
				echo json_encode($data);
				break;
			case "selecP":
				require_once "../modelos/Persona.php";
				$obj = new Persona();
				$rspta = $obj->selectP();
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'nombre' => $reg->nombre_completo
					];
				}
				Response::JSON(200, 'Datos de areas', $data);
				break;
			case "selectTipoAts":
				require_once "../modelos/Parametro.php";
				$obj = new Parametro();
				$rspta = $obj->select('tipo_ats');
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->valor,
						'nombre' => $reg->nombre
					];
				}
				Response::JSON(200, 'Datos de tipo de area', $data);
				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
