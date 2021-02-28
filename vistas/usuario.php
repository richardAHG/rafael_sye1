<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';
  if ($_SESSION['acceso'] == 1) {
?>
    <!--Contenido-->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Usuario </h1>
                <div class="box-tools pull-right">
                  <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <!-- <a href="../reportes/rptusuarios.php" target="_blank"><button class="btn btn-info"><i class="fa fa-clipboard"></i> Reporte</button></a> -->
                </div>
              </div>
              <!-- /.box-header -->
              <!-- centro -->
              <div class="panel-body table-responsive" id="listadoregistros">
              <table id="tbllistado" data-search="true" data-show-refresh="true" data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true" data-show-columns-toggle-all="true" data-detail-view="true" data-show-export="true" data-minimum-count-columns="2" data-show-pagination-switch="true" data-pagination="true" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true">
                </table>
              </div>
              <div class="panel-body" id="formularioregistros">
                <form name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Nombre(*):</label>
                    <input type="hidden" name="idusuario" id="idusuario">
                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Nombre" required>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Apellido Paterno(*):</label>
                    <input type="text" class="form-control" name="ape_pat" id="ape_pat" maxlength="100" placeholder="Apellido Paterno" required>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Apellido Materno(*):</label>
                    <input type="text" class="form-control" name="ape_mat" id="ape_mat" maxlength="100" placeholder="Apellido Materno" required>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Email:</label>
                    <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Cargo(*):</label>
                    <select id="cargo_id" name="cargo_id" class="form-control selectpicker" data-live-search="true" required></select>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Regimen(*):</label>
                    <select id="regimen_id" name="regimen_id" class="form-control selectpicker" data-live-search="true" required></select>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Dirección:</label>
                    <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección" maxlength="70">
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Celular:</label>
                    <input type="text" class="form-control" name="cell" id="cell" maxlength="20" placeholder="Celular">
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Tipo Documento(*):</label>
                    <select id="tipo_documento" name="tipo_documento" class="form-control selectpicker" data-live-search="true" required></select>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Número(*):</label>
                    <input type="text" class="form-control" name="numero_documento" id="numero_documento" maxlength="20" placeholder="Documento" required>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Area(*):</label>
                    <select id="area_id" name="area_id" class="form-control selectpicker" data-live-search="true" required></select>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Sub Area(*):</label>
                    <select id="subarea_id" name="subarea_id" class="form-control selectpicker" data-live-search="true" required></select>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Fecha de ingreso:</label>
                    <input type="text" class="form-control" name="fecha_ingreso" id="fecha_ingreso" maxlength="20" placeholder="Fecha ingreso">
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Fecha de cese:</label>
                    <input type="text" class="form-control" name="fecha_cese" id="fecha_cese" maxlength="20" placeholder="Fecha Cese">
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Login (*):</label>
                    <input type="text" class="form-control" name="login" id="login" maxlength="20" placeholder="Login" required>
                    <p class='text-danger '>Se recomienda ingresar numero de documento</p>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12 clave">

                    <label>Clave (*):</label>
                    <input type="password" class="form-control" name="clave" id="clave" maxlength="64" placeholder="Clave" required>
                    <p class='text-danger '>Se recomienda ingresar numero de documento</p>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Permisos:</label>
                    <ul style="list-style: none;" id="permisos">

                    </ul>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Imagen:</label>
                    <input type="file" class="form-control" name="imagen" id="imagen">
                    <input type="hidden" name="imagenactual" id="imagenactual">
                    <img src="" width="150px" height="120px" id="imagenmuestra">
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>

                    <button class="btn btn-danger" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                    <span data-toggle="modal" data-target="#myModal" class="btn btn-warning" id="btnEditClave"><i class="fa fa-edit"></i> Clave</span>
                  </div>
                </form>
              </div>
              <!--Fin centro -->
            </div><!-- /.box -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
    <!--Fin-Contenido-->

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Actualiza Password</h4>
          </div>
          <div class="modal-body">
            <form id="frmEditClave">
              <!--<div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">-->
              <label>Ingrese Password actual</label>
              <input type="password" name="passActual" id="passActual" class="form-control" placeholder="Password Actual">
              <label>Ingrese Nueva Password</label>
              <input type="password" name="passNuevo" id="passNuevo" class="form-control" placeholder="Nuevo Password">

              <!--</div>-->

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary" id="EditClave">Guardar</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Fin Modal -->
  <?php
  } else {
    require 'noacceso.php';
  }
  require 'footer.php';
  ?>

  <script type="text/javascript" src="scripts/usuario.js"></script>
<?php
}
ob_end_flush();
?>