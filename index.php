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

  //require_once($ruta_raiz . 'clases/funciones_generales.php');
  //require_once($ruta_raiz . 'clases/Session.php');
  require_once($ruta_raiz . 'clases/librerias.php');

  /*$session = new Session();

  if(@$session->exist('usuario')){
    header('location: '. $ruta_raiz . 'central');
    die();
  }*/

  $lib = new Libreria;
?>
<!DOCTYPE html>
<html>
<head>
  <?php  
    echo $lib->metaTagsRequired();
  ?>

  <title>Mandino</title>
  <?php  
    echo $lib->iconoPag();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->jqueryValidate();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>

  <style media="screen">
      html,body {
        background: url('img/bg.jpg') no-repeat center center fixed;
        background-color: rgba(0,0,0,0.8);
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        overflow: hidden;
      }
      .centrar {
        height: 85vh;
      }

      /* Extra pequeño */
      @media (max-width: 576px) {
        html, body{
          background: url('img/bg-small.png') no-repeat center center fixed;
          background-color: rgba(0,0,0,0.8);
          -webkit-background-size: cover;
          -moz-background-size: cover;
          -o-background-size: cover;
          background-size: cover;
          overflow: hidden;
        }
      }

      /* Pantalla Pequeña */
      @media (min-width: 577px) and (max-width: 768px) {
        html, body{
          background: url('img/bg-small.png') no-repeat center center fixed;
          background-color: rgba(0,0,0,0.8);
          -webkit-background-size: cover;
          -moz-background-size: cover;
          -o-background-size: cover;
          background-size: cover;
          overflow: hidden;
        }
      }

    </style>
</head>
<body class="container">
  <nav class="navbar navbar-light bg-trasparent">
    <a class="navbar-brand" href="<?= rutaBase ?>">
      <img src="img/logo1.png" alt="">
    </a>
  </nav>

  <div class="row centrar justify-content-sm-center justify-content-lg-start">
    <div class="col-12 col-sm-8 col-lg-4 align-self-start align-self-lg-center">
      <h1 class="text-white titulo text-center text-lg-left">Evoluciona con Mandino</h1>
      <p class="text-white">Una plataforma digital que alimenta nuestras fortalezas individuales y nos hace mejorar como equipo.</p>
      <div class="card" style="background-color: rgba(255, 255, 255, 0.8)">
        <div class="card-body">
          <form id="formLogin" name="formLogin" method="post" autocomplete="off" class="needs-validation" novalidate>
            <input type="hidden" name="accion" value="formLogin">
            <div class="form-group">
              <input class="form-control" type="text" id="m_usuario" name="m_usuario" placeholder="Usuario o Nro Documento" autofocus required>
            </div>
            <div class="form-group">
              <div class="input-group">
                <input class="form-control" type="password" id="m_password" name="m_password" placeholder="Contraseña" required>
                <div class="input-group-append">
                  <button class="btn btn-secondary btn-login" type="button" id="btnEye" data-toggle="button" aria-pressed="false" autocomplete="off"><i id="passicon" class="fas fa-eye"></i></button>
                </div>
              </div>
            </div>
            <div class="text-center mt-3">
              <button class="btn btn-primary rounded-pill" type="submit" name="btnIngresar">Ingresar <i class="fas fa-sign-in-alt"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> 
</body>
<script type="text/javascript">
  $(function(){
    $("#btnEye").on("click", function(){
      if ($("#btnEye").attr("aria-pressed") == "false") {
        $("#passicon").removeClass("fa-eye");
        $("#passicon").addClass("fa-eye-slash");
        $("#m_password").attr("type", "text");
      }else if ($("#btnEye").attr("aria-pressed") == "true") {
        $("#passicon").removeClass("fa-eye-slash");
        $("#passicon").addClass("fa-eye");
        $("#m_password").attr("type", "password");
      }
    });

    $("#formLogin").validate({
      debug: false,
      rules:{
        usuario: "required",
        password: "required"
      },
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
        $(element).removeClass('is-valid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
        $(element).addClass('is-valid');
      }
    });

    $("#formLogin").submit(function(event) {
      event.preventDefault();
      if($("#formLogin").valid()){
        $.ajax({
          type: "POST",
          url: "ajax/login",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              window.location.href="central";
            }else{
              alertify.error(data);
            }
          },
          error: function(){
              alertify.error("No se ha encontrado el archivo");
          }
        });
      }
    });
  }); 
</script>
</html>