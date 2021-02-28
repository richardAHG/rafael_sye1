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
		require_once "../modelos/GestionPeligrosE.php";
		require_once "../helper/response/Response.php";
		$gestionPeligros = new GestionPeligrosE();
		$TIPO_ESPECIFICO = 2;

		$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
		$subactividad = isset($_POST["subactividad"]) ? limpiarCadena($_POST["subactividad"]) : "";
		$tipo_ats_id = isset($_POST["tipo_ats_id"]) ? limpiarCadena($_POST["tipo_ats_id"]) : "";
		$peligroG_id = isset($_POST["peligroG_id"]) ? limpiarCadena($_POST["peligroG_id"]) : "";
		$riesgoG_id = isset($_POST["riesgoG_id"]) ? limpiarCadena($_POST["riesgoG_id"]) : "";
		$medidaG_id = isset($_POST["medidaG_id"]) ? limpiarCadena($_POST["medidaG_id"]) : "";
		$respuesta = isset($_POST["respuesta"]) ? limpiarCadena($_POST["respuesta"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($id)) {
					$rspta = $gestionPeligros->insertar($subactividad, $tipo_ats_id, $peligroG_id, $riesgoG_id, $medidaG_id, $respuesta);
					// echo $rspta ? "Area registrada" : "Area no se pudo registrar";
					if ($rspta) {
						Response::JSON(200, 'Gestion de peligro registrada');
					} else {
						Response::JSON(400, 'Gestion de peligro no se pudo registrar');
					}
				} else {
					$rspta = $gestionPeligros->editar($id, $subactividad, $tipo_ats_id, $peligroG_id, $riesgoG_id, $medidaG_id, $respuesta);
					// echo $rspta ? "Area actualizada" : "Area no se pudo actualizar";
					if ($rspta) {
						Response::JSON(200, 'Gestion de peligro actualizada');
					} else {
						Response::JSON(400, 'Gestion de peligro no se pudo actualizar');
					}
				}
				break;

			case 'desactivar':
				$rspta = $gestionPeligros->desactivar($id);
				//  echo $rspta ? "Area Desactivada" : "Area no se puede desactivar";
				if ($rspta) {
					Response::JSON(200, 'Gestion de peligro Desactivada');
				} else {
					Response::JSON(400, 'Gestion de peligro no se pudo desactivar');
				}
				break;

			case 'activar':
				$rspta = $gestionPeligros->activar($id);
				// echo $rspta ? "Area activada" : "Area no se puede activar";
				if ($rspta) {
					Response::JSON(200, 'Gestion de peligro activada');
				} else {
					Response::JSON(400, 'Gestion de peligro no se pudo activar');
				}
				break;

			case 'mostrar':
				$rspta = $gestionPeligros->mostrar($id);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de area', $rspta);
				break;

			case 'listar':
				$rspta = $gestionPeligros->listar();
				//Vamos a declarar un array
				$data = array();
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'subactividad_id' => $reg->subactividad_id,
						'peligro_id' => $reg->peligro_id,
						'riesgo_id' => $reg->riesgo_id,
						'medida_id' => $reg->medida_id,
						'respuesta' => $reg->respuesta,
						'tipo_ats_id' => $reg->tipo_ats_id,
						'estado' => $reg->estado,
						'subactividad' => $reg->subactividad,
						'peligro' => $reg->peligro,
						'riesgo' => $reg->riesgo,
						'medida' => $reg->medida,
						'tipo_ats' => $reg->tipo_ats
					];
				}
				echo json_encode($data);
				break;
			case "selectSubActividad":
			    
				require_once "../modelos/SubActividad.php";
				
				$obj = new SubActividad();
				$rspta = $obj->select();
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'nombre' => $reg->nombre
					];
				}
				
				Response::JSON(200, 'Datos de sub actividad', $data);
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
			case "selecPG":
				require_once "../modelos/Peligro.php";
				$obj = new Peligro();
				$rspta = $obj->selectP($TIPO_ESPECIFICO);
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'nombre' => $reg->nombre
					];
				}
				Response::JSON(200, 'Datos de areas', $data);
				break;
			case "selecRG":
				require_once "../modelos/Riesgo.php";
				$obj = new Riesgo();
				$rspta = $obj->selectR($TIPO_ESPECIFICO);
				$data = [];
				while ($reg = $rspta->fetch_object()) {
					$data[] = [
						'id' => $reg->id,
						'nombre' => $reg->nombre
					];
				}
				Response::JSON(200, 'Datos de areas', $data);
				break;
			case "selecMG":
				require_once "../modelos/MedidaControl.php";
				$obj = new MedidaControl();
				$rspta = $obj->selectM($TIPO_ESPECIFICO);
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
