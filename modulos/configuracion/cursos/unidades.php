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
  <style type="text/css">
    .cursor:hover{
      cursor: pointer;
    }
  </style>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container mt-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index">Cursos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Unidades</li>
      </ol>
    </nav>
    <div class="row d-flex justify-content-between">
      <div class="col-12 col-md-6">
        <table id="tabla" class="table table-hover">
          <thead class="text-center">
            <tr>
              <th>Orden</th>
              <th>Unidades</th>
            </tr>
          </thead>
          <tbody id="contenido-tabla" class="text-center"></tbody>
        </table>
      </div>
      <div class="col-12 col-md-5 mt-5 mt-md-0">
        <div class="d-flex justify-content-between mb-4">
          <button class="btn btn-primary" data-toggle="modal" data-target="#crearUnidad"><i class="fas fa-plus"></i> Crear</button>
          <button class="btn btn-info disabled" disabled id="btn-abrirUnidad"><i class="fas fa-window-restore"></i>  Abrir</button>
          <button class="btn btn-success disabled" data-toggle="modal" data-target="#modalEditarUnidad" disabled id="btn-editarUnidad"><i class="far fa-edit"></i> Editar</button>
        </div>
        <table class="table">
          <tbody>
            <tr>
              <th>Nombre:</th>
              <td id="nombreUnidad">N/A</td>
            </tr>
            <tr>
              <th>Descripcion:</th>
              <td id="descripcionUnidad">N/A</td>
            </tr>
            <tr>
              <th>Fecha Creación:</th>
              <td id="fechaCrecionUnidad">N/A</td>
            </tr>
            <tr>
              <th>Creador:</th>
              <td id="creadorUnidad">N/A</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Crear Unidad -->
  <div class="modal fade" id="crearUnidad" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Crear Unidad</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCrearUnidad" autocomplete="off">
          <input type="hidden" name="accion" value="formCrearUnidad">
          <input type="hidden" name="idCurso" value="<?php echo($_GET['idCurso']); ?>">
          <input type="hidden" name="idUsu" value="<?php echo($usuario['id']); ?>">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="crearNombreUnidad" id="crearNombreUnidad" required>
            </div>
            <div class="form-group">
              <label>Descripción: </label>
              <textarea class="form-control" rows="3" id="crearDescripcionUnidad" name="crearDescripcionUnidad"></textarea>
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
  <div class="modal fade" id="modalEditarUnidad" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Unidad</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditarUnidad" autocomplete="off">
          <input type="hidden" name="accion" value="formEditarUnidad">
          <input type="hidden" name="idUnidad" id="idUnidad">
          <input type="hidden" name="editIdCurso" id="editIdCurso" value="<?php echo($_GET['idCurso']); ?>">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre: <span class="text-danger">*</span></label>
              <input class="form-control" type="text" name="editNombreUnidad" id="editNombreUnidad" required>
            </div>
            <div class="form-group">
              <label>Descripción: </label>
              <textarea class="form-control" rows="3" id="editDescripcionUnidad" name="editDescripcionUnidad"></textarea>
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
    listaUnidades();

    //Crear unidad
    $("form").validate({
      debug: true,
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

    $("#formCrearUnidad").submit(function(event){
      event.preventDefault();
      if ($("#formCrearUnidad").valid()) {
        $.ajax({
          url: "acciones",
          type: "POST",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data == 1) {
              $("#crearNombreUnidad").val("");
              $("#crearDescripcionUnidad").val("");
              listaUnidades();
              $("#crearUnidad").modal("hide");
              alertify.success("Se ha creado correctamente.");
            }else{
              alertify.error(data.replace(/['"]+/g, ''));
            }
          },
          error: function(){
            alertify.error("No se ha guardado.");
          }
        });
      }
    });


    //Form de editar uniadad
    $("#formEditarUnidad").submit(function(event){
      event.preventDefault();
      if($("#formEditarUnidad").valid()){
        top.$("#cargando").modal("show");
        $.ajax({
          url: "acciones",
          type: "POST",
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if(data == 1){
              listaUnidades();
              datosUnidad($("#idUnidad").val());
              $("#modalEditarUnidad").modal("hide");
              alertify.success("La unidad se ha modificado correctamente.");
            }else{
              alertify.error(data.replace(/['"]+/g, ''));
            }
          },
          error: function(){
            alertify.error("Se ha editado la unidad."); 
          },
          complete: function(){
            setTimeout(function() {
              top.$("#cargando").modal("hide");
            }, 1000);
          }
        });
      }
    });

    $("#btn-abrirUnidad").on("click", function(){
      dobleClick($(this).val());
    });
  });

  function listaUnidades(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaUnidades', idCurso: <?php echo($_GET['idCurso']); ?>},
      success: function(data){
        $("#tabla").dataTable().fnDestroy();
        $("#contenido-tabla").empty();
        $("#contenido-tabla").html(data);
        definirdataTableDragAndDrop('#tabla');
        $("#tabla").DataTable().on('row-reorder', function (e, diff, edit ) {
          var cont = 0;
          for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            $(diff[i].node).addClass("reordered");
            orden = diff[i].newData;
            idUnidad = $(diff[i].node).attr("id");
            //console.log(diff[i].newData);
            //console.log($(diff[i].node).find("td:first").html());
            //alert("id: " + $(diff[i].node).attr("id"));
            //alert("Orden: " + diff[i].newData);
            $.ajax({
              url: 'acciones',
              type: 'POST',
              dataType: 'html',
              async: false,
              data: {accion: 'actualizarOrdenUnidades', idUnidad: idUnidad, orden: orden},
              success: function(data){
                if (data == "Ok") {
                  cont++;
                }else{
                  alertify.error(data);
                }
              },
              error: function(){
                alertify.error("No se ha podido actualizar");
              }
            });
          }

          if (diff.length != 0) {
            if (diff.length == cont) {
              alertify.success("Se ha actualizado correctamente");
            }else{
              alertify.error("No se han actualizado 2");
            }
          }
        });
      },
      error: function(){
        alertify.error("Error al cargar la lista.");
      }
    });
  }

  function datosUnidad(id){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosUnidad', idUnidad: id},
      success: function(data){
        console.log(data);
        //Habilitamos los bótones
        $("#btn-editarUnidad").removeAttr('disabled');
        $("#btn-editarUnidad").removeClass('disabled'); 

        $("#btn-abrirUnidad").removeAttr('disabled');
        $("#btn-abrirUnidad").removeClass('disabled');  
        $("#btn-abrirUnidad").val(data.id);

        //Llenamos los datos datos del formulario editar
        $("#idUnidad").val(id);
        $("#editNombreUnidad").val(data.nombre);
        $("#editDescripcionUnidad").val(data.descripcion);

        //Datos
        $("#nombreUnidad").html(data.nombre);
        $("#descripcionUnidad").html(data.descripcion);
        $("#fechaCrecionUnidad").html(data.fecha_creacion);
        $("#creadorUnidad").html(data.usuario);
      },
      error: function(){
        alertify.error("No se han cargado los datos.");
      }
    });
  }

  function dobleClick(id){
    window.location.href = "lecciones?idCurso=<?php echo($_GET['idCurso']); ?>&idUnidad=" + id;
  }
</script>
</html>