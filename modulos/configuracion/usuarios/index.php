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
    echo $lib->bootstrapTempusDominus();
    echo $lib->alertify();
    echo $lib->bootstrapSelect();
    echo $lib->mandino();
  ?>
  <style>
    .spinner * {
      text-align: center;
    }
    .spinner input::-webkit-outer-spin-button,
    .spinner input::-webkit-inner-spin-button {
      margin: 0;
      -webkit-appearance: none;
    }
    .spinner input:disabled {
      background-color: white;
    }

  </style>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container-fluid mt-4">
    <div class="container mt-4 bg-white pt-3 pb-3 border rounded">
      <select class="selectpicker form-control" name="empresa" id="empresa" data-live-search="true" data-size="5" title="Seleccione una empresa" data-selected-text-format="count > 3"></select>
    </div>

    <div id="contenido-usuarios" class="d-none">
      <div class="d-flex justify-content-between mt-3 mb-3">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <label class="btn btn-secondary bg-primary active activoUser">
            <i class="fas fa-user-check"></i> <input type="radio" class="activoUser" name="options" value="1" autocomplete="off" checked> Activos
          </label>
          <label class="btn btn-secondary activoUser">
            <i class="fas fa-user-times"></i> <input type="radio" class="activoUser" name="options" value="0" autocomplete="off"> Inactivos
          </label>
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
            <th class="text-center">Ciudad</th>
            <th class="text-center">Nro Documento</th>
            <th class="text-center">Usuario</th>
            <th class="text-center">Nombre</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="contenido_tabla_coordinadores">
      
        </tbody>
      </table>
    </div>
    </div>

  <div class="modal fade" id="crearUsuario" tabindex="-1" role="dialog" aria-labelledby="crearUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-user-plus"></i> Crear Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearUsuario" autocomplete="off" enctype="multipart/form-data">
          <input type="hidden" name="accion" value="crearUsuario">
          <input type="hidden" name="usuarioCreador" value="<?php echo($usuario['id']); ?>">
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
              <div class="form-group col-6">
                <label for="">Departamento <span class="text-danger">*</span></label>
                <select class="selectpicker form-control" required id="departamentos" name="departamentos" data-live-search="true" data-size="5" title="Seleccione un departamento"></select>
              </div>
              <div class="form-group col-6">
                <label for="">Ciudades <span class="text-danger">*</span></label>
                <select class="selectpicker form-control" required disabled id="ciudades" name="ciudades" data-live-search="true" data-size="5" title="Seleccione una ciudad"></select>
              </div>
              <div class="form-group col-6">
                <label for="">Empresas <span class="text-danger">*</span></label>
                <select class="selectpicker form-control" required id="empresas" name="empresas[]" data-live-search="true" data-size="5" title="Seleccione una empresa" multiple data-selected-text-format="count > 3"></select>
              </div>
              <div class="form-group col-6">
                <label>Cursos <span class="text-danger">*</span></label>
                <select class="selectpicker form-control" required id="cursos" name="cursos[]" disabled data-live-search="true" data-size="5" title="Seleccione un curso" multiple data-selected-text-format="count > 3"></select>
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
          <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Usuario</h5>
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
              <div class="form-group col-12 col-md-6">
                <label for="">Departamento <span class="text-danger">*</span></label>
                <select class="selectpicker form-control" required id="editdepartamentos" name="editdepartamentos" data-live-search="true" data-size="5" title="Seleccione un departamento"></select>
              </div>
              <div class="form-group col-12 col-md-6">
                <label for="">Ciudades <span class="text-danger">*</span></label>
                <select class="selectpicker form-control" required id="editciudades" name="editciudades" data-live-search="true" data-size="5" title="Seleccione una ciudad"></select>
              </div>
              <div class="form-group col-12 col-md-6">
                <label for="">Empresas <span class="text-danger">*</span></label>
                <select class="selectpicker form-control" multiple data-selected-text-format="count > 3" required id="editEmpresas" name="editEmpresas[]" data-live-search="true" data-size="5" title="Seleccione una empresa"></select>
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
  
  <!-- Modal Editar Curso Usuario -->
  <div class="modal fade" id="modal-cursoUsuarios" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-book"></i> Cursos - Usuarios</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCursosUsuarios" autocomplete="off" enctype="multipart/form-data">
          <input type="hidden" name="accion" value="editarCursosUsuario">
          <input type="hidden" name="cursoUsuId" id="cursoUsuId">
          <input type="hidden" name="cursoUsuIdCreador" value="<?php echo($usuario['id']); ?>">
          <div class="modal-body" id="contenido-cursos-usuarios">
            
          </div>
          <div class="modal-footer d-flex justify-content-center">
            <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  
  <!-- Modal Editar Curso Usuario -->
  <div class="modal fade" id="modal-progresoUsuarios" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-book"></i> Progreso - Usuarios</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 col-sm-6 col-lg-4">
              <div class="list-group" id="listaProgresoCurso"></div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4" id="listaProgresoUnidad"></div>
            <div class="col-12 col-sm-12 col-lg-4" id="listaProgresoLeccion"></div>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Editar Curso Usuario -->
  <div class="modal fade" id="modal-logUsuarios" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-list-alt"></i> Inicio de Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="usuarioLog">
          <div class="row">
            <div class="col-6">
              <select id="logFecha" class="custom-select">
              </select>
            </div>
            <div class="col-6">
              <select name="" id="logMeses" disabled class="custom-select">
                <option value="" disabled selected>Seleccione un mes</option>
              </select>
            </div>
            <div class="col-12 mt-3">
              <table id="tabla-logs" class="table">
                <thead>
                  <tr>
                    <th>Nro</th>
                    <th>Día</th>
                    <th>Hora</th>
                  </tr>
                </thead>
                <tbody id="contenido-logs"></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade bd-example-modal-xl" id="verUsuario" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <iframe id="contenido-verUsuario" name="contenido-verUsuario" class="w-100" style="height: 75vh" frameborder="0"></iframe>
      </div>
    </div>
  </div>



</body>
<?php 
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    $(".activoUser").on("click", function(){
      $(".activoUser").removeClass("bg-primary");
      $(this).addClass("bg-primary");
      cargarUsuarios($("input", this).val(), $("#empresa").val());
    });

    //Se carga la lista de empresas
    $.ajax({
      url: '<?php echo($ruta_raiz) ?>modulos/configuracion/empresas/acciones',
      type: "POST",
      dataType: "json",
      cache: false,
      data: {accion: "listaEmpresas"},
      success: function(data){
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#empresas").append(`
            <option value="${data[i].e_id}">${data[i].e_nombre}</option>
          `);

          $("#empresa").append(`
            <option value="${data[i].e_id}">${data[i].e_nombre}</option>
          `);
        }
        $("#empresas").selectpicker('refresh');
        $("#empresa").selectpicker('refresh');
      },
      error: function(){
        alertify.error("No se ha cargado la lista de empresas.");
      }
    });

    //Formulario de selecion de la empresa
    $("#empresa").on("change", function(){
      //Se carga la lista de usuario
      cargarUsuarios($("input[name='options']:checked").val(), $("#empresa").val());
      $("#contenido-usuarios").removeClass("d-none");
    });

    //Caundo selecciones la empresas se habilitan los cursos
    $("#empresas").on("change", function(){
      //Validamos si el campo esta lleno
      if($(this).val() != ""){
        $("#cursos").attr("disabled", false);
        $.ajax({
          url: '<?php echo($ruta_raiz) ?>modulos/configuracion/cursos/acciones',
          type: 'POST',
          dataType: 'json',
          data: {accion: 'CursosXEmpresa', empresas: $(this).val()},
          success: function(data){
            $("#cursos").empty();
            for (let i = 0; i < data.cantidad_registros; i++) {
              $("#cursos").append(`
                <option value="${data[i].mc_id}">${data[i].mc_nombre}</option>
              `);
            }
            $("#cursos").selectpicker('refresh');
          },
          error: function(){
            alertify.error("No han cargado los cursos");
          }
        });
      }else{
        $("#cursos").empty();
        $("#cursos").attr("disabled", true);
        $("#cursos").selectpicker('refresh');
      }
    });

    $.ajax({
      url: "acciones",
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
        url: "acciones",
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

    $("#editdepartamentos").on('change', function() {
      $.ajax({
        url: "acciones",
        type: "POST",
        dataType: "json",
        cache: false,
        data: {accion: "ciudades", dep: $(this).val()},
        success: function(data){
          $("#editciudades").empty();
          for (let i = 0; i < data.cantidad_registros; i++) {
            $("#editciudades").append(`
              <option value="${data[i].m_id}">${data[i].m_nombre}</option>
            `);
          }
          $("#editciudades").selectpicker('refresh');
          $("#editciudades").focus().select();
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
                cargarUsuarios($("input[name='options']:checked").val(), $("#empresa").val());
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
              cargarUsuarios($("input[name='options']:checked").val(), $("#empresa").val());
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

    //Form editar curso por usuario
    $("#formCursosUsuarios").submit(function(event){
      event.preventDefault();
      $.ajax({
        url: 'acciones',
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: new FormData(this),
        success: function(data){
          alertify.success(data);
        },
        error: function(){
          alertify.error("Error al actualizar");
        }
      });
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


    //Cambio de fecha en logs de usuario
    $("#logFecha").on("change", function(){
      $.ajax({
        url: "acciones",
        type: "POST",
        dataType: "json",
        data: {accion: "logMeses", idUsu: $("#usuarioLog").val(), year: $("#logFecha").val()},
        success: function(data){
          $("#logMeses").attr("disabled", false);
          $("#logMeses").empty();
          $("#logMeses").append(`<option value="" selected disabled>Seleccione un mes</option>`);
          for (let i = 0; i < data.cantidad_registros; i++) {
            $("#logMeses").append(`<option value="${data[i].mes}">${moment.months(data[i].mes - 1)}</option>`);
          }
        },
        error: function(){
          alertify.error("Se han cargado los meses");
        }
      });
    });

    $("#logMeses").on("change", function(){ 
      tablaLogs($("#usuarioLog").val(), $("#logFecha").val(), $("#logMeses").val());
    });

    //Fin Cambio de fecha en logs de usuario
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

  function cargarUsuarios(id, empresas){
    top.$("#cargando").modal("show");
    $.ajax({
      url: 'acciones',
      type: 'GET',
      dataType: 'html',
      data: {accion: 'listaUsuario', habilitado: id, empresa: empresas},
      success: function(data){
        $('[data-toggle="tooltip"]').tooltip('hide');
        $("#tabla").dataTable().fnDestroy();
        $("#contenido_tabla_coordinadores").empty();
        if (data != false) {
          $("#contenido_tabla_coordinadores").html(data);
        }
        $('[data-toggle="tooltip"]').tooltip();
        definirdataTable('#tabla');
      },
      error: function(){
        alertify.error("No han cargado los datos");
      },
      complete: function(){
        setTimeout(function() {
          top.$("#cargando").modal("hide");
        }, 1000);
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
        cargarUsuarios($("input[name='options']:checked").val(), $("#empresa").val());
        if (activo == 1) {
          alertify.success("Usuario habilitado");
        }else{
          alertify.success("Usuario deshabilitado");
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

        $.ajax({
          url: "acciones",
          type: "POST",
          dataType: "json",
          cache: false,
          data: {accion: "editCiudades", m_id: data.fk_ciudad},
          success: function(data1){
            for (let i = 0; i < data1.cantidad_registros; i++) {
              $("#editciudades").append(`
                <option value="${data1[i].m_id}">${data1[i].m_nombre}</option>
              `);
            }
            if (data.fk_ciudad != 0) {
              $('#editciudades').selectpicker('val', data.fk_ciudad);
              $("#editdepartamentos").selectpicker('val', data1[0].fk_departamento);
            }else{
              $('#editciudades').selectpicker('val', 0);
              $('#editdepartamentos').selectpicker('val', 0);
            }
            $("#editciudades").selectpicker('refresh');
          },
          error: function(){
            alertify.error("No se han cargado la ciudades");
          }
        });

        $.ajax({
          url: "acciones",
          type: "POST",
          dataType: "json",
          cache: false,
          data: {accion: "UsuariosEmpresas", idUsuario: data.u_id},
          success: function(data){
            $("#editEmpresas").empty();
            for (let i = 0; i < data.cantidad_registros; i++) {
              if (data[i].check) {
                $("#editEmpresas").append(`
                  <option selected value="${data[i].e_id}">${data[i].e_nombre}</option>
                `);
              } else {  
                $("#editEmpresas").append(`
                  <option value="${data[i].e_id}">${data[i].e_nombre}</option>
                `);
              }
            }
            $("#editEmpresas").selectpicker('refresh');
          },
          error: function(){
            alertify.error("Error al cargar la empresas.");
          }
        });

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

  function cursos(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaCursoUsuarios', idUsu: id},
      success: function(data){
        $("#cursoUsuId").val(id);
        $("#contenido-cursos-usuarios").html(data);
        $("#modal-cursoUsuarios").modal("show");
      },
      error: function(){
        alertify.error("No se ha podido cargar. los cursos.");  
      }
    });
  }

  function estadoUsuario(idUsu){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaCursosUsuarioProgreso', idUsu: idUsu},
      success: function(data){
        $("#listaProgresoCurso").html(data);
        $("#listaProgresoUnidad").empty();
        $("#listaProgresoLeccion").empty();
        //Activamos el botón al darle click
        $(".pro_curso").on("click", function(){
          $(".pro_curso").removeClass("active");
          $(this).addClass("active");
        });

        $("#modal-progresoUsuarios").modal("show");

        $(".pro_curso").on("click", function(){
          //Traemos la evaluaciones que ha realizado de ese taller
          $.ajax({
            url: "acciones",
            type: "GET",
            dataType: "html",
            data: {accion: "listaUnidadesUsuariosProgreso", curso: $(this).val(), idUsu: idUsu},
            success: function(data){
              $("#listaProgresoUnidad").empty();
              $("#listaProgresoLeccion").empty();
              $("#listaProgresoUnidad").html(data);

              $(".pro_unidad").on("click", function(){
                $(".pro_unidad").removeClass("active");
                $(this).addClass("active");

                $.ajax({
                  url: "acciones",
                  type: "GET",
                  dataType: "html",
                  data: {accion: "listaLeccionesUsuarioProgreso", idUnidad: $(this).val(), idUsu: idUsu},
                  success: function(data){
                    $("#listaProgresoLeccion").html(data);

                    //Botones de intentos
                    //$('.count').prop('disabled', true);
                    $(".minus").on("click", function() {
                      var input = $(this).parent().next();
                      if (input.val() > 0) {
                        input.val(parseInt(input.val()) - 1 );
                        actualizarIntentoTaller($(this).val(), input.val());    
                        /*$('.count').val(parseInt($('.count').val()) - 1 );
                        $('.counter').text(parseInt($('.counter').text()) - 1 );*/
                      }
                    });
                    $(".plus").on("click", function() {
                      var input = $(this).parent().prev();
                      if (input.val() < 30) {
                        input.val(parseInt(input.val()) + 1 ); 
                        actualizarIntentoTaller($(this).val(), input.val());       
                        //$('.count').val(parseInt($('.count').val()) + 1 );
                        //$('.counter').text(parseInt($('.counter').text()) + 1 );
                        //console.log($(this).parent().prev().val());
                      }
                    });
                    //Fin botónes de intentos
                  },
                  error: function(){
                    alertify.error("No se han cargado la lecciones");
                  }
                });
              });
            },
            error: function(){
              alertify.error("No se han podido cargar la unidades.");
            }
          });
        });
      },
      error: function(){
        alertify.error("No se ha cargado la lista");
      }
    });
  }

  function actualizarIntentoTaller(idMlv, cont){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: "actualizarIntentosTaller", idMLV: idMlv, intentos: cont},
      success: function(data){
        if(data == "Ok"){
          alertify.success("Intento agregado.");
        }else{
          alertify.error("No se ha podido agregar el inteto.");
        }
      },
      error: function(){
        alertity.error("No se ha actualizado...");
      }
    });
  }

  function logUsuarios(idUsuario){
    $("#modal-logUsuarios").modal("show");
    $("#usuarioLog").val(idUsuario);
    tablaLogs(idUsuario);
    $("#logMeses").attr("disabled", true);
    $("#logMeses").html(`<option value="" selected disabled>Seleccione un mes</option>`);

    $.ajax({
      url:"acciones",
      type: "POST",
      dataType: "json",
      data: {accion: "logFecha", idUsu: idUsuario},
      success: function(data){
        $("#logFecha").empty();
        $("#logFecha").append(`<option value="0" selected disabled>Seleccione una fecha</option>`);
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#logFecha").append(`<option value="${data[i].fecha}">${data[i].fecha}</option>`);
        }
      },
      error: function(){
        alertify.erro("No se han cargado los años");
      }
    });
  }

  function tablaLogs(idUsuario, year = moment().format("YYYY"), mes = moment().format("MM")){
    //console.log(fecha);
    $.ajax({
      url: "acciones",
      type: "GET",
      dataType: "json",
      data: {accion: "logs", idUsu: idUsuario, year: year, mes: mes},
      success: function(data){
        $("#tabla-logs").dataTable().fnDestroy();
        $("#contenido-logs").empty();
        for (let i = 0; i < data.cantidad_registros; i++) {   
          //console.log(data[i].log_fecha_creacion);                 
          $("#contenido-logs").append(`<tr>
                                        <td>${moment(data[i].log_fecha_creacion, "YYYY-MM-DD hh:mm").format("DD")}</td>
                                        <td>${moment.weekdays(moment(data[i].log_fecha_creacion).day()) }</td>
                                        <td>${moment(data[i].log_fecha_creacion, "YYYY-MM-DD hh:mm").format("hh:mm a")}</td>
                                      </tr>`);
        }
        definirdataTable('#tabla-logs');
      },
      error: function(){
        alertify.error("Error al cargar los logs.");
      }
    });
  }

  function verUsuario(id){
    $("#contenido-verUsuario").attr("src", "<?php echo($ruta_raiz) ?>modulos/cursos/cursos?id_usuario="+id);
    $("#verUsuario").modal("show");
    //window.location.href = "<?php echo($ruta_raiz) ?>modulos/cursos/cursos?id_usuario="+id;
  }
    
</script>
</html>