<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['Administrar'] == 1) {
?>
    <html>

    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous"> -->
      <!-- <link href="../public/css/ticket.css" rel="stylesheet" type="text/css"> -->
      <link rel="stylesheet" href="../public/css/bootstrap_4.5.min.css">

    </head>

    <body onload="window.print();">

      <!-- <body> -->
      <?php

      require_once "../modelos/Equipo.php";
      $equipo = new Equipo();


      $rspta = $equipo->listar();


      //Establecemos los datos de la empresa
      $empresa = "S&E";
      $documento = "123654789";
      $direccion = "Chorrillos Av. Huaylas";
      $telefono = "969400636";
      $email = "info@sye.com";

      ?>
      <div class="zona_impresion">
        <!-- codigo imprimir -->
        <br>
        <div class="container">
          <div class="row">
            <div class="col-8 justify-content-around">
              <h2 class="text-center">Reporte de Equipos</h2>
            </div>
            <div class="col-4">
              <img src="logo_sye.jpeg" alt="" width="100">
            </div>

          </div>
          <div class="row">

            <div class="col-12">
              <p>Empresa : <?php echo $empresa; ?></p>
              <p>Ruc: <?php echo $documento; ?></p>
              <div class="d-flex justify-content-between">
                <p>Direccion: <?php echo $direccion; ?></p>
                <p>Telefono: <?php echo $telefono; ?></p>
              </div>

            </div>
          </div>
          <div class="row">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Tipo</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                while ($reg = $rspta->fetch_object()) {
                  echo '
                            <tr>
                              <td>' . $i . '</td>
                              <td>' . $reg->nombre . '</td>
                              <td>' . $reg->tipo . '</td>
                          
                            </tr>';
                  $i++;
                } ?>
              </tbody>
            </table>

            <br>
            <!-- Mostramos los detalles de la venta en el documento HTML -->

            <br>
          </div>
          <p>&nbsp;</p>

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