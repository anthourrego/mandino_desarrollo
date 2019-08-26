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

  require_once($ruta_raiz . 'clases/sessionActiva.php');
  require_once($ruta_raiz . 'clases/Conectar.php');
  require_once($ruta_raiz . 'clases/funciones_generales.php');

  function contenidoTaller(){  
    //En esta variable se guarda todo lo del taller
    $contenido = "";
    $db = new Bd();
    $db->conectar();

    //Consulta del taller
    $sql_mt = $db->consulta("SELECT * FROM mandino_taller WHERE mt_id = :mt_id", array(":mt_id" => $_POST['taller']));
    $contenido .= '<input type="hidden" name="leccion" value="' . $_POST['leccion'] . '">';
    $contenido .= '<input type="hidden" name="unidad" value="' . $_POST['unidad'] . '">';
    $contenido .= '<input type="hidden" name="accion" value="resolverTaller">';
    $contenido .= '<input type="hidden" name="taller" value="' . $_POST['taller'] . '">';
    $contenido .= '<input type="hidden" name="inicio" value="' . date("Y-m-d H:i:s") . '">';
    for ($i=0; $i <$sql_mt['cantidad_registros']; $i++) { 
      $contenido .= '<h2 class="text-hyundai titulo text-center pt-3">' . $sql_mt[$i]['mt_titulo'] . '</h2>';
      if ($sql_mt[$i]['mt_descripcion'] != NULL) {
        $contenido .= '<p class="mt-4">' . $sql_mt[$i]['mt_descripcion'] . '</p>';
      }
      //Buscamos la preguntas relacionadas con la encuesta
      $sql_mtp = $db->consulta("SELECT * FROM mandino_taller_preguntas WHERE fk_mt = :id_mt", array(":id_mt" => $sql_mt[$i]['mt_id']));

      for ($j=0; $j <$sql_mtp['cantidad_registros']; $j++) { 
        $contenido .= "<hr>";
        $contenido .= '<p>' . $sql_mtp[$j]['mtp_pregunta'] . '</p>';
        
        //Traemos la respuesta por pregunta
        $sql_mtpo = $db->consulta("SELECT * FROM mandino_taller_preguntas_opciones  WHERE fk_mtp = :id_mtp", array(":id_mtp" => $sql_mtp[$j]['mtp_id']));
        $contenido .= "<ul>";
        for ($h=0; $h <$sql_mtpo['cantidad_registros']; $h++) { 
          // Pregunta tipo radio
          switch ($sql_mtp[$j]['fk_mtpt']) {
            case '1':
              $contenido .= '<div class="custom-control custom-radio">
                            <input type="radio" id="re' . $sql_mtpo[$h]['mtpo_id'] . '" name="pre' . $sql_mtp[$j]['mtp_id'] . '" class="custom-control-input" value="' . $sql_mtpo[$h]['mtpo_id'] . '" required>
                            <label class="custom-control-label" for="re' . $sql_mtpo[$h]['mtpo_id'] . '">' . $sql_mtpo[$h]['mtpo_descripcion'] . '</label>
                          </div>';
              break;
            case '2':
              $contenido .= '<div class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" id="re' . $sql_mtpo[$h]['mtpo_id'] . '">
                              <label class="custom-control-label" for="re' . $sql_mtpo[$h]['mtpo_id'] . '">' . $sql_mtpo[$h]['mtpo_descripcion'] . '</label>
                            </div>';
              break;
          }
        }
        $contenido .= "</ul>";
      }
    }

    $db->desconectar();
    return "$contenido";
  }

  function registroTalleresIntentos(){
    $contenidoTabla = '';
    $aprobo = '';
    $cont = 0;
    $ultimaHora = 0;
    $contAprobado = 0;
    //Hacemos la conexion con la base de datos
    $db = new Bd();
    $db->conectar();

    //Realizamos la consulta
    $sql_mtu = $db->consulta('SELECT * FROM mandino_taller_usuarios WHERE fk_usuario = :fk_usuario AND fk_mt = :fk_mt', array(":fk_usuario" => $_POST['usu'], ":fk_mt" => $_POST['taller']));

    if ($sql_mtu['cantidad_registros'] > 0) {
      for ($i=0; $i <$sql_mtu['cantidad_registros']; $i++) { 
        $cont++;
        
        
        $horaInicio = new DateTime($sql_mtu[$i]['mtu_fecha_inicio']);
        $horaTermino = new DateTime($sql_mtu[$i]['mtu_fecha_final']);
        
        $interval = $horaInicio->diff($horaTermino);
        
        $porcentaje = ($sql_mtu[$i]['mtu_preguntas_correctas'] * 100)/$sql_mtu[$i]['mtu_preguntas_totales'];
        
        //Validamos el si aprobo segun el porcentaje
        if ($porcentaje >= 70) {
          $contAprobado++;
          $aprobo = "alert-success";
        }else{
          $aprobo = "alert-danger";
        }

        $contenidoTabla .= '<tr class="' . $aprobo . '">';
        $contenidoTabla .= '<td>' . $cont . '</td>';
        $contenidoTabla .= '<td>' . $sql_mtu[$i]['mtu_preguntas_correctas'] . '/' . $sql_mtu[$i]['mtu_preguntas_totales'] . '</td>';
        //$contenidoTabla .= '<td>' . round($porcentaje) . '%</td>';
        $contenidoTabla .= '<td>' . $interval->format('%H horas %i minutos %s segundos') . '</td>';    
        //$contenidoTabla .= '<td><button class="btn btn-info" onclick="revisionTaller(' . $sql_mtu[$i]['mtu_id'] . ')"><i class="fas fa-tasks"></i> Revisión</button></td>';
        $contenidoTabla .= '</tr>';
        
        $ultimaHora = $sql_mtu[$i]['mtu_fecha_final'];
      }
    }else{
      $contenidoTabla .= '<tr><td colspan="4">No se han encontrado registros</td></tr>';
    }

    $ultimaHora = date("Y-m-d H:i:s", strtotime('+24 hour', strtotime($ultimaHora)));

    //Validamos si tiene oportunidades adicionales
    $intentos_adicionles = $db->consulta("SELECT mlv_taller_intento_adicional FROM mandino_lecciones_visto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":fk_usuario" => $_REQUEST['usu'], ":fk_ml" => $_REQUEST['less']));

    if ($contAprobado <= 0) {
      if ($sql_mtu['cantidad_registros'] < ($intentos_adicionles[0]['mlv_taller_intento_adicional'] + 5)) {
        
        $contenidoTabla .= '<script type="text/javascript">
                              $(function(){
                                $("#btn-realizar-examen").removeClass("d-none");
                              });
                            </script>';

      }else if(date("Y-m-d H:i:s") > $ultimaHora){
        
        $db->sentencia("UPDATE mandino_lecciones_visto SET mlv_taller_intento_adicional = :inteto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":inteto" => ($intentos_adicionles[0]['mlv_taller_intento_adicional'] + 1), ":fk_usuario" =>  $_REQUEST['usu'], ":fk_ml" => $_REQUEST['less']));
        
        $contenidoTabla .= '<script type="text/javascript">
                              $(function(){
                                $("#btn-realizar-examen").removeClass("d-none");
                              });
                            </script>';
      }else{
        
        $tiempoInicio = new DateTime($ultimaHora);
        $tiempo = $tiempoInicio->diff(new DateTime());

        $contenidoTabla .= '<tr id="tiempo"><td colspan="4">Se desbloqueará en: <strong id="timerDiv">00:00:00</strong> </td></tr>';

        $contenidoTabla .= '<script type="text/javascript">
                              $(function(){
                                $("#alerta-tiempo").removeClass("d-none");
                                $("#timerDiv").timer("remove");
                                $("#timerDiv").timer({
                                  countdown: true,
                                  duration: "' . $tiempo->format('%Hh %im %ss') . '",
                                  callback: function() {  
                                    $("#btn-realizar-examen").removeClass("d-none");
                                    $("#tiempo").remove();
                                    agregarIntento(' . $intentos_adicionles[0]['mlv_taller_intento_adicional'] . ');
                                  },     
                                  format: "%H:%M:%S"  
                                });
                              });
                            </script>';
      }
    }

    $db->desconectar();
    return $contenidoTabla;
  }

  function agregarIntento(){
    $db = new Bd();
    $db->conectar();

    $db->sentencia("UPDATE mandino_lecciones_visto SET mlv_taller_intento_adicional = :inteto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":inteto" => ($_REQUEST['intento'] + 1), ":fk_usuario" =>  $_REQUEST['usu'], ":fk_ml" => $_REQUEST['less']));
    
    $db->desconectar();

    return json_encode(1);
  }

  function resolverTaller(){
    $uniqid = uniqid();
    $id_primario;
    $cont = 0;
    $id_respuesta_correcta = 0;
    $cont_respuesta_correcta = 0;
    $resp = "";
    $session = new Session();
    $usuario = $session->get('usuario');

    $db = new Bd();
    $db->conectar();

    $sql_insert_mtu = $db->sentencia("INSERT INTO mandino_taller_usuarios VALUES (NULL,  :mtu_uid, :fk_usuario, :fk_mt, :mtu_preguntas_correctas, :mtu_preguntas_totales, :mtu_fecha_inicio, :mtu_fecha_final)", array(":mtu_uid" => $uniqid,
                              ":fk_usuario" => $usuario['id'],
                              ":fk_mt" => $_POST['taller'],
                              ":mtu_preguntas_correctas" => 0,
                              ":mtu_preguntas_totales" => 0,
                              ":mtu_fecha_inicio" => $_POST['inicio'],
                              ":mtu_fecha_final" => date("Y-m-d H:i:s")
                              ));


    $sql_select_mtu = $db->consulta("SELECT * FROM mandino_taller_usuarios WHERE mtu_uid = :mtu_uid", array(":mtu_uid" => $uniqid));

    if ($sql_select_mtu['cantidad_registros'] == 1) {
      $id_primario = $sql_select_mtu[0]['mtu_id'];

      $sql_select_mtp = $db->consulta("SELECT * FROM mandino_taller_preguntas WHERE fk_mt = :fk_mt", array(":fk_mt" => $_POST['taller']));

      for ($i=0; $i <$sql_select_mtp['cantidad_registros']; $i++) { 
        # code...
        $sql_select_mtpo = $db->consulta("SELECT mtpo_id FROM mandino_taller_preguntas_opciones WHERE fk_mtp = :fk_mtp AND mtpo_correcta = 1", array(":fk_mtp" => $sql_select_mtp[$i]['mtp_id']));
        
        $id_respuesta_correcta = $sql_select_mtpo[0]['mtpo_id'];

        if ($id_respuesta_correcta == $_POST['pre' . $sql_select_mtp[$i]['mtp_id']]) {
          $cont_respuesta_correcta++;
        }


        $sql_insert_mtr = $db->sentencia("INSERT INTO mandino_taller_respuestas VALUES (NULL, :fk_mtu, :fk_mtp, :mtr_respuesta, :mtr_fecha_creacion)", array(":fk_mtu" => $id_primario, ":fk_mtp" => $sql_select_mtp[$i]['mtp_id'], ":mtr_respuesta" => $_POST['pre' . $sql_select_mtp[$i]['mtp_id']], ":mtr_fecha_creacion" => date("Y-m-d H:i:s")));

        $cont++;
      } 

      if ($sql_select_mtp['cantidad_registros'] == $cont) {
        $sql_update_mtu = $db->sentencia("UPDATE mandino_taller_usuarios SET mtu_preguntas_correctas = :mtu_preguntas_correctas, mtu_preguntas_totales = :mtu_preguntas_totales WHERE mtu_uid = :mtu_uid", array(":mtu_preguntas_correctas"=> $cont_respuesta_correcta, ":mtu_preguntas_totales" => $cont, ":mtu_uid"=> $uniqid));

        $porcentaje = ($cont_respuesta_correcta * 100)/$cont;
        if (round($porcentaje) > 70) {
          //Validamos cual es la ultima leccion de la unidad
          if (ultimaLeccion($_POST['unidad']) == $_POST['leccion']) {
            $siguienteUnidad = siguienteUnidad($_POST['unidad']);
            if (validarSiguienteUnidad($siguienteUnidad) == 1) {
              if (validarLeccion($siguienteUnidad) == 0) {
                if (actualizarVistoLeccion($usuario['id'], $_POST['leccion']) == 1) {
                  agregarVistoLeccion($usuario['id'], $siguienteUnidad);
                  $resp = "Ok";
                }else{
                  $resp = "Algo anda mal";
                }
              }else{
                $resp = "Ok";
              }
            }else{
              if (validarUltimaUnidad($_POST['unidad']) == $_POST['unidad']) {
                if (actualizarVistoLeccion($usuario['id'], $_POST['leccion']) == 1) {
                  //agregarVistoLeccion($usuario['id'], $siguienteUnidad);
                  $resp = "Ok";
                }else{
                  $resp = "Algo anda mal en este.";
                }
              }else{
                $resp = "No funca 2";
              }
            }
          }else{
            $resp = "No funca";
          }
        }else{
          $resp = "Ok";
        }
      }else{
        $resp = "No se ha encontrado las preguntas";
      }
    }else{
      $resp = "No se ha realizado el registro";
    }

    $db->desconectar();
    return $resp;
  }

  function siguienteUnidad($unidad){
    $db = new Bd();
    $db->conectar();
    $id = 0;

    $sql1 = $db->consulta("SELECT * FROM mandino_unidades WHERE mu_id = :mu_id", array(":mu_id" => $unidad));

    $sql2 = $db->consulta("SELECT * FROM mandino_unidades WHERE mu_orden = :mu_orden AND fk_mc = :fk_mc", array(":mu_orden" => $sql1[0]['mu_orden'] + 1, ":fk_mc" => $sql1[0]['fk_mc']));

    if ($sql2['cantidad_registros'] > 0) {
      $sql = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden ASC LIMIT 1", array(":fk_mu" => $sql2[0]['mu_id']));

      if ($sql['cantidad_registros'] > 0) {
        $id = $sql[0]['ml_id'];
      }
    }

    $db->desconectar();
    return $id;
  }

  function ultimaLeccion($unidad){
    $db = new Bd();
    $db->conectar();
    $id = "";
    $sql_select_ml = $db->consulta("SELECT * FROM mandino_lecciones WHERE fk_mu = :fk_mu ORDER BY ml_orden DESC LIMIT 1", array(":fk_mu" => $unidad));
    $db->desconectar();
    return $sql_select_ml[0]['ml_id'];
  }

  function validarSiguienteUnidad($leccion){
    $db = new Bd();
    $db->conectar();
    $cont = '';
    $sql_select_mu_ml = $db->consulta("SELECT * FROM mandino_unidades INNER JOIN mandino_lecciones ON mu_id = fk_mu WHERE ml_id = :ml_id", array(":ml_id" => $leccion));
    $db->desconectar();
    return $sql_select_mu_ml['cantidad_registros'];
  }

  function validarLeccion($leccion){
    $session = new Session();
    $usuario = $session->get('usuario');
    $db = new Bd();
    $db->conectar();
    $sql_select_mlv = $db->consulta("SELECT * FROM mandino_lecciones_visto WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":fk_usuario" => $usuario['id'], ":fk_ml" => $leccion));
 
    $db->desconectar();
    return $sql_select_mlv['cantidad_registros'];
  }

  function agregarVistoLeccion($usuario, $leccion){
    $db = new Bd();
    $db->conectar();
    $taller = validarLeccionTaller($leccion);

    $sql_insert_mlv = $db->sentencia("INSERT INTO mandino_lecciones_visto VALUES(NULL, :fk_usuario, :fk_ml, :mlv_fecha_creacion, :mlv_taller_aprobo, 0)", array(":fk_usuario" => $usuario, ":fk_ml" => $leccion, ":mlv_fecha_creacion" => date("Y-m-d H:i:s"), ":mlv_taller_aprobo" => $taller));
    $db->desconectar();
  }

  function actualizarVistoLeccion($usuario, $leccion){
    $db = new Bd();
    $db->conectar();
    $sql_insert_mlv = $db->sentencia("UPDATE mandino_lecciones_visto SET mlv_taller_aprobo = :mlv_taller_aprobo  WHERE fk_usuario = :fk_usuario AND fk_ml = :fk_ml", array(":fk_usuario" => $usuario, ":fk_ml" => $leccion, ":mlv_taller_aprobo" => 2));
    $db->desconectar();

    return 1;
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

  function validarUltimaUnidad($unidad){
    $db = new Bd();
    $db->conectar();

    $cursoActual = $db->consulta("SELECT * FROM mandino_unidades where mu_id = :mu_id", array(":mu_id" => $_POST['unidad']));

    $datos = $db->consulta("SELECT mu_id FROM mandino_unidades WHERE fk_mc = :fk_mc ORDER BY mu_orden DESC LIMIT 1", array(":fk_mc" => $cursoActual[0]['fk_mc']));

    $db->desconectar();

    return $datos[0]['mu_id'];
  }

  function revisionTaller(){
    $contenido = "";
    $id_taller = '';
    $db = new Bd();
    $db->conectar();

    $sql_select_mtu = $db->consulta('SELECT * FROM mandino_taller_usuarios WHERE mtu_id = :mtu_id', array(":mtu_id" => $_POST['id_mtu']));

    $id_taller = $sql_select_mtu[0]['fk_mt'];

    //Consulta del taller
    $sql_mt = $db->consulta("SELECT * FROM mandino_taller WHERE mt_id = :mt_id", array(":mt_id" => $id_taller));

    for ($i=0; $i <$sql_mt['cantidad_registros'] ; $i++) { 
      $contenido .= '<h2 class="text-hyundai titulo text-center pt-3">' . $sql_mt[$i]['mt_titulo'] . '</h2>';
      if ($sql_mt[$i]['mt_descripcion'] != NULL) {
        $contenido .= '<p class="mt-4">' . $sql_mt[$i]['mt_descripcion'] . '</p>';
      }
      //Buscamos la preguntas relacionadas con la encuesta
      $sql_mtp = $db->consulta("SELECT * FROM mandino_taller_preguntas WHERE fk_mt = :id_mt", array(":id_mt" => $sql_mt[$i]['mt_id']));

      for ($j=0; $j <$sql_mtp['cantidad_registros']; $j++) { 
        $contenido .= "<hr>";
        $contenido .= '<p>' . $sql_mtp[$j]['mtp_pregunta'] . '</p>';
        
        //Pruebas de respuesas
        $sql_select_mtr = $db->consulta("SELECT * FROM mandino_taller_respuestas WHERE fk_mtu = :fk_mtu AND fk_mtp = :fk_mtp", array(":fk_mtu" => $_POST['id_mtu'], ":fk_mtp" => $sql_mtp[$j]['mtp_id'] ));

        for ($h=0; $h <$sql_select_mtr['cantidad_registros'] ; $h++) { 
          //$contenido .= '<p>' . $fila_select_mtr['mtr_respuesta'] . '</p>';
          //Traemos la respuesta por pregunta
          $sql_mtpo = $db->consulta("SELECT * FROM mandino_taller_preguntas_opciones  WHERE fk_mtp = :id_mtp", array(":id_mtp" => $sql_mtp[$j]['mtp_id']));
          $contenido .= "<ul>";
          for ($a=0; $a < $sql_mtpo['cantidad_registros'] ; $a++) { 
            // Pregunta tipo radio
            if ($sql_select_mtr[$h]['mtr_respuesta'] == $sql_mtpo[$a]['mtpo_id'] && $sql_mtpo[$a]['mtpo_correcta'] == 1) {
              # code...
              $contenido .= '<li class="alert-success">' . $sql_mtpo[$a]['mtpo_descripcion'] . '</li>'; 
            }else if($sql_select_mtr[$h]['mtr_respuesta'] == $sql_mtpo[$a]['mtpo_id'] && $sql_mtpo[$a]['mtpo_correcta'] == 0){
              $contenido .= '<li class="alert-danger">' . $sql_mtpo[$a]['mtpo_descripcion'] . '</li>'; 
            }else if($sql_mtpo[$a]['mtpo_correcta'] == 1){
              $contenido .= '<li class="alert-warning">' . $sql_mtpo[$a]['mtpo_descripcion'] . '</li>'; 
            }else{
              $contenido .= '<li>' . $sql_mtpo[$a]['mtpo_descripcion'] . '</li>'; 
            }
          }
          $contenido .= "</ul>";
        }
      }
    }
    return $contenido;
  }

  if(@$_REQUEST['accion']){
    if(function_exists($_REQUEST['accion'])){
      echo($_REQUEST['accion']());
    }
  }
?>
