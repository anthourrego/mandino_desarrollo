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
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container mt-5">
		<h1 class="titulo text-hyundai text-center text-lg-left"></h1>
    <div id="contenido-modulos">
      
    </div>
  </div>
</body>
<script type="text/javascript">
  $(function(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosCurso', curso: <?php echo($_GET['curso']); ?>},
      success: function(data){
        if (data != false) {
          $(".titulo").html(data.mc_nombre);
        }else{
          window.location.href = "<?php echo($ruta_raiz); ?>cursos";
        }
      },
      error: function(){
        alertify.error("No se ha podido traer el nombre del curso");
      }
    });
      
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'modulosUnidades', curso: '<?php echo($_GET['curso']); ?>', user: '<?php echo $usuario['id']; ?>'},
      success: function(data){
        $("#contenido-modulos").html(data);
      },
      error: function(){
        alertify.error("No se han podido traer los modulos");
      }
    });
    
  });
</script>
<?php 
  echo $lib->cambioPantalla();
?>
</html>