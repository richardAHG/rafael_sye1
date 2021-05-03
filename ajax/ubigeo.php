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
		require_once "../modelos/Ubigeo.php";
		require_once "../helper/response/Response.php";
		$area = new Ubigeo();

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$tipo_id = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : "";
		$parent_id = isset($_POST["parent"]) ? limpiarCadena($_POST["parent"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $area->insertar($nombre, $tipo_id, $parent_id);
					// echo $rspta ? "Area registrada" : "Area no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Area registrada');
					} else {
						Response::JSON(400, 'Area no se pudo registrar');
					}
				} else {
					$rspta = $area->editar($id, $nombre, $tipo_id, $parent_id);
					// echo $rspta ? "Area actualizada" : "Area no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Area actualizada');
					} else {
						Response::JSON(400, 'Area no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $area->desactivar($id);
				//  echo $rspta ? "Area Desactivada" : "Area no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Area Desactivada');
				} else {
					Response::JSON(400, 'Area no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $area->activar($id);
				// echo $rspta ? "Area activada" : "Area no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Area activada');
				} else {
					Response::JSON(400, 'Area no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $area->mostrar($id);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de area', $rspta);
				break;

			case 'listar':
				$rspta = $area->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] =[
						'id'=>$reg->id,
						'nombre'=>$reg->nombre,
						'tipo_id'=>$reg->tipo_id,
						'estado'=>$reg->estado,
						'tipo'=>$reg->tipo,
					];
				}
				echo json_encode($data);

				break;
			case 'listarBK':
				$rspta = $area->listar();
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => ($reg->estado) ? '<button class="btn btn-warning" onclick="mostrar(' . $reg->id . ')"><i class="fa fa-pencil"></i></button>' .
							' <button class="btn btn-danger" onclick="desactivar(' . $reg->id . ')"><i class="fa fa-close"></i></button>' :
							'<button class="btn btn-warning" onclick="mostrar(' . $reg->id . ')"><i class="fa fa-pencil"></i></button>' .
							' <button class="btn btn-primary" onclick="activar(' . $reg->id . ')"><i class="fa fa-check"></i></button>',
						"1" => $reg->nombre,
						"2" => $reg->tipo_id,
						"3" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' :
							'<span class="label bg-red">Desactivado</span>'
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results);

				break;
			case "selectTipo":
				$rspta = $area->selectTipo();

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
				$rspta = $area->selectArea();

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
