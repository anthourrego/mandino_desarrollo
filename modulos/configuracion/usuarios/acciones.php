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

  function actualizarTema(){
    $respuesta = "";
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE usuarios SET fk_mt = :fk_mt WHERE u_id = :u_id", array(":fk_mt" => $_POST['radio-tema'], ":u_id" => $_POST['id_usuario']));

    $sql_tema = $db->consulta("SELECT * FROM mandino_temas WHERE mt_id = :mt_id", array(":mt_id" => $_POST['radio-tema']));

    if ($sql_tema['cantidad_registros'] == 1) {
      
      $session = new Session();
      $session->setCampo('usuario', 'navbar', $sql_tema[0]['mt_navbar']);
      $session->setCampo('usuario', 'logo_navbar', $sql_tema[0]['mt_logo_navbar']);

      $respuesta = "Ok";
    }else{
      $respuesta = "No se ha actualizado el tema";
    }

    $db->desconectar();

    return $respuesta;
  }

  function actualizarPass(){
    $db = new Bd();
    $db->conectar();
    $respuesta = "";

    $pass = encriptarPass($_POST['pass_actual']);
    $newPass = encriptarPass($_POST['pass_nuevo']);
    $renewPass = encriptarPass($_POST['pass_renuevo']);

    $validarPass = $db->consulta("SELECT * FROM usuarios WHERE u_id = :u_id AND u_password = :u_password", array(":u_password" => $pass, ":u_id" => $_POST['id_usuario']));

    if ($validarPass['cantidad_registros'] == 1) {
      if ($newPass == $renewPass) {
        $db->sentencia("UPDATE usuarios SET u_password = :u_password WHERE u_id = :u_id", array(":u_password" => $newPass, ":u_id" => $_POST['id_usuario']));
        $respuesta = "Ok";
      }else{
        $respuesta = "Las contraseñas no coinciden";
      }
    }else{
      $respuesta = "La contraseña actual es incorrecta";
    }

    $db->desconectar();

    return $respuesta;
  }

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
