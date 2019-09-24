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
  $id_usuario = 0;

  if (@$_REQUEST['id_usuario']) {
    $id_usuario = $_REQUEST['id_usuario'];
  } else {
    $id_usuario = $usuario['id'];
  }

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
  <?php 
    if (@!$_REQUEST['id_usuario']){
      include_once($ruta_raiz . 'navBar.php'); 
    }
  ?>
  <!-- Contenido -->
  <div class="container mt-4" >
		<h1 class="titulo text-hyundai text-center text-lg-left">¡Ya estás cerca de tu objetivo!</h1>
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

<div class="modal fade" id="modalFelicitaciones" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Vendedor del Mes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="<?php echo($ruta_raiz); ?>img/vendedor/01.gif" class="w-100" alt="">
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	$(function(){
		$.ajax({
			url: 'acciones',
			type: 'POST',
			dataType: 'html',
			data: {accion: "centralCursos", id_usu: <?php echo($id_usuario); ?>},
			success: function(datos){
				$("#cursos").empty();
				$("#cursos").html(datos);
			},
			error: function(){
				alertify.error("No se han cargado los cursos");
			}
		});	

    <?php 
      if (@!$_REQUEST['id_usuario']) {
        echo('$("#modalFelicitaciones").modal("show");');	
      }
    ?>
	});

  function mostrarInfo(val){
    $("#infoContenido").html(val);
    $("#modalInfoCurso").modal("show");
  }

  function unidades(id){
    <?php
      if (@!$_REQUEST['id_usuario']){
        echo 'window.location.href = "unidades?curso="+id;'; 
      }else{
        echo 'window.location.href = "unidades?curso="+id+"&id_usuario=' . $id_usuario . '";'; 
      }
    ?>
  }
</script>
<?php 
  if (@!$_REQUEST['id_usuario']){
    echo $lib->cambioPantalla();
  }
?>
</html>