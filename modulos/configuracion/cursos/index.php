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

  if ($permisos->validarPermiso($usuario['id'], 'cursos') == 0) {
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
    echo $lib->jqueryValidate();
    echo $lib->datatables();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container mt-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Cursos</li>
      </ol>
    </nav>
    <div class="row d-felx justify-content-between">
      <div class="col-12 col-md-6">
        <table id="tabla" class="table table-hover">
          <thead>
            <tr>
              <th class="text-center">Cursos</th>
            </tr>
          </thead>
          <tbody id="contenido-tabla-cursos" class="text-center"></tbody>
        </table>
      </div>
      <div class="col-12 col-md-5 mt-5 mt-md-0">
        <div class="mb-3 d-flex justify-content-between">
          <button class="btn btn-primary" data-toggle="modal" data-target="#crearCurso"><i class="fas fa-plus"></i> Crear</button>
          
          <button class="btn btn-info disabled" id="btn-abrir" disabled><i class="fas fa-window-restore"></i>  Abrir</button>

          <button class="btn btn-success disabled" id="btn-editar" data-toggle="modal" data-target="#modalEditarCurso" disabled><i class="far fa-edit"></i> Editar</button>
        </div>
        <div class="form-group row">
          <label class="col-4 col-form-label font-weight-bold">Nombre:</label>
          <div class="col-8">
            <input type="text" class="form-control-plaintext" disabled id="nombre">
          </div>
        </div> 
        <hr>
        <div class="form-group">
          <label class="font-weight-bold">Descripción:</label>
          <p id="descripcion"></p>
        </div>
        <hr>
        <div class="form-group row">
          <label class="col-4 col-form-label font-weight-bold">Fecha creación:</label>
          <div class="col-8">
            <input type="text" name="fecha" id="fecha" disabled class="form-control-plaintext">
          </div>
        </div>
        <hr>
      </div>  
    </div>
  </div>

  <!-- Modal Crear curso -->
  <div class="modal fade" id="crearCurso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Crear Curso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearCurso">
          <input type="hidden" name="accion" value="formCrearCurso">
          <input type="hidden" name="idUsu" value="<?php echo($usuario['id']); ?>">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="crearNombre" id="crearNombre" required>
            </div>
            <div class="form-group">
              <label>Descripción: </label>
              <textarea class="form-control" rows="3" id="crearDescripcion" name="crearDescripcion"></textarea>
            </div>
          </div>
          <div class="d-flex justify-content-center modal-footer">
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Crear curso -->
  <div class="modal fade" id="modalEditarCurso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Editar Curso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditarCurso" autocomplete="off">
          <input type="hidden" name="accion" value="formEditarCurso">
          <input type="hidden" name="idCurso" id="idCurso">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="editNombre" id="editNombre" required>
            </div>
            <div class="form-group">
              <label>Descripción: </label>
              <textarea class="form-control" rows="3" id="editDescripcion" name="editDescripcion"></textarea>
            </div>
          </div>
          <div class="d-flex justify-content-center modal-footer">
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Actualizar</button>
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
    cargarCursos();

    //Formulario crear curso
    $("#formCrearCurso").validate({
      debug: true,
      rules: {
        crearNombre: "required"
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

    $("#formCrearCurso").submit(function(event){
      event.preventDefault();
      if ($("#formCrearCurso").valid()){
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              $("#formCrearCurso")[0].reset();
              $("#crearCurso").modal("hide");
              cargarCursos();
              alertify.success("Se ha agregado el curso");
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("No ha enviado la información")
          }
        });
      }
    });


    //Formulario Editar Curso
    $("#formEditarCurso").validate({
      debug: true,
      rules:{
        editNombre: "required"
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

    $("#formEditarCurso").submit(function(event){
      event.preventDefault();
      if ($("#formEditarCurso").valid()) {
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
              $('#modalEditarCurso').modal("hide");
              $('#formEditarCurso')[0].reset();
              cargarCursos();
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("Error al enviar el formulario.");
          }
        });
      }
      
    });

    $("#btn-abrir").on("click", function(){
      dobleClick($(this).val());
    });

  });

  function datosCurso(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      cache : false,
      data: {accion: 'datosCurso', idCurso: id},
      success: function(data){
        //Formulario Editar
        $("#idCurso").val(data.mc_id);
        $("#editNombre").val(data.mc_nombre);
        $("#editDescripcion").val(data.mc_descripcion);
        
        //Datos
        $("#nombre").val(data.mc_nombre);
        $("#descripcion").html(data.mc_descripcion);
        $("#fecha").val(data.mc_fecha_creacion);
        
        $("#btn-editar").removeAttr('disabled');
        $("#btn-editar").removeClass('disabled');  

        $("#btn-abrir").removeAttr('disabled');
        $("#btn-abrir").removeClass('disabled');  
        $("#btn-abrir").val(data.mc_id);
        

      },
      error: function(){
        alertify.error("No se han cargado los datos.");
      }
    });
  }

  function cargarCursos(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaCursos'},
      success: function(data){
        $("#tabla").dataTable().fnDestroy();
        $("#contenido-tabla-cursos").empty();
        $("#contenido-tabla-cursos").html(data);
        definirdataTable('#tabla');
      },
      error: function(){
        alertify.error("No se han listado los cursos"); 
      }
    });
  }

  function dobleClick(id){
    window.location.href = "modulos?idCurso=" + id;
  }
</script>
</html>