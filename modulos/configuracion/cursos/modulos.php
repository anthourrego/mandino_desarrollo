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
        <li class="breadcrumb-item"><a href="index">Cursos</a></li>
        <li class="breadcrumb-item active" id="nav-curso" aria-current="page">Modulos</li>
      </ol>
    </nav>
    <div class="row d-flex justify-content-between">
      <div class="col-12 col-md-6">
        <table id="tabla" class="table table-hover">
          <thead class="text-center">
            <tr>
              <th>Modulos</th>
            </tr>
          </thead>
          <tbody id="contenido-tabla" class="text-center"></tbody>
        </table>
      </div>
      <div class="col-12 col-md-5 mt-5 mt-md-0">
        <div class="d-flex justify-content-between mb-4">
          <button class="btn btn-primary" data-toggle="modal" data-target="#crearModulo"><i class="fas fa-plus"></i> Crear</button>
          <button class="btn btn-info disabled" disabled id="btn-abrirModulo"><i class="fas fa-window-restore"></i>  Abrir</button>
          <button class="btn btn-success disabled" data-toggle="modal" data-target="#modalEditarModulo" disabled id="btn-editarModulo"><i class="far fa-edit"></i> Editar</button>
        </div>
        <table class="table">
          <tbody>
            <tr>
              <th>Nombre:</th>
              <td id="nombreModulo">N/A</td>
            </tr>
            <tr>
              <th>Descripcion:</th>
              <td id="descripcionModulo">N/A</td>
            </tr>
            <tr>
              <th>Fecha Creación:</th>
              <td id="fechaCrecionModulo">N/A</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Crear curso -->
  <div class="modal fade" id="crearModulo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Crear Curso</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearModulo">
          <input type="hidden" name="accion" value="formCrearModulo">
          <input type="hidden" name="idCurso" value="<?php echo($_GET['idCurso']); ?>">
          <input type="hidden" name="idUsu" value="<?php echo($usuario['id']); ?>">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="crearNombreModulo" id="crearNombreModulo" required>
            </div>
            <div class="form-group">
              <label>Descripción: </label>
              <textarea class="form-control" rows="3" id="crearDescripcionModulo" name="crearDescripcionModulo"></textarea>
            </div>
          </div>
          <div class="d-flex justify-content-center modal-footer">
            <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Editar Modulo -->
  <div class="modal fade" id="modalEditarModulo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Modulo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditarModulo" autocomplete="off">
          <input type="hidden" name="accion" value="formEditarModulo">
          <input type="hidden" name="idModulo" id="idModulo">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="editNombreModulo" id="editNombreModulo" required>
            </div>
            <div class="form-group">
              <label>Descripción: </label>
              <textarea class="form-control" rows="3" id="editDescripcionModulo" name="editDescripcionModulo"></textarea>
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
    navCurso();
    listaModulo();

    $("#btn-abrirModulo").on("click", function(){
      redireccionModulo($(this).val())
    }); 

    //Formulari crear modulo
    $("#formCrearModulo").validate({
      debug: true,
      rules: {
        crearNombreModulo: "required"
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

    $("#formCrearModulo").submit(function(e){
      e.preventDefault();
      if ($("#formCrearModulo").valid()) {
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              listaModulo();
              $("#crearModulo").modal("hide");
              $("#formCrearModulo")[0].reset();
              alertify.success("Se ha creado el modulo " + $("#crearNombreModulo").val())
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("Se ha enviado el formulario de crear.");
          } 
        });
      }
    });

    //Formulario editar modulo
    $("#formEditarModulo").validate({
      debug: true,
      rules: {
        editNombreModulo: "required"
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

    $("#formEditarModulo").submit(function(e){
      e.preventDefault();
      if ($("#formEditarModulo").valid()){
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == "Ok") {
              $("#modalEditarModulo").modal("hide");
              listaModulo();
              alertify.success("Se ha editado correctamente.");
            }else{
              alertify.error(data);
            }
          },
          error: function(){
            alertify.error("No se ha enviado el formulario.");
          }
        });
      }
    });
  });

  function listaModulo(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaModulos', idCurso: <?php echo($_GET['idCurso']); ?>},
      success: function(data){
        $("#tabla").dataTable().fnDestroy();
        $("#contenido-tabla").empty();
        $("#contenido-tabla").html(data);
        definirdataTable('#tabla');
      },
      error: function(){
        alertify.error("No se ha cargado la lista de modulos");
      }
    });
  }

  function datosModulo(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosModulo', idModulo: id},
      success: function(data){
        //Datos
        $("#nombreModulo").html(data.mm_nombre);
        $("#descripcionModulo").html(data.mm_descripcion);
        $("#fechaCrecionModulo").html(data.mm_fecha_creacion);

        //Formulario de editar
        $("#idModulo").val(data.mm_id);
        $("#editNombreModulo").val(data.mm_nombre);
        $("#editDescripcionModulo").val(data.mm_descripcion);

        //Se desbloquean los botónes
        $("#btn-editarModulo").removeAttr('disabled');
        $("#btn-editarModulo").removeClass('disabled'); 

        $("#btn-abrirModulo").removeAttr('disabled');
        $("#btn-abrirModulo").removeClass('disabled'); 
        $("#btn-abrirModulo").val(data.mm_id);
      },  
      error: function(){
        alertify.error("Se se ha cargado el modulo."); 
      }
    });
  }

  function navCurso(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      cache : false,
      data: {accion: 'datosCurso', idCurso: <?php echo($_GET['idCurso']); ?>},
      success: function(data){
        $("#nav-curso").html(data.mc_nombre);
      },
      error: function(){
        alertify.error("No se han cargado las datos del curso.");
      }
    });
  }

  function redireccionModulo(id){
    window.location.href = "unidades?idCurso=<?php echo($_GET['idCurso']); ?>&idModulo=" + id;
  }
</script>
</html>