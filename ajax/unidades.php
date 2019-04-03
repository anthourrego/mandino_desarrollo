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

  function datosUnidad(){
  	$db = new Bd();
  	$db->conectar();

  	$sql_mc = $db->consulta("SELECT * FROM mandino_unidades WHERE mu_id = :mu_id", array(":mu_id" => $_POST['unidad']));

  	$db->desconectar();
		
		if ($sql_mc['cantidad_registros'] > 0) {
  		return json_encode($sql_mc[0]);
		}else{
			return false;
		} 
  }
  
  function primerModulo($unidad){
    $db = new Bd();
    $sql_primerLeccion = $db->consulta("SELECT mu_id FROM mandino_modulos INNER JOIN mandino_unidades ON fk_mm = mm_id WHERE mm_id = :mm_id LIMIT 1", array(":mm_id" => $unidad));
    return $sql_primerLeccion[0]['mu_id']; 
  }

  function modulosUnidades(){
  	$lista = '';
  	$contCollapsed = 0;
	  $db = new Bd();
	  $db->conectar();
	  //titulo del curso
	  $sql_mc = $db->consulta("SELECT * FROM mandino_curso WHERE mc_id = :id_mc", array(":id_mc" => $_POST['curso']));
	 		
	 	for ($i=0; $i < $sql_mc['cantidad_registros'] ; $i++) { 
	 		$sql_mm = $db->consulta("SELECT * FROM mandino_modulos WHERE fk_mc = :id_mc", array(":id_mc" => $sql_mc[0]['mc_id']));
	    $lista .= '<div class="accordion border-bottom" id="accordionModulo' . $sql_mc[0]['mc_id'] . '">';
	    for ($j=0; $j < $sql_mm['cantidad_registros']; $j++) { 

	    	$contCollapsed++;
	      if ($contCollapsed == 1) {
	        $lista .= '<div class="card">
	                  <button class="card-header card-modulos d-flex justify-content-between btn btn-light" id="modulo' . $sql_mm[$j]['mm_id'] . '" data-toggle="collapse" data-target="#collapseModulo' . $sql_mm[$j]['mm_id'] . '" aria-expanded="true" aria-controls="collapseModulo' . $sql_mm[$j]['mm_id'] . '">
	                    <h5 class="mb-0 my-auto">
	                      ' . $sql_mm[$j]['mm_nombre'] . '
	                    </h5>
	                  </button>
	                  <div id="collapseModulo' . $sql_mm[$j]['mm_id'] .'" class="collapse show" aria-labelledby="modulo' . $sql_mm[$j]['mm_id'] . '" data-parent="#accordionModulo' . $sql_mc[$i]['mc_id'] . '">
	                    <div class="card-body">
	                    <div class="row justify-content-center">';
	      }else{
	        $lista .= '<div class="card">
	                  <div class="card-header card-modulos d-flex justify-content-between collapsed" id="modulo' . $sql_mm[$j]['mm_id'] . '" data-toggle="collapse" data-target="#collapseModulo' . $sql_mm[$j]['mm_id'] . '" aria-expanded="true" aria-controls="collapseModulo' . $sql_mm[$j]['mm_id'] . '">
	                    <h5 class="mb-0 my-auto">
	                      ' . $sql_mm[$j]['mm_nombre'] . '
	                    </h5>
	                  </div>
	                  <div id="collapseModulo' . $sql_mm[$j]['mm_id'] .'" class="collapse" aria-labelledby="modulo' . $sql_mm[$j]['mm_id'] . '" data-parent="#accordionModulo' . $sql_mc[$i]['mc_id'] . '">
	                    <div class="card-body">
	                    <div class="row justify-content-center">';
	      }


	      $sql_mu = $db->consulta("SELECT * FROM mandino_unidades WHERE fk_mm = :fk_mm ORDER BY mu_orden ASC", array(":fk_mm" => $sql_mm[$j]['mm_id']));
	      
	      for ($k=0; $k < $sql_mu['cantidad_registros']; $k++) { 
	      	$sql_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :id_mu AND ISNULL(fk_ml) ORDER BY ml_orden ASC", array(":id_mu" => $sql_mu[$k]['mu_id']));
	        
	        if ($sql_ml['cantidad_registros'] > 0) {
	          $cont_lecciones_vista = 0;
	          //modificacion de colores en los botones de inicio
	          $sql_select_ml_mlv = $db->consulta("SELECT * FROM mandino_lecciones INNER JOIN mandino_lecciones_visto AS mlv ON mlv.fk_ml = ml_id WHERE fk_mu = :fk_mu AND fk_usuario = :fk_usuario AND mlv.mlv_taller_aprobo != 1", array(":fk_mu" => $sql_mu[$k]['mu_id'], ":fk_usuario" => $_POST['user']));


	          if($sql_select_ml_mlv['cantidad_registros'] == 0 && primerModulo($sql_mm[$j]['mm_id']) == $sql_mu[$k]['mu_id']){
	            $lista .= '<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 card-deck">
	                <div class="card bg-light mb-4 border-bottom m-shadow m-shadow-primary border-primary">
	                  <div class="card-header font-weight-bold">' .$sql_mu[$k]['mu_nombre'] . '</div>
	                  <div class="card-body">
	                    <p class="card-text">
	                      <ul>';
	            for ($a=0; $a < $sql_ml['cantidad_registros']; $a++) { 
	              $lista .= '<li>' . $sql_ml[$a]['ml_nombre'] . '</li>';
	            }
	            $lista .= '</ul>
	                      </p>          
	                      </div>
	                      <div class="card-footer text-center">
	                        <a class="btn btn-primary rounded-pill" href="leccion?uni=' . $sql_mu[$k]['mu_id'] . '&curso=' . $_POST['curso'] . '"><i class="fas fa-pencil-alt"></i> Iniciar</a>
	                      </div>
	                    </div>
	                  </div>';
	          }elseif ($sql_select_ml_mlv['cantidad_registros'] == $sql_ml['cantidad_registros']) {
	            $lista .= '<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 card-deck">
	                <div class="card bg-light mb-4 border-bottom m-shadow m-shadow-info border-info">
	                  <div class="card-header font-weight-bold">' . $sql_mu[$k]['mu_nombre'] . '</div>
	                  <div class="card-body">
	                    <p class="card-text">';
	            $lista .= '<ul>';
	            for ($a=0; $a <$sql_ml['cantidad_registros']; $a++) { 
	              $lista .= '<li>' . $sql_ml[$a]['ml_nombre'] . '</li>';
	            }
	            $lista .= '</ul>';
	            $lista .= '</p>          
	                      </div>
	                      <div class="card-footer text-center">
	                        <a class="btn btn-info rounded-pill" href="leccion?uni=' . $sql_mu[$k]['mu_id'] . '&curso=' . $_POST['curso'] . '"><i class="fas fa-star" style="color: #FFDD43;"></i> Finalizado</a>
	                      </div>
	                    </div>
	                  </div>';
	          }elseif($sql_select_ml_mlv['cantidad_registros'] == 1){
	            $lista .= '<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 card-deck">
	                <div class="card bg-light mb-4 border-bottom m-shadow m-shadow-primary border-primary">
	                  <div class="card-header font-weight-bold">' . $sql_mu[$k]['mu_nombre'] . '</div>
	                  <div class="card-body">
	                    <p class="card-text">';
	            $lista .= '<ul>';
	            for ($a=0; $a < $sql_ml['cantidad_registros']; $a++) { 
	              $lista .= '<li>' . $sql_ml[$a]['ml_nombre'] . '</li>';
	            }
	            $lista .= '</ul>';
	            $lista .= '</p>          
	                      </div>
	                      <div class="card-footer text-center">
	                        <a class="btn btn-primary rounded-pill" href="leccion?id=' . $sql_mu[$k]['mu_id'] . '&curso=' . $_POST['curso'] . '"><i class="fas fa-pencil-alt"></i> Iniciar</a>
	                      </div>
	                    </div>
	                  </div>';
	          }elseif($sql_select_ml_mlv['cantidad_registros'] > 1 && $sql_select_ml_mlv['cantidad_registros'] < $sql_ml['cantidad_registros']){
	            $lista .= '<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 card-deck">
	                <div class="card bg-light mb-4 border-bottom m-shadow m-shadow-warning border-warning">
	                  <div class="card-header font-weight-bold">' . $sql_mu[$k]['mu_nombre'] . '</div>
	                  <div class="card-body">
	                    <p class="card-text">';
	            $lista .= '<ul>';
	            for ($a=0; $a < $sql_ml['cantidad_registros']; $a++) { 
	              $lista .= '<li>' . $sql_ml[$a]['ml_nombre'] . '</li>';
	            }
	            $lista .= '</ul>';
	            $lista .= '</p>          
	                    </div>
	                    <div class="card-footer text-center">
	                      <a class="btn btn-warning rounded-pill" href="leccion?uni=' . $sql_mu[$k]['mu_id'] . '&curso=' . $_POST['curso'] . '"><i class="fas fa-user-edit"></i> Continuar</a>
	                    </div>
	                  </div>
	                </div>';
	          }else{
	            $lista .= '<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 card-deck">
	                <div class="card bg-light mb-4 border-bottom">
	                  <div class="card-header font-weight-bold">' . $sql_mu[$k]['mu_nombre'] . '</div>
	                  <div class="card-body">
	                    <p class="card-text">';
	            $lista .= '<ul>';
	            for ($a=0; $a < $sql_ml['cantidad_registros']; $a++) { 
	              $lista .= '<li class="text-muted">' . $sql_ml[$a]['ml_nombre'] . '</li>';
	            }
	            $lista .= '</ul>';
	            $lista .= '</p>          
	                    </div>
	                    <div class="card-footer text-center">
	                      <button class="btn btn-secondary rounded-pill disabled" disabled><i class="fas fa-pencil-ruler"></i> Iniciar</button>
	                    </div>
	                  </div>
	                </div>';
	          }
	        }
	      }
	      $lista .= '</div>
	                </div>
	                </div>
	                </div>'; 
	    }

	    $lista .= "</div>";
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