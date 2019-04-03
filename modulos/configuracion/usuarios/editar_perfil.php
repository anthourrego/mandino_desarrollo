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
  ?>
</head>
<body>
  <!-- Contenido -->
  <div class="container mt-4">
    <div class="row">
      <div class="col-5">
        <img class="w-100 img-thumbnail mb-2" src="<?php echo RUTA_ALMACENAMIENTO . $usuario['foto']; ?>">
        <div class="list-group" id="list-perfil" role="tablist">
          <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#pefil" role="tab" aria-controls="home">Perfil</a>
          <a class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#cambio-password" role="tab" aria-controls="profile">Cambiar Contraseña</a>
          <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#temas" role="tab" aria-controls="messages">Temas</a>
        </div>
      </div>
      <div class="col-7">
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="pefil" role="tabpanel" aria-labelledby="list-home-list">
            <div class="row d-flex align-items-center">
              <div class="col-5">
                Nro Documento:
              </div>
              <div class="col-7">
                <span id="nro_documento"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-5">
                Usuario:
              </div>
              <div class="col-7">
                <span id="usuario"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-5">
                Nombre:
              </div>
              <div class="col-7">
                <span id="nombre"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-5">
                Correo Corportativo:
              </div>
              <div class="col-7">
                <span id="correo_corporativo"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-5">
                Correo Personal:
              </div>
              <div class="col-7">
                <span id="correo_personal"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-5">
                Teléfono:
              </div>
              <div class="col-7">
                <span id="telefono"></span>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="cambio-password" role="tabpanel" aria-labelledby="list-profile-list">
            <form>
              
            </form>
          </div>
          <div class="tab-pane fade" id="temas" role="tabpanel" aria-labelledby="list-messages-list">
            Temas
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
<script type="text/javascript">
  $(function(){
    setTimeout(function() {
     top.$("#cargando").modal("hide");
    }, 1000);

    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosUsuario', id_usu: <?php echo($usuario['id']); ?>},
      success: function(data){
        $("#nro_documento").html(data.u_nro_documento);
        $("#usuario").html(data.u_usuario);
        $("#nombre").html(data.u_nombre1 + " " + data.u_nombre2 + " " + data.u_apellido1 + " " + data.u_apellido2);
        $("#correo_corporativo").html(data.u_correo_corporativo);
        $("#correo_personal").html(data.u_correo_personal);
        $("#telefono").html(data.u_telefono);

      },
      error: function(){
        top.alertify.error("No han podido traer los datos usuarios");
      }
    });
    
  });

  (function() {
    'use strict';
      window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
</script>
</html>