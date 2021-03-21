<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
require_once "../modelos/Usuario.php";
require_once "../helper/response/Response.php";
$usuario = new Usuario();

$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$ape_pat = isset($_POST["ape_pat"]) ? limpiarCadena($_POST["ape_pat"]) : "";
$ape_mat = isset($_POST["ape_mat"]) ? limpiarCadena($_POST["ape_mat"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
$numero_documento = isset($_POST["numero_documento"]) ? limpiarCadena($_POST["numero_documento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$cell = isset($_POST["cell"]) ? limpiarCadena($_POST["cell"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$cargo_id = isset($_POST["cargo_id"]) ? limpiarCadena($_POST["cargo_id"]) : "";
$regimen_id = isset($_POST["regimen_id"]) ? limpiarCadena($_POST["regimen_id"]) : "";
$area_id = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";
$subarea_id = isset($_POST["subarea_id"]) ? limpiarCadena($_POST["subarea_id"]) : "";
$fecha_ingreso = isset($_POST["fecha_ingreso"]) ? limpiarCadena($_POST["fecha_ingreso"]) : "";
$fecha_cese = isset($_POST["fecha_cese"]) ? limpiarCadena($_POST["fecha_cese"]) : "";
$login = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
$permiso = isset($_POST["permiso"]) ? $_POST["permiso"] : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
		} else {
			//Validamos el acceso solo al usuario logueado y autorizado.
			if ($_SESSION['Administrar'] == 1) {
				if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
					$imagen = $_POST["imagenactual"];
				} else {
					$ext = explode(".", $_FILES["imagen"]["name"]);
					if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {
						$imagen = round(microtime(true)) . '.' . end($ext);
						move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/usuarios/" . $imagen);
					}
				}
				//Hash SHA256 en la contraseña
				$clavehash = hash("SHA256", $clave);

				if (empty($idusuario)) {
					$rspta = $usuario->insertar(
						$nombre,
						$ape_pat,
						$ape_mat,
						$email,
						$cargo_id,
						$regimen_id,
						$direccion,
						$cell,
						$tipo_documento,
						$numero_documento,
						$area_id,
						$subarea_id,
						$fecha_ingreso,
						$fecha_cese,
						$login,
						$clavehash,
						$imagen,
						$permiso
					);
					if ($rspta['correcto']) {
						Response::JSON(200, $rspta['mensaje']);
					} else {
						Response::JSON(400, $rspta['mensaje'] . ' - Usuario no se registró');
					}
				} else {
					$rspta = $usuario->editar(
						$idusuario,
						$nombre,
						$ape_pat,
						$ape_mat,
						$email,
						$cargo_id,
						$regimen_id,
						$direccion,
						$cell,
						$tipo_documento,
						$numero_documento,
						$area_id,
						$subarea_id,
						$fecha_ingreso,
						$fecha_cese,
						$login,
						$clavehash,
						$imagen,
						$permiso
					);
					if ($rspta['correcto']) {
						Response::JSON(200, $rspta['mensaje']);
					} else {
						Response::JSON(400, $rspta['mensaje'] . ' - Usuario no se puede actualizar');
					}
				}
				//Fin de las validaciones de acceso
			} else {
				// require 'noacceso.php';
				Response::JSON(400, 'No tiene acceso');
			}
		}
		break;

	case 'desactivar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
		} else {
			//Validamos el acceso solo al usuario logueado y autorizado.
			if ($_SESSION['Administrar'] == 1) {
				$rspta = $usuario->desactivar($idusuario);
				if ($rspta) {
					Response::JSON(200, 'Usuario Desactivado');
				} else {
					Response::JSON(400, 'Usuario no se puede desactivar');
				}
				//Fin de las validaciones de acceso
			} else {
				// require 'noacceso.php';
				Response::JSON(400, 'No tiene acceso');
			}
		}
		break;

	case 'activar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
		} else {
			//Validamos el acceso solo al usuario logueado y autorizado.
			if ($_SESSION['Administrar'] == 1) {
				$rspta = $usuario->activar($idusuario);

				if ($rspta) {
					Response::JSON(200, 'Usuario activado');
				} else {
					Response::JSON(400, 'Usuario no se puede activar');
				}
				//Fin de las validaciones de acceso
			} else {
				// require 'noacceso.php';
				Response::JSON(400, 'No tiene acceso');
			}
		}
		break;

case 'bloquerATS':
			if (!isset($_SESSION["nombre"])) {
				header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
			} else {
				//Validamos el acceso solo al usuario logueado y autorizado.
				if ($_SESSION['Administrar'] == 1) {
					$rspta = $usuario->bloquerATS($idusuario);
					if ($rspta) {
						Response::JSON(200, 'Usuario bloqueado para ATS');
					} else {
						Response::JSON(400, 'Usuario no se puede bloquear');
					}
					//Fin de las validaciones de acceso
				} else {
					// require 'noacceso.php';
					Response::JSON(400, 'No tiene acceso');
				}
			}
			break;
	
		case 'habilitarATS':
			if (!isset($_SESSION["nombre"])) {
				header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
			} else {
				//Validamos el acceso solo al usuario logueado y autorizado.
				if ($_SESSION['Administrar'] == 1) {
					$rspta = $usuario->habilitarATS($idusuario);
	
					if ($rspta) {
						Response::JSON(200, 'Usuario Habilitado para ATS');
					} else {
						Response::JSON(400, 'Usuario no se puede Habilitar');
					}
					//Fin de las validaciones de acceso
				} else {
					// require 'noacceso.php';
					Response::JSON(400, 'No tiene acceso');
				}
			}
			break;

	case 'mostrar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
		} else {
			//Validamos el acceso solo al usuario logueado y autorizado.
			if ($_SESSION['Administrar'] == 1) {
				$rspta = $usuario->mostrar($idusuario);
				//Codificar el resultado utilizando json
				// echo json_encode($rspta);
				Response::JSON(200, 'Datos de usuario', $rspta);
				//Fin de las validaciones de acceso
			} else {
				// require 'noacceso.php';
				Response::JSON(400, 'No tiene acceso');
			}
		}
		break;

	case 'listar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
		} else {
			//Validamos el acceso solo al usuario logueado y autorizado.
			if ($_SESSION['Administrar'] == 1) {
				$rspta = $usuario->listar();
				//Vamos a declarar un array
				$data = array();
				while ($reg = $rspta->fetch_object()) {
					$data[] =[
						'id'=>$reg->id,
						'nombre'=>$reg->nombre,
						'apepat'=>$reg->ape_pat,
						'apemat'=>$reg->ape_mat,
						'email'=>$reg->email,
						'cargo_id'=>$reg->cargo_id,
						'regimen_id'=>$reg->regimen_id,
						'direccion'=>$reg->direccion,
						'cell'=>$reg->cell,
						'tipo_documento'=>$reg->tipo_documento,
						'numero_documento'=>$reg->numero_documento,
						'area_id'=>$reg->area_id,
						'subarea_id'=>$reg->subarea_id,
						'fecha_ingreso'=>$reg->fecha_ingreso,
						'fecha_cese'=>$reg->fecha_cese,
						'login'=>$reg->login,
						'imagen'=>$reg->imagen,
						'estado'=>$reg->estado,
						'ats'=>$reg->ats,
						'grupo_sanguineo'=>$reg->grupo_sanguineo,

						'cargo'=>$reg->cargo,
						'regimen'=>$reg->regimen,
						'tipoDocumento'=>$reg->tipoDocumento,
						'area'=>$reg->area,
						'subarea'=>$reg->subarea,
						'nombre_completo'=>$reg->nombre.' '.$reg->ape_pat.' '.$reg->ape_mat,
						'documento'=>$reg->tipoDocumento.' - '.$reg->numero_documento,
					];
				}
				echo json_encode($data);
				//Fin de las validaciones de acceso
			} else {
				// require 'noacceso.php';
				Response::JSON(400, 'No tiene acceso');
			}
		}
		break;

	case 'permisos':
		//Obtenemos todos los permisos de la tabla permisos
		require_once "../modelos/Permiso.php";
		$permiso = new Permiso();
		$rspta = $permiso->listar();

		//Obtener los permisos asignados al usuario
		$id = $_GET['id'];
		$marcados = $usuario->listarmarcados($id);
		//Declaramos el array para almacenar todos los permisos marcados
		$valores = array();

		//Almacenar los permisos asignados al usuario en el array
		while ($per = $marcados->fetch_object()) {
			array_push($valores, $per->idpermiso);
		}

		//Mostramos la lista de permisos en la vista y si están o no marcados
		while ($reg = $rspta->fetch_object()) {
			$sw = in_array($reg->idpermiso, $valores) ? 'checked' : '';
			echo '<li> <input type="checkbox" ' . $sw . '  name="permiso[]" value="' . $reg->idpermiso . '">' . $reg->nombre . '</li>';
		}
		break;

	case 'verificar':
		$logina = $_POST['logina'];
		$clavea = $_POST['clavea'];

		//Hash SHA256 en la contraseña
		$clavehash = hash("SHA256", $clavea);
		// print_r($clavehash); die();
		// $clavehash = $clavea;

		$rspta = $usuario->verificar($logina, $clavehash);

		$fetch = $rspta->fetch_object();

		$acceso = false;
		$mensaje = 'Usuario Incorrecto';
		$status = 400;
		if (isset($fetch)) {
			$mensaje = 'Usuario Correcto';
			$acceso = true;
			$status = 200;
			//Declaramos las variables de sesión
			$_SESSION['idusuario'] = $fetch->id;
			$_SESSION['nombre'] = $fetch->nombre;
			$_SESSION['imagen'] = $fetch->imagen;
			$_SESSION['login'] = $fetch->login;

			//Obtenemos los permisos del usuario
			$marcados = $usuario->listarmarcados($fetch->id);

			//Declaramos el array para almacenar todos los permisos marcados
			$valores = array();

			//Almacenamos los permisos marcados en el array
			while ($per = $marcados->fetch_object()) {
				array_push($valores, $per->idpermiso);
			}

			//Determinamos los accesos del usuario
			in_array(1, $valores) ? $_SESSION['escritorio'] = 1 : $_SESSION['escritorio'] = 0;
			in_array(2, $valores) ? $_SESSION['Administrar'] = 1 : $_SESSION['Administrar'] = 0;
			in_array(3, $valores) ? $_SESSION['Asignar'] = 1 : $_SESSION['Asignar'] = 0;
			in_array(4, $valores) ? $_SESSION['acceso'] = 1 : $_SESSION['acceso'] = 0;
			in_array(5, $valores) ? $_SESSION['Consulta_ats'] = 1 : $_SESSION['Consulta_ats'] = 0;
			in_array(6, $valores) ? $_SESSION['Consulta_fecha'] = 1 : $_SESSION['Consulta_fecha'] = 0;

		} else {
			$fetch = [];
		}

		Response::JSON($status, $mensaje, $fetch, $acceso);
		break;

case 'verificar_app':
    $data = json_decode(file_get_contents('php://input'), true);
		$clavea = $data['clavea'];

		//Hash SHA256 en la contraseña
		$clavehash = hash("SHA256", $clavea);
		// print_r($clavehash); die();
		// $clavehash = $clavea;

		$rspta = $usuario->verificarApp($clavehash);

		$fetch = $rspta->fetch_object();

		$acceso = false;
		$mensaje = 'Usuario Incorrecto';
		$status = 400;
		if (isset($fetch)) {
			$mensaje = 'Usuario Correcto';
			$acceso = true;
			$status = 200;
			//Declaramos las variables de sesión
			$_SESSION['idusuario'] = $fetch->id;
			$_SESSION['nombre'] = $fetch->nombre;
			$_SESSION['imagen'] = $fetch->imagen;
			$_SESSION['login'] = $fetch->login;

			//Obtenemos los permisos del usuario
			$marcados = $usuario->listarmarcados($fetch->id);

			//Declaramos el array para almacenar todos los permisos marcados
			$valores = array();

			//Almacenamos los permisos marcados en el array
			while ($per = $marcados->fetch_object()) {
				array_push($valores, $per->idpermiso);
			}

			//Determinamos los accesos del usuario
			in_array(1, $valores) ? $_SESSION['escritorio'] = 1 : $_SESSION['escritorio'] = 0;
			in_array(2, $valores) ? $_SESSION['Administrar'] = 1 : $_SESSION['Administrar'] = 0;
			in_array(3, $valores) ? $_SESSION['Asignar'] = 1 : $_SESSION['Asignar'] = 0;
			in_array(4, $valores) ? $_SESSION['acceso'] = 1 : $_SESSION['acceso'] = 0;
			in_array(5, $valores) ? $_SESSION['Consulta_ats'] = 1 : $_SESSION['Consulta_ats'] = 0;
			in_array(6, $valores) ? $_SESSION['Consulta_fecha'] = 1 : $_SESSION['Consulta_fecha'] = 0;

		} else {
			$fetch = [];
		}

		Response::JSON($status, $mensaje, $fetch, $acceso);
		break;
		
	case 'salir':
		//Limpiamos las variables de sesión   
		session_unset();
		//Destruìmos la sesión
		session_destroy();
		//Redireccionamos al login
		header("Location: ../index.php");

		Response::JSON(200, 'Session finalizada');
		break;

	case 'Editclave':

		$C_Actual = $_POST['passActual'];
		$C_Nueva = $_POST['passNuevo'];
		// print_r($C_Actual); die();
		//Hash SHA256 en la contraseña
		$clavehash_Act = hash("SHA256", $C_Actual);
		$clavehash_Nuev = hash("SHA256", $C_Nueva);

		//se captura el id del usuario q inicio sesion;
		$id_usuario_login = $_SESSION['idusuario'];
		
		$rspta = $usuario->editClave($clavehash_Act, $clavehash_Nuev, $id_usuario_login);

		// echo $rspta ? "Clave actualizada" : "Clave no se pudo actualizar";
		if ($rspta) {
			Response::JSON(200, 'Clave actualizada');
		} else {
			Response::JSON(400, 'Clave no se pudo actualizar');
		}
		break;
	case "selectCargo":
		require_once "../modelos/Cargo.php";
		$obj = new Cargo();
		$rspta = $obj->select();

		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de tipo de peligro', $data);
		break;
	case "selectRegimen":
		require_once "../modelos/Parametro.php";
		$obj = new Parametro();
		$rspta = $obj->select('regimen_laboral');

		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->valor,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de tipo de peligro', $data);
		break;
	case "selectTipoDocumento":
		require_once "../modelos/Parametro.php";
		$obj = new Parametro();
		$rspta = $obj->select('tipo_documento');

		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->valor,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de tipo de peligro', $data);
		break;
	case "selectArea":
		require_once "../modelos/Area.php";
		$obj = new Area();
		$rspta = $obj->selectArea();

		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de tipo de peligro', $data);
		break;
	case "selectSubArea":
		require_once "../modelos/Area.php";
		$idArea=$_POST['idarea'];
		// print_r($idArea); die();
		$obj = new Area();
		$rspta = $obj->selectSubArea($idArea);

		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'id' => $reg->id,
				'nombre' => $reg->nombre
			];
		}
		Response::JSON(200, 'Datos de tipo de peligro', $data);
		break;
}
ob_end_flush();
