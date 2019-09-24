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
    $per = 0;
    $log = 0;
    $ingresar_usuario = 0;
    $taller_intentos = 0;
    $editar = 0;
    $cursos = 0;
    $inhabilitar = 0; 

    $usuarios = $db->consulta("SELECT * FROM mandino_usuarios INNER JOIN municipios ON m_id = fk_ciudad WHERE u_activo = :u_activo", array(":u_activo" => $_GET['habilitado']));

    $db->desconectar();
    
    if ($usuarios['cantidad_registros'] > 0) {
      if ($permisos->validarPermiso($usuario['id'], "usuarios_logs")) {
        $log = 1;
      }

      if ($permisos->validarPermiso($usuario['id'], "usuarios_taller_intentos")) {
        $taller_intentos = 1;
      }

      if ($permisos->validarPermiso($usuario['id'], "usuarios_editar")) {
        $editar = 1;
      }

      if ($permisos->validarPermiso($usuario['id'], 'usuarios_permisos')) {
        $per = 1;
      }

      if ($permisos->validarPermiso($usuario['id'], 'usuarios_cursos')) {
        $cursos = 1;
      }

      if ($permisos->validarPermiso($usuario['id'], "usuarios_habilitar_inhabilitar")) {
        $inhabilitar = 1;
      }

      if ($permisos->validarPermiso($usuario['id'], "ingresar_usuario")){
        $ingresar_usuario = 1;
      }

      for ($i=0; $i < $usuarios['cantidad_registros']; $i++) { 

        $respuesta .= "<tr>
                        <td class='align-middle'>" . $usuarios[$i]['m_nombre'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_nro_documento'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_usuario'] . "</td>
                        <td class='align-middle'>" . $usuarios[$i]['u_nombre1'] . " " . $usuarios[$i]['u_nombre2'] . " " . $usuarios[$i]['u_apellido1'] . " " . $usuarios[$i]['u_apellido2'] . "</td>
                        <td class='d-flex justify-content-around'>";
        
        if($ingresar_usuario == 1){
          $respuesta .= "<button class='btn btn-info' onClick='verUsuario(". $usuarios[$i]['u_id'] .")' data-toggle='tooltip' title='Ingresar a usuario'><i class='far fa-address-card'></i></button>";
        }
        
        if ($log == 1) {
          $respuesta .= "<button class='btn btn-info' onClick='logUsuarios(". $usuarios[$i]['u_id'] .")' data-toggle='tooltip' title='Logs'><i class='fas fa-list-alt'></i></button>";
        }
        
        if ($taller_intentos == 1) {
          $respuesta .= "<button class='btn btn-info' onClick='estadoUsuario(". $usuarios[$i]['u_id'] .")' data-toggle='tooltip' title='Progreso'><i class='far fa-calendar-check'></i></button>";
        }

        if ($editar == 1) {
          $respuesta .= "<button class='btn btn-success' onClick='editarUsuario(". $usuarios[$i]['u_id'] .")' data-toggle='tooltip' title='Editar'><i class='fas fa-user-edit'></i></button>";
        }

        if ($per == 1) {
          $respuesta .= "<button class='btn btn-info' onClick='permisos(". $usuarios[$i]['u_id'] .")' data-toggle='tooltip' title='Permisos'><i class='fas fa-user-shield'></i></button>";
        }

        if ($cursos == 1) {
          $respuesta .= "<button class='btn btn-secondary' onClick='cursos(". $usuarios[$i]['u_id'] .")' data-toggle='tooltip' title='Cursos'><i class='fas fa-book'></i></button>";
        }

        if ($inhabilitar == 1) {
          if ($usuarios[$i]['u_activo'] == 1) {
            $respuesta .= "<button class='btn btn-danger' onClick='inHabilitarUsuario(" . $usuarios[$i]['u_id'] . ", 0)' data-toggle='tooltip' title='Inhabilitar'><i class='fas fa-user-minus'></i></button>";
          } else {
            $respuesta .= "<button class='btn btn-primary' onClick='inHabilitarUsuario(" . $usuarios[$i]['u_id'] . ", 1)' data-toggle='tooltip' title='Habilitar'><i class='fas fa-user-plus'></i></button>";            
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
              
      $id_usu = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_nro_documento = :u_nro_documento", array(":u_nro_documento" => $_POST['nro_doc']) );
      
      if (isset($_POST['empresas'])) {
        foreach ($_POST['empresas'] as $emp) {
          $db->sentencia("INSERT INTO empresas_usuarios(fk_usuario, fk_empresa, eu_fechaCreacion) VALUES(:fk_usuario, :fk_empresa, :eu_fechaCreacion)", array(":fk_usuario" => $id_usu[0]['u_id'], ":fk_empresa" => $emp, ":eu_fechaCreacion" => date("Y-m-d H:i:s")));
        }
      }

      if(isset($_POST['cursos'])){
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

    //Borramos toda las empresas con el usuario a editar
    $db->sentencia("DELETE FROM empresas_usuarios WHERE fk_usuario = :fk_usuario", array(":fk_usuario" => $_POST['editarIdUsu']));

    //Validamos si seleccionaron alguna empresa
    if ($_POST['editEmpresas']) {
      foreach ($_POST['editEmpresas'] as $emp) {
        $db->sentencia("INSERT INTO empresas_usuarios (fk_usuario, fk_empresa, eu_fechaCreacion) VALUES(:fk_usuario, :fk_empresa, :eu_fechaCreacion)", array(":fk_usuario" => $_POST['editarIdUsu'], ":fk_empresa" => $emp, ":eu_fechaCreacion" => date("Y-m-d H:i:s")));
      }
    }

    $db->desconectar();
    
    return "Ok";
  }

  function listaCursoUsuarios(){
    $resp = "";
    $db = new Bd();
    $db->conectar();
    
    $cursos = $db->consulta("SELECT mc.mc_id AS mc_id, mc.mc_nombre AS mc_nombre FROM empresas_usuarios AS eu INNER JOIN empresas_cursos AS ec ON eu.fk_empresa = ec.fk_empresa INNER JOIN mandino_curso AS mc ON mc.mc_id = ec.fk_curso WHERE fk_usuario = :fk_usuario GROUP BY ec.fk_curso", array(":fk_usuario" => $_POST['idUsu']));

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
      $resp .= '<button type="button" value="' . $listaCursos[$i]['fk_mc'] . '" class="pro_curso list-group-item d-flex justify-content-between align-items-center">' . $listaCursos[$i]['mc_nombre'] . '<span class="badge badge-primary badge-pill">' . porcentajeCurso($listaCursos[$i]['fk_mc'], $_REQUEST['idUsu']) . '%</span></button>';
    }

    $db->desconectar();

    return $resp;
  }

  function listaUnidadesUsuariosProgreso(){
    $db = new Bd();
    $db->conectar();
    $resp = "";

    $listaUnidades = $db->consulta("SELECT * FROM mandino_unidades WHERE fk_mc = :fk_mc ORDER BY mu_orden ASC", array(":fk_mc" => $_GET['curso']));

    for ($i=0; $i < $listaUnidades['cantidad_registros']; $i++) { 
      $listaLeccionesXUnidad = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu", array(":fk_mu" => $listaUnidades[$i]['mu_id']));
      $listaLeccionesXUnidadVisto = $db->consulta("SELECT * FROM mandino_lecciones_visto AS mlv INNER JOIN mandino_lecciones AS ml ON ml.ml_id = mlv.fk_ml  WHERE mlv.fk_usuario = :fk_usuario AND ml.fk_mu = :fk_mu AND (mlv.mlv_taller_aprobo = 0 OR mlv.mlv_taller_aprobo = 2)", array(":fk_mu" => $listaUnidades[$i]['mu_id'], ":fk_usuario" => $_GET['idUsu']));
      
      if ($listaLeccionesXUnidadVisto['cantidad_registros'] > 0) {
        $resp .= '<button type="button" value="' . $listaUnidades[$i]['mu_id'] . '" class="pro_unidad list-group-item list-group-item-action d-flex justify-content-between">' . $listaUnidades[$i]['mu_nombre'] . '<span class="badge badge-primary badge-pill">' . $listaLeccionesXUnidadVisto['cantidad_registros'] . '/' . $listaLeccionesXUnidad['cantidad_registros'] . '<span></button>';
      }else{
        $resp .= '<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between disabled bg-light text-dark" disabled>' . $listaUnidades[$i]['mu_nombre'] . '<span class="badge badge-primary badge-pill">' . $listaLeccionesXUnidadVisto['cantidad_registros'] . '/' . $listaLeccionesXUnidad['cantidad_registros'] . '<span></button>';
      }
    }

    $db->desconectar();

    return $resp;
  }

  function listaLeccionesUsuarioProgreso(){
    $db = new Bd();
    $db->conectar();
    $resp = "";

    $listaLecciones = $db->consulta("SELECT * FROM mandino_lecciones_visto AS mlv INNER JOIN mandino_lecciones AS ml ON ml.ml_id = mlv.fk_ml  WHERE mlv.fk_usuario = :fk_usuario AND ml.fk_mu = :fk_mu ORDER BY ml_orden ASC", array(":fk_mu" => $_GET['idUnidad'], ":fk_usuario" => $_GET['idUsu']));

    $resp .= '<div class="row">
                <div class="col-6">
                  <h6 class="font-weight-bold">Lección</h6>
                </div>
                <div class="col-6">
                  <h6 class="font-weight-bold">Fecha</h6>
                </div>
              </div><hr>';

    for ($i=0; $i < $listaLecciones['cantidad_registros']; $i++) { 
      if ($listaLecciones[$i]['fk_mt'] == "") {
        $resp .= "<div class='row'>
                    <div class='col-6'>" . $listaLecciones[$i]['ml_nombre'] . "</div>
                    <div class='col-6'>" . date("d/m/Y h:i:s a", strtotime($listaLecciones[$i]['mlv_fecha_creacion'])) . "</div>
                  </div>";
      }else{
        $resp .= '<div class="row">
                    <div class="col-6 align-self-center">
                      <span>' . $listaLecciones[$i]['ml_nombre'] . '</span>
                    </div>
                    <div class="col-6 align-self-center">
                      <div class="input-group spinner">
                        <div class="input-group-prepend">
                          <button value="' . $listaLecciones[$i]['mlv_id'] . '" class="btn text-monospace minus btn-primary" type="button">-</button>
                        </div>
                        <input type="number" class="count form-control" disabled="true" min="0" max="30" step="0" value="'. $listaLecciones[$i]['mlv_taller_intento_adicional'] .'">
                        <div class="input-group-append">
                          <button value="' . $listaLecciones[$i]['mlv_id'] . '" class="btn text-monospace plus btn-primary" type="button">+</button>
                        </div>
                      </div>
                    </div>
                  </div>';
      }
      if(($listaLecciones['cantidad_registros']-1) != $i){
        $resp .= "<hr>";
      }   
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

    $sql_select_cantidadLecciones_usuario = $db->consulta("SELECT * FROM mandino_curso INNER JOIN mandino_unidades ON fk_mc = mc_id INNER JOIN mandino_lecciones ON fk_mu = mu_id INNER JOIN mandino_lecciones_visto AS mlv ON mlv.fk_ml = ml_id WHERE mc_id = :mc_id AND mlv.fk_usuario = :fk_usuario AND (mlv.mlv_taller_aprobo = 0 OR mlv.mlv_taller_aprobo = 2)", array(":mc_id" => $curso, ":fk_usuario" => $usuario));

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

  function logFecha(){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT YEAR(log_fecha_creacion) AS fecha FROM log_session WHERE fk_usuario = :idUsu GROUP BY fecha", array(":idUsu" => $_POST['idUsu']));

    $db->desconectar();

    return json_encode($sql);
  }

  function logMeses(){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT MONTH(log_fecha_creacion) AS mes FROM log_session WHERE YEAR(log_fecha_creacion) = :year AND  fk_usuario = :idUsu GROUP BY mes", array(":idUsu" => $_POST['idUsu'], ":year" => $_POST['year']));

    $db->desconectar();

    return json_encode($sql);
  }

  function logs(){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT * FROM log_session WHERE YEAR(log_fecha_creacion) = :year AND MONTH(log_fecha_creacion) = :mes  AND fk_usuario = :idUser", array(":year" => $_GET['year'], ":mes" => $_GET['mes'], ":idUser" => $_GET['idUsu']));

    $db->desconectar();

    return json_encode($sql);
  }

  function UsuariosEmpresas(){
    $db = new Bd();
    $db->conectar();

    $empresas = $db->consulta("SELECT e_id, e_nombre FROM empresas");

    for ($i=0; $i < $empresas['cantidad_registros']; $i++) { 
      $sql = $db->consulta("SELECT * FROM empresas_usuarios WHERE fk_usuario = :fk_usuario AND fk_empresa = :fk_empresa", array(":fk_usuario" => $_REQUEST['idUsuario'], ":fk_empresa" => $empresas[$i]['e_id']));

      if ($sql['cantidad_registros'] == 1) {
        $empresas[$i]['check'] = true;
        array_push($empresas[$i], true);
      } else {
        $empresas[$i]['check'] = false;
        array_push($empresas[$i], false);
      }
    }

    $db->desconectar();

    return json_encode($empresas);
  }

  function CrearUsuarioAsodelco(){
    $db = new Bd();
    $db->conectar();
    $respuesta = "";

    if ((validarNroDocumento() == 0) && (validarUsuario() == 0)) {
      $db->sentencia("INSERT INTO mandino_usuarios(u_nro_documento, u_usuario, u_password, u_nombre1, u_nombre2, u_apellido1, u_apellido2, u_foto, u_correo, u_telefono, fk_mt, u_fecha_creacion, u_cambio_pass, u_activo, fk_ciudad) VALUES (:u_nro_documento, :u_usuario, :u_password, :u_nombre1, :u_nombre2, :u_apellido1, :u_apellido2, :u_foto, :u_correo, :u_telefono, :fk_mt, :u_fecha_creacion, :u_cambio_pass, :u_activo, :fk_ciudad)", array(":u_nro_documento" => $_POST['nro_doc'], 
              ":u_usuario" => $_POST['usuario'], 
              ":u_password" => encriptarPass($_POST['nro_doc']),
              ":u_nombre1" => $_POST['primer_nombre'], 
              ":u_nombre2" => @$_POST['segundo_nombre'],   
              "u_apellido1" => $_POST['primer_apellido'], 
              "u_apellido2" => @$_POST['segundo_apellido'], 
              ":u_foto" => "foto-usuario/0.png", 
              ":u_correo" => $_POST['correo'], 
              ":u_telefono" => @$_POST['telefono'], 
              ":fk_mt" => 1, 
              ":u_fecha_creacion" => date('Y-m-d H:i:s'), 
              ":u_cambio_pass" => date('Y-m-d'), 
              ":u_activo" => 1,
              ":fk_ciudad" => $_REQUEST['ciudades']
              ));
              
      $id_usu = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_nro_documento = :u_nro_documento", array(":u_nro_documento" => $_POST['nro_doc']) );
      
      $db->sentencia("INSERT INTO empresas_usuarios(fk_usuario, fk_empresa, eu_fechaCreacion) VALUES(:fk_usuario, :fk_empresa, :eu_fechaCreacion)", array(":fk_usuario" => $id_usu[0]['u_id'], ":fk_empresa" => 2, ":eu_fechaCreacion" => date("Y-m-d H:i:s")));
   

      $db->sentencia("INSERT INTO mandino_curso_usuario(fk_mc, id_usuario, fecha_creacion, id_creador, mcu_activo) VALUES(:fk_mc, :id_usuario, :fecha_creacion, :id_creador, 1)", array(":fk_mc" => 2, ":id_usuario" => $id_usu[0]['u_id'], ":fecha_creacion" => date('Y-m-d H:i:s'), ":id_creador" => 1));

      $respuesta = "Ok";
    }else{
      $respuesta = "Usuario o Nro de documento ya se encuentra registrado.";
    }

    $db->desconectar();

    return $respuesta;
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>
