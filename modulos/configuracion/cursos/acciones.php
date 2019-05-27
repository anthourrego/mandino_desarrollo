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

  require_once($ruta_raiz . "clases/Conectar.php");
  require_once($ruta_raiz . "clases/funciones_generales.php");

  function listaCursos(){
  	$db = new Bd();
  	$cursos = "";
  	$resp = "";
  	$db->conectar();

  	$cursos = $db->consulta("SELECT * FROM mandino_curso");

  	for ($i=0; $i < $cursos['cantidad_registros']; $i++) { 
  		$resp .= "<tr onClick='datosCurso(" . $cursos[$i]['mc_id'] . ")' ondblclick='dobleClick(" . $cursos[$i]['mc_id'] . ")'>";

  		$resp .= "<td>" . $cursos[$i]['mc_nombre'] . "</td>";

  		$resp .= "</tr>";
  	}

  	$db->desconectar();

  	return $resp;
  }

  function datosCurso(){
  	$db = new Bd();
  	$datos = "";
  	$db->conectar();

  	$datos = $db->consulta("SELECT * FROM mandino_curso WHERE mc_id = :mc_id", array(":mc_id" => $_POST['idCurso']));

  	$db->desconectar();

  	return json_encode($datos[0]);
  }

  function formCrearCurso(){
  	$resp = "";
  	if (validarNombreCurso($_POST['crearNombre']) == 1) {
  		$db = new Bd();
  		$db->conectar();

  		$db->sentencia("INSERT INTO mandino_curso (mc_nombre, mc_descripcion, mc_fecha_creacion, mc_id_creador) VALUES (:mc_nombre, :mc_descripcion, :mc_fecha_creacion, :mc_id_creador)", array(":mc_nombre" => $_POST['crearNombre'], ":mc_descripcion" => $_POST['crearDescripcion'], ":mc_fecha_creacion" => date("Y-m-d H:i:s"), ":mc_id_creador" => $_POST['idUsu']));

  		$db->desconectar();
  		$resp = "Ok";
  	}else{
  		$resp = "El nombre ya se encuentra en uso";
  	}

  	return $resp;
  }

  function validarNombreCurso($nombre){
  	$db = new Bd();
  	$resp = "";
  	$db->conectar();

  	$resp = $db->consulta("SELECT * FROM mandino_curso WHERE mc_nombre = :mc_nombre", array(":mc_nombre" => $nombre));

  	$db->desconectar();

  	if ($resp['cantidad_registros'] > 0) {
  		return 0;
  	}else{
  		return 1;
  	}
  }

  function formEditarCurso(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_curso SET mc_nombre = :mc_nombre, mc_descripcion = :mc_descripcion WHERE mc_id = :mc_id", array(":mc_id" => $_POST['idCurso'], ":mc_nombre" => $_POST['editNombre'], ":mc_descripcion" => $_POST['editDescripcion']));

    $db->desconectar();

    return "Ok";
  }

  function listaUnidades(){
    $resp = "";
    $unidades = "";
    $db = new Bd();
    $db->conectar();

    $unidades = $db->consulta("SELECT * FROM mandino_unidades WHERE fk_mc = :fk_mc", array(":fk_mc" => $_POST['idCurso']));

    for ($i=0; $i <$unidades['cantidad_registros']; $i++) { 
      $resp .= "<tr id='" . $unidades[$i]['mu_id'] . "' onClick='datosUnidad(" . $unidades[$i]['mu_id'] . ")'>";
      $resp .= "<td class='cursor'>" . $unidades[$i]['mu_orden'] . "</td>";
      $resp .= "<td>" . $unidades[$i]['mu_nombre'] . "</td>";
      $resp .= "</tr>";
    }

    $db->desconectar();
  
    return $resp; 
  }

  function actualizarOrdenUnidades(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_unidades SET mu_orden = :mu_orden WHERE mu_id = :mu_id", array(":mu_id" => $_POST['idUnidad'], ":mu_orden" => $_POST['orden']));

    $db->desconectar();
    return "Ok";  
  }

  function datosUnidad(){
    $datos = "";
    $db = new Bd();
    $db->conectar();

    $datos = $db->consulta("SELECT mu.mu_nombre AS nombre, mu.mu_descripcion AS descripcion, mu.mu_fecha_creacion AS fecha_creacion, CONCAT(u.u_nombre1, ' ', u.u_nombre2, ' ', u.u_apellido1, ' ', u.u_apellido2) AS usuario FROM mandino_unidades AS mu INNER JOIN mandino_usuarios AS u ON mu.fk_mm = u.u_id WHERE mu.mu_id = :mu_id", array(":mu_id" => $_POST['idUnidad']));

    $db->desconectar();

    return json_encode($datos[0]);
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>