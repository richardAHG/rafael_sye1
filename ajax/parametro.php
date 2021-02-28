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
		require_once "../modelos/Parametro.php";
		require_once "../helper/response/Response.php";
		$parametro = new Parametro();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$grupo = isset($_POST["grupo"]) ? limpiarCadena($_POST["grupo"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':

				if (empty($id)) {
					$rspta = $parametro->insertar($grupo,$nombre);
					// echo $rspta ? "Parametro registrada" : "Parametro no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Parametro registrada');
					} else {
						Response::JSON(400, 'Parametro no se pudo registrar');
					}
				} else {
					$rspta = $parametro->editar($id,$grupo, $nombre);
					// echo $rspta ? "Parametro actualizada" : "Parametro no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Parametro actualizada');
					} else {
						Response::JSON(400, 'Parametro no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $parametro->desactivar($id);
				//  echo $rspta ? "Parametro Desactivada" : "Parametro no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Parametro Desactivada');
				} else {
					Response::JSON(400, 'Parametro no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $parametro->activar($id);
				// echo $rspta ? "Parametro activada" : "Parametro no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Parametro activada');
				} else {
					Response::JSON(400, 'Parametro no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $parametro->mostrar($id);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de Parametro', $rspta);
				break;

			case 'listar':
				$rspta = $parametro->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'grupo' => $reg->grupo,
						'nombre' => $reg->nombre,
						'valor' => $reg->valor,
						'estado' => $reg->estado,
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
