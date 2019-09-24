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

  include_once($ruta_raiz . 'clases/librerias.php');
  include_once($ruta_raiz . 'clases/sessionActiva.php');
  include_once($ruta_raiz . 'clases/Conectar.php');

  $session = new Session();

  $usuario = $session->get("usuario");
  $id_usuario = 0;

  if (@$_REQUEST['id_usuario']) {
    $id_usuario = $_REQUEST['id_usuario'];
  } else {
    $id_usuario = $usuario['id'];
  }
  

  $lib = new Libreria;
  $titulo = '';
  $lista = '';
  $contenido = '';
  $taller = '';
  $btnAntHtml = '';
  $btnSigHtml = '';
  $ultimaLeccionVista = '';
  $siguienteUnidad = '';  

  //Funcion para la leccion si ya se encuentra creada
  function validarLeccion($leccion, $usuario){
    $db = new Bd();
    $db->conectar();
    $sql_select_mlv = $db->consulta("SELECT * FROM mandino_lecciones_visto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":fk_usuario" => $usuario, ":fk_ml" => $leccion));
    $db->desconectar();
    return $sql_select_mlv['cantidad_registros'];
  }

  function primeraLeccion($unidad){
    $db = new Bd();
    $db->conectar();
    $sql_select_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu LIMIT 1", array(":fk_mu" => $unidad));      
    $db->desconectar();
    return $sql_select_ml[0]['ml_id'];
  }

  function ultimaLeccion($unidad){
    $db = new Bd();
    $db->conectar();
    $sql_select_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden DESC LIMIT 1", array(":fk_mu" => $unidad));
    $db->desconectar();
    return $sql_select_ml[0]['ml_id'];
  }

  function validarLeccionTaller($leccion){
    $db = new Bd();
    $db->conectar();
    $taller = 0;
    $sql_select_ml = $db->consulta("SELECT fk_mt FROM mandino_lecciones WHERE ml_id = :ml_id", array(":ml_id" => $leccion));

    if ($sql_select_ml[0]['fk_mt'] == NULL) {
      $taller = 0;
    }else{
      $taller = 1;
    }

    $db->desconectar();
    return $taller;
  }

  function validar($id, $unidad){
    $db = new Bd();
    $db->conectar();
    $sql_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE ml_id = :ml_id AND fk_mu = :fk_mu", array(":ml_id" => $id, ":fk_mu" => $unidad));
    $db->desconectar();
    return $sql_ml['cantidad_registros'];
  }

  function agregarVistoLeccion($usuario, $leccion){
    $db = new Bd();
    $db->conectar();

    $taller = validarLeccionTaller($leccion);

    $sql_insert_mlv = $db->sentencia("INSERT INTO mandino_lecciones_visto VALUES(NULL, :fk_usuario, :fk_ml, :mlv_fecha_creacion, :mlv_taller_aprobo, 0)" , array(":fk_usuario" => $usuario,
                                      ":fk_ml" => $leccion,
                                      ":mlv_fecha_creacion" => date("Y-m-d H:i:s"),
                                      ":mlv_taller_aprobo" => $taller
                                      ));
    $db->desconectar();
  }

  function siguienteUnidad($unidad){
    $db = new Bd();
    $db->conectar();
    $id = 0;

    $sql = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden ASC LIMIT 1", array(":fk_mu" => $unidad));

    if($sql['cantidad_registros'] != 0){
      $id = $sql[0]['ml_id']; 
    }

    $db->desconectar();
    
    return $id;
  }


  function validarSiguienteUnidad($leccion){
    $db = new Bd();
    $db->conectar();

    $sql_select_mu_ml = $db->consulta("SELECT * FROM mandino_unidades INNER JOIN mandino_lecciones ON mu_id = fk_mu WHERE ml_id = :ml_id", array(":ml_id" => $leccion));
    
    $db->desconectar();
    return $sql_select_mu_ml['cantidad_registros'];
  }

  function moduloActual($unidad){
    $db = new Bd();
    $db->conectar();

    $sql_moduloActual = $db->consulta("SELECT * FROM mandino_unidades WHERE mu_id = :mu_id", array(":mu_id" => $unidad));

    $db->desconectar();
    return $sql_moduloActual[0]['fk_mc'];
  }

  function leccionesVistas($usuario, $modulo){
    $db = new Bd();
    $db->conectar();
    
    $sql_leccionesVistas = $db->consulta("SELECT * FROM mandino_unidades INNER JOIN mandino_lecciones ON fk_mu = mu_id INNER JOIN mandino_lecciones_visto AS mlv ON mlv.fk_ml = ml_id WHERE fk_mc = :fk_mc AND mlv.fk_usuario = :fk_usuario", array("fk_mc" => $modulo,"fk_usuario" => $usuario));

    $db->desconectar();
    return $sql_leccionesVistas['cantidad_registros'];
  }


  function botones($unidad, $orden){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu AND ml_orden = :ml_orden", array(":fk_mu" => $unidad, ":ml_orden" =>$orden));

    $db->desconectar();
    
    if ($sql['cantidad_registros'] == 0) {
      return 0;
    }else{
      return $sql[0]['ml_id'];
    }
  }

  function botonesUnidadAnterior($curso, $unidad){
    global $id_usuario;
    $db = new Bd();
    $db->conectar();
    $btn = "";


    $sql = $db->consulta("SELECT * FROM mandino_unidades WHERE fk_mc = :fk_mc AND mu_id = :mu_id", array(":fk_mc" => $curso, ":mu_id" =>$unidad));

    if ($sql['cantidad_registros'] == 1) {
      $sql1 = $db->consulta("SELECT * FROM mandino_unidades WHERE fk_mc = :fk_mc AND mu_orden = :mu_orden", array(":fk_mc" => $curso, ":mu_orden" => $sql[0]['mu_orden'] - 1));
      if($sql1['cantidad_registros'] == 1){
        $sql2 = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden DESC LIMIT 1", array(":fk_mu" => $sql1[0]['mu_id']));

        if (@!$_REQUEST['id_usuario']){
          $btn = '<a class="btn btn-success" href="leccion?uni=' . $sql1[0]['mu_id'] . '&less=' . $sql2[0]['ml_id'] . '&curso=' . $curso . '"><i class="fas fa-angle-left"></i> Unidad Anterior</a>';
        }else{
          $btn = '<a class="btn btn-success" href="leccion?uni=' . $sql1[0]['mu_id'] . '&less=' . $sql2[0]['ml_id'] . '&curso=' . $curso . '&id_usuario=' . $id_usuario . '"><i class="fas fa-angle-left"></i> Unidad Anterior</a>';
        }
      }else{
        $btn = '<button type="button" class="btn btn-success" disabled><i class="fas fa-angle-left"></i> Anterior</button>';
      }
    }else{
      $btn = '<button type="button" class="btn btn-success" disabled><i class="fas fa-angle-left"></i> Anterior</button>';
    }


    $db->desconectar();
    return $btn;
  }

  function botonesUnidadSiguiente($curso, $unidad, $leccionActual, $usuario){
    global $id_usuario;
    $db = new Bd();
    $db->conectar();
    $btn = "";

    if(validarLeccionTallerAprobado($leccionActual, $usuario) == 0){
      $sql = $db->consulta("SELECT * FROM mandino_unidades WHERE fk_mc = :fk_mc AND mu_id = :mu_id", array(":fk_mc" => $curso, ":mu_id" =>$unidad));

      if ($sql['cantidad_registros'] == 1) {
        $sql1 = $db->consulta("SELECT * FROM mandino_unidades WHERE fk_mc = :fk_mc AND mu_orden = :mu_orden", array(":fk_mc" => $curso, ":mu_orden" => $sql[0]['mu_orden'] + 1));
        if($sql1['cantidad_registros'] == 1){
          $sql2 = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden ASC LIMIT 1", array(":fk_mu" => $sql1[0]['mu_id']));

          if (@!$_REQUEST['id_usuario']){
            $btn = '<a class="btn btn-success" href="leccion?uni=' . $sql1[0]['mu_id'] . '&less=' . $sql2[0]['ml_id'] . '&curso=' . $curso . '">Unidad Siguiente <i class="fas fa-angle-right"></i></a>';
          }else{
            $btn = '<a class="btn btn-success" href="leccion?uni=' . $sql1[0]['mu_id'] . '&less=' . $sql2[0]['ml_id'] . '&curso=' . $curso . '&id_usuario=' . $id_usuario . '">Unidad Siguiente <i class="fas fa-angle-right"></i></a>';
          }
        }else{
          $btn = '<button type="button" class="btn btn-success" disabled>Siguiente <i class="fas fa-angle-right"></i></button>';
        }
      }else{
        $btn = '<button type="button" class="btn btn-success" disabled>Siguiente <i class="fas fa-angle-right"></i></button>';
      }
    }else{
      $btn = '<button type="button" class="btn btn-success" disabled>Unidad Siguiente <i class="fas fa-angle-right"></i></button>';
    }

    $db->desconectar();
    return $btn;
  }

  function validarLeccionTallerAprobado($leccion, $usuario){
    $db = new Bd();
    $db->conectar();
    $resp = 1;
    $sql_select_ml = $db->consulta("SELECT mlv_taller_aprobo FROM mandino_lecciones_visto WHERE fk_ml = :fk_ml AND fk_usuario = :fk_usuario", array(":fk_ml" => $leccion, ":fk_usuario" => $usuario));
    if ($sql_select_ml[0]['mlv_taller_aprobo'] == 0) {
      $resp = 0;
    }else if ($sql_select_ml[0]['mlv_taller_aprobo'] == 2) {
      $resp = 0;
    }

    $db->desconectar();
    return $resp;
  }

  //Traemos los datos de la base de datos segun la unidad 
  $db = new Bd();
  $db->conectar();

  if (leccionesVistas($id_usuario, moduloActual($_GET['uni'])) == 0) {
    agregarVistoLeccion($id_usuario, primeraLeccion($_GET['uni']));  
    if (@!$_REQUEST['id_usuario']){
      header('Location: ' . $ruta_raiz . 'leccion?uni=' . $_GET['uni'] . '&curso=' . $_GET['curso'] . '&less=' . primeraLeccion($_GET['uni']));
    }else{
      header('Location: ' . $ruta_raiz . 'leccion?uni=' . $_GET['uni'] . '&curso=' . $_GET['curso'] . '&less=' . primeraLeccion($_GET['uni'] . '&id_usuario=' . $id_usuario));
    }
  }


  if (!isset($_GET['less'])) {

    //Validamos la ultima leccion en la que estaba y la redireccionamos a dicha leccion segun la unidad
    $sql_select_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu", array(":fk_mu" => $_GET['uni']));

    for ($i=0; $i < $sql_select_ml['cantidad_registros']; $i++) { 
      $sql_select_mlv = $db->consulta("SELECT * FROM mandino_lecciones_visto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":fk_usuario" => $id_usuario, ":fk_ml" => $sql_select_ml[$i]['ml_id']));
      if ($sql_select_mlv['cantidad_registros'] == 1) {
        $ultimaLeccionVista = $sql_select_mlv[0]['fk_ml'];
      }
    }
    if (@!$_REQUEST['id_usuario']){
      header('Location: leccion?uni=' . $_GET['uni'] . '&curso=' . $_GET['curso'] . '&less=' . $ultimaLeccionVista);
    }else{
      header('Location: leccion?uni=' . $_GET['uni'] . '&curso=' . $_GET['curso'] . '&less=' . $ultimaLeccionVista . '&id_usuario=' . $id_usuario);
    }
    
  }elseif ($_GET['less'] == 0 && $_GET['less'] == "") {
    if (@!$_REQUEST['id_usuario']){
      header('Location: unidades?curso=' . $_GET['curso']);
    }else{
      header('Location: unidades?curso=' . $_GET['curso'] . '&id_usuario=' . $id_usuario);
    }
  }

  //Se agrega la leccion actual si no se a visto anteriormente
  if (validarLeccion(@$_GET['less'], $id_usuario) == 0 && isset($_GET['less']) && $_GET['less'] != "") {
    agregarVistoLeccion($id_usuario, $_GET['less']);
  }

  //Validamos cual es la ultima leccion de la unidad
  if (ultimaLeccion($_GET['uni']) == $_GET['less']) {
    $siguienteUnidad = siguienteUnidad($_GET['uni']+1);
    if (validarSiguienteUnidad($siguienteUnidad) == 1) {
      if (validarLeccion($siguienteUnidad, $id_usuario) == 0) {
        if (validarLeccionTaller($_GET['less']) == 0) {
          agregarVistoLeccion($id_usuario, $siguienteUnidad);
        }
      }
    }
  }

  //Crea la lista del menu lateral izquierdo
  $sql_mu = $db->consulta("SELECT * FROM mandino_unidades WHERE mu_id = :mu_id", array(":mu_id" => $_GET['uni']));
  for ($i=0; $i < $sql_mu['cantidad_registros']; $i++) { 
    $titulo = $sql_mu[$i]['mu_nombre'];
    $sql_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :id_mu ORDER BY ml_orden ASC", array(":id_mu" => $sql_mu[$i]['mu_id']));

    for ($j=0; $j < $sql_ml['cantidad_registros']; $j++) { 
      if (validarLeccion($sql_ml[$j]['ml_id'], $id_usuario) == 1) {
        if (@$_GET['less'] == $sql_ml[$j]['ml_id']) {
          if (@!$_REQUEST['id_usuario']){
            $lista .= '<a href="leccion?uni=' . $sql_mu[$i]['mu_id'] . '&less=' . $sql_ml[$j]['ml_id'] . '&curso=' . $_GET['curso'] . '" class="list-group-item active">' . $sql_ml[$j]['ml_nombre'] . '</a>';
          }else{
            $lista .= '<a href="leccion?uni=' . $sql_mu[$i]['mu_id'] . '&less=' . $sql_ml[$j]['ml_id'] . '&curso=' . $_GET['curso'] . '&id_usuario=' . $id_usuario . '" class="list-group-item active">' . $sql_ml[$j]['ml_nombre'] . '</a>';
          }
          
        }else{
          if (@!$_REQUEST['id_usuario']){
            $lista .= '<a href="leccion?uni=' . $sql_mu[$i]['mu_id'] . '&less=' . $sql_ml[$j]['ml_id'] . '&curso=' . $_GET['curso'] . '" class="list-group-item">' . $sql_ml[$j]['ml_nombre'] . '</a>';
          }else{
            $lista .= '<a href="leccion?uni=' . $sql_mu[$i]['mu_id'] . '&less=' . $sql_ml[$j]['ml_id'] . '&curso=' . $_GET['curso'] . '&id_usuario=' . $id_usuario . '" class="list-group-item">' . $sql_ml[$j]['ml_nombre'] . '</a>';
          }
        }
      }else{
        $lista .= '<a href="#" class="list-group-item disabled">' . $sql_ml[$j]['ml_nombre'] . '</a>';
      }

    }
  }

  //Sacamos lo botónes de anterior y siguiente
  $sql_ml2 = $db->consulta("SELECT * FROM mandino_lecciones WHERE ml_id = :ml_id", array(":ml_id" => @$_GET['less']));
  
  for ($i=0; $i < $sql_ml2['cantidad_registros']; $i++) { 
    $btnAnt = botones($_GET['uni'], $sql_ml2[$i]['ml_orden'] - 1);
    $btnSig = botones($_GET['uni'], $sql_ml2[$i]['ml_orden'] + 1);

    $taller = $sql_ml2[$i]['fk_mt'];
    $contenido = $sql_ml2[$i]['ml_contenido'];
  }

  if (validar(@$btnAnt, $_GET['uni']) == 1) {
    if (@!$_REQUEST['id_usuario']){
      $btnAntHtml = '<a class="btn btn-success" href="leccion?uni=' . $_GET['uni'] . '&less=' . $btnAnt . '&curso=' . $_GET['curso'] . '"><i class="fas fa-angle-left"></i> Anterior</a>';
    }else{
      $btnAntHtml = '<a class="btn btn-success" href="leccion?uni=' . $_GET['uni'] . '&less=' . $btnAnt . '&curso=' . $_GET['curso'] . '&id_usuario=' . $id_usuario . '"><i class="fas fa-angle-left"></i> Anterior</a>';
    }
  }else{
    $btnAntHtml = botonesUnidadAnterior($_GET['curso'], $_GET['uni']);
  }

  if (validar(@$btnSig, $_GET['uni']) == 1) {
    if (@!$_REQUEST['id_usuario']){
      $btnSigHtml = '<a class="btn btn-success" href="leccion?uni=' . $_GET['uni'] . '&less=' . $btnSig . '&curso=' . $_GET['curso'] . '">Siguiente <i class="fas fa-angle-right"></i></a>';
    }else{
      $btnSigHtml = '<a class="btn btn-success" href="leccion?uni=' . $_GET['uni'] . '&less=' . $btnSig . '&curso=' . $_GET['curso'] . '&id_usuario=' . $id_usuario . '">Siguiente <i class="fas fa-angle-right"></i></a>';
    }
  }else{
    $btnSigHtml = botonesUnidadSiguiente($_GET['curso'], $_GET['uni'], $_GET['less'], $id_usuario);
    //$btnSigHtml = '<button type="button" class="btn btn-success" disabled>Siguiente <i class="fas fa-angle-right"></i></button>';
  } 

  $nombreUnidades = $db->consulta("SELECT * FROM mandino_unidades WHERE mu_id = :mu_id", array(":mu_id" => $_GET['uni']));

  $nombreUnidades = $nombreUnidades[0]['mu_nombre'];

  $db->desconectar();

?>
<!DOCTYPE html>
<html>
<head>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
</head>
<body>
  <?php 
    if (@!$_REQUEST['id_usuario']) {
      include_once($ruta_raiz . 'navBar.php');
    }
  ?>
  <!-- Contenido -->
  <div class="container-fluid">
    <div class="row mt-5 mb-5">
      <div class="col-12 col-md-3">
        <div class="mb-3">
          <?php 
            if(@!$_REQUEST['id_usuario']){
              echo '<a class="btn btn-secondary" href="unidades?curso=' . $_GET['curso'] . '"><i class="fas fa-angle-left"></i> Unidades</a>';
            }else{
              echo '<a class="btn btn-secondary" href="unidades?curso=' . $_GET['curso'] . '&id_usuario=' . $id_usuario . '"><i class="fas fa-angle-left"></i> Unidades</a>';
            }
          ?>
        </div>
        <h4 id="titulo-unidad" class="titulo text-hyundai my-2 text-center text-md-left"></h4>
        <div class="list-group rounded">
          <?php echo $lista; ?>
        </div>
      </div>
      <div class="col-12 col-md-9 mt-5 mt-md-0">
        <div class="d-flex justify-content-between">
          <?php 
            echo $btnAntHtml;
          ?>
          <h4 class="titulo text-hyundai"><?php echo($nombreUnidades);?></h4>
          <?php
            echo $btnSigHtml;
          ?>
        </div>
        <hr>
        <div class="container mt-3">  
          <?php 
            if ($taller != NULL) {
              include_once($ruta_raiz . "modulos/" .  $contenido);
            }elseif ($contenido != NULL) {
              echo $contenido;
            }else{
              echo '<h2 class="titulo text-hyundai my-2 text-center">No existe contenido relacionado</h2>';
            }
          ?>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
          <?php 
            echo $btnAntHtml;
            echo $btnSigHtml;
          ?>
        </div>
      </div>
    </div>
  </div>
</body>
<?php
  if (@!$_REQUEST['id_usuario']) {
    echo $lib->cambioPantalla();
  }
?>
</html>