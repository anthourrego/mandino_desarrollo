<?php
  @session_start();
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

  require_once($ruta_raiz . "clases/Conectar.php");
  require_once($ruta_raiz . "clases/SessionActiva.php");
  require_once($ruta_raiz . "clases/funciones_generales.php");

  function datosUsuario(){
  	$db = new Bd();
  	$db->conectar();

  	$usuario = $db->consulta("SELECT * FROM usuarios WHERE u_id = :u_id", array(":u_id" => $_POST['id_usu']));

  	$db->desconectar();

  	if ($usuario['cantidad_registros'] == 1) {
  		return json_encode($usuario[0]);
  	}else{
  		return false; 
  	}
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>
