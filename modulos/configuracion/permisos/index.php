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
  include_once($ruta_raiz . 'clases/Permisos.php');
  include_once($ruta_raiz . 'clases/Conectar.php');

  $session = new Session();
  $lib = new Libreria();
  $permisos = new Permisos();

  $usuario = $session->get("usuario");
  $acordeon = "";

  if ($permisos->validarPermiso($usuario['id'], 'permisos') == 0) {
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
    echo $lib->bsCustomFileInput();
    echo $lib->jqueryValidate();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
  <style type="text/css">
    [data-toggle="collapse"]:after {
      display: inline-block;
        display: inline-block;
        font-family: 'Font Awesome 5 Free';
        font: normal normal normal 15px/1;
        font-size: 1.2em;
        font-weight: bold;
        text-rendering: auto;
        -webkit-font-mdoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      content: "\f105";
      transform: rotate(90deg) ;
      transition: all linear 0.25s;
      float: right;
    }

    [data-toggle="collapse"].collapsed:after {
      transform: rotate(0deg) ;
    }

    [data-toggle="collapse"].navbar-toggler:after {
      content: none;
    }
  </style>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container mt-5">
    <div class="row">
      <div class="col-6" id="permisos">
        
      </div>
      <div class="col-6">
        <div class="d-flex justify-content-around mb-2">
          <?php  
            if ($permisos->validarPermiso($usuario['id'], "permisos_agregar_hijo")) {
              echo('<button class="boton-per btn btn-primary disabled" disabled data-toggle="modal" data-target="#modal-agregarHijo"><i class="fas fa-plus"></i> Agregar Hijo</button>');
            }

            if ($permisos->validarPermiso($usuario['id'], "permisos_editar")) {
              echo('<button class="boton-per btn btn-success disabled" disabled data-toggle="modal" data-target="#modal-editar"><i class="far fa-edit"></i> Editar</button>');
            }
          ?>
        </div>
        <table class="table">
          <tbody>
            <tr>
              <th>Nombre:</th>
              <td id="table-nombre">N/A</td>
            </tr>
            <tr>
              <th>Etiqueta:</th>
              <td id="table-etiqueta">N/A</td>
            </tr>
            <tr>
              <th>Icono:</th>
              <td id="table-icono">N/A</td>
            </tr>
            <tr>
              <th>Padre:</th>
              <td id="table-padre">N/A</td>
            </tr>
            <tr>
              <th>Ruta:</th>
              <td id="table-ruta">N/A</td>
            </tr>
            <tr>
              <th>Fecha Creacion:</th>
              <td id="table-fecha_creacion">N/A</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Modal Editar Permiso -->
  <div class="modal fade" id="modal-editar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Permiso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditarPermiso" autocomplete="off">
          <input type="hidden" name="accion" value="editarPermiso">
          <input type="hidden" name="editarIdPermiso" id="editarIdPermiso">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="editNombre" id="editNombre" required>
            </div>
            <div class="form-group">
              <label>Etiqueta <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="editEtiqueta" id="editEtiqueta" required>
            </div>
            <div class="form-group">
              <label>Icono</label>
              <input class="form-control" type="text" id="editIcono" name="editIcono">
            </div>
            <div class="form-group">
              <label>Ruta <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="editRuta" name="editRuta" required>
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-center">
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Agregar Hijo -->
  <div class="modal fade" id="modal-agregarHijo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="crearUsuarioLabel"><i class="fas fa-plus"></i> Agregar Hijo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAgregarHijo" autocomplete="off">
          <input type="hidden" name="accion" value="agregarHijo">
          <input type="hidden" name="agregarIdPadre" id="agregarIdPadre">
          <div class="modal-body">
            <div class="form-group">
              <label>Padre:</label>
              <input class="form-control" type="text" name="agregarPadre" id="agregarPadre" readonly>
            </div>
            <div class="form-group">
              <label>Nombre: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="agregarNombre" id="agregarNombre" autocomplete="off">
            </div>
            <div class="form-group">
              <label>Etiqueta: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="agregarEtiqueta" id="agregarEtiqueta" autocomplete="off">
            </div>
            <div class="form-group">
              <label>Icono:</label>
              <input class="form-control" type="text" name="agregarIcono" id="agregarIcono" autocomplete="off">
            </div>
            <div class="form-group">
              <label>Ruta: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="agregarRuta" id="agregarRuta" autocomplete="off">
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-center">
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  
</body>
<?php 
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    cargarAcordeon();

    //Formulario editar Permiso
    $("#formEditarPermiso").validate({
      debug: true,
      rules: {
        editNombre: "required",
        editEtiqueta: "required",
        editRuta: "required"
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

    $("#formEditarPermiso").submit(function(event){
      event.preventDefault();
      if ($(this).valid()) {
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              cargarAcordeon();
              $("#modal-editar").modal("hide");
              $("#formEditarPermiso")[0].reset();
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("Error al actualizar");
          }
        });
        
      }
    });

    // Formulario agregar Hijo 
    $("#formAgregarHijo").validate({
      debug: true,
      rules: {
        agregarNombre: "required",
        agregarEtiqueta: "required",
        agregarRuta: "required"
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

    $("#formAgregarHijo").submit(function(event) {
      event.preventDefault();
      if ($("#formAgregarHijo").valid()) {
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              cargarAcordeon();
              $("#modal-agregarHijo").modal("hide");
              $("#formAgregarHijo")[0].reset();
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("No se ha podido enviar el formulario.");
          }
        });
      }
    });

    //Fin formulario agregar hijo
  }); 

  function cargarAcordeon(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'acordeon'},
      success: function(data){
        $("#permisos").empty();
        $("#permisos").html(data);
        $(".btn-permiso").on("click", function(){
          $(".btn-permiso").removeClass('active');
          $(this).addClass('active');
          datosPermiso($(this).val());
        });
      },
      error: function(){
        alertify.error("Error al cargar los permisos.");
      }
    });
  }

  function datosPermiso(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosPermiso', permiso: id},
      success: function(data){
        $(".boton-per").removeClass('disabled');
        $(".boton-per").removeAttr('disabled');
        $("#table-nombre").html(data.nombre);
        $("#table-etiqueta").html(data.tag);
        $("#table-icono").html(data.icono);
        $("#table-padre").html(data.padre);
        $("#table-ruta").html(data.ruta);
        $("#table-fecha_creacion").html(data.fecha);

        //formulario agregar hijo
        $("#agregarPadre").val(data.padre);
        $("#agregarIdPadre").val(data.id);
      
        //Formulario Editar Permiso
        $("#editarIdPermiso").val(data.id);
        $("#editNombre").val(data.nombre);
        $("#editEtiqueta").val(data.tag);
        $("#editIcono").val(data.icono);
        $("#editRuta").val(data.ruta);
      },
      error: function(){
        alertify.error("Error al traer los datos.");
      }
    });

  }
</script>
</html>