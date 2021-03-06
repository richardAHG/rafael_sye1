<?php
require_once "../vendor/autoload.php";

require_once "../modelos/Cargo.php";
require_once "../modelos/Area.php";

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class CsvUtil
{
    const RUTA_FINAL = '../files/temp/';
    public function __construct()
    {
    }
    public static function getStructure()
    {
        return [
            'ESTADO_EMPRESA',
            'REGIMEN_LABORAL',
            'TIPO_DOC',
            'NRO_DOC',
            'APEPAT',
            'APEMAT',
            'NOMBRES',
            'REMUNERACION_BASICA',
            'ASIG_FAMILIAR',
            'CARGO',
            'SUB_AREA',
            'AREA',
            'CENTRO_COSTO',
            'FECHA_INGRESO',
            'FECHA_CESE',
            'SEXO',
            'NACIONALIDAD',
            'FECHA_NACIMIENTO',
            'ESTADO_CIVIL',
            'CELULAR',
            'TELEFONO_EMERGENCIA',
            'EMAIL',
            'DIRECCIÓN',
            'DEPARTAMENTO',
            'PROVINCIA',
            'DISTRITO',
            'NIVEL_EDUCATIVO',
            'SISTEMA_PENSIÓN',
            'CUSPP',
            // 'TIPO_COMISION',
            // 'FEHCA_SPP',
            'BANCO_SUELDO',
            'CUENTA_SUELDO',
            'INTERBANCARIO_SUELDO',
            'BANCO_CTS',
            'CUENTA_CTS',
            'CUENTA_INTERBANCARIA_CTS',
            'TIPO_CONTRATO',
            'HIJOS_MENORES',
            'HIJOS_MAYORES',
            'ACTIVIDAD',
            'GRUPO_SANGUINEO',
            'TALLA_ZAPATOS',
            'TALLA_CAMISA',
            'TALLA_PANTALÓN',
            'SCTR_SALUD',
            'SCTR_PENSIÓN',
            'PLANILLA',
            'EPS_PLAN',
            'EPS',
            'JEFE_CARGO',
        ];
    }

    public static function fieldsRequired()
    {
        return  [
            'ESTADO_EMPRESA',
            'REGIMEN_LABORAL',
            'TIPO_DOCUMENTO',
            'ASIG_FAMILIAR',
            'CENTRO_COSTO',
            'SEXO',
            'ESTADO_CIVIL',
            'NIVEL_EDUCATIVO',
            'SISTEMA_PENSION',
            // 'TIPO_COMISION',
            'TIPO_CONTRATO',
            'GRUPO_SANGUINEO',
            'SCTR_SALUD',
            'SCTR_PENSION',
            'PLANILLA',
            'PLAN',
            'BANCO_SUELDO',
            'BANCO_CTS',
            'NACIONALIDAD',
            'EPS'
        ];
    }

    public static function getDataCargo()
    {
        $obj = new Cargo();
        $rspta = $obj->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[$reg->id] = $reg->nombre;
        }
        return $data;
    }
    public static function getDataArea()
    {
        $obj = new Area();
        $rspta = $obj->selectAllByTipo(1);
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[$reg->id] = $reg->nombre;
        }
        return $data;
    }

    public static function getDataSubArea()
    {
        $obj = new Area();
        $rspta = $obj->selectAllByTipo(2);
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[$reg->id] = $reg->nombre;
        }
        return $data;
    }

    public static function LoadFileCreateJson_bk($name_archivo)
    {
        $archivo = self::load($name_archivo);
        // Separamos el string ruta, para obtener el nombre en microtime 
        [$nombre, $ext] = explode('.', $name_archivo);
        //creamos el archivo JSON
        $rutaArchivo = self::createFileJSON($nombre, json_encode($archivo));
        return $rutaArchivo;
    }
    public static function load($name_archivo)
    {
        $rutaArchivo = self::RUTA_FINAL . $name_archivo;
        $documento = IOFactory::load($rutaArchivo);
        $worksheet = $documento->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // total de filas
        $highestColumn = $worksheet->getHighestColumn(); // total de columnas en letras
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // total de columnas ne numeros
        $data = [];
        $headers = self::getHeaders($worksheet, $highestColumnIndex);
        for ($row = 1; $row <= $highestRow; ++$row) {
            // for ($col = 1; $col <= $highestColumnIndex; $col++) {
            //     $valuex = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            //     $data[$row][$col] = trim($valuex);
            // }
            foreach ($headers as $col => $value) {
                // if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($value)) {
                //     echo $value->getCoordinate().',';
                //     die();
                // }

                // if ($value == 'FECHA_INGRESO') {
                //     print_r(gettype($value));
                //     // $valuex = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                //     if (is_numeric($value)) {
                //         $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);    
                //         echo 'soy numeor '; 
                //     }else{
                //         $valuex = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                //         echo 'soy strign '; 
                //     }
                // }else{
                //     $valuex = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                // }
                $valuex = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                $data[$row][$value] = trim($valuex);
            }
        }
        $header = $data[1];
        //eliminamos la cabezera
        $data = array_splice($data, 1);
        return [
            'header' => $header,
            'data' => $data
        ];
    }

    public static function getHeaders($hojaActual, $totalColumnas)
    {
        $estructura = [];
        for ($col = 1; $col <= $totalColumnas; $col++) {
            //posicion
            $celda = $hojaActual->getCellByColumnAndRow($col, 1);
            $estructura[$col] = utf8_decode(trim($celda->getValue()));
        }
        return $estructura;
    }

    public static function createFileJSON($nameJSON, $data)
    {
        //obtiene ruta + nombre del archivo
        $nuevoNombre = self::RUTA_FINAL . $nameJSON . '.json';
        $fh = fopen($nuevoNombre, 'w');
        if (!$fh) {
            return ['error' => 'Se produjo un error al crear el archivo'];
        }
        // fwrite($fh, $data);
        if (!fwrite($fh, $data)) {
            return ['error' => 'No se pudo escribir en el archivo'];
        }
        fclose($fh);
        return $nameJSON;
    }

    public static function validateTotalHeaders($headers)
    {
        $structure = self::getStructure();
        $rpta = [];
        $errors['estado'] = true;
        if (count($structure) != count($headers)) {
            $errors['mensaje'] = 'Cantidad de columnas incorrecta';
            $errors['error'] = [];
            $errors['estado'] = false;
        }
        return $errors;
    }

    public static function validateRequiredFields($data, $namefile, $params)
    {
        $errors = [];
        $errors['estado'] = true;
        //columans requeridas
        $required = self::fieldsRequired();
        //obtener los id de parametros
        // $params = self::getParamsId();
        foreach ($required as $key => $value) {
            //obtenemos los ids de params por grupo
            $ids = $params[$value];
            if (empty($ids)) {
                return ['error' => 'columna no encontrada ' . $value];
            }
            //obtenemos un array solo de una columna especifica
            $tabla = array_column($data, $value);
            foreach ($tabla as $key => $valuex) {
                //obtenemos el id de valor buscado
                $id = array_search($valuex, $ids);
                //sobre escribir el id en la columna correspondinet del array principal
                $data[$key][$value] = $id;
            }
        }

        //Validacion de id para la tabla cargo,area y subarea
        $colums = ['CARGO', 'AREA', 'SUB_AREA'];

        foreach ($colums as $key => $value) {
            //obtenemos los ids 
            switch ($value) {
                case 'CARGO':
                    $dataT = self::getDataCargo();
                    break;
                case 'AREA':
                    $dataT = self::getDataArea();
                    break;
                case 'SUB_AREA':
                    $dataT = self::getDataSubArea();
                    break;
                default:
                    # code...
                    break;
            }
            //obtenemos un array solo de una columna especifica
            $tabla = array_column($data, $value);
            foreach ($tabla as $key => $valuex) {
                //obtenemos el id de valor buscado
                $id = array_search($valuex, $dataT);
                //sobre escribir el id en la columna correspondinet del array principal
                $data[$key][$value] = $id;
            }
        }

        //sobre escribimos el archivo json, con los nuevos datos
        $rutaJson = self::RUTA_FINAL . $namefile . '.json';
        $fh = fopen($rutaJson, 'w');
        if (!$fh) {
            $errors['estado'] = false;
            $errors['error'] = [];
            $errors['mensaje'] = 'Error al abrir fichero de salida';
            return $errors;
        }
        fwrite($fh, json_encode($data, JSON_UNESCAPED_UNICODE));
        fclose($fh);
        return $errors;
    }

    public static function validateIdparams($data, $params)
    {
        //columans requeridas
        $required = self::fieldsRequired();

        $errors = [];
        $errors['estado'] = true;
        foreach ($required as $key => $value) {
            //obtenemos los ids de params por grupo
            $ids = $params[$value];
            if (empty($ids)) {
                return ['error' => 'columna no encontrada ' . $value];
            }
            //obtenemos un array solo de una columna especifica
            $tabla = array_column($data, $value);
            //obtengo solo datos unicos
            $tablaUnique = array_unique($tabla);
            foreach ($tablaUnique as $key => $valuex) {
                //obtenemos el id de valor buscado
                if (!in_array($valuex, $ids)) {
                    $errors['mensaje'] = 'No existe los siguientes valores';
                    $errors['error'][$value][] = $valuex;
                    $errors['estado'] = false;
                }
            }
        }

        $colums = ['CARGO', 'AREA', 'SUB_AREA'];

        foreach ($colums as $key => $value) {
            //obtenemos los ids 

            switch ($value) {
                case 'CARGO':
                    $dataT = self::getDataCargo();

                    break;
                case 'AREA':
                    $dataT = self::getDataArea();
                    break;
                case 'SUB_AREA':
                    $dataT = self::getDataSubArea();
                    break;
                default:
                    # code...
                    break;
            }
            //obtenemos un array solo de una columna especifica
            $tabla = array_column($data, $value);

            //obtengo solo datos unicos
            $tablaUnique = array_unique($tabla);
            foreach ($tablaUnique as $key => $valuex) {
                //obtenemos el id de valor buscado
                //obtenemos el id de valor buscado
                if (!in_array($valuex, $dataT)) {
                    $errors['mensaje'] = 'No existe los siguientes valores';
                    $errors['error'][$value][] = $valuex;
                    $errors['estado'] = false;
                }
            }
        }

        return $errors;
    }


    public static function destroyFileTemp()
    {
        //obtenemos todos los nombres de los ficheros
        $path = self::RUTA_FINAL;
        $files = glob($path . '*');
        foreach ($files as $file) {
            if (is_file($file))
                //elimino el fichero    
                unlink($file);
        }
    }

    public static function createSheet()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet
            ->getProperties()
            ->setCreator("sye")
            ->setLastModifiedBy('sye') // última vez modificado por
            ->setTitle('Plantilla de carga masiva')
            ->setSubject('Plantilla')
            ->setDescription('Esta plantilla permite subir información ordenada al sistema')
            ->setCategory('Carga Masiva');
        return $spreadsheet;
    }

    public static function writeSheet($sheet, $header = [])
    {
        // print_r($header); die();
        try {
            $fila = 0;
            foreach ($header as $fil => $row) {
                $fila = $fil + 1;
                $columna = 1;
                foreach ($row as $col => $value) {
                    // $columna=
                    // print_r($columna); 
                    // print_r($fila); 
                    // print_r($value); die();
                    $sheet->setCellValueByColumnAndRow($columna, $fila, $value);
                    $columna++;
                }

                // $fila = 1;
                // $columna = ($key + 1);
                // $sheet->setCellValueByColumnAndRow($columna, $fila, $value);
            }
        } catch (PhpSpreadsheetException $ex) {
            throw $ex->getMessage();
        }
        return true;
    }

    public static function saveSheet($spreadsheet, $nameSheet)
    {
        try {
            // $writer = new Xlsx($spreadsheet);
            // $writer->save($ruta);
            self::download_($spreadsheet, $nameSheet);
        } catch (PhpSpreadsheetException $ex) {
            throw $ex->getMessage();
        }
        return  $nameSheet;
    }

    public static function download_($spreadsheet, $nameSheet)
    {
        $nameSheet = $nameSheet . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nameSheet . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public static function LoadFileCreateJson($archivoModel, $delimiter = ';', $codificacion = 'CP1252')
    {
        //leer csv
        $filecsv = self::RUTA_FINAL . $archivoModel;
        // print_r($filecsv); die();
        $data = self::readCsv($filecsv, $delimiter, $codificacion);

        [$nombre, $ext] = explode('.', $archivoModel);
        //creamos el archivo JSON
        $rutaArchivojson = self::createFileJSON(
            $nombre,
            json_encode($data)
        );
        return $rutaArchivojson;
    }

    public static function readCsv($path, $delimiter, $codificacion)
    {
        try {
            //leer archivo
            $reader = new Csv();
            $reader->setInputEncoding($codificacion); // codificacion correcta de entrada
            // $reader->setInputEncoding('utf-8');
            $reader->setDelimiter("{$delimiter}"); //delimitador
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0); //establece indice de la hoja a leer
            $spreadsheet = $reader->load($path);
            $worksheet = $spreadsheet->getActiveSheet();
            // obteniendo numero de filas y columas de la hoja actual
            $highestRow = $worksheet->getHighestRow(); // total de filas
            $highestColumn = $worksheet->getHighestColumn(); // total de columnas en letras
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // total de columnas ne numeros
            $data = [];
            $headers = self::getHeaders($worksheet, $highestColumnIndex);
            // print_r($headers); die();
            for ($row = 1; $row <= $highestRow; ++$row) {
                foreach ($headers as $col => $value) {
                    $valuex = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $data[$row][$value] = trim($valuex);
                }
            }
        } catch (Exception $e) {
            throw  $e->getMessage();
        }
        $header = $data[1];
        //eliminamos la cabezera
        $data = array_splice($data, 1);
        return [
            'data' => $data,
            'headers' => $header
        ];
    }

    public static function validateTypeDate($Archivojson)
    {
        $errors = [];
        $errors['estado'] = true;
        [$nombre, $ext] = explode('.', $Archivojson);
        $rutaArchivojson = self::RUTA_FINAL . $nombre . '.json';
        $str_datos = file_get_contents($rutaArchivojson);
        $datos = json_decode($str_datos, true);

        foreach ($datos['data'] as $key => $row) {
            // print_r($row);
            $fechaI = explode('/', $row['FECHA_INGRESO']);

            // $fechaF = explode('/', $row['FECHA_NACIMIENTO']);
            // $fechaspp = explode('/', $row['FEHCA_SPP']);

            if (
                count($fechaI) < 3 ||
                count($fechaI) > 3
                // count($fechaF) < 3 || 
                // count($fechaF) > 3
            ) {
                $errors['estado'] = false;
                $errors['data']['mensaje'] = 'no es una fecha valida';
                $errors['data']['tipo'] = 1;
                $errors['data']['data'][] = [
                    'fila' => $key + 2,
                    'FECHA_INGRESO' => $row['FECHA_INGRESO'],
                    'FECHA_NACIMIENTO' => $row['FECHA_NACIMIENTO'],
                ];
                continue;
            }
            if (
                !is_numeric($fechaI[0])
                || !is_numeric($fechaI[1])
                || !is_numeric($fechaI[2])
                // || !is_numeric($fechaF[0])
                // || !is_numeric($fechaF[1])
                // || !is_numeric($fechaF[2])
            ) {
                $errors['estado'] = false;
                $errors['data']['mensaje'] = 'no es una fecha valida';
                $errors['data']['tipo'] = 1;
                $errors['data']['data'][] = [
                    'fila' => $key + 2,
                    'FECHA_INGRESO' => $row['FECHA_INGRESO'],
                    'FECHA_NACIMIENTO' => $row['FECHA_NACIMIENTO'],
                ];
                continue;
            }

            if (
                !checkdate($fechaI[1], $fechaI[0], $fechaI[2])
                // || !checkdate($fechaF[1], $fechaF[0], $fechaF[2])
            ) {
                $errors['estado'] = false;
                $errors['data']['mensaje'] = 'no es una fecha valida';
                $errors['data']['tipo'] = 1;
                $errors['data']['data'][] = [
                    'fila' => $key + 2,
                    'FECHA_INGRESO' => $row['FECHA_INGRESO'],
                    'FECHA_NACIMIENTO' => $row['FECHA_NACIMIENTO'],
                ];
            }
        }
        return $errors;
    }

    public static function setFormatterDateInFileJson($Archivojson)
    {
        [$nombre, $ext] = explode('.', $Archivojson);
        $rutaArchivojson = self::RUTA_FINAL . $nombre . '.json';
        $str_datos = file_get_contents($rutaArchivojson);
        $datos = json_decode($str_datos, true);
        $null = "NULL";
        foreach ($datos['data'] as $key => $row) {

            $fecha_ingreso = str_replace('/', '-', $row['FECHA_INGRESO']);
            $fecha_nacimiento = str_replace('/', '-', $row['FECHA_NACIMIENTO']);
            // $fecha_spp = str_replace('/', '-', $row['FEHCA_SPP']);
            $fecha_ingreso = self::format($fecha_ingreso, 'Y-m-d');

            if ($row['FECHA_NACIMIENTO'] != '00/00/0000') {
                $fecha_nacimiento = self::format($fecha_nacimiento, 'Y-m-d');
                $datos['data'][$key]["FECHA_NACIMIENTO"] = $fecha_nacimiento;
            }else{
                $datos['data'][$key]["FECHA_NACIMIENTO"] = $null;
            }
            // $fecha_spp = self::format($fecha_spp, 'Y-m-d');

            $datos['data'][$key]["FECHA_INGRESO"] = $fecha_ingreso;
            
            // $datos['data'][$key]["FEHCA_SPP"] = $fecha_spp;
            // $datos['data'][$key]["FECHA_CESE"] = '0000-00-00';
        }

        $fh = fopen($rutaArchivojson, 'w');
        if (!$fh) {
            throw new Exception(404, "Error al abrir fichero de salida");
        }
        fwrite($fh, json_encode($datos, JSON_UNESCAPED_UNICODE));
        fclose($fh);
        return true;
    }
    public static function format($fecha = null, $nuevoFormato = null)
    {
        // $fecha = new DateTime($fecha);
        $fecha = new DateTime($fecha, new DateTimeZone('America/Lima'));
        return $fecha->format($nuevoFormato);
    }
}
