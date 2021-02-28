<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Parametro
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($grupo,$nombre)
	{
		//obtenemos el maximo valor y generamos el nuevo registro
		$sql="SELECT (IFNULL(max(valor), 0)+1) as valor FROM `parametros` WHERE grupo='$grupo'";
		$new_valor=ejecutarConsultaSimpleFila($sql);
		$valor=$new_valor['valor'];
		//registro del nuevo parametro
		$sqlI="INSERT INTO parametros (grupo, nombre, valor) VALUES ('$grupo','$nombre','$valor')";
		return ejecutarConsulta($sqlI);
	}

	//Implementamos un método para editar registros
	public function editar($id,$grupo,$nombre)
	{
		$sql="UPDATE parametros 
				SET grupo='$grupo',nombre='$nombre' 
				WHERE id='$id'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($id)
	{
		$sql="UPDATE parametros SET estado='0' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($id)
	{
		$sql="UPDATE parametros SET estado='1' WHERE id='$id'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($id)
	{
		$sql="SELECT * FROM parametros WHERE id='$id'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT id, grupo, nombre, valor, estado FROM parametros";
		return ejecutarConsulta($sql);		
	}
	//Implementar un método para listar los registros y mostrar en el select
	public function select($grupo)
	{
		$sql = "SELECT * FROM parametros where estado=1 and grupo='$grupo'";
		return ejecutarConsulta($sql);		
	}

}
