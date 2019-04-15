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

  include_once($ruta_raiz . 'clases/librerias.php');
  include_once($ruta_raiz . 'clases/sessionActiva.php');
  include_once($ruta_raiz . 'clases/Permisos.php');
  include_once($ruta_raiz . 'clases/Conectar.php');

  $session = new Session();
  $lib = new Libreria;
  $permisos = new Permisos();
  
  $usuario = $session->get("usuario");

  if ($permisos->validarPermisoPadre($usuario['id'], 1) == 0) {
    header('Location: ' . $ruta_raiz . 'modulos/cursos/cursos');
  }

  $ver_permisos = "";


  $db = new Bd();
  $db->conectar();

  $permisos = $db->consulta("SELECT * FROM mandino_permisos_usuarios INNER JOIN mandino_permisos ON mandino_permisos_usuarios.fk_mp = mandino_permisos.mp_id WHERE mandino_permisos_usuarios.fk_u = :fk_u AND mandino_permisos.fk_mp = 1", array(":fk_u" => $usuario['id']));

  for ($i=0; $i < $permisos['cantidad_registros'] ; $i++) { 
    $ver_permisos .= '<div class="card">
        <a class="card-header card-modulos d-flex justify-content-between btn btn-light" href="' . $permisos[$i]['mp_ruta'] . '">
          <h5 class="mb-0 my-auto">
             ' . $permisos[$i]['mp_icono'] . ' ' . $permisos[$i]['mp_tag'] . '
          </h5>
        </a>
      </div>';
  }

  $db->desconectar();
?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->mandino();
  ?>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container mt-4">
    <div class="accordion border-bottom" id="accordionConfiguracion">
      <?php echo($ver_permisos); ?>
    </div>
  </div>
</body>
<?php 
  echo $lib->cambioPantalla();
?>
</html>