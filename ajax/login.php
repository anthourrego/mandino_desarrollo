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

  		$select_usuario = $bd->consulta("SELECT * FROM usuarios WHERE u_usuario = :usuario AND u_password = :password AND u_activo = 1", array(":usuario" => $log_usuario, ":password" => $log_password));

      if ($select_usuario['cantidad_registros'] == 0) {
        $select_usuario = $bd->consulta("SELECT * FROM usuarios WHERE u_nro_documento = :usuario AND u_password = :password AND u_activo = 1", array(":usuario" => $log_usuario, ":password" => $log_password));
      }

  		if ($select_usuario['cantidad_registros'] == 1) {

        $config = $bd->consulta("SELECT * FROM mandino_temas WHERE mt_id = :mt_id LIMIT 1", array(":mt_id" => $select_usuario[0]['fk_mt']));
        
        if ($config['cantidad_registros'] != 0) {
          $navbar = $config[0]['mt_navbar'];
          $logo_navbar = $config[0]['mt_logo_navbar'];
        }else{
          $navbar = "navbar-light bg-white";
          $logo_navbar = "logo.png";
        }

        $bd->desconectar();

        $session = new Session();

  			$array_session_usuario = array('nombre' => $select_usuario[0]['u_nombre1'] . " " . $select_usuario[0]['u_nombre2'] . " " . $select_usuario[0]['u_apellido1'] . " " . $select_usuario[0]['u_apellido2'],
  																			'usuario' => $select_usuario[0]['u_usuario'],
                                        'foto' => $select_usuario[0]['u_foto'],
  																			'id' => $select_usuario[0]['u_id'],
                                        'navbar' => $navbar,
                                        'logo_navbar' => $logo_navbar,
  																		);

  			$session->set('usuario', $array_session_usuario);

  			echo "Ok";
  		}else{
  			echo "Usuario y/o contraseña incorrecta";
  		}
  	}else{
  		echo "Algunos campos se encuentran en blanco";
  	}
	}else{
		echo "Los campos no se encuentran definidos";
	}
?>