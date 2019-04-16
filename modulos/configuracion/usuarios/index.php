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
  include_once($ruta_raiz . 'clases/funciones_generales.php');
  include_once($ruta_raiz . 'clases/sessionActiva.php');
  include_once($ruta_raiz . 'clases/Permisos.php');
  include_once($ruta_raiz . 'clases/Conectar.php');

  $session = new Session();
  $permisos = new Permisos();
  $usuario = $session->get("usuario");
  $lib = new Libreria;

  if ($permisos->validarPermiso($usuario['id'], "usuarios") == 0) {
    header('Location: ' . $ruta_raiz . 'modulos/cursos/cursos');
  }

?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->datatables();
    echo $lib->bsCustomFileInput();
    echo $lib->jqueryValidate();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container-fluid mt-4">
    <div class="d-flex justify-content-between mt-3 mb-3">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-primary btn-usu active" value="1"><i class="fas fa-user-check"></i> Activos</button>
        <button type="button" class="btn btn-danger btn-usu" value="0"><i class="fas fa-user-alt-slash"></i> Inactivos</button>
        <button type="button" class="btn btn-secondary btn-usu" value="2"><i class="fas fa-users"></i> Todos</button>
      </div>
      <?php  

        if ($permisos->validarPermiso($usuario['id'], "usuarios_crear")) {
          echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#crearUsuario"><i class="fas fa-user-plus"></i> Crear</button>');
        }

      ?>
    </div>
    <table id="tabla" class="table table-bordered table-hover table-sm">
      <thead>
        <tr>
          <th class="text-center">Fecha Creacion</th>
          <th class="text-center">Nro Documento</th>
          <th class="text-center">Usuario</th>
          <th class="text-center">Nombre</th>
          <th class="text-center">Telefono</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody id="contenido_tabla_coordinadores">
    
      </tbody>
    </table>
  </div>

  <div class="modal fade" id="crearUsuario" tabindex="-1" role="dialog" aria-labelledby="crearUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="crearUsuarioLabel"><i class="fas fa-user-plus"></i> Crear Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearUsuario" autocomplete="off" enctype="multipart/form-data">
          <input type="hidden" name="accion" value="crearUsuario">
          <div class="modal-body">
            <div class="row">
              <div class="col-6 text-center">
                <img id="imagen" class="w-75 img-thumbnail rounded" src="<?php echo($ruta_raiz); ?>almacenamiento/foto-usuario/0.png">
              </div>
              <div class="col-6"> 
                <label>Foto</label>
                <div class="custom-file mb-3">
                  <input type="file" class="custom-file-input" name="foto[]" id="foto">
                  <label class="custom-file-label" for="foto[]" data-browse="Elegir">Seleccionar Archivo</label>
                </div>
                <div class="form-group">
                  <label>Nro Documento <span class="text-danger">*</span></label>
                  <input class="form-control" type="text" id="nro_doc" name="nro_doc" onkeypress="return soloNumeros(event);" required>
                </div>
                <div class="form-group">
                  <label>Usuario <span class="text-danger">*</span></label>
                  <input class="form-control" type="text" id="usuario" name="usuario" required>
                </div>
              </div>
              <div class="form-group col-6">
                <label>Primer Nombre <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="primer_nombre" id="primer_nombre" required>
              </div>
              <div class="form-group col-6">
                <label>Segundo Nombre</label>
                <input class="form-control" type="text" name="segundo_nombre" id="segundo_nombre">
              </div>
              <div class="form-group col-6">
                <label>Primer Apellido <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="primer_apellido" id="primer_apellido" required>
              </div>
              <div class="form-group col-6">
                <label>Segundo Apellido</label>
                <input class="form-control" type="text" name="segundo_apellido" id="segundo_apellido">
              </div>
              <div class="form-group col-6">
                <label>Correo <span class="text-danger">*</span></label>
                <input class="form-control" type="email" name="correo" id="correo" required>
              </div>
              <div class="form-group col-6">
                <label>Teléfono</label>
                <input class="form-control" type="tel" name="telefono" onkeypress="return soloNumeros(event);" id="telefono">
              </div>
              <div>
                
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Vaciar Campos</button>
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Modal Editar Usuario -->
  <div class="modal fade" id="modal-editarUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="crearUsuarioLabel"><i class="fas fa-user-edit"></i> Editar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditarUsuario" autocomplete="off" enctype="multipart/form-data">
          <input type="hidden" name="accion" value="editarUsuario">
          <input type="hidden" name="editarIdUsu" id="editarIdUsu">
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-12 col-md-6">
                <label>Nro Documento</label>
                <input class="form-control" type="text" name="editNro_doc" id="editNro_doc" readonly>
              </div>
              <div class="form-group col-12 col-md-6">
                <label>Usuario</label>
                <input class="form-control" type="text" name="editUsuario" id="editUsuario" readonly>
              </div>
              <div class="form-group col-12 col-md-6">
                <label>Primer Nombre <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="editNombre1" name="editNombre1" required>
              </div>
              <div class="form-group col-12 col-md-6">
                <label>Segundo Nombre</label>
                <input class="form-control" type="text" id="editNombre2" name="editNombre2">
              </div>
              <div class="form-group col-12 col-md-6">
                <label>Primer Apellido <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="editApellido1" name="editApellido1" required>
              </div>
              <div class="form-group col-12 col-md-6">
                <label>Segundo Apellido</label>
                <input class="form-control" type="text" id="editApellido2" name="editApellido2">
              </div>
              <div class="form-group col-12 col-md-6">
                <label>Correo</label>
                <input class="form-control" type="email" id="editCorreo" name="editCorreo">
              </div>
              <div class="form-group col-12 col-md-6">
                <label>Teléfono</label>
                <input class="form-control" type="text" onkeypress="return soloNumeros(event);" id="editTelefono"  name="editTelefono">
              </div>
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-center">
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Permisos -->
  <div class="modal fade" id="modal-permisos" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class='fas fa-user-shield'></i> Permisos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body mb-4" id="contenido-permiso"></div>
      </div>
    </div>
  </div>

</body>
<?php 
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    var idBoton = 1;
    cargarUsuarios(idBoton);
    $(".btn-usu").on("click", function(){
      $(".btn-usu").removeClass('active');
      $(this).addClass('active');
      idBoton = $(this).val();
      cargarUsuarios(idBoton);
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

    $("#formEditarUsuario").validate({
      debug: true,
      rules: {
        editNombre1: "required",
        editApellido1: "required",
        editCorreo: {
          required: true,
          email: true
        },
        editTelefono: "number"
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

    $("#formCrearUsuario").submit(function(event) {
      event.preventDefault();
      if ($("#formCrearUsuario").valid()) {
        top.$("#cargando").modal("show");
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              alertify.success("Se ha creado correctamente.");
              $("#formCrearUsuario")[0].reset();
              setTimeout(function() {
                top.$("#cargando").modal("hide");
                $("#crearUsuario").modal("hide");
                cargarUsuarios(idBoton);
              }, 1000);
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("Se ha podido enviar el formulario.");
          }
        });
        
      }
    });

    $("#formEditarUsuario").submit(function(event){
      event.preventDefault();
      if ($("#formEditarUsuario").valid()){
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              $("#modal-editarUsuario").modal("hide");
              $("#formEditarUsuario")[0].reset();
              cargarUsuarios(1);
              alertify.success("Se ha actualizado el usuario");
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("Error al actulizar el usuario");
          }
        });
        
      }
    });

    $("#nro_doc").focusout(function(){
      if ($("#nro_doc").val() != "") {
        $.ajax({
          url: 'acciones',
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
          url: 'acciones',
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

    $("#foto").change(function () {
      filePreview(this);
    });
  });

  function filePreview(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $("#imagen").attr("src", e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }

  function cargarUsuarios(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaUsuario', activo: id},
      success: function(data){
        $("#tabla").dataTable().fnDestroy();
        $("#contenido_tabla_coordinadores").empty();
        if (data != false) {
          $("#contenido_tabla_coordinadores").html(data);
        }
        definirdataTable('#tabla');
      },
      error: function(){
        alertify.error("No han cargado los datos");
      }
    });
  }

  function inHabilitarUsuario(id, activo){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: "inHabilitarUsuario", id: id, activo: activo},
      success: function(){
        cargarUsuarios(activo);
        if (activo == 1) {
          alertify.success("Usuario habilitado");
        }else{
          alertify.warning("Usuario deshabilitado");
        }
      },
      error: function(){
        alertify.error("No ha podido inhabilitar el usuario");
      }
    });
  }

  function editarUsuario(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosUsuario', id_usu: id},
      success: function(data){
        $("#editarIdUsu").val(data.u_id);
        $("#editNro_doc").val(data.u_nro_documento);
        $("#editUsuario").val(data.u_usuario);
        $("#editNombre1").val(data.u_nombre1);
        $("#editNombre2").val(data.u_nombre2);
        $("#editApellido1").val(data.u_apellido1);
        $("#editApellido2").val(data.u_apellido2);
        $("#editCorreo").val(data.u_correo);
        $("#editTelefono").val(data.u_telefono);
      },
      error: function(){
        alertify.error('Error al editar el usuario');
      }
    });
    
    $("#modal-editarUsuario").modal('show');
    
  }

  function permisos(id){
    top.$("#cargando").modal("show");
    top.$("#contenido-modal").attr("data", "<?php echo(RUTA_RAIZ); ?>/modulos/configuracion/permisos/permisos_usuario?idUsu="+id);
    top.$("#modal-link").modal("show");
  }
    
</script>
</html>