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
		require_once "../modelos/JefeCargo.php";
		require_once "../helper/response/Response.php";
		$jefeCargo = new JefeCargo();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$idParticipante = isset($_POST["idParticipante"]) ? limpiarCadena($_POST["idParticipante"]) : "";
		$area_id = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";
		$subarea_id = isset($_POST["subarea_id"]) ? limpiarCadena($_POST["subarea_id"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $jefeCargo->insertar($idParticipante, $area_id, $subarea_id);
					// echo $rspta ? "efe a cargo registrada" : "efe a cargo no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Jefe a cargo registrado');
					} else {
						Response::JSON(400, 'Jefe a cargo no se pudo registrar');
					}
				} else {
					$rspta = $jefeCargo->editar($id, $idParticipante, $area_id, $subarea_id);
					// echo $rspta ? "Jefe a cargo actualizada" : "Jefe a cargo no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Jefe a cargo actualizado');
					} else {
						Response::JSON(400, 'Jefe a cargo no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $jefeCargo->desactivar($id);
				//  echo $rspta ? "efe a cargo Desactivada" : "efe a cargo no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Jefe a cargo Desactivado');
				} else {
					Response::JSON(400, 'Jefe a cargo no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $jefeCargo->activar($id);
				// echo $rspta ? "Jefe a cargo activada" : "Jefe a cargo no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Jefe a cargo activado');
				} else {
					Response::JSON(400, 'Jefe a cargo no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $jefeCargo->mostrar($id);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de area', $rspta);
				break;

			case 'listar':
				$rspta = $jefeCargo->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] =[
						'id'=>$reg->id,
						'personal_id'=>$reg->personal_id,
						'area_id'=>$reg->area_id,
						'subarea_id'=>$reg->subarea_id,
						'estado'=>$reg->estado,
						'personal'=>$reg->personal,
						'area'=>$reg->area,
						'subarea'=>$reg->subarea,
					];
				}
				echo json_encode($data);

				break;
			
			case "selectTipo":
				$rspta = $jefeCargo->selectTipo();

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
				Response::JSON(200, 'Datos de tipo de area', $data);
				break;
			case "selectArea":
				$rspta = $jefeCargo->selectArea();

				// while ($reg = $rspta->fetch_object()) {
				// 	echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
				// }
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'nombre' => $reg->nombre
					];
				}
				Response::JSON(200, 'Datos de areas', $data);
				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
