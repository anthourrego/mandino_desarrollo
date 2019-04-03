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
	                      <h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
	                      <p class="text-justify">' . $sql_mc[$i]['mc_descripcion'] . '</p>
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
	                        <h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
	                        <p class="text-justify">' . $sql_mc[$i]['mc_descripcion'] . '</p>
	                        <div class="d-flex justify-content-between text-muted">
	                          <span>' . $porcentaje . '%</span>
	                          <span>' . $sql_mm->rowCount() . ' Módulo</span>
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
	                        <h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
	                        <p class="text-justify">' . $sql_mc[$i]['mc_descripcion'] . '</p>
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
	                        <h3 class="card-title text-center mb-3">' . $sql_mc[$i]['mc_nombre'] . '</h3>
	                        <p class="text-justify">' . $sql_mc[$i]['mc_descripcion'] . '</p>
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

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>