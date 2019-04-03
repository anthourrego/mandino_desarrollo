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

    $sql_insert_mlv = $db->sentencia("INSERT INTO mandino_lecciones_visto VALUES(NULL, :fk_usuario, :fk_ml, :mlv_fecha_creacion, :mlv_taller_aprobo)" , array(":fk_usuario" => $usuario,
                                      ":fk_ml" => $leccion,
                                      ":mlv_fecha_creacion" => date("Y-m-d H:i:s"),
                                      ":mlv_taller_aprobo" => $taller
                                      ));
    $db->desconectar();
  }

  function siguienteUnidad($unidad){
    $db = new Bd();
    $db->conectar();
    $id = "";
    $sql = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden ASC LIMIT 1", array(":fk_mu" => $unidad));

    $db->desconectar();
    
    return $sql[0]['ml_id'];
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
    return $sql_moduloActual[0]['fk_mm'];
  }

  function leccionesVistas($usuario, $modulo){
    $db = new Bd();
    $db->conectar();
    
    $sql_leccionesVistas = $db->consulta("SELECT * FROM mandino_unidades INNER JOIN mandino_lecciones ON fk_mu = mu_id INNER JOIN mandino_lecciones_visto AS mlv ON mlv.fk_ml = ml_id WHERE fk_mm = :fk_mm AND mlv.fk_usuario = :fk_usuario", array("fk_mm" => $modulo,"fk_usuario" => $usuario));

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
  //Traemos los datos de la base de datos segun la unidad 
  $db = new Bd();
  $db->conectar();

  if (leccionesVistas($usuario['id'], moduloActual($_GET['uni'])) == 0) {
    agregarVistoLeccion($usuario['id'], primeraLeccion($_GET['uni']));
    header('Location: ' . $ruta_raiz . 'leccion?uni=' . $_GET['uni'] . '&curso=' . $_GET['curso'] . '&less=' . primeraLeccion($_GET['uni']));
  }


  if (!isset($_GET['less'])) {
    //Validamos la ultima leccion en la que estaba y la redireccionamos a dicha leccion segun la unidad
    $sql_select_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu", array(":fk_mu" => $_GET['uni']));
   
    for ($i=0; $i < $sql_select_ml['cantidad_registros']; $i++) { 
      $sql_select_mlv = $db->consulta("SELECT * FROM mandino_lecciones_visto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array( ":fk_usuario" => $usuario['id'], ":fk_ml" => $sql_select_ml[$i]['ml_id']));

      $ultimaLeccionVista = $sql_select_mlv[0]['fk_ml'];
    }
    header('Location: ' . $ruta_raiz . 'leccion?uni=' . $_GET['uni'] . '&curso=' . $_GET['curso'] . '&less=' . $ultimaLeccionVista);
  }elseif ($_GET['less'] == 0 && $_GET['less'] == "") {
    header('Location: ' . $ruta_raiz . 'unidades?curso=' . $_GET['curso'] );
  }

  //Se agrega la leccion actual si no se a visto anteriormente
  if (validarLeccion($_GET['less'], $usuario['id']) == 0 && isset($_GET['less']) && $_GET['less'] != "") {
    agregarVistoLeccion($usuario['id'], $_GET['less']);
  }

  //Validamos cual es la ultima leccion de la unidad
  if (ultimaLeccion($_GET['uni']) == $_GET['less']) {
    $siguienteUnidad = siguienteUnidad($_GET['uni']+1);
    if (validarSiguienteUnidad($siguienteUnidad) == 1) {
      if (validarLeccion($siguienteUnidad, $usuario['id']) == 0) {
        if (validarLeccionTaller($_GET['less']) == 0) {
          agregarVistoLeccion($usuario['id'], $siguienteUnidad);
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
      if (validarLeccion($sql_ml[0]['ml_id'], $usuario['id']) == 1) {
        if (@$_GET['less'] == $sql_ml[$j]['ml_id']) {
          $lista .= '<a href="' . $ruta_raiz . "leccion?uni=" . $sql_mu[$i]['mu_id'] . "&less=" . $sql_ml[$j]['ml_id'] . '&curso=' . $_GET['curso'] . '" class="list-group-item active">' . $sql_ml[$j]['ml_nombre'] . '</a>';
        }else{
          $lista .= '<a href="' . $ruta_raiz . "leccion?uni=" . $sql_mu[$i]['mu_id'] . "&less=" . $sql_ml[$j]['ml_id'] . '&curso=' . $_GET['curso'] . '" class="list-group-item">' . $sql_ml[$j]['ml_nombre'] . '</a>';
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
    $btnAntHtml = '<a class="btn btn-success" href="' . $ruta_raiz . 'leccion?uni=' . $_GET['uni'] . '&less=' . $btnAnt . '&curso=' . $_GET['curso'] . '"><i class="fas fa-angle-left"></i> Anterior</a>';
  }else{
    $btnAntHtml = '<button type="button" class="btn btn-success" disabled><i class="fas fa-angle-left"></i> Anterior</button>';
  }

  if (validar(@$btnSig, $_GET['uni']) == 1) {
    $btnSigHtml = '<a class="btn btn-success" href="' . $ruta_raiz . 'leccion?uno=' . $_GET['uni'] . '&less=' . $btnSig . '&curso=' . $_GET['curso'] . '">Siguiente <i class="fas fa-angle-right"></i></a>';
  }else{
    $btnSigHtml = '<button type="button" class="btn btn-success" disabled>Siguiente <i class="fas fa-angle-right"></i></button>';
  } 

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
  <?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container-fluid">
    <div class="row mt-5 mb-5">
      <div class="col-12 col-md-3">
        <div class="mb-3">
          <a class="btn btn-secondary" href="unidades?curso=<?php echo $_GET['curso'] ?>"><i class="fas fa-angle-left"></i> Unidades</a>
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
            echo $btnSigHtml;
          ?>
        </div>
        <hr>
        <div class="container mt-3">  
          <?php 
            if ($taller != NULL) {
              include_once($ruta_raiz . $contenido);
            }elseif ($contenido != NULL) {
              echo $contenido;
            }else{
              echo '<h2 class="titulo text-hyundai my-2 text-center">No existe contenido relacionado</h2>';
            }
          ?>
        </div>
      </div>
    </div>
  </div>
</body>
<?php 
  echo $lib->cambioPantalla();
?>
</html>