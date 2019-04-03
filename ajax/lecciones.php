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

  
  function validarLeccion($leccion, $usuario){
    $db = new Bd();
    $db->conectar();

    $sql_select_mlv = $db->consulta("SELECT * FROM mandino_lecciones_visto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":fk_usuario" => $usuario, ":fk_ml" => $leccion));

    $db->desconectar();
    
    return $sql_select_mlv['cantidad_registros'];
  }


  function menuLecciones(){
  	$lista = "";
  	$db = new Bd();
  	$db->conectar();

  	$sql_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden ASC", array(":fk_mu" => $_POST['unidad']));

  	for ($i=0; $i < $sql_ml['cantidad_registros']; $i++) { 
  		if (validarLeccion($sql_ml[$i]['ml_id'], $_POST['usuario']) == 1) {
  			if (@$_POST['less'] == $sql_ml[$i]['ml_id']) {
          $lista .= '<a href="' . RUTA_RAIZ . "leccion?uni=" . $_POST['unidad'] . "&less=" . $sql_ml[$i]['ml_id'] . '" class="list-group-item active">' . $sql_ml[$i]['ml_nombre'] . '</a>';
        }else{
          $lista .= '<a href="' . RUTA_RAIZ . "leccion?uni=" . $_POST['unidad'] . "&less=" . $sql_ml[$i]['ml_id'] . '" class="list-group-item">' . $sql_ml[$i]['ml_nombre'] . '</a>';
        }
  		}else{
        $lista .= '<a href="#" class="list-group-item disabled">' . $sql_ml[$i]['ml_nombre'] . '</a>';
      }
  	}

  	$db->desconectar();
  
  	return $lista;
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>