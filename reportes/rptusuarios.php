<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['ventas'] == 1) {
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

      require_once "../modelos/Usuario.php";
      $usuario = new Usuario();

      $rspta = $usuario->listar();


      //Establecemos los datos de la empresa
      $empresa = "Anveco Textil";
      $documento = "20600554477";
      $direccion = "San Miguel - Leoncio Prado Nro 103";
      $telefono = "969400636";
      $email = "info@anvecotextil.com";

      ?>
      <div class="zona_impresion">
        <!-- codigo imprimir -->
        <br>
        <div class="container">
          <div class="row">
            <div class="col-8 justify-content-around">
              <h2 class="text-center">Reporte de Usuarios</h2>
            </div>
            <div class="col-4">
              <img src="logo_anveco.jpg" alt="" width="100">
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
                  <th>Documento</th>
                  <th>Numero</th>
                  <th>Telefono</th>
                  <th>Email</th>
                  <th>Login</th>
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
                              <td>' . $reg->tipo_documento . '</td>
                              <td>' . $reg->num_documento . '</td>
                              <td>' . $reg->telefono . '</td>
                              <td>' . $reg->email . '</td>
                              <td>' . $reg->login . '</td>
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

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    </body>

    </html>
<?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>