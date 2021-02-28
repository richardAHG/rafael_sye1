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
		require_once "../modelos/MedidaControl.php";
		require_once "../helper/response/Response.php";
		$medidaControl = new MedidaControl();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$tipo_id = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $medidaControl->insertar($nombre, $tipo_id);
					// echo $rspta ? "Area registrada" : "Area no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Medida de Control registrada');
					} else {
						Response::JSON(400, 'Medida de Control no se pudo registrar');
					}
				} else {
					$rspta = $medidaControl->editar($id, $nombre, $tipo_id);
					// echo $rspta ? "Area actualizada" : "Area no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Medida de Control actualizada');
					} else {
						Response::JSON(400, 'Medida de Control no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $medidaControl->desactivar($id);
				//  echo $rspta ? "Area Desactivada" : "Area no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Medida de Control Desactivada');
				} else {
					Response::JSON(400, 'Medida de Control no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $medidaControl->activar($id);
				// echo $rspta ? "Area activada" : "Area no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Medida de Control activada');
				} else {
					Response::JSON(400, 'Medida de Control no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $medidaControl->mostrar($id);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de peligro', $rspta);
				break;

			case 'listar':
				$rspta = $medidaControl->listar();
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
			case "selectTipo":
				$rspta = $medidaControl->selectTipo();

				// while ($reg = $rspta->fetch_object()) {
				// 	echo '<option value=' . $reg->valor . '>' . $reg->nombre . '</option>';
				// }
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->valor,
						'nombre' => $reg->nombre
					];
				}
				Response::JSON(200, 'Datos de tipo de peligro', $data);
				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
