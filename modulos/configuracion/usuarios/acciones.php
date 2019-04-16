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
  require_once($ruta_raiz . "clases/Permisos.php");
  require_once($ruta_raiz . "clases/Upload.php");

  function actualizarTema(){
    $respuesta = "";
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_usuarios SET fk_mt = :fk_mt WHERE u_id = :u_id", array(":fk_mt" => $_POST['radio-tema'], ":u_id" => $_POST['id_usuario']));

    $sql_tema = $db->consulta("SELECT * FROM mandino_temas WHERE mt_id = :mt_id", array(":mt_id" => $_POST['radio-tema']));

    if ($sql_tema['cantidad_registros'] == 1) {
      
      $session = new Session();
      $session->setCampo('usuario', 'tema', $sql_tema[0]['mt_id']);
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

    $validarPass = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_id = :u_id AND u_password = :u_password", array(":u_password" => $pass, ":u_id" => $_POST['id_usuario']));

    if ($validarPass['cantidad_registros'] == 1) {
      if ($newPass == $renewPass) {
        $db->sentencia("UPDATE mandino_usuarios SET u_password = :u_password, u_cambio_pass = :u_cambio_pass WHERE u_id = :u_id", array(":u_password" => $newPass, ":u_id" => $_POST['id_usuario'], ":u_cambio_pass" => date('Y-m-d')));
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

  	$usuario = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_id = :u_id", array(":u_id" => $_POST['id_usu']));

  	$db->desconectar();

  	if ($usuario['cantidad_registros'] == 1) {
  		return json_encode($usuario[0]);
  	}else{
  		return false; 
  	}
  }

  function listaUsuario(){
    $permisos = new Permisos();
    $session = new Session();
    $usuario = $session->get('usuario');
    $db = new Bd();
    $db->conectar();
    $respuesta = "";

    if ($_POST['activo'] != 2) {
      $usuarios = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_activo = :u_activo", array(":u_activo" => $_POST['activo']));
    }else{
      $usuarios = $db->consulta("SELECT * FROM mandino_usuarios");
    }

    $db->desconectar();
    
    if ($usuarios['cantidad_registros'] > 0) {
      for ($i=0; $i < $usuarios['cantidad_registros']; $i++) { 
        $respuesta .= "<tr>
                        <td class='align-middle'>" . $usuarios[$i]['u_fecha_creacion'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_nro_documento'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_usuario'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_nombre1'] . " " . $usuarios[$i]['u_nombre2'] . " " . $usuarios[$i]['u_apellido1'] . " " . $usuarios[$i]['u_apellido2'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_telefono'] . "</td>
                        <td class='d-flex justify-content-around'>";

        if ($permisos->validarPermiso($usuario['id'], "usuarios_editar")) {
          $respuesta .= "<button class='btn btn-success' onClick='editarUsuario(". $usuarios[$i]['u_id'] .")'><i class='fas fa-user-edit'></i></button>";
        }

        if ($permisos->validarPermiso($usuario['id'], 'usuarios_permisos')) {
          $respuesta .= "<button class='btn btn-info' onClick='permisos(". $usuarios[$i]['u_id'] .")'><i class='fas fa-user-shield'></i></button>";
        }

        if ($permisos->validarPermiso($usuario['id'], "usuarios_habilitar_inhabilitar")) {
          if($usuarios[$i]['u_activo'] == 1){
            $respuesta .= "<button class='btn btn-danger' onClick='inHabilitarUsuario(" . $usuarios[$i]['u_id'] . ", 0)'><i class='fas fa-user-minus'></i></button>";
          }elseif ($usuarios[$i]['u_activo'] == 0) {
            $respuesta .= "<button class='btn btn-primary' onClick='inHabilitarUsuario(" . $usuarios[$i]['u_id'] . ", 1)'><i class='fas fa-user-check'></i></button>";
          }
        }

        $respuesta .= "</td>
                      </tr>";
                          
      }

      return $respuesta;
    }else{
      return 0;
    }
  }

  function validarUsuario(){
    $db = new Bd();
    $db->conectar();

    $usuario = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_usuario = :u_usuario", array(":u_usuario" => $_POST['usuario']));

    $db->desconectar();

    if ($usuario['cantidad_registros'] == 1) {
      return 1;
    }else{
      return 0;
    }

  }

  function validarNroDocumento(){
    $db = new Bd();
    $db->conectar();

    $usuario = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_nro_documento = :u_nro_documento", array(":u_nro_documento" => $_POST['nro_doc']));

    $db->desconectar();

    if ($usuario['cantidad_registros'] == 1) {
      return 1;
    }else{
      return 0;
    }
  }

  function crearUsuario(){
    

    $db = new Bd();
    $db->conectar();
    $respuesta = "";

    if (validarNroDocumento() == 0 AND validarUsuario() == 0) {

      if (@$_FILES['foto']['size'][0] > 0) {
        $ruta = 'foto-usuario/' . $_POST['nro_doc'] . ".jpg";
      }else{
        $ruta = 'foto-usuario/0.png';
      }

      $db->sentencia("INSERT INTO mandino_usuarios(u_nro_documento, u_usuario, u_password, u_nombre1, u_nombre2, u_apellido1, u_apellido2, u_foto, u_correo, u_telefono, fk_mt, u_fecha_creacion, u_cambio_pass, u_activo) VALUES (:u_nro_documento, :u_usuario, :u_password, :u_nombre1, :u_nombre2, :u_apellido1, :u_apellido2, :u_foto, :u_correo, :u_telefono, :fk_mt, :u_fecha_creacion, :u_cambio_pass, :u_activo)", array(":u_nro_documento" => $_POST['nro_doc'], 
              ":u_usuario" => $_POST['usuario'], 
              ":u_password" => encriptarPass($_POST['nro_doc']),
              ":u_nombre1" => $_POST['primer_nombre'], 
              ":u_nombre2" => @$_POST['segundo_nombre'],   
              "u_apellido1" => $_POST['primer_apellido'], 
              "u_apellido2" => @$_POST['segundo_apellido'], 
              ":u_foto" => $ruta, 
              ":u_correo" => $_POST['correo'], 
              ":u_telefono" => @$_POST['telefono'], 
              ":fk_mt" => 1, 
              ":u_fecha_creacion" => date('Y-m-d H:i:s'), 
              ":u_cambio_pass" => date('Y-m-d'), 
              ":u_activo" => 1
              ));

      if(@$_FILES['foto']['size'][0] > 0){ //GUARDAMOS IMAGENES
        $config_upload=array(
          'file_selector'=>'foto',
          'ruta_save'=> 'almacenamiento/' . $ruta,
          'file_name'=>$_POST['nro_doc']
        );            
                
        $up=new Upload($config_upload,@$_FILES);
        $up->save_img();
      }

      $respuesta = "Ok";
    }else{
      $respuesta = "Usuario o Nro de documento ya se encuentra registrado.";
    }

    $db->desconectar();

    return $respuesta;

  }

  function inHabilitarUsuario(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_usuarios SET u_activo = :u_activo WHERE u_id = :u_id", array(":u_id" => $_POST['id'], ":u_activo" => $_POST['activo']));

    $db->desconectar();
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }

  function editarUsuario(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_usuarios SET u_nombre1 = :u_nombre1, u_nombre2 = :u_nombre2, u_apellido1 = :u_apellido1, u_apellido2 = :u_apellido2, u_correo = :u_correo, u_telefono = :u_telefono WHERE u_id = :u_id", array(":u_nombre1" => $_POST['editNombre1'], ":u_nombre2" => $_POST['editNombre2'], ":u_apellido1" => $_POST['editApellido1'], ":u_apellido2" => $_POST['editApellido2'], ":u_correo" => $_POST['editCorreo'], ":u_telefono" => $_POST['editTelefono'], ":u_id" => $_POST['editarIdUsu']));

    $db->desconectar();
    
    return "Ok";
  }
?>
