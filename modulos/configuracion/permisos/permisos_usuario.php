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
  include_once($ruta_raiz . 'clases/Permisos.php');
  include_once($ruta_raiz . 'clases/Conectar.php');

  $session = new Session();
  $lib = new Libreria();
  $permisos = new Permisos();
  $usuario = $session->get("usuario");

  if($permisos->validarPermiso($usuario['id'], 'usuarios_permisos') == 0) {
    echo '<script type="text/javascript">
            top.window.location.href="' . RUTA_RAIZ . 'central"
          </script>';
  }

  function permisos($id = 0){
    $permisos = new Permisos();
    $resp = "";
    $checked = "";
    $db = new Bd();
    $db->conectar();

    if($id == 0){
      $sql = $db->consulta("SELECT * FROM mandino_permisos WHERE fk_mp IS NULL"); 
    }else{
      $sql = $db->consulta("SELECT * FROM mandino_permisos WHERE fk_mp = :fk_mp", array(":fk_mp" => $id));
    }

    if ($sql['cantidad_registros'] != 0) {
      $resp .= "<ul>";
      
      for ($i=0; $i <$sql['cantidad_registros']; $i++) { 

        if ($permisos->validarPermiso($_GET['idUsu'], $sql[$i]['mp_nombre'])) {
          $checked = "checked";
        }else{
          $checked = "";
        }

        $resp .= '<div class="custom-control custom-switch">
                    <input type="checkbox" ' . $checked . ' name="per[]" value="' . $sql[$i]['mp_id'] . '" class="custom-control-input" id="permiso' . $sql[$i]['mp_id'] . '">
                    <label class="custom-control-label" for="permiso' . $sql[$i]['mp_id'] . '">' . $sql[$i]['mp_tag'] . '</label>
                  </div>';

        $sql2 = $db->consulta("SELECT * FROM mandino_permisos WHERE fk_mp = :fk_mp", array(":fk_mp" => $sql[$i]['mp_id']));

        if ($sql2['cantidad_registros'] != 0) {
          for ($j=0; $j < $sql2['cantidad_registros']; $j++) { 

            if ($permisos->validarPermiso($_GET['idUsu'], $sql2[$j]['mp_nombre'])) {
              $checked = "checked";
            }else{
              $checked = "";
            }

            $resp .= "<ul>";
            $resp .= '<div class="custom-control custom-switch">
                        <input type="checkbox" ' . $checked . ' name="per[]" value="' . $sql2[$j]['mp_id'] . '" class="custom-control-input" id="permiso' . $sql2[$j]['mp_id'] . '">
                        <label class="custom-control-label" for="permiso' . $sql2[$j]['mp_id'] . '">' . $sql2[$j]['mp_tag'] . '</label>
                      </div>';
            $resp .= permisos($sql2[$j]['mp_id']);
            $resp .= "</ul>";
          }
        }

      }

      $resp .= "</ul>";
    }
    

    $db->desconectar();

    return $resp;
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
  <?php  
    echo $lib->jquery();
    echo $lib->mandino();
    echo $lib->fontawesome();
    echo $lib->alertify();
    echo $lib->bootstrap();
    echo $lib->jqueryValidate();
  ?>
</head>
<body class="bg-white">
  <!-- Contenido -->
  <div class="container mt-4">
    <form id="formPermisos">
      <input type="hidden" name="accion" value="formPemisos">
      <input type="hidden" name="usuarioCreador" value="<?php echo($usuario['id']); ?>">
      <input type="hidden" name="idUsu" value="<?php echo($_GET['idUsu']); ?>">
      <?php echo(permisos()); ?>
      
      <div class="text-center">
        <button class="btn btn-success" type="submit"><i class="far fa-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</body>
<script type="text/javascript">
  $(function(){
    setTimeout(function() {
     top.$("#cargando").modal("hide");
    }, 1000);


    $("#formPermisos").submit(function(event){
      event.preventDefault();
      $.ajax({
        url: 'acciones',
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: new FormData(this),
        success: function(data){
          top.alertify.success("Se han actualizado los permisos correctamente.");
        },
        error: function(){
          top.alertify.error("Error al enviar");
        }
      });
    })
    
  });
</script>
</html>