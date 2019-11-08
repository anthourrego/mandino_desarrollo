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

  require_once($ruta_raiz . 'clases/funciones_generales.php');
  require_once($ruta_raiz . 'clases/librerias.php');

  $lib = new Libreria;
?>
<!DOCTYPE html>
<html>
<head>
  <?php  
    echo $lib->metaTagsRequired();
  ?>

  <title>Mandino | Registro</title>
  <?php  
    echo $lib->iconoPag();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->bootstrapSelect();
    echo $lib->fontAwesome();
    echo $lib->jqueryValidate();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
</head>
<body class="container bg-transparent">
  <nav class="navbar navbar-light bg-transparent">
    <a class="navbar-brand" href="index">
      <img src="img/logo.png" alt="">
    </a>
  </nav>

  <hr>

  <form id="formCrearUsuarioAsodelco" class="mt-3" autocomplete="off" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="CrearUsuarioAsodelco">
    <div class="row">
      <div class="form-group col-12 col-md-6">
        <label>Nro Documento <span class="text-danger">*</span></label>
        <input class="form-control" type="text" id="nro_doc" name="nro_doc" onkeypress="return soloNumeros(event);" required>
      </div>
      <div class="form-group col-12 col-md-6">
        <label>Usuario <span class="text-danger">*</span></label>
        <input class="form-control" type="text" id="usuario" name="usuario" required>
      </div>
      <div class="form-group col-12 col-md-6">
        <label>Primer Nombre <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="primer_nombre" id="primer_nombre" required>
      </div>
      <div class="form-group col-12 col-md-6">
        <label>Segundo Nombre</label>
        <input class="form-control" type="text" name="segundo_nombre" id="segundo_nombre">
      </div>
      <div class="form-group col-12 col-md-6">
        <label>Primer Apellido <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="primer_apellido" id="primer_apellido" required>
      </div>
      <div class="form-group col-12 col-md-6">
        <label>Segundo Apellido</label>
        <input class="form-control" type="text" name="segundo_apellido" id="segundo_apellido">
      </div>
      <div class="form-group col-12 col-md-6">
        <label>Correo <span class="text-danger">*</span></label>
        <input class="form-control" type="email" name="correo" id="correo" required>
      </div>
      <div class="form-group col-12 col-md-6">
        <label>Teléfono</label>
        <input class="form-control" type="tel" name="telefono" onkeypress="return soloNumeros(event);" id="telefono">
      </div>
      <div class="form-group col-12 col-md-6">
        <label for="">Departamento <span class="text-danger">*</span></label>
        <select class="selectpicker form-control" required id="departamentos" name="departamentos" data-live-search="true" data-size="5" title="Seleccione un departamento"></select>
      </div>
      <div class="form-group col-12 col-md-6">
        <label for="">Ciudades <span class="text-danger">*</span></label>
        <select class="selectpicker form-control" required disabled id="ciudades" name="ciudades" data-live-search="true" data-size="5" title="Seleccione una ciudad"></select>
      </div>
      <div class="form-group col-12 col-md-6">
        <label for="">Empresa <span class="text-danger">*</span></label>
        <select class="selectpicker form-control" required id="empresa" name="empresa" data-live-search="true" data-size="5" title="Seleccione una empresa">
          <option value="Navarro">Navarro</option>
          <option value="Luma">Luma</option>
          <option value="Electrocreditos del cauca">Electrocreditos del cauca</option>
          <option value="Diselco">Diselco</option>
          <option value="Lagobo">Lagobo</option>
          <option value="Emuebles">Emuebles</option>
          <option value="Asyco">Asyco</option>
          <option value="Best buy">Best buy</option>
        </select>
      </div>
    </div>
    <div class="text-center mt-3">
      <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Vaciar Campos</button>
      <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
    </div>
  </form>

  <div class="modal fade" id="completo" tabindex="-1" role="dialog" aria-labelledby="modal_recaptchaLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static" data-focus="false">
  	<div class="modal-dialog modal-dialog-centered" role="document">
  		<div class="modal-content">
  			<div class="modal-header">
  				<h5 class="modal-title">Registro completo</h5>
  			</div>
  			<div class="modal-body">
          La contraseña para el ingreso a la plataforma por defecto es el número de documento digitado en el formulario anterior.
  			</div>
  			<div class="modal-footer justify-content-center text-center">
          <a href="index" class="btn btn-primary">Cerrar</a>
  			</div>
  		</div>
  	</div>
  </div>
</body>
<script type="text/javascript">
  $(function(){
    $("#nro_doc").focusout(function(){
      if ($("#nro_doc").val() != "") {
        $.ajax({
          url: '<?php echo($ruta_raiz); ?>modulos/configuracion/usuarios/acciones',
          type: 'POST',
          dataType: 'html',
          data: {accion: 'validarNroDocumento', nro_doc: $("#nro_doc").val()},
          success: function(data){
            if (data != 0) {
              alertify.error("El nro de documento " + $("#nro_doc").val() + " ya se encuentra registrado.");
              $("#nro_doc").focus();
            }
          },
          error: function(){
            alertify.error("No se ha validado el número de documento");
          }
        });
      }
    });

    $("#usuario").focusout(function(){
      if ($("#usuario").val() != "") {
        $.ajax({
          url: '<?php echo($ruta_raiz); ?>modulos/configuracion/usuarios/acciones',
          type: 'POST',
          dataType: 'html',
          data: {accion: 'validarUsuario', usuario: $("#usuario").val()},
          success: function(data){
            if (data != 0) {
              alertify.error("El usuario " + $("#usuario").val() + " ya existe.");
              $("#usuario").focus();
            }
          },
          error: function(){
            alertify.error("No se ha validado el usuario");
          }
        });
      }
    });

    //Se cargan los departamentos y la ciudades

    $.ajax({
      url: "<?php echo($ruta_raiz); ?>modulos/configuracion/usuarios/acciones",
      type: "POST",
      dataType: "json",
      cache: false,
      data: {accion: "departamentos"},
      success: function(data){

        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#departamentos").append(`
            <option value="${data[i].d_id}">${data[i].d_nombre}</option>
          `);

          $("#editdepartamentos").append(`
            <option value="${data[i].d_id}">${data[i].d_nombre}</option>
          `);
        }
        $("#departamentos").selectpicker('refresh');
        $("#editdepartamentos").selectpicker('refresh');
      },
      error: function(){
        alertify.error("No se han cargado la departamentos");
      }
    });

    $("#departamentos").on('change', function() {
      $.ajax({
        url: "<?php echo($ruta_raiz); ?>modulos/configuracion/usuarios/acciones",
        type: "POST",
        dataType: "json",
        cache: false,
        data: {accion: "ciudades", dep: $(this).val()},
        success: function(data){
          $("#ciudades").attr("disabled", false);
          $("#ciudades").empty();
          for (let i = 0; i < data.cantidad_registros; i++) {
            $("#ciudades").append(`
              <option value="${data[i].m_id}">${data[i].m_nombre}</option>
            `);
          }
          $("#ciudades").selectpicker('refresh');
          $("#ciudades").focus().select();
        },
        error: function(){
          alertify.error("Error al cargar las ciudades");
        }
      });
    });

    $("#formCrearUsuario").validate({
      debug: true,
      rules: {
        nro_doc: {
          required: true,
          number: true
        },
        usuario: "required",
        primer_nombre: "required",
        primer_apellido: "required",
        correo: {
          required: true,
          email: true
        },
        departamentos: "required",
        ciudades: "required"
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

    $("#formCrearUsuarioAsodelco").submit(function(event){
      event.preventDefault();
      if($("#formCrearUsuarioAsodelco").valid()){
        $.ajax({
          url: '<?php echo($ruta_raiz); ?>modulos/configuracion/usuarios/acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              $("#completo").modal("show");
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("No se ha podido hacer el registro.");
          }
        });
      }
    });
  });
</script>
</html>