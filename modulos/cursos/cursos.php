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


  <!-- Modal e Información -->
  <div class="modal fade" id="modalInfoCurso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Información Curso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="infoContenido"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</body>
<script type="text/javascript">
	$(function(){
		$.ajax({
			url: 'acciones',
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

  function mostrarInfo(val){
    $("#infoContenido").html(val);
    $("#modalInfoCurso").modal("show");
  }
</script>
<?php 
  echo $lib->cambioPantalla();
?>
</html>