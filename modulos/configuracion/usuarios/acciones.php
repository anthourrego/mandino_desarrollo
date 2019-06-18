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
      $usuarios = $db->consulta("SELECT * FROM mandino_usuarios INNER JOIN municipios ON m_id = fk_ciudad WHERE u_activo = :u_activo", array(":u_activo" => $_POST['activo']));
    }else{
      $usuarios = $db->consulta("SELECT * FROM mandino_usuarios INNER JOIN municipios ON m_id = fk_ciudad");
    }

    $db->desconectar();
    
    if ($usuarios['cantidad_registros'] > 0) {
      for ($i=0; $i < $usuarios['cantidad_registros']; $i++) { 
        $respuesta .= "<tr>
                        <td class='align-middle'>" . $usuarios[$i]['m_nombre'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_nro_documento'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_usuario'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_nombre1'] . " " . $usuarios[$i]['u_nombre2'] . " " . $usuarios[$i]['u_apellido1'] . " " . $usuarios[$i]['u_apellido2'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_telefono'] . "</td>
                        <td class='d-flex justify-content-around'>";
        if ($permisos->validarPermiso($usuario['id'], "usuarios_taller_intentos")) {
          $respuesta .= "<button class='btn btn-info' onClick='estadoUsuario(". $usuarios[$i]['u_id'] .")'><i class='far fa-calendar-check'></i></button>";
        }

        if ($permisos->validarPermiso($usuario['id'], "usuarios_editar")) {
          $respuesta .= "<button class='btn btn-success' onClick='editarUsuario(". $usuarios[$i]['u_id'] .")'><i class='fas fa-user-edit'></i></button>";
        }

        if ($permisos->validarPermiso($usuario['id'], 'usuarios_permisos')) {
          $respuesta .= "<button class='btn btn-info' onClick='permisos(". $usuarios[$i]['u_id'] .")'><i class='fas fa-user-shield'></i></button>";
        }

        if ($permisos->validarPermiso($usuario['id'], 'usuarios_cursos')) {
          $respuesta .= "<button class='btn btn-secondary' onClick='cursos(". $usuarios[$i]['u_id'] .")'><i class='fas fa-book'></i></button>";
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

      $db->sentencia("INSERT INTO mandino_usuarios(u_nro_documento, u_usuario, u_password, u_nombre1, u_nombre2, u_apellido1, u_apellido2, u_foto, u_correo, u_telefono, fk_mt, u_fecha_creacion, u_cambio_pass, u_activo, fk_ciudad) VALUES (:u_nro_documento, :u_usuario, :u_password, :u_nombre1, :u_nombre2, :u_apellido1, :u_apellido2, :u_foto, :u_correo, :u_telefono, :fk_mt, :u_fecha_creacion, :u_cambio_pass, :u_activo, :fk_ciudad)", array(":u_nro_documento" => $_POST['nro_doc'], 
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
              ":u_activo" => 1,
              ":fk_ciudad" => $_REQUEST['ciudades']
              ));

      if(isset($_POST['cursos'])){
        $id_usu = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_nro_documento = :u_nro_documento", array(":u_nro_documento" => $_POST['nro_doc']) );

        foreach ($_POST['cursos'] as $cursos) {
          $db->sentencia("INSERT INTO mandino_curso_usuario(fk_mc, id_usuario, fecha_creacion, id_creador, mcu_activo) VALUES(:fk_mc, :id_usuario, :fecha_creacion, :id_creador, 1)", array(":fk_mc" => $cursos, ":id_usuario" => $id_usu[0]['u_id'], ":fecha_creacion" => date('Y-m-d H:i:s'), ":id_creador" => $_POST['usuarioCreador']));
        }

      }


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

  function editarUsuario(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_usuarios SET u_nombre1 = :u_nombre1, u_nombre2 = :u_nombre2, u_apellido1 = :u_apellido1, u_apellido2 = :u_apellido2, u_correo = :u_correo, u_telefono = :u_telefono, fk_ciudad = :fk_ciudad WHERE u_id = :u_id", array(":u_nombre1" => $_POST['editNombre1'], ":u_nombre2" => $_POST['editNombre2'], ":u_apellido1" => $_POST['editApellido1'], ":u_apellido2" => $_POST['editApellido2'], ":u_correo" => $_POST['editCorreo'], ":u_telefono" => $_POST['editTelefono'], ":u_id" => $_POST['editarIdUsu'], ":fk_ciudad" => $_POST['editciudades']));

    $db->desconectar();
    
    return "Ok";
  }

  function listaCursoUsuarios(){
    $resp = "";
    $db = new Bd();
    $db->conectar();

    $cursos = $db->consulta("SELECT * FROM mandino_curso");

    for ($i=0; $i < $cursos['cantidad_registros']; $i++) { 
      $cursos_usuarios = $db->consulta("SELECT * FROM mandino_curso_usuario WHERE fk_mc = :fk_mc AND id_usuario = :id_usuario AND mcu_activo = 1", array(":fk_mc" => $cursos[$i]['mc_id'], ":id_usuario" => $_POST['idUsu']));

      if ($cursos_usuarios['cantidad_registros'] == 1) {
        $resp .= '<div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" value="' . $cursos[$i]['mc_id'] . '" class="custom-control-input" name="cursoEditar[]" id="curso' . $cursos[$i]['mc_id'] . '" checked>
                    <label class="custom-control-label" for="curso' . $cursos[$i]['mc_id'] . '">' . $cursos[$i]['mc_nombre'] . '</label>
                  </div>';
      }else{
        $resp .= '<div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" value="' . $cursos[$i]['mc_id'] . '" class="custom-control-input" name="cursoEditar[]" id="curso' . $cursos[$i]['mc_id'] . '">
                    <label class="custom-control-label" for="curso' . $cursos[$i]['mc_id'] . '">' . $cursos[$i]['mc_nombre'] . '</label>
                  </div>';
      }

    }

    $cursos_usuarios = "";

    $db->desconectar();

    return $resp;
  }

  function editarCursosUsuario(){
    $resp = "";
    $db = new Bd();
    $db->conectar();

    //Se dehabilitan todos los cursos del usuario
    $db->sentencia("UPDATE mandino_curso_usuario SET mcu_activo = 0 WHERE id_usuario = :id_usuario", array(":id_usuario" => $_REQUEST['cursoUsuId']));
    
    //Validamos si seleccionaron los cheackbox
    if (@$_REQUEST['cursoEditar']) {
      foreach ($_REQUEST['cursoEditar'] as $curso) {
        $validar = $db->consulta("SELECT * FROM mandino_curso_usuario WHERE id_usuario = :id_usuario AND fk_mc = :fk_mc", array(":id_usuario" => $_REQUEST['cursoUsuId'], ":fk_mc" => $curso));
        if ($validar['cantidad_registros'] == 1) {
          $db->sentencia("UPDATE mandino_curso_usuario SET mcu_activo = 1 WHERE id_usuario  = :id_usuario AND fk_mc = :fk_mc", array(":id_usuario" => $_REQUEST['cursoUsuId'], ":fk_mc" => $curso));
        }else{
          $db->sentencia("INSERT INTO mandino_curso_usuario(fk_mc, id_usuario, fecha_creacion, id_creador, mcu_activo) VALUES(:fk_mc, :id_usuario, :fecha_creacion, :id_creador, :mcu_activo)", array(":fk_mc" => $curso, ":id_usuario" => $_REQUEST['cursoUsuId'], ":fecha_creacion" => date('Y-m-d H:i:s'), ":id_creador" => $_REQUEST['cursoUsuIdCreador'], ":mcu_activo" =>1));
        }
      }
    }
    

    $db->desconectar();
    return $resp;
  }

  function listaCursosUsuarioProgreso(){
    $db = new Bd();
    $db->conectar();
    $resp = "";
    
    $listaCursos = $db->consulta("SELECT * FROM mandino_curso_usuario INNER JOIN mandino_curso ON mc_id = fk_mc WHERE id_usuario = :id_usuario AND mcu_activo = 1", array(":id_usuario" => $_REQUEST['idUsu']));

    for ($i=0; $i < $listaCursos['cantidad_registros']; $i++) { 
      $resp .= '<button type="button" value="' . $listaCursos[$i]['fk_mc'] . '" class="evaluaciones list-group-item list-group-item-action d-flex justify-content-between"><span>' . $listaCursos[$i]['mc_nombre'] . '</span><span>' . porcentajeCurso($listaCursos[$i]['fk_mc'], $_REQUEST['idUsu']) . '%</span></button>';
    }

    $db->desconectar();

    return $resp;
  }

  function talleresRealizados(){
    $db = new Bd();
    $resp = "";
    $db->conectar();

    $sql = $db->consulta('SELECT mlv.mlv_id AS mlv_id, mu.mu_nombre AS nombre_unidad, mlv.mlv_taller_intento_adicional AS intento_adicional FROM mandino_lecciones AS ml INNER JOIN mandino_lecciones_visto AS mlv ON ml.ml_id = mlv.fk_ml INNER JOIN mandino_unidades AS mu ON ml.fk_mu = mu.mu_id WHERE ml.fk_mt <> "NULL" AND mu.fk_mc = :fk_mc AND mlv.fk_usuario = :idUsu', 
                          array(":fk_mc" => $_REQUEST['idCurso'], ":idUsu" => $_REQUEST['idUsu']));
    
    if ($sql['cantidad_registros'] > 0) {
      for ($i=0; $i < $sql['cantidad_registros']; $i++) { 
        $resp .= '<div class="row">
                    <div class="col-6 align-self-center">
                      <span>' . $sql[$i]['nombre_unidad'] . '</span>
                    </div>
                    <div class="col-6 align-self-center">
                      <div class="input-group spinner">
                        <div class="input-group-prepend">
                          <button value="' . $sql[$i]['mlv_id'] . '" class="btn text-monospace minus btn-primary" type="button">-</button>
                        </div>
                        <input type="number" class="count form-control" disabled="true" min="0" max="30" step="0" value="'. $sql[$i]['intento_adicional'] .'">
                        <div class="input-group-append">
                          <button value="' . $sql[$i]['mlv_id'] . '" class="btn text-monospace plus btn-primary" type="button">+</button>
                        </div>
                      </div>
                    </div>
                  </div>';
        if($sql['cantidad_registros'] > 1 && ($sql['cantidad_registros']-1) != $i){
          $resp .= "<hr>";
        }      
      }
    }else{
      $resp .= "<p class='text-center'>No se ha realizado ninguna evaluación</p>";
    }
    

    $db->desconectar();
    return $resp;
  }

  function actualizarIntentosTaller(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_lecciones_visto SET mlv_taller_intento_adicional = :intento WHERE mlv_id = :mlv_id", array(":mlv_id" => $_REQUEST['idMLV'], ":intento" =>$_REQUEST['intentos']));

    $db->desconectar();

    return "Ok";
  }

  function porcentajeCurso($curso, $usuario){
    $db = new Bd();
    $db->conectar();
    $cont = 0; 
    $contUsu = 0;
    $porcentaje = 0;

    $sql_select_cantidadLecciones = $db->consulta("SELECT * FROM mandino_curso INNER JOIN mandino_unidades ON fk_mc = mc_id INNER JOIN mandino_lecciones ON fk_mu = mu_id WHERE mc_id = :mc_id", array(":mc_id" => $curso));
    $cont = $sql_select_cantidadLecciones['cantidad_registros'];

    $sql_select_cantidadLecciones_usuario = $db->consulta("SELECT * FROM mandino_curso INNER JOIN mandino_unidades ON fk_mc = mc_id INNER JOIN mandino_lecciones ON fk_mu = mu_id INNER JOIN mandino_lecciones_visto AS mlv ON mlv.fk_ml = ml_id WHERE mc_id = :mc_id AND mlv.fk_usuario = :fk_usuario", array(":mc_id" => $curso, ":fk_usuario" => $usuario));

    $contUsu = $sql_select_cantidadLecciones_usuario['cantidad_registros'];

    //Formulamos el porcentaje
    $porcentaje = ($contUsu * 100)/$cont;

    $db->desconectar();
    return round($porcentaje);
  }

  function departamentos(){
    $db = new Bd();
    $db->conectar();

    $departamentos = $db->consulta("SELECT * FROM departamentos");

    $db->desconectar();

    return json_encode($departamentos);
  }

  function ciudades(){
    $db = new Bd();
    $db->conectar();

    $municipios = $db->consulta("SELECT * FROM municipios WHERE fk_departamento = :fk_departamento", array(":fk_departamento" => $_REQUEST['dep']));

    $db->desconectar();

    return json_encode($municipios);
  }

  function editCiudades(){
    $db = new Bd();
    $db->conectar();
    $dep = 1;

    if($_REQUEST['m_id'] != 0){
      $sql = $db->consulta("SELECT * FROM municipios WHERE m_id = :m_id", array(":m_id" => $_REQUEST['m_id']));
      $dep = $sql[0]['fk_departamento'];
    }

    $municipios = $db->consulta("SELECT * FROM municipios WHERE fk_departamento = :fk_departamento", array(":fk_departamento" => $dep));  

    $db->desconectar();

    return json_encode($municipios);
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>
