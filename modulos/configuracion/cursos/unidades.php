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
        <li class="breadcrumb-item"><a href="" id="nav-curso">Modulo</a></li>
        <li class="breadcrumb-item active" aria-current="page" id="nav-modulo">Unidades</li>
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
          <button class="btn btn-primary" data-toggle="modal" data-target="#crearModulo"><i class="fas fa-plus"></i> Crear</button>
          <button class="btn btn-info disabled" disabled id="btn-abrirModulo"><i class="fas fa-window-restore"></i>  Abrir</button>
          <button class="btn btn-success disabled" data-toggle="modal" data-target="#modalEditarModulo" disabled id="btn-editarModulo"><i class="far fa-edit"></i> Editar</button>
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
    navModulo();
    listaUnidades();
    
  });

  function listaUnidades(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'html',
      data: {accion: 'listaUnidades', idModulo: <?php echo($_GET['idModulo']); ?>},
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

  function navCurso(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      cache : false,
      data: {accion: 'datosCurso', idCurso: <?php echo($_GET['idCurso']); ?>},
      success: function(data){
        $("#nav-curso").attr("href", "modulos?idCurso=<?php echo($_GET['idCurso']); ?>");
        $("#nav-curso").html(data.mc_nombre);
      },
      error: function(){
        alertify.error("No se han cargado las datos del curso.");
      }
    });
  }


  function navModulo(){
    $.ajax({
      url: 'acciones',
      type: 'POST',
      dataType: 'json',
      data: {accion: 'datosModulo', idModulo: <?php echo($_GET['idModulo']); ?>},
      success: function(data){
        $("#nav-modulo").html(data.mm_nombre);
      },  
      error: function(){
        alertify.error("Se se ha cargado el modulo."); 
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
</script>
</html>