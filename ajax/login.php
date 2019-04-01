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

  require($ruta_raiz . "clases/funciones_generales.php");
  require($ruta_raiz . "clases/Conectar.php");
  require($ruta_raiz . "clases/Session.php");

	$log_usuario = "";
	$log_password = "";

	if (isset($_POST['m_usuario']) && isset($_POST['m_password'])) {
		$log_usuario = htmlentities(addslashes($_POST['m_usuario']), ENT_QUOTES);
  	$log_password = htmlentities(addslashes($_POST['m_password']), ENT_QUOTES);
  	if (textoblanco($log_usuario) && textoblanco($log_password)) {
  		//Encriptamos la contraseña
  		$log_password = encriptarPass($log_password);
  		
  		$bd = new Bd();
  		$bd->conectar();

  		$select_usuario = $bd->consulta("SELECT * FROM usuarios WHERE usuario = :usuario AND password = :password", array(":usuario" => $log_usuario, ":password" => $log_password));

      $bd->desconectar();

  		if ($select_usuario['cantidad_registros'] == 1) {
  			
  			$session = new Session();
  			$array_session_usuario = array('nombre' => $select_usuario[0]['nombre'] . " " . $select_usuario[0]['nombre2'] . " " . $select_usuario[0]['apellido'] . " " . $select_usuario[0]['apellido2'],
  																			'usuario' => $select_usuario[0]['usuario'],
  																			'id' => $select_usuario[0]['id']
  																		);

  			$session->set('usuario', $array_session_usuario);

  			echo "Ok";
  		}else{
  			"Usuario y/o contraseña incorrecta";
  		}
  	}else{
  		echo "Algunos campos se encuentran en blanco";
  	}
	}else{
		echo "Los campos no se encuentran definidos";
	}
?>