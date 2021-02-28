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
		require_once "../modelos/Cargo.php";
		require_once "../helper/response/Response.php";
		$cargo = new Cargo();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $cargo->insertar($nombre);
					// echo $rspta ? "Cargo registrado" : "Cargo no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Cargo registrado');
					} else {
						Response::JSON(400, 'Cargo no se pudo registrar');
					}
				} else {
					$rspta = $cargo->editar($id, $nombre);
					// echo $rspta ? "Cargo actualizado" : "Cargo no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Cargo actualizado');
					} else {
						Response::JSON(400, 'Cargo no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $cargo->desactivar($id);
				//  echo $rspta ? "Cargo Desactivado" : "Cargo no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Cargo Desactivado');
				} else {
					Response::JSON(400, 'Cargo no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $cargo->activar($id);
				//  echo $rspta ? "Cargo activado" : "Cargo no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Cargo activado');
				} else {
					Response::JSON(400, 'Cargo no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $cargo->mostrar($id);
				//Codificar el resultado utilizando json
				//  echo json_encode($rspta);
				Response::JSON(200, 'Datos de cargo', $rspta);
				break;

			case 'listar':
				$rspta = $cargo->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] =[
						'id'=>$reg->id,
						'nombre'=>$reg->nombre,
						'estado'=>$reg->estado,
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
