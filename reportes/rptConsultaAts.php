<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['Administrar'] == 1 || $_SESSION['Consulta_ats']==1) {
?>
    <html>

    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous"> -->
      <!-- <link href="../public/css/ticket.css" rel="stylesheet" type="text/css"> -->
      <link rel="stylesheet" href="../public/css/bootstrap_4.5.min.css">
      <style>
        .tabla_con_bordes tr {
          border: 1px solid #339BFF;
        }

        .tabla_con_bordes th {
          background-color: #6D9EEB;
          /* color: #FFFFFF; */
          text-align: center;
          padding: 0;
        }
      </style>
    </head>

    <body onload="window.print();">

      <!-- <body> -->
      <?php

      require_once "../modelos/Ats.php";
      $ats = new Ats();

      $id=isset($_GET['id'])?$_GET['id']:'';
     
      $dataAts = $ats->reporte($id);
      
      $codigo = isset($dataAts['ats']['codigo']) ? $dataAts['ats']['codigo'] : '';
      $tipo_ats = isset($dataAts['ats']['tipo_ats']) ? $dataAts['ats']['tipo_ats'] : '';
      $fecha_hora_inicio = isset($dataAts['ats']['fecha_hora_inicio']) ? $dataAts['ats']['fecha_hora_inicio'] : '';
      $version = isset($dataAts['ats']['version']) ? $dataAts['ats']['version'] : '';
      $actividad = isset($dataAts['ats']['actividad']) ? $dataAts['ats']['actividad'] : '';
      $tipo_ats = isset($dataAts['ats']['tipo_ats']) ? $dataAts['ats']['tipo_ats'] : '';
      $distrito = isset($dataAts['ats']['distrito']) ? $dataAts['ats']['distrito'] : '';
      $direccion = isset($dataAts['ats']['direccion']) ? $dataAts['ats']['direccion'] : '';
      $hora_fin = isset($dataAts['ats']['hora_fin']) ? $dataAts['ats']['hora_fin'] : '';
      $firma_ruta = isset($dataAts['ats']['firma_ruta']) ? $dataAts['ats']['firma_ruta'] : '';
      $ep = isset($dataAts['ep']) ? $dataAts['ep'] : '';
      $ee = isset($dataAts['ee']) ? $dataAts['ee'] : '';
      $pc = isset($dataAts['pc']) ? $dataAts['pc'] : '';
      $otros_peligros = isset($dataAts['ats']['otros_peligros']) ? $dataAts['ats']['otros_peligros'] : '';
      $otros_riesgos = isset($dataAts['ats']['otros_riesgos']) ? $dataAts['ats']['otros_riesgos'] : '';
      $otras_medidas = isset($dataAts['ats']['otras_medidas']) ? $dataAts['ats']['otras_medidas'] : '';
      $personal = isset($dataAts['ats']['personal']) ? $dataAts['ats']['personal'] : '';
      $numero_documento = isset($dataAts['ats']['numero_documento']) ? $dataAts['ats']['numero_documento'] : '';
      $cargo = isset($dataAts['ats']['cargo']) ? $dataAts['ats']['cargo'] : '';
      $tipo_ats = isset($dataAts['ats']['tipo_ats']) ? $dataAts['ats']['tipo_ats'] : '';

      ?>
      <div class="container">
        <div class="row mt-3">
          <!-- <div class="col-sm-12 col-md-12"> -->
          <table class="table table-bordered tabla_con_bordes text-center" style="width:100%">
            <tr>
              <td rowspan="4" style="width:300px;">
                <img src="logo_sye.jpeg" alt="" class="img-fluid">
              </td>
              <td class="py-0">Formato</td>
              <td class="py-0">Código</td>
              <td class="py-0"><?= $codigo; ?></td>
            </tr>
            <tr>
              <td rowspan="3">ANÁLISIS DE TRABAJO SEGURO <br>
                ATS- <?= $tipo_ats; ?><br>
                Código: <?= $id . ' - ' .
                          substr($fecha_hora_inicio,0,10) ?></td>
              <td class="py-0">Versión</td>
              <td class="py-0"><?= $version; ?></td>
            </tr>
            <tr>

              <td class="py-0">Fecha</td>
              <td class="py-0">25/08/2020</td>
            </tr>
            <tr>
              <td class="py-0">Página</td>
              <td class="py-0">1 de 2</td>
            </tr>
          </table>
          <!-- </div> -->
        </div>

        <div class="row mt-3">
          <table class="table table-bordered tabla_con_bordes">
            <tr>
              <td class="py-0">ACTIVIDAD A REALIZAR:</td>
              <td class="py-0"><?= $actividad; ?></td>
              <td class="py-0">ÁREA:</td>
              <td class="py-0"><?= $tipo_ats; ?></td>
            </tr>

          </table>
        </div>
        <div class="row mt-3">
          <table class="table table-bordered tabla_con_bordes">
            <tr>
              <td class="py-0">LUGAR (DISTRITO): </td>
              <td class="py-0"><?= $distrito . ' - ' .
                                  $direccion ?></td>
            </tr>

          </table>
        </div>
        <div class="row mt-3">
          <table class="table table-bordered tabla_con_bordes ">
            <tr>
              <td class="py-0">FECHA DE ELABORACIÓN:
                <?= substr($fecha_hora_inicio,0,10) ?></td>
              <td class="py-0">HORA DE INICIO:
                <?= substr($fecha_hora_inicio,11,8) ?>
              </td>
              <td class="py-0">HORA DE TÉRMINO:
                <?= $hora_fin ?>
              </td>
            </tr>
            <tr>
              <td class="py-0">EQUIPOS DE PROTECCIÓN PERSONAL (EPP)
                GENERALES:</td>
              <td colspan="2" class="py-0"><?= $ep ?></td>
            </tr>
            <tr>
              <td class="py-0">EQUIPOS DE RESPUESTA ANTE
                EMERGENCIA (ESTACIÓN DE EMERGENCIA):</td>
              <td colspan="2" class="py-0"><?= $ee ?></td>
            </tr>
            <tr>
              <td class="py-0">DISPOSITIVO DE PROTECCIÓN COLECTIVA:</td>
              <td colspan="2" class="py-0"><?= $pc ?>
              </td>
            </tr>
          </table>
        </div>

        <div class="row mt-3">
          <table class="table table-bordered tabla_con_bordes">
            <tr>
              <th>Peligro General</th>
              <th>Riesgos Generales</th>
              <th>Medidas de Control Generales</th>
            </tr>
            <?php
            $rspta = $dataAts['peligroG'];

            while ($reg = $rspta->fetch_object()) {
              echo "<tr>
                <td class='py-0'>{$reg->peligro}</td>
                <td class='py-0'>{$reg->riesgo}</td>
                <td class='py-0'>{$reg->medida}</td>
              </tr>";
            }
            ?>

            <!-- <tr>
              <td class="py-0">Superficies contaminadas con
                virus SARS-CoV-2
              </td>
              <td class="py-0">Contacto directo con las
                superficies contaminadas . </td>
              <td class="py-0">Desinfección de toda superficie</td>
            </tr> -->
          </table>
        </div>
        <div class="row mt-3">
          <table class="table table-bordered tabla_con_bordes">
            <tr>
              <th>Sub Actividades</th>
              <th>Peligro Específicos</th>
              <th>Riesgos Específicos</th>
              <th>Medidas de Control Específicos</th>
            </tr>
            <?php
            $rspta = $dataAts['peligroE'];

            while ($reg = $rspta->fetch_object()) {
              echo "<tr>
              <td class='py-0'>{$reg->subactividad}</td>  
              <td class='py-0'>{$reg->peligro}</td>
                <td class='py-0'>{$reg->riesgo}</td>
                <td class='py-0'>{$reg->medida}</td>
              </tr>";
            }
            ?>
            <!-- <tr>
              <td class="py-0">Gasificación
              </td>
              <td class="py-0">Tubería en servicio |
                incorrecto prensado de
                tubería | instalación
                incorrecta de accesorios
                para empalmes </td>
              <td class="py-0">Inhalación de gases |
                quemaduras | incendio |
                proyección gas a presión.</td>
              <td class="py-0">Monitoreo de gas natural | extintor | uso EPP |
                traje ignífugo | no manipular la tubería de gas
                afectada | señalizar la zona | herramientas
                normadas.

              </td>
            </tr> -->
          </table>
        </div>
        <div class="row mt-3">
          <table class="table table-bordered tabla_con_bordes">
            <tr>
              <th>Otros Peligros</th>
              <th>Otros Riesgos</th>
              <th>Otros Medidas De Control</th>
            </tr>
            <tr>
              <td><?= $otros_peligros; ?></td>
              <td><?= $otros_riesgos; ?></td>
              <td><?= $otras_medidas; ?></td>
            </tr>

          </table>
        </div>
        <div class="row mt-3">
          <p>NOTA:</p>
          <p>
            Al firmar o registrarse(escanear código QR) en este documento, el trabajador indica que conoce los procedimientos, fue entrenado
            y se siente preparado para ejecutar la actividad y que su incumplimiento pone en riesgo su salud y de las demás personas. Así mismo reconoce y entiende que el uso de los EPP y las medidas de control es obligatorio y es parte de su trabajo.
          </p>
          <p>
            Si la tarea presenta riesgos que no puede controlar, NO INICIE la actividad y comunique a su jefe inmediato
          </p>
        </div>
        <div class="row mt-3">
          <table class="table table-bordered text-center tabla_con_bordes">
            <tr>
              <th colspan="5">TRABAJADORES PARTICIPANTES EN LA ACTIVIDAD</th>
            </tr>
            <tr>
              <th>DNI_CE</th>
              <th>Cargo</th>
              <th>Area</th>
              <th>Nombre Apellidos</th>
            </tr>
            <!-- <tr>
              <td class="py-0">46249467</td>
              <td class="py-0">PEON</td>
              <td class="py-0">ALVA SANCHEZ ANTERO</td>
            </tr> -->
            <?php
            $rspta = $dataAts['trabajador'];
    
            while ($reg = $rspta->fetch_object()) {
              echo "<tr>
              <td class='py-0'>{$reg->numero_documento}</td>  
              <td class='py-0'>{$reg->cargo}</td>
              <td class='py-0'>{$reg->area}</td>
                <td class='py-0'>{$reg->nombre}</td>
              </tr>";
            }
            ?>
          </table>
        </div>
        <div class="row mt-3">
          <table class="table table-bordered text-center tabla_con_bordes">
            <tr class="table-primary">
              <th colspan="5">RESPONSABLE DEL TRABAJO</th>
            </tr>
            <tr>
              <td colspan="5">Nombre Apellidos: <?= $personal ?></td>
            </tr>
            <tr>
              <td class="py-0">Firma:  <img src="<?= $firma_ruta ?>" width="100" height="100" >   </td>
              <td class="py-0">DNI_CE: <?= $numero_documento ?></td>
              <td class="py-0">Cargo: <?= $cargo ?></td>
              <td class="py-0">Área: <?= $tipo_ats ?></td>
            </tr>
          </table>
        </div>

      </div>

    </body>

    </html>
<?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>