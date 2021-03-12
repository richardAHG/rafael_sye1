<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['Consulta_ats'] == 1 || $_SESSION['Consulta_ats'] == 1) {
		require_once "../modelos/Consultas.php";
		require_once "../utils/CsvUtil.php";
		$consulta = new Consultas();


		switch ($_GET["op"]) {
			case 'atsfecha':
				$fecha_inicio = $_REQUEST["fecha_inicio"];
				$fecha_fin = $_REQUEST["fecha_fin"];
				// print_r($fecha_inicio); die();
				$rspta = $consulta->atsfecha($fecha_inicio, $fecha_fin);
				//Vamos a declarar un array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"id" => $reg->id,
						"personal_id" => $reg->personal_id,
						"empleado" => $reg->empleado,
						"numero_documento" => $reg->numero_documento,
						"firma_ruta" => $reg->firma_ruta,
						"hora_fin" => $reg->hora_fin,
						"fecha_creacion" => $reg->fecha_creacion,
					);
				}

				echo json_encode($data);

				break;
			case 'atsparticipante':
				$fecha_inicio = $_REQUEST["fecha_inicio"];
				$fecha_fin = $_REQUEST["fecha_fin"];
				$idParticipante = $_REQUEST["idParticipante"];
				// print_r($fecha_inicio); die();
				$rspta = $consulta->atsParticipantes($fecha_inicio, $fecha_fin, $idParticipante);
				//Vamos a declarar un array
				$data = array();
				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"id" => $reg->id,
						"personal_id" => $reg->personal_id,
						"empleado" => $reg->empleado,
						"numero_documento" => $reg->numero_documento,
						"firma_ruta" => $reg->firma_ruta,
						"hora_fin" => $reg->hora_fin,
						"fecha_creacion" => $reg->fecha_creacion,
					);
				}

				echo json_encode($data);

				break;
			case 'rptCumplimiento':
				$fecha_inicio = $_REQUEST["fecha_inicio"];
				$fecha_fin = $_REQUEST["fecha_fin"];

				$data = [];
				$fechas = [];
				$data[0] = [
					'numero_documento',
					'area',
					'subarea',
					'cargo',
					'nombre',
					// 'fecha'
				];

				$comienzo = new DateTime($fecha_inicio);
				$final = new DateTime($fecha_fin);
				for ($i = $comienzo; $i <= $final; $i->modify('+1 day')) {
					$fecha = $i->format("Y-m-d");
					$data[0][$fecha] = $fecha;
				}

				$rspta = $consulta->rptCumplimiento($fecha_inicio, $fecha_fin);

				$cont = 1;
				while ($reg = $rspta->fetch_object()) {
					$data[$cont] = [
						'numero_documento' => $reg->numero_documento,
						'area' => $reg->area,
						'subarea' => $reg->subarea,
						'cargo' => $reg->cargo,
						'nombre' => $reg->nombre,
						// 'fecha' => $reg->fecha,
					];
					$fechas = explode(',', $reg->fechas);
					// foreach ($fechas as $key => $value) {
					// 	$data[$cont][$value] = 1;
					// }

					$comienzo = new DateTime($fecha_inicio);
					$final = new DateTime($fecha_fin);
					for ($i = $comienzo; $i <= $final; $i->modify('+1 day')) {
						$fecha = $i->format("Y-m-d");
						$exits = 0;
						foreach ($fechas as $key => $value) {
							$valuex = new DateTime($value);
							$fechax = new DateTime($fecha);
							if ($fechax == $valuex) {
								$exits = 1;
								$data[$cont][$fecha] = 1;
								break;
							}
						}

						if ($exits == 0) {
							$data[$cont][$fecha] = 0;
						}
					}
					$cont++;
				}
				// print_r($data);die();
				try {
					$worksheet = CsvUtil::createSheet();
					$hoja = $worksheet->getActiveSheet();
					CsvUtil::writeSheet($hoja, $data);

					$sheet = CsvUtil::saveSheet($worksheet, 'reporte_cumplimiento');
				} catch (Exception $ex) {
					throw $ex->getMessage();
				}

				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
