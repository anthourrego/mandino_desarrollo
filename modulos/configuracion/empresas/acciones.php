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

  function listaEmpresas(){
    $db = new Bd();
    $db->conectar();

    $resp = $db->consulta("SELECT * FROM empresas WHERE e_activo = 1");

    $db->desconectar();
  
    return json_encode($resp);
  }

  function formCrearEmpresa(){
    $db = new Bd();
    $db->conectar();
    $resp = [];

    if (validarNombre($_REQUEST['nombreEmpresa']) == 0) {
      $sql = $db->sentencia("INSERT INTO empresas (e_nombre, e_activo, fk_creador, e_fechaCreacion) VALUE(:e_nombre, :e_activo, :fk_creador, :e_fechaCreacion)", array(":e_nombre" => $_REQUEST['nombreEmpresa'], ":e_activo" => 1, ":fk_creador" => $_REQUEST['creador'], ":e_fechaCreacion" => date('Y-m-d H:i:s')));
      
      //Repuesta
      $resp['success'] = true;
      $resp['msj'] =  "Se ha creado " . $_REQUEST['nombreEmpresa'] . " correctamente";
    }else{
      //Repuesta
      $resp['success'] = false;
      $resp['msj'] =  "El nombre de esta empresa ya existe";
    }

    $db->desconectar();

    return json_encode($resp);
  }

  function validarNombre($nombre){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT * FROM empresas WHERE e_nombre = :e_nombre", array(":e_nombre" => $nombre));

    $db->desconectar();

    return $sql['cantidad_registros'];
  }

  function datosEmpresa(){
    $db = new Bd();
    $db->conectar();

    $datos = $db->consulta("SELECT * FROM empresas WHERE e_id = :e_id", array(":e_id" => $_REQUEST['idEmpresa']));

    $db->desconectar();

    return json_encode($datos[0]);
    
  }

  function formEditarEmpresa(){
    $resp = [];
    $db = new Bd();
    $db->conectar();

    $validarNombre = $db->consulta("SELECT * FROM empresas WHERE e_id != :e_id AND e_nombre LIKE :e_nombre", array("e_nombre" =>$_REQUEST['nombreEditarEmpresa'], ":e_id" =>$_REQUEST['idEmpresa']));

    if ($validarNombre['cantidad_registros'] == 0) {
      
      $db->sentencia("UPDATE empresas SET e_nombre = :nombre WHERE e_id = :e_id", array(":nombre" => $_REQUEST['nombreEditarEmpresa'], ":e_id" => $_REQUEST['idEmpresa']));

      //Repuesta
      $resp['success'] = true;
      $resp['msj'] =  "Se ha actualiado correctamente";
    } else {
      //Repuesta
      $resp['success'] = false;
      $resp['msj'] =  "El nombre de esta empresa ya existe";
    }
    

    $db->desconectar();

    return json_encode($resp);
  }

  function eliminarEmpresa(){
    $resp = 1;
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE empresas SET e_activo = 0 WHERE e_id = :e_id", array(":e_id" => $_REQUEST['idEmpresa']));

    $db->desconectar();

    return $resp;
  }

  function checkCursos(){
    $db = new Bd();
    $db->conectar();

    $cursos = $db->consulta("SELECT * FROM mandino_curso");

    for ($i=0; $i < $cursos['cantidad_registros']; $i++) { 
      $sql = $db->consulta("SELECT * FROM empresas_cursos WHERE fk_curso = :fk_curso AND fk_empresa = :fk_empresa", array(":fk_curso" => $cursos[$i]['mc_id'], ":fk_empresa" => $_REQUEST['idEmpresa']));

      if ($sql['cantidad_registros'] == 1) {
        $cursos[$i]['check'] = true;
        array_push($cursos[$i], true);
      } else {
        $cursos[$i]['check'] = false;
        array_push($cursos[$i], false);
      }
    }

    $db->desconectar();

    return json_encode($cursos);
  }

  function actualizarCursos(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("DELETE FROM empresas_cursos WHERE fk_empresa = :fk_empresa", array(":fk_empresa" => $_REQUEST['idActEmpresa']));

    if(@$_REQUEST['cursos']){
      foreach ($_REQUEST['cursos'] as $curso) {
        $db->sentencia("INSERT INTO empresas_cursos (fk_curso, fk_empresa, ec_fechaCreacion) VALUES (:fk_curso, :fk_empresa, :ec_fechaCreacion)", array(":fk_curso" => $curso, ":fk_empresa" => $_REQUEST['idActEmpresa'], ":ec_fechaCreacion" => date('Y-m-d H:i:s')));
      }
    }

    $db->desconectar();

    return 1;
  }

  function agregarEmpresa(){
    $db = new Bd();
    $db->conectar();

    $usuario = $db->consulta("SELECT * FROM mandino_usuarios");

    for ($i=0; $i < $usuario['cantidad_registros']; $i++) { 
      $validar = $db->consulta("SELECT * FROM empresas_usuarios WHERE fk_usuario = :fk_usuario AND fk_empresa = :fk_empresa", array(":fk_usuario" => $usuario[$i]['u_id'], ":fk_empresa" => 1));

      if ($validar['cantidad_registros'] == 0) {
        $db->sentencia("INSERT INTO empresas_usuarios (fk_usuario, fk_empresa, eu_fechaCreacion) VALUES(:fk_usuario, :fk_empresa, :eu_fechaCreacion)", array(":fk_usuario" => $usuario[$i]['u_id'], ":fk_empresa" => 1, ":eu_fechaCreacion" => date("Y-m-d H:i:s")));
      }

    }

    $db->desconectar();

    return 1;
  }

  function empresasUsuario(){
    $db = new Bd();
    $db->conectar();

    $usuarioEmpresas = $db->consulta("SELECT * FROM empresas_usuarios WHERE fk_usuario = :fk_usuario", array(":fk_usuario" => $_REQUEST['id_usu']));

    $db->desconectar();

    return json_encode($usuarioEmpresas);
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>