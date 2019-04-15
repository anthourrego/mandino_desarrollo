	<!-- Barra de Manú -->
  <?php  
    $max_salida=10; // Previene algun posible ciclo infinito limitando a 10 los ../
    $ruta_raiz=$ruta="";
    while($max_salida>0){
      if(@is_file($ruta.".htaccess")){
        $ruta_raiz=$ruta; //Preserva la ruta superior encontrada
        break;
      }
      $ruta.="../";
      $max_salida--;
    }

    require_once($ruta_raiz . "clases/Permisos.php");

    $permisos = new Permisos();
    $configuracion = "";

    if ($permisos->validarPermisoPadre($usuario['id'], 1) == 1) {
      $configuracion = '<a class="dropdown-item" href="' . RUTA_RAIZ . 'modulos/configuracion"><i class="fas fa-cogs"></i> Configuración</a>';
    }
  ?>

  <nav class="navbar navbar-expand-lg <?php echo $usuario['navbar'] ?> border-bottom shadow">
    <div class="container">
      <a class="navbar-brand logo-barra" href="<?php echo RUTA_RAIZ ?>modulos/cursos/cursos">
        <img src="<?php echo $ruta_raiz; ?>img/<?php echo $usuario['logo_navbar']; ?>" alt="">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto usuario-barra">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="rounded-circle" width="40px" src="<?php if($usuario['foto'] != NULL){ echo RUTA_ALMACENAMIENTO . $usuario['foto']; }else{ echo(RUTA_ALMACENAMIENTO . "foto-usuario/0.png");} ?>">
            </a>  
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
              <span class="dropdown-item-text text-center"><?php echo $usuario['nombre'] ?></span>
              <a class="dropdown-item modal-link" href="<?php echo RUTA_RAIZ ?>modulos/configuracion/usuarios/editar_perfil"><i class="fas fa-user-edit"></i> Perfil</a>
              <?php 
                echo($configuracion);
              ?>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" onclick="top.cerrarSesion();"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>

