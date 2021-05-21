<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['Administrar'] == 1 || $_SESSION['acceso'] == 1) {
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
                <h1 class="box-title">Carga Masiva </h1>
                <div class="box-tools pull-right">
                  <a href="../ajax/carga_masiva.php?op=descarga" class="btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> Descargar</a>
                  <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button>
                </div>
              </div>
              <!-- /.box-header -->
              <!-- centro -->
              <div class="panel-body table-responsive" id="listadoregistros">
                <table id="tbllistado" data-search="true" data-show-refresh="true" data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true" data-show-columns-toggle-all="true" data-detail-view="true" data-show-export="true" data-minimum-count-columns="2" data-show-pagination-switch="true" data-pagination="true" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true">
                </table>
              </div>
              <div class="panel-body" style="height: 400px;" id="formularioregistros">
                <form name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>archivo(*):</label>
                    <input type="file" name="archivo" id="archivo" class="form-control">
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 seccion-botones">
                    <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-check" aria-hidden="true"></i> Validar</button>

                    <button class="btn btn-danger" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                  </div>
                </form>

                <div class="row">
                  <div class="col-md-12">
                  <div class="respuesta_error">
                  </div>
                  </div>
                </div>
              </div>
              <!--Fin centro -->
            </div><!-- /.box -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
    <!--Fin-Contenido-->
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script type="text/javascript" src="scripts/carga_masiva.js"></script>
<?php
}
ob_end_flush();
?>