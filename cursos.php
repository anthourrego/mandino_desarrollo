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
  <title></title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->mandino();
  ?>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container mt-4">
		<h1 class="titulo text-hyundai text-center text-lg-left">¡Ya estas cerca de tu objetivo!</h1>
    <div id="cursos" class="row mt-2"></div>
  </div>
</body>
<script type="text/javascript">
	$(function(){
		$.ajax({
			url: '<?php echo $ruta_raiz; ?>ajax/cursos',
			type: 'POST',
			dataType: 'html',
			data: {accion: "centralCursos", id_usu: <?php echo($usuario['id']); ?>},
			success: function(datos){
				$("#cursos").empty();
				$("#cursos").html(datos);
			},
			error: function(){
				alertify.error("No se han cargado los cursos");
			}
		});		
	});
</script>
<?php 
  echo $lib->cambioPantalla();
?>
</html>