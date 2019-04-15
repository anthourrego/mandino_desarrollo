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

  $session = new Session();

  $usuario = $session->get("usuario");

  $lib = new Libreria;

  $db = new Bd();
  $db->conectar();
  $tema = "";

  $sql_tema = $db->consulta("SELECT * FROM mandino_temas ORDER BY mt_id ASC");

  for ($i=0; $i < $sql_tema['cantidad_registros']; $i++) { 
    if($sql_tema[$i]['mt_id'] == $usuario['tema']){
      $tema .= '<div class="custom-control custom-radio mb-3">
                <input type="radio" class="custom-control-input" value="' . $sql_tema[$i]['mt_id'] . '" id="tema-' . $sql_tema[$i]['mt_id'] . '" name="radio-tema" required checked>
                <label class="custom-control-label w-100" for="tema-' . $sql_tema[$i]['mt_id'] . '">
                  <nav class="navbar ' . $sql_tema[$i]['mt_navbar'] . '">
                    <div class="navbar-brand">
                      <img src="' . $ruta_raiz . 'img/' . $sql_tema[$i]['mt_logo_navbar'] . '" class="w-100" alt="">
                    </div>
                  </nav>
                </label>
              </div>';
    }else{
     $tema .= '<div class="custom-control custom-radio mb-3">
                <input type="radio" value="' . $sql_tema[$i]['mt_id'] . '" class="custom-control-input" id="tema-' . $sql_tema[$i]['mt_id'] . '" name="radio-tema" required>
                <label class="custom-control-label w-100" for="tema-' . $sql_tema[$i]['mt_id'] . '">
                  <nav class="navbar ' . $sql_tema[$i]['mt_navbar'] . '">
                    <div class="navbar-brand">
                      <img src="' . $ruta_raiz . 'img/' . $sql_tema[$i]['mt_logo_navbar'] . '" class="w-100" alt="">
                    </div>
                  </nav>
                </label>
              </div>';
    }
  }

  $db->desconectar();

?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
  <?php  
    echo $lib->jquery();
    echo $lib->mandino();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->alertify();
    echo $lib->jqueryValidate();
  ?>
</head>
<body class="bg-white">
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
              <div class="col-12">
                <b>Nro Documento:</b>
              </div>
              <div class="col-12">
                <span id="nro_documento"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <b>Usuario:</b>
              </div>
              <div class="col-12">
                <span id="usuario"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <b>Nombre:</b>
              </div>
              <div class="col-12">
                <span id="nombre"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <b>Correo Corportativo:</b>
                <span id="correo_corporativo"></span>
              </div>
              <div class="col-12">
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <b>Correo Personal:</b>
              </div>
              <div class="col-12">
                <span id="correo_personal"></span>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <b>Teléfono:</b>
              </div>
              <div class="col-12">
                <span id="telefono"></span>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="cambio-password" role="tabpanel" aria-labelledby="list-profile-list">
            <form id="formPass" class="needs-validation" novalidate autocomplete="nope">
              <input type="hidden" id="id_usuario" name="id_usuario" value="">
              <input type="hidden" name="accion" value="actualizarPass">
              <div class="form-group">
                <label>Contraseña Actual *</label>
                <input class="form-control" type="password" id="pass_actual" name="pass_actual" required>
              </div>
              <div class="form-group">
                <label>Contraseña Nueva *</label>
                <input class="form-control" type="password" id="pass_nuevo" name="pass_nuevo" required>
              </div>
              <div class="form-group">
                <label>Repetir Contraseña Nueva *</label>
                <input class="form-control" type="password" id="pass_renuevo" name="pass_renuevo" required>
              </div>
              <div class="text-center">
                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Actualizar Contraseña</button>
              </div>
            </form>
          </div>
          <div class="tab-pane fade" id="temas" role="tabpanel" aria-labelledby="list-messages-list">
            <form id="formTema">
              <input type="hidden" id="id_usuario_tema" name="id_usuario" value="">
              <input type="hidden" name="accion" value="actualizarTema">
              <?php echo($tema); ?>
              <div class="text-center">
                <button class="btn btn-success" type="submit"><i class="far fa-save"></i> Guardar</button>
              </div>
            </form>
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
        $("#id_usuario").val(data.u_id);
        $("#id_usuario_tema").val(data.u_id);
      },
      error: function(){
        top.alertify.error("No han podido traer los datos usuarios");
      }
    });
    
    $("#formPass").validate({
      debug: false,
      rules:{
        pass_actual: "required",
        pass_nuevo: "required",
        pass_renuevo: {
          required: true,
          equalTo: "#pass_nuevo"
        }
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

    $("#formPass").submit(function(event){
      event.preventDefault();
      if ($("#formPass").valid()) {
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              alertify.success("Se ha actualizado correctamente");
              $("#formPass")[0].reset();
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("Error al cambiar la contraseña");
          }
        });
      }
    });

    $("#formTema").submit(function(event){
      event.preventDefault();
      $.ajax({
        url: 'acciones',
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: new FormData(this),
        success: function(data){
          if (data == "Ok") {
            alertify.success("Tema actualizado");
            setTimeout(function() {
              top.location.reload();
            }, 1000);

          }else{
            alertify.error(data);
          }
        },
        error: function(){
          alertify.error("No ha actualizado el tema");
        }
      });
      
    });

  });
</script>
</html>