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

  //Funcion para saber cuantas lecciones tiene un curso
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

		if ($cont > 0) {
			//Formulamos el porcentaje
			$porcentaje = ($contUsu * 100)/$cont;
		}

    $db->desconectar();
    return round($porcentaje);
  }

  function listaCuidadesHabilitadas(){
    $db = new Bd();
    $db->conectar();

    $sql = $db->consulta("SELECT fk_ciudad, m_nombre FROM mandino_usuarios INNER JOIN municipios ON m_id = fk_ciudad INNER JOIN empresas_usuarios ON u_id = fk_usuario AND u_activo = 1 AND fk_empresa = :fk_empresa GROUP BY fk_ciudad ORDER BY m_nombre ASC", array(":fk_empresa" => $_REQUEST['empresa']));

    $db->desconectar();

    return json_encode($sql);
  }

  function listaCursosEmpresas(){
    $db = new Bd();
    $db->conectar();

    $sql_cursos = $db->consulta("SELECT mc_id, mc_nombre FROM mandino_curso INNER JOIN empresas_cursos ON mc_id = fk_curso WHERE fk_empresa = :fk_empresa", array(":fk_empresa" => $_REQUEST['empresa']));

    $db->desconectar();

    return json_encode($sql_cursos);
  }

  function reporteUsuario(){
    $resp = "";
    $ciudades = "";
    $cont = 1;

    foreach ($_REQUEST['ciudad'] as $ciu) {
      if ($cont == count($_REQUEST['ciudad'])) {
        $ciudades .= $ciu;
      } else {
        $ciudades .= $ciu . ", ";
      }
      $cont++;
    }

    $db = new Bd();
    $db->conectar();

    $sql_usuarios = $db->consulta("SELECT u_id, concat(u_nombre1, ' ', u_nombre2, ' ', u_apellido1, ' ', u_apellido2) AS nombre_completo, m_nombre FROM mandino_usuarios INNER JOIN municipios ON m_id = fk_ciudad INNER JOIN empresas_usuarios ON fk_usuario = u_id WHERE u_activo = 1 AND fk_ciudad IN (" . $ciudades . ") AND fk_empresa = :fk_empresa ORDER BY nombre_completo ASC", array(":fk_empresa" => $_REQUEST['empresa']));

    $sql_cursos = $db->consulta("SELECT mc_id, mc_nombre FROM mandino_curso");

    //Encabezado de la tabla
    $resp .= '<thead class="thead-light text-center">
                <tr>
                  <th>Ciudad</th>
                  <th>Nombre</th>';

    foreach ($_REQUEST['cursos'] as $valor) {
      $curso_sql = $db->consulta("SELECT mc_nombre FROM mandino_curso WHERE mc_id = :mc_id", array(":mc_id" => $valor));
      $resp .= "<th>" . $curso_sql[0]['mc_nombre'] . "</th>";
    }
    $resp .= "</tr>
            </thead>";

    //Cuerpo de la tabla
    $resp .= "<tbody>";
    for ($i=0; $i < $sql_usuarios['cantidad_registros']; $i++) { 
      $resp .= "<tr><td>" . $sql_usuarios[$i]['m_nombre'] . "</td>
                    <td>" . $sql_usuarios[$i]['nombre_completo'] . "</td>";
      foreach ($_REQUEST['cursos'] as $valor) {
        $resp .= "<td class='text-center'>" . porcentajeCurso($valor, $sql_usuarios[$i]['u_id']) . "%</td>";
      }
      $resp .= "</tr>";
    }

    $resp .= "</tbody>";

    $db->desconectar();

    return $resp;
  }


  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>