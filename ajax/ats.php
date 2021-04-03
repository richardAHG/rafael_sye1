<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesi車n
}
/*
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {*/
//Validamos el acceso solo al usuario logueado y autorizado.
//if ($_SESSION['Administrar'] == 1) {
require_once "../modelos/Ats.php";
require_once "../helper/response/Response.php";
require_once "../modelos/GestionPeligrosG.php";
require_once "../modelos/GestionPeligrosE.php";
$ats = new Ats();
$objG = new GestionPeligrosG();
$objE = new GestionPeligrosE();


// $idusuario=$_SESSION["idusuario"];
switch ($_GET["op"]) {
	case 'guardaryeditar':
		// echo 'hola'; die();
		$data = json_decode(file_get_contents('php://input'), true);
		// print_r($data); die();
		$id = isset($data["id"]) ? limpiarCadena($data["id"]) : "";

		$personal_id = isset($data["personal_id"]) ? limpiarCadena($data["personal_id"]) : "";
		$tipo_ats_id = isset($data["tipo_ats_id"]) ? limpiarCadena($data["tipo_ats_id"]) : "";
		$jefe_id = isset($data["jefe_id"]) ? limpiarCadena($data["jefe_id"]) : "";
		$horaFin = isset($data["horaFin"]) ? limpiarCadena($data["horaFin"]) : "";
		$fecha_login = isset($data["fecha_login"]) ? limpiarCadena($data["fecha_login"]) : "";
		$distrito_id = isset($data["distrito_id"]) ? limpiarCadena($data["distrito_id"]) : "";
		$lugar = isset($data["lugar"]) ? limpiarCadena($data["lugar"]) : "";
		// print_r($lugar); die();
		$equipoP = isset($data["equipoP"]) ? $data["equipoP"] : ""; //array
		// print_r($equipoP); die();
		$equipoE = isset($data["equipoE"]) ? $data["equipoE"] : ""; //array
		$equipoPC = isset($data["equipoPC"]) ? $data["equipoPC"] : ""; //array
		$peligrosG = isset($data["peligrosG"]) ? $data["peligrosG"] : ""; //array
		$peligrosE = isset($data["peligrosE"]) ? $data["peligrosE"] : ""; //array
		$trabajadores = isset($data["trabajadores"]) ? $data["trabajadores"] : ""; //array
		$otros_peligros = isset($data["otros_peligros"]) ? limpiarCadena($data["otros_peligros"]) : "";
		$otros_riesgos = isset($data["otros_riesgos"]) ? limpiarCadena($data["otros_riesgos"]) : "";
		$otras_medidas = isset($data["otras_medidas"]) ? limpiarCadena($data["otras_medidas"]) : "";
		// print_r($trabajadores); die();
		$firma = isset($data["firma"]) ? limpiarCadena($data["firma"]) : "";
		$ubicacion = isset($data["ubicacion"]) ? limpiarCadena($data["ubicacion"]) : "";
		$idusuario = $personal_id;

		// 		$fecha=new DateTime($fecha_login);
		// 		$now =new DateTime('now'); 
		// 		print_r($now->format('Y-m-d H:i:s'));
		// 		echo substr(str_replace(' ',':',$now->format('Y-m-d H:i:s')),11,6);
		// 		print_r($fecha->format('Y-m-d H:i:s')); die();


		// print_r( str_replace(': ',':',$fecha_login));
		// echo ' ********************** ';
		// print_r($fecha_login);

		// echo '*****************************';
		// print_r($horaFin); die();
		if (empty($id)) {
			$rspta = $ats->insertar(
				$personal_id,
				$tipo_ats_id,
				$jefe_id,
				$horaFin,
				$distrito_id,
				$lugar,
				$equipoP,
				$equipoE,
				$equipoPC,
				$peligrosG,
				$peligrosE,
				$trabajadores,
				$firma,
				$ubicacion,
				$idusuario,
				$otros_peligros,
				$otros_riesgos,
				$otras_medidas,
				$fecha_login
			);
			// echo $rspta ? "Area registrada" : "Area no se pudo registrar";
			if ($rspta['correcto']) {
				Response::JSON(200, $rspta['mensaje']);
			} else {
				Response::JSON(400, $rspta['mensaje'] . ' - ATS no se registró');
			}
		}
		// else {
		// 	$rspta = $ats->editar($id, $personal_id,$tipo_ats_id,$jefe_id,$horaFin,$distrito_id,$lugar,
		// 	$equipoP,$equipoE,$equipoPC,$peligrosG,$peligrosE,$trabajadores,$firma,$ubicacion);
		// 	// echo $rspta ? "Area actualizada" : "Area no se pudo actualizar";
		// 	if ($rspta) {
		// 		Response::JSON(200, 'Jefe de ATS actualizada');
		// 	} else {
		// 		Response::JSON(400, 'Jefe de ATS no se pudo actualizar');
		// 	}
		// }
		break;

		// case 'desactivar':
		// 	$rspta = $ats->desactivar($id);
		// 	//  echo $rspta ? "Area Desactivada" : "Area no se puede desactivar";
		// 	if ($rspta) {
		// 		Response::JSON(200, 'Jefe de ATS Desactivada');
		// 	} else {
		// 		Response::JSON(400, 'Jefe de ATS no se pudo desactivar');
		// 	}
		// 	break;

	case 'listar':
		$rspta = $ats->listar();
		//Vamos a declarar un array
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => ($reg->estado) ? '<button class="btn btn-warning" onclick="mostrar(' . $reg->id . ')"><i class="fa fa-pencil"></i></button>' .
					' <button class="btn btn-danger" onclick="desactivar(' . $reg->id . ')"><i class="fa fa-close"></i></button>' :
					'<button class="btn btn-warning" onclick="mostrar(' . $reg->id . ')"><i class="fa fa-pencil"></i></button>' .
					' <button class="btn btn-primary" onclick="activar(' . $reg->id . ')"><i class="fa fa-check"></i></button>',
				"1" => $reg->personal_id,
				"2" => $reg->tipoats_id,
				"3" => $reg->cargo_id,
				"4" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' :
					'<span class="label bg-red">Desactivado</span>'
			);
		}
		$results = array(
			"sEcho" => 1, //Informaci車n para el datatables
			"iTotalRecords" => count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;
	case "selectJefeByAts":
		require_once "../modelos/JefeAts.php";
		$obj = new JefeAts();
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$rspta = $obj->selectJefeByAts($tipo_ats_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->empleado
			];
		}

		Response::JSON(200, 'Datos de jefes por tipo de ATS', $data);
		break;
		//select distrito
	case "selectD":
		require_once "../modelos/Distrito.php";
		$obj = new Distritos();
		$rspta = $obj->select();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de distritos', $data);
		break;
	case "selectEP":
		require_once "../modelos/Equipo.php";
		$obj = new Equipo();
		$rspta = $obj->selectEP();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		//	print_r($data); die();
		Response::JSON(200, 'Datos de equipos de proteción', $data);
		break;
	case "selectEE":
		require_once "../modelos/Equipo.php";
		$obj = new Equipo();
		$rspta = $obj->selectEE();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		//	print_r($data); die();
		Response::JSON(200, 'Datos de equipos de emergencia', $data);
		break;
	case "selectEPC":
		require_once "../modelos/Equipo.php";
		$obj = new Equipo();
		$rspta = $obj->selectEPC();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;
	case "selectPG": //peligro general
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$rspta = $objG->selectPGByAtsId($tipo_ats_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->peligro_id,
				'nombre' => $reg->nombre
			];
		}
		shuffle($data);
		Response::JSON(200, 'Datos de peligro general', $data);
		break;
	case "selectRG": //riesgo general
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$peligroG_id = $_GET['peligroG_id'];
		$rspta = $objG->selectRGByAtsId($tipo_ats_id, $peligroG_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->riesgo_id,
				'nombre' => $reg->nombre
			];
		}
		shuffle($data);
		Response::JSON(200, 'Datos de riesgo general', $data);
		break;
	case "selectMCG": //medida de control general
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$riesgoG_id = $_GET['riesgoG_id'];
		$peligroG_id = $_GET['peligroG_id'];
		$rspta = $objG->selectMCByAtsId($tipo_ats_id, $riesgoG_id, $peligroG_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->medida_id,
				'nombre' => $reg->nombre
			];
		}
		shuffle($data);
		Response::JSON(200, 'Datos de medidas de control general', $data);
		break;
	case "selectRespG": // respuesta general
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$medidaG_id = $_GET['medidaG_id'];
		$riesgoG_id = $_GET['riesgoG_id'];
		$peligroG_id = $_GET['peligroG_id'];
		$rspta = $objG->selectRByAtsId($tipo_ats_id, $medidaG_id, $riesgoG_id, $peligroG_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->respuesta,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de respuesta', $data);
		break;

	case "selectSAE": //sub actividad
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$rspta = $objE->selectSAByAtsId($tipo_ats_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->subactividad_id,
				'nombre' => $reg->nombre
			];
		}
		shuffle($data);
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;

	case "selectPE": //peligro especifico
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$subactividad_id = $_GET['subactividad_id'];
		$rspta = $objE->selectPGByAtsId($tipo_ats_id, $subactividad_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->peligro_id,
				'nombre' => $reg->nombre
			];
		}
		shuffle($data);
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;
	case "selectRE": //riesgo especifico
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$peligroG_id = $_GET['peligroG_id'];
		$subactividad_id = $_GET['subactividad_id'];
		$rspta = $objE->selectRGByAtsId($tipo_ats_id, $peligroG_id, $subactividad_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->riesgo_id,
				'nombre' => $reg->nombre
			];
		}
		shuffle($data);
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;
	case "selectMCE": //medida de control especifico
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$riesgoG_id = $_GET['riesgoG_id'];
		$peligroG_id = $_GET['peligroG_id'];
		$subactividad_id = $_GET['subactividad_id'];
		$rspta = $objE->selectMCByAtsId($tipo_ats_id, $riesgoG_id, $peligroG_id, $subactividad_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->medida_id,
				'nombre' => $reg->nombre
			];
		}
		shuffle($data);
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;
	case "selectRespE": // respuesta especifico
		$tipo_ats_id = $_GET['tipo_ats_id'];
		$medidaG_id = $_GET['medidaG_id'];
		$riesgoG_id = $_GET['riesgoG_id'];
		$peligroG_id = $_GET['peligroG_id'];
		$subactividad_id = $_GET['subactividad_id'];
		$rspta = $objE->selectRByAtsId($tipo_ats_id, $medidaG_id, $riesgoG_id, $peligroG_id, $subactividad_id);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->respuesta,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;
	case "selectQr": // lista qr de trabajadores
		require_once "../modelos/Persona.php";
		$obj = new Persona();
		$rspta = $obj->selectQR();

		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;
	case "selectPByQr": // selecciona personal por QR
		require_once "../modelos/Persona.php";
		$obj = new Persona();
		$qr = $_GET['qr'];
		$rspta = $obj->selectDataByQr($qr);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'contratista' => $reg->contratista,
				'nombre' => $reg->nombre_completo,
				'cargo' => $reg->cargo_id,
				'dni' => $reg->numero_documento
			];
		}
		Response::JSON(200, 'Datos de equipos de proteccion colectiva', $data);
		break;
	case "consultasATS": // OPCION DE CONSULTAS DE ATS
		require_once "../modelos/Consultas.php";
		$obj = new Consultas();
		$fi = $_GET['fi']; //fecha inicial
		$ff = $_GET['ff']; //fina fin
		$ui = $_GET['ui']; //user id
		$rspta = $obj->consultasATS($fi, $ff, $ui);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'fecha_creacion' => $reg->fecha_creacion,
				'tipo_ats_id' => $reg->tipo_ats_id,
				'tipo_ats' => $reg->tipo_ats,
			];
		}
		Response::JSON(200, 'Datos de ATS por fecha', $data);
		break;
	case "ubicarmeInAts": // OPCION DE PARTICPANTES EN ATS
		require_once "../modelos/Consultas.php";
		$obj = new Consultas();
		$fi = $_GET['fi']; //fecha inicial
		$ff = $_GET['ff']; //fina fin
		$ui = $_GET['ui']; //user id
		$rspta = $obj->ubicarmeInAts($fi, $ff, $ui);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'fecha_creacion' => $reg->fecha_creacion,
				'tipo_ats_id' => $reg->tipo_ats_id,
				'tipo_ats' => $reg->tipo_ats,
			];
		}
		Response::JSON(200, 'Datos de ATS por fecha', $data);
		break;
	case "getTipoAts": // listado de tipos de ATS
		require_once "../modelos/Ats.php";
		$obj = new Ats();

		$rspta = $obj->getTipoAts();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->valor,
				'nombre' => $reg->nombre,
				'grupo' => $reg->grupo,
				'especifico' => $reg->especifico,
			];
		}
		Response::JSON(200, 'Datos de tipo de ATS', $data);
		break;
	case "participanteATS":
		$rspta = $ats->selectParticipantes();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de tipo de area', $data);
		break;
	case "allUsuarios":
		$rspta = $ats->allUsuairos();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre,
				'ape_pat' => $reg->ape_pat,
				'ape_mat' => $reg->ape_mat,
				'cargo_id' => $reg->cargo_id,
				'cargo' => $reg->cargo,
				'numero_documento' => $reg->numero_documento,
				'login' => $reg->login
			];
		}
		Response::JSON(200, 'Datos de tipo de usuarios', $data);
		break;
	case "allJefes":
		$rspta = $ats->allJefes();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'personal_id' => $reg->personal_id,
				'tipoats_id' => $reg->tipoats_id
			];
		}
		Response::JSON(200, 'Datos de tipo de jefes', $data);
		break;
	case "allPG":
		$rspta = $ats->allPG();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'peligro_id' => $reg->peligro_id,
				'riesgo_id' => $reg->riesgo_id,
				'medida_id' => $reg->medida_id,
				'respuesta' => $reg->respuesta,
				'tipo_ats_id' => $reg->tipo_ats_id
			];
		}
		Response::JSON(200, 'Datos de peligros generales', $data);
		break;
	case "allPE":
		$rspta = $ats->allPE();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'subactividad_id' => $reg->subactividad_id,
				'peligro_id' => $reg->peligro_id,
				'riesgo_id' => $reg->riesgo_id,
				'medida_id' => $reg->medida_id,
				'respuesta' => $reg->respuesta,
				'tipo_ats_id' => $reg->tipo_ats_id
			];
		}
		Response::JSON(200, 'Datos de peligros especificos', $data);
		break;
}

//Fin de las validaciones de acceso
/*} else {
		require 'noacceso.php';
	}*/
//}
ob_end_flush();
