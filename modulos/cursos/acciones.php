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

  function datosCurso(){
  	$db = new Bd();
  	$db->conectar();

  	$sql_mc = $db->consulta("SELECT * FROM mandino_curso WHERE mc_id = :mc_id", array(":mc_id" => $_POST['curso']));

  	$db->desconectar();
		
		if ($sql_mc['cantidad_registros'] > 0) {
  		return json_encode($sql_mc[0]);
		}else{
			return false;
		}  
  }

  //Funcion para saber cuantas lecciones tiene un curso
  function porcentajeCurso($curso, $usuario){
    $db = new Bd();
    $db->conectar();
    $cont = 0; 
    $contUsu = 0;
    $porcentaje = 0;

    $sql_select_cantidadLecciones = $db->consulta("SELECT * FROM mandino_curso INNER JOIN mandino_modulos ON fk_mc = mc_id INNER JOIN mandino_unidades ON fk_mm = mm_id INNER JOIN mandino_lecciones ON fk_mu = mu_id WHERE mc_id = :mc_id", array(":mc_id" => $curso));
    $cont = $sql_select_cantidadLecciones['cantidad_registros'];

    $sql_select_cantidadLecciones_usuario = $db->consulta("SELECT * FROM mandino_curso INNER JOIN mandino_modulos ON fk_mc = mc_id INNER JOIN mandino_unidades ON fk_mm = mm_id INNER JOIN mandino_lecciones ON fk_mu = mu_id INNER JOIN mandino_lecciones_visto AS mlv ON mlv.fk_ml = ml_id WHERE mc_id = :mc_id AND mlv.fk_usuario = :fk_usuario", array(":mc_id" => $curso, ":fk_usuario" => $usuario));

    $contUsu = $sql_select_cantidadLecciones_usuario['cantidad_registros'];

    //Formulamos el porcentaje
    $porcentaje = ($contUsu * 100)/$cont;

    $db->desconectar();
    return round($porcentaje);
  }

  function centralCursos(){
  	//Generamos la conexion con la base de datos
	  $curso = '';
	  $porcentaje = 0;

  	$db = new Bd();
  	$db->conectar();

  	$sql_mc = $db->consulta("SELECT * FROM mandino_curso_usuario INNER JOIN mandino_curso ON fk_mc = mc_id WHERE id_usuario = :id_usu", array(":id_usu" => $_POST['id_usu']));

  	for ($i=0; $i < $sql_mc['cantidad_registros'] ; $i++) { 
  		$sql_mm = $db->consulta("SELECT * FROM mandino_modulos WHERE fk_mc = :id", array(":id"=>$sql_mc[$i]['mc_id']));

  		$porcentaje = porcentajeCurso($sql_mc[$i]['mc_id'], $_POST['id_usu']);

  		if ($porcentaje == 0) {
	      $curso .= '<div class="col-12 col-sm-6 col-lg-4 mt-4">
	                  <div class="card">
	                    <div class="card-body m-shadow m-shadow-primary">
                     		<div class="row">
                      		<div class="col-10">
                        		<h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
                      		</div>
                      		<div class="col-2">';
        if ($sql_mc[$i]['mc_descripcion'] != "") {
        	$curso .= '<button class="btn btn-info" onclick="mostrarInfo(\''. $sql_mc[$i]['mc_descripcion'] .'\')"><i class="fas fa-info"></i></button>';
        }
                        		
                      		
      	$curso .= '</div>
      									</div>
	                      <div class="d-flex justify-content-between text-muted">
	                        <span>' . $porcentaje . '%</span>
	                        <span>' . $sql_mm['cantidad_registros'] . ' Módulo</span>
	                      </div>
	                      <div class="progress mb-4" style="height: 6px;">
	                        <div class="progress-bar" role="progressbar" style="width:' . $porcentaje . '%;" aria-valuenow="' . $porcentaje . '" aria-valuemin="0" aria-valuemax="100"></div>
	                      </div>
	                      <div class="text-center">
	                        <a class="btn btn-primary rounded-pill" href="unidades?curso=' . $sql_mc[$i]['mc_id'] . '"><i class="fas fa-pencil-alt"></i> Iniciar</a>
	                      </div>
	                    </div>
	                  </div>
	                </div>';
	      }elseif ($porcentaje > 0 && $porcentaje < 100) {
	        $curso .= '<div class="col-12 col-sm-6 col-lg-4 mt-4">
	                    <div class="card">
	                      <div class="card-body border border-warning m-shadow m-shadow-warning">
	                      	<div class="row">
	                      		<div class="col-10">
	                        		<h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
	                      		</div>
                      		<div class="col-2">';
	        if ($sql_mc[$i]['mc_descripcion'] != "") {
	        	$curso .= '<button class="btn btn-info" onclick="mostrarInfo(\''. $sql_mc[$i]['mc_descripcion'] .'\')"><i class="fas fa-info"></i></button>';
	        }
	                        		
	                      		
	      	$curso .= '</div>
	                      	</div>
	                        <div class="d-flex justify-content-between text-muted">
	                          <span>' . $porcentaje . '%</span>
	                          <span>' . $sql_mm['cantidad_registros'] . ' Módulo</span>
	                        </div>
	                        <div class="progress mb-4" style="height: 6px;">
	                          <div class="progress-bar bg-warning" role="progressbar" style="width:' . $porcentaje . '%;" aria-valuenow="' . $porcentaje . '" aria-valuemin="0" aria-valuemax="100"></div>
	                        </div>
	                        <div class="text-center">
	                          <a class="btn btn-warning rounded-pill" href="unidades?curso=' . $sql_mc[$i]['mc_id'] . '"><i class="fas fa-user-edit"></i> Continuar</a>
	                          </div>
	                        </div>
	                      </div>
	                    </div>';
	      }elseif ($porcentaje == 100) {
	        $curso .= '<div class="col-12 col-sm-6 col-lg-4 mt-4">
	                    <div class="card">
	                      <div class="card-body border border-info m-shadow m-shadow-info">
	                        <div class="row">
	                      		<div class="col-10">
	                        		<h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
	                      		</div>
                      		<div class="col-2">';
	        if ($sql_mc[$i]['mc_descripcion'] != "") {
	        	$curso .= '<button class="btn btn-info" onclick="mostrarInfo(\''. $sql_mc[$i]['mc_descripcion'] .'\')"><i class="fas fa-info"></i></button>';
	        }
	                        		
	                      		
	      	$curso .= '</div>
	                      	</div>
	                        <div class="d-flex justify-content-between text-muted">
	                          <span>' . $porcentaje . '%</span>
	                          <span>' . $sql_mm['cantidad_registros'] . ' Módulo</span>
	                        </div>
	                        <div class="progress mb-4" style="height: 6px;">
	                          <div class="progress-bar bg-info" role="progressbar" style="width:' . $porcentaje . '%;" aria-valuenow="' . $porcentaje . '" aria-valuemin="0" aria-valuemax="100"></div>
	                        </div>
	                        <div class="text-center">
	                          <a class="btn btn-info rounded-pill" href="unidades?curso=' . $sql_mc[$i]['mc_id'] . '"><i class="fas fa-star" style="color: #FFDD43;"></i> Finalizado</a>
	                        </div>
	                      </div>
	                    </div>
	                  </div>';
	      }else{
	        $curso .= '<div class="col-12 col-sm-6 col-lg-4 mt-4">
	                    <div class="card">
	                      <div class="card-body border border-success m-shadow m-shadow-success">
	                        <div class="row">
	                      		<div class="col-10">
	                        		<h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
	                      		</div>
                      		<div class="col-2">';
	        if ($sql_mc[$i]['mc_descripcion'] != "") {
	        	$curso .= '<button class="btn btn-info" onclick="mostrarInfo(\''. $sql_mc[$i]['mc_descripcion'] .'\')"><i class="fas fa-info"></i></button>';
	        }
	                        		
	                      		
	      	$curso .= '</div>
	                      	</div>
	                        <div class="d-flex justify-content-between text-muted">
	                          <span>' . $porcentaje . '%</span>
	                          <span>' . $sql_mm['cantidad_registros'] . ' Módulo</span>
	                        </div>
	                        <div class="progress mb-4" style="height: 6px;">
	                          <div class="progress-bar" role="progressbar" style="width:' . $porcentaje . '%;" aria-valuenow="' . $porcentaje . '" aria-valuemin="0" aria-valuemax="100"></div>
	                        </div>
	                        <div class="text-center">
	                          <button class="btn btn-primary rounded-pill disabled" disabled><i class="fas fa-pencil-ruler"></i> Iniciar</button>
	                        </div>
	                      </div>
	                    </div>
	                  </div>';
	      }
  	}

  	$db->desconectar();

  	return $curso;
  }

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
    $db->conectar();
    $sql_primerLeccion = $db->consulta("SELECT mu_id FROM mandino_modulos INNER JOIN mandino_unidades ON fk_mm = mm_id WHERE mm_id = :mm_id LIMIT 1", array(":mm_id" => $unidad));
    $db->desconectar();
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
	                        <a class="btn btn-primary rounded-pill" href="leccion?uni=' . $sql_mu[$k]['mu_id'] . '&curso=' . $_POST['curso'] . '"><i class="fas fa-pencil-alt"></i> Iniciar</a>
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