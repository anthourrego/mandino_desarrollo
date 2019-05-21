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

      <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarSupportedContent">
        <span></span>
        <div class="w-50">
          <?php 
          if (@$_GET['curso']) {
            $db = new Bd();
            $db->conectar();
            $cont = 0; 
            $contUsu = 0;
            $porcentaje = 0;
            $bg = "bg-mandino";

            $sql_select_cantidadLecciones = $db->consulta("SELECT * FROM mandino_curso INNER JOIN mandino_unidades ON fk_mc = mc_id INNER JOIN mandino_lecciones ON fk_mu = mu_id WHERE mc_id = :mc_id", array(":mc_id" => $_GET['curso']));
            $cont = $sql_select_cantidadLecciones['cantidad_registros'];

            $sql_select_cantidadLecciones_usuario = $db->consulta("SELECT * FROM mandino_curso INNER JOIN mandino_unidades ON fk_mc = mc_id INNER JOIN mandino_lecciones ON fk_mu = mu_id INNER JOIN mandino_lecciones_visto AS mlv ON mlv.fk_ml = ml_id WHERE mc_id = :mc_id AND mlv.fk_usuario = :fk_usuario AND (mlv.mlv_taller_aprobo = 0 OR mlv.mlv_taller_aprobo = 2)", array(":mc_id" => $_GET['curso'], ":fk_usuario" => $usuario['id']));

            $contUsu = $sql_select_cantidadLecciones_usuario['cantidad_registros'];

            //Formulamos el porcentaje
            $porcentaje = ($contUsu * 100)/$cont;
            $db->desconectar();

            if (round($porcentaje) == 100) {
              $bg = "bg-info";
            }

            echo('<div class="progress">
                    <div class="progress-bar ' . $bg . '" role="progressbar" style="width: ' . round($porcentaje) . '%;" aria-valuenow="' . round($porcentaje) . '" aria-valuemin="0" aria-valuemax="100">' . round($porcentaje) . '%</div>
                  </div>');
          }
        ?>
        </div>
        <ul class="navbar-nav usuario-barra">
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

