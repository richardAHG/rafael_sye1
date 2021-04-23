<?php
if (strlen(session_id()) < 1) 
  session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>S&E | www.sye.com</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../public/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../public/css/font-awesome.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../public/css/_all-skins.min.css">
    <link rel="apple-touch-icon" href="../public/img/apple-touch-icon.png">
    <link rel="shortcut icon" href="../public/img/favicon.ico">

    <!-- bootstrapTable -->
  <link href="../public/bt/bootstrap-table.min.css" rel="stylesheet"/>

    <!-- DATATABLES -->
    <link rel="stylesheet" type="text/css" href="../public/datatables/jquery.dataTables.min.css">    
    <link href="../public/datatables/buttons.dataTables.min.css" rel="stylesheet"/>
    <link href="../public/datatables/responsive.dataTables.min.css" rel="stylesheet"/>

    <link rel="stylesheet" type="text/css" href="../public/css/bootstrap-select.min.css">
    <!-- <link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet"> -->
  

  </head>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <header class="main-header">

        <!-- Logo -->
        <a href="index2.html" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>S&E</b></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>S & E</b></span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Navegación</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->
              
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="user-image" alt="User Image">
                  <span class="hidden-xs"><?php echo $_SESSION['nombre']; ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="img-circle" alt="User Image">
                    <p>
                      S & E
                      <small>Comercializadora</small>
                    </p>
                  </li>
                  
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    
                    <div class="pull-right">
                      <a href="../ajax/usuario.php?op=salir" class="btn btn-default btn-flat">Cerrar</a>
                    </div>
                  </li>
                </ul>
              </li>
              
            </ul>
          </div>

        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">       
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header"></li>
            <?php 
            if ($_SESSION['escritorio']==1)
            {
              echo '<li id="mEscritorio">
              <a href="escritorio.php">
                <i class="fa fa-tasks"></i> <span>Inicio</span>
              </a>
            </li>';
            }
            ?>
            <?php 
            if ($_SESSION['Administrar']==1)
            {
              echo '<li id="mAlmacen" class="treeview">
              <a href="#">
                <i class="fa fa-laptop"></i>
                <span>Administrar ATS</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                
                <li id="lSubActividad"><a href="subActividad.php"><i class="fa fa-circle-o"></i> SubActividad</a></li>
                <li id="ltiposAts"><a href="tipos_ats.php"><i class="fa fa-circle-o"></i> ATS versi&oacute;n</a></li>
                <li id="lDistrito"><a href="distrito.php"><i class="fa fa-circle-o"></i> Distrito</a></li>
                <li id="lPeligro"><a href="peligro.php"><i class="fa fa-circle-o"></i> Peligro</a></li>
                <li id="lRiesgo"><a href="riesgo.php"><i class="fa fa-circle-o"></i> Riesgo</a></li>
                <li id="lMedidaControl"><a href="medida_control.php"><i class="fa fa-circle-o"></i> Medida de Control</a></li>
                <li id="lEquipo"><a href="equipo.php"><i class="fa fa-circle-o"></i> Equipo</a></li>
                <li id="lGestionPeligrosG"><a href="gestion_peligrosG.php"><i class="fa fa-circle-o"></i> Gestion Peligros Generales</a></li>
                <li id="lGestionPeligrosE"><a href="gestion_peligrosE.php"><i class="fa fa-circle-o"></i> Gestion Peligros Especificos</a></li>
                <li id="lJefeAts"><a href="jefe_ats.php"><i class="fa fa-circle-o"></i> Jefe ATS</a></li>
              </ul>
            </li>';
            }
            ?>

            <?php 
            if ($_SESSION['Asignar']==1)
            {
              echo '<li id="mAsignar" class="treeview">
              <a href="#">
                <i class="fa fa-folder"></i> <span>Administrar Padr&oacute;n</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li id="lParametro"><a href="parametro.php"><i class="fa fa-circle-o"></i> Parametro</a></li>
                <li id="lJefeCargo"><a href="jefe_cargo.php"><i class="fa fa-circle-o"></i> Jefe a Cargo</a></li>
                <li id="lArea"><a href="area.php"><i class="fa fa-circle-o"></i> Area</a></li>
                <li id="lCargo"><a href="cargo.php"><i class="fa fa-circle-o"></i> Cargo</a></li>
                <li id="lPermisos"><a href="permiso.php"><i class="fa fa-circle-o"></i> Permisos</a></li>
              </ul>
            </li>';
            }
            ?>

            <?php 
            if ($_SESSION['acceso']==1)
            {
              echo '<li id="mAcceso" class="treeview">
              <a href="#">
                <i class="fa fa-folder"></i> <span>Padr&oacute;n</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li id="lUsuarios"><a href="usuario.php"><i class="fa fa-circle-o"></i> Agregar Personal</a></li>
                <li id="lCargaMasiva"><a href="carga_masiva.php"><i class="fa fa-circle-o"></i> Carga Masiva</a></li>
                
              </ul>
            </li>';
            }
            ?>
            
             <?php
              if ($_SESSION['Consulta_ats'] == 1) {
                echo '<li id="mConsultaC" class="treeview">
                  <a href="#">
                    <i class="fa fa-bar-chart"></i> <span>Consulta ATS</span>
                    <i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
                    <li id="lConsulasC"><a href="consultaAts_fecha.php"><i class="fa fa-circle-o"></i> Consulta Fecha</a></li>
                    <li id="lConsulasP"><a href="consultaAts_participante.php"><i class="fa fa-circle-o"></i> Consulta Participante</a></li>                
                  </ul>
                </li>';
              }
              ?>
            
            <!-- <li>
              <a href="ayuda.php">
                <i class="fa fa-plus-square"></i> <span>Ayuda</span>
                <small class="label pull-right bg-red">PDF</small>
              </a>
            </li>
            <li>
              <a href="acerca.php">
                <i class="fa fa-info-circle"></i> <span>Acerca De...</span>
                <small class="label pull-right bg-yellow">IT</small>
              </a>
            </li> -->
                        
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
