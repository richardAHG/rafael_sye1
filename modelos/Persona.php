<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Persona
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email)
	{
		$sql="INSERT INTO personal (tipo_persona,nombre,tipo_documento,num_documento,direccion,telefono,email)
		VALUES ('$tipo_persona','$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idpersona,$tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email)
	{
		$sql="UPDATE personal SET tipo_persona='$tipo_persona',nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email' WHERE idpersona='$idpersona'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar categorías
	public function eliminar($idpersona)
	{
		$sql="DELETE FROM personal WHERE idpersona='$idpersona'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idpersona)
	{
		$sql="SELECT * FROM personal WHERE idpersona='$idpersona'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function selectP()
	{
		$sql="SELECT id,CONCAT(numero_documento,' - ',nombre,' ',ape_pat,' ',ape_mat) as nombre_completo FROM personal WHERE estado=1";
		return ejecutarConsulta($sql);		
	}

    public function selectQR()
	{
		$sql="SELECT id,numero_documento,concat(ape_pat,' ',ape_mat,' ',nombre)as nombre FROM personal WHERE estado=1";
		return ejecutarConsulta($sql);		
	}

	public function selectDataByQr($qr)
	{
		$sql="SELECT id,'' as contratista,CONCAT(nombre,' ',ape_pat,' ',ape_mat) as nombre_completo,cargo_id, numero_documento FROM personal WHERE numero_documento='$qr' and estado=1";
		return ejecutarConsulta($sql);		
	}
}

?>