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
  require_once($ruta_raiz . "clases/Upload.php");

  function datosPermiso(){
    $db = new Bd();
    $db->conectar();

    $permiso = $db->consulta("SELECT mp2.mp_id AS id, mp2.mp_nombre AS nombre, mp2.mp_tag AS tag, mp2.mp_icono AS icono, mp2.mp_ruta AS ruta, mp2.mp_fecha_creacion AS fecha, mp1.mp_tag AS padre FROM mandino_permisos AS mp1 INNER JOIN mandino_permisos AS mp2 ON mp1.mp_id = mp2.fk_mp WHERE mp2.mp_id = :mp_id1", array(":mp_id1" => $_POST['permiso']));

    if ($permiso['cantidad_registros'] == 0) {
      $permiso = $db->consulta("SELECT mp_id AS id, mp_nombre AS nombre, mp_tag AS tag, mp_icono AS icono, mp_ruta AS ruta, mp_fecha_creacion AS fecha, fk_mp AS padre FROM mandino_permisos WHERE mp_id = :mp_id", array(":mp_id" => $_POST['permiso']));
    }

    $db->desconectar();

    return json_encode($permiso[0]);
  }
  
  function acordeon($permiso = 0){
    $db = new Bd();
    $db->conectar();
    //global $db;
    $acordeon = "";

    if ($permiso == 0) {
      $sql = $db->consulta("SELECT * FROM mandino_permisos WHERE fk_mp IS NULL");
    }else{
      $sql = $db->consulta("SELECT * FROM mandino_permisos WHERE fk_mp = :fk_mp", array(":fk_mp" => $permiso));
    }
    
    $acordeon .= '<div class="accordion border-bottom" id="accordion' . $permiso . '">';

    $acordeon .= "<div class='card'>";

    for ($i=0; $i <$sql['cantidad_registros'] ; $i++) {
      $sql2 = $db->consulta("SELECT * FROM mandino_permisos WHERE fk_mp = :fk_mp", array(":fk_mp" => $sql[$i]['mp_id']));
      if ($sql2['cantidad_registros'] > 0) {
        $acordeon .= '<button class="btn-permiso card-header card-modulos d-flex justify-content-between btn btn-light collapsed" value="' . $sql[$i]['mp_id'] . '" id="' . $sql[$i]['mp_nombre'] . '" data-toggle="collapse" data-target="#collapse' . $sql[$i]['mp_nombre'] . '" aria-expanded="true" aria-controls="collapse' . $sql[$i]['mp_nombre'] . '">
                          <h5 class="mb-0 my-auto">
                            ' . $sql[$i]['mp_tag'] . '
                          </h5>
                        </button>
                        <div id="collapse' . $sql[$i]['mp_nombre'] . '" class="collapse" aria-labelledby="' . $sql[$i]['mp_nombre'] . '" data-parent="#accordion' . $permiso . '">
                          <div class="card-body">';
        $acordeon .= acordeon($sql[$i]['mp_id']);
        $acordeon .= '</div></div>';
      }else{
        $acordeon .= '<button class="btn-permiso card-header card-modulos d-flex justify-content-between btn btn-light" value="' . $sql[$i]['mp_id'] . '" id="' . $sql[$i]['mp_nombre'] . '">
                        <h5 class="mb-0 my-auto">
                          ' . $sql[$i]['mp_tag'] . '
                        </h5>
                      </button>'; 
      }
    }

    $acordeon .= "</div></div>";
    $db->desconectar();

    return $acordeon;
  }

  function validarNombre($nombre){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT * FROM mandino_permisos WHERE mp_nombre = :mp_nombre", array(":mp_nombre" => $nombre));

    $db->desconectar();
    
    return $sql['cantidad_registros'];
  }

  function agregarHijo(){
    $db = new Bd();
    $db->conectar();
    $icono = "";
    $respuesta = "";

    if (validarNombre($_POST['agregarNombre']) == 0) {

      if($_POST['agregarIcono'] != ""){
        $icono = $_POST['agregarIcono'];
      }else{
        $icono = NULL;
      }

      $db->sentencia("INSERT INTO mandino_permisos(mp_nombre, mp_tag, mp_icono, mp_ruta, fk_mp, mp_fecha_creacion) VALUES(:mp_nombre, :mp_tag, :mp_icono, :mp_ruta, :fk_mp, :mp_fecha_creacion)", array("mp_nombre" => $_POST['agregarNombre'], 
                                                                          "mp_tag" => $_POST['agregarEtiqueta'], 
                                                                          "mp_icono" => $icono, 
                                                                          "mp_ruta" => $_POST['agregarRuta'], 
                                                                          "fk_mp" => $_POST['agregarIdPadre'], 
                                                                          "mp_fecha_creacion" => date('Y-m-d H:i:s')));
      $respuesta = "Ok";
    }else{
      $respuesta = "El nombre del permiso ya se encuentra en uso.";
    }


    $db->desconectar();

    return $respuesta;
  }

  function editarPermiso(){
    $db = new Bd();
    $db->conectar();
    $respuesta = "";
    $icono = "";

    if ($_POST['editIcono'] != "") {
      $icono = $_POST['editIcono'];
    }else{
      $icono = NULL;
    }


    $db->sentencia("UPDATE mandino_permisos SET mp_nombre = :mp_nombre, mp_tag = :mp_tag, mp_icono = :mp_icono, mp_ruta = :mp_ruta WHERE mp_id = :mp_id", array(":mp_nombre" => $_POST['editNombre'],
                                  ":mp_tag" => $_POST['editEtiqueta'],
                                  ":mp_icono" => $icono,
                                  ":mp_ruta" => $_POST['editRuta'],
                                  ":mp_id" => $_POST['editarIdPermiso']
                                  ));

    $db->desconectar();
  
    return "Ok";
  }

  function formPemisos(){

    borarPemisosUsuario($_POST['idUsu']);

    $db = new Bd();
    $db->conectar();

    if (isset($_POST['per'])) {
      foreach ($_POST['per'] as $per) {
        $db->sentencia("INSERT INTO mandino_permisos_usuarios (fk_mp, fk_u, mpu_creador, mpu_fecha_creacion) VALUES(:fk_mp, :fk_u, :mpu_creador, :mpu_fecha_creacion)", array(":fk_mp" => $per, ":fk_u" => $_POST['idUsu'], ":mpu_creador" => $_POST['usuarioCreador'], ":mpu_fecha_creacion" => date('Y-m-d H:i:s')));
      }
    }

    $db->desconectar();

    return "Ok";
  }

  function borarPemisosUsuario($id){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("DELETE FROM mandino_permisos_usuarios WHERE fk_u = :fk_u", array(":fk_u" => $id));

    $db->desconectar();
  }
    
  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }

  
?>
