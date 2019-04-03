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

  $usuario = $session->get("usuario");

  $lib = new Libreria;

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
	<?php include_once('navBar.php'); ?>
  <!-- Contenido -->
  <div class="container-fluid">
    <div class="row mt-5 mb-5">
      <div class="col-12 col-md-3">
        <h4 id="titulo-unidad" class="titulo text-hyundai my-2 text-center text-md-left"></h4>
        <div id="menuLecciones" class="list-group rounded">
          
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
<script type="text/javascript">
  $(function(){
    $.ajax({
      url: '<?php echo($ruta_raiz); ?>ajax/unidades',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosUnidad', unidad: <?php echo($_GET['uni']); ?>},
      success: function(data){
        if (data != false) {
          $("#titulo-unidad").html(data.mu_nombre);
        }else{
          window.history.go(-1);
        }
      },
      error: function(){
        alertify.error("No se ha cargado los datos de la unidad");
      }
    });

    $.ajax({
      url: '<?php echo($ruta_raiz); ?>ajax/lecciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'menuLecciones', unidad: <?php echo($_GET['uni']); ?>, usuario: <?php echo($usuario['id']); if (@$_GET['less']){ echo(', less: ' . $_GET['less']);} ?>},
      success: function(data){
        $("#menuLecciones").html(data);
      },
      error: function(){
        alertify.error("No ha cargado el menú de lecciones");
      }
    });
  });
</script>
<?php 
  echo $lib->cambioPantalla();
?>
</html>