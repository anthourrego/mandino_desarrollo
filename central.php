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

	require_once($ruta_raiz . 'clases/funciones_generales.php');
  require_once($ruta_raiz . 'clases/sessionActiva.php');
  require_once($ruta_raiz . 'clases/librerias.php');

  //Traemos la session del usuario
  $session = new Session();
  $usuario = $session->get('usuario');

  $lib = new Libreria();

?>
<!DOCTYPE html>
<html>
<head>
	<?php  
    echo $lib->metaTagsRequired();
  ?>
	<title>Mandino</title>

	<?php  
    echo $lib->iconoPag();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->jqueryValidate();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
</head>
<body>
	<!-- Barra de Manú -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow">
    <div class="container">
      <a class="navbar-brand logo-barra" href="<?php echo RUTA_RAIZ ?>">
        <img src="img/logo.png" alt="">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto usuario-barra">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="rounded-circle" width="40px" src="http://www.consumerelectronicsgroup.com/intranet/img/usuarios/0.png">
            </a>  
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
              <span class="dropdown-item-text text-center"><?php echo $usuario['nombre'] ?></span>
              <!--<a class="dropdown-item" href="perfil"><i class="fas fa-user-edit"></i> Editar Perfil</a>-->
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo $ruta_raiz; ?>clases/sessionCerrar"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</body>
</html>