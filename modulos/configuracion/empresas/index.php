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
    echo $lib->datatables();
    echo $lib->bsCustomFileInput();
    echo $lib->jqueryValidate();
    echo $lib->bootstrapSelect();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->

  <div class="container mt-5">
    <div class="row">
      <div class="col-6">
        <table id="tabla-empresas" class="table table-hover">
          <thead>
            <tr class="text-center">
              <th>Nombre</th>
            </tr>
          </thead>
          <tbody class="text-center" id="tbody-empresas"></tbody>
        </table>
      </div>
      <div class="offset-1 col-4">
        <div class="d-flex justify-content-around">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearEmpresa"><i class="fas fa-plus"></i> Crear</button>
          <button id="btn-editarEmpresa" disabled class="btn-edit btn btn-success disabled" value="0"><i class="fas fa-edit"></i> Editar</button>
          <button disabled class="btn-edit btn btn-danger disabled" data-toggle="modal" data-target="#modalEliminar" ><i class="fas fa-trash-alt"></i> Eliminar</button>
        </div>
        <h5 class="text-center mt-3">Cursos</h5>
        <hr>
        <form id="formActulizarCursos" class="d-none">
          <input type="hidden" name="accion" value="actualizarCursos">
          <input type="hidden" name="idActEmpresa" id="idActEmpresa">
          <div id="checkCursos" class="overflow-auto w-100" style="max-height: 50vh;">

          </div>
          <div class="text-center mt-3">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
          </div>
        </form>
        <hr>
      </div>
    </div>
  </div>
</body>

<!-- Modal Crear Empresa-->
<div class="modal fade" id="modalCrearEmpresa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear empresa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formCrearEmpresa" autocomplete="off">
        <input type="hidden" name="creador" value="<?php echo($usuario['id'])?>">
        <input type="hidden" name="accion" value="formCrearEmpresa">
        <div class="modal-body">
          <div class="form-group">
            <label for="Nombre">Nombre:</label>
            <input class="form-control" type="text" name="nombreEmpresa" autofocus autocomplete="off"  required>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Empresa-->
<div class="modal fade" id="modalEditarEmpresa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar empresa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formEditarEmpresa" autocomplete="off">
        <input type="hidden" name="idEmpresa" id="idEmpresa" value="">
        <input type="hidden" name="accion" value="formEditarEmpresa">
        <div class="modal-body">
          <div class="form-group">
            <label for="Nombre">Nombre:</label>
            <input class="form-control" type="text" name="nombreEditarEmpresa" id="nombreEditarEmpresa" autofocus autocomplete="off"  required>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de confirmación de eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Eliminar emrpesa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>¿Estas seguro?</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" id="btn-eliminarEmpresa" val="0" class="btn btn-success"><i class="fas fa-check"></i> Si</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
      </div>
    </div>
  </div>
</div>

<?php 
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    //Cargamos la lista de empresas
    cargarEmpresas();

    //Validamos los formularios
    $("#formCrearEmpresa").validate({
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

    //Validamos el formulario
    $("#formCrearEmpresa").submit(function(event){
      event.preventDefault();
      if ($("#formCrearEmpresa").valid()) {
        top.$("#cargando").modal("show");
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          dataType: "json",
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data.success) {
              $("#nombreEmpresa").val("");
              $("#modalCrearEmpresa").modal("hide");
              alertify.success(data.msj);
            }else{
              alertify.error(data.msj);
            }
            cargarEmpresas();
          },
          error: function(data){
            console.log(data);
            alertify.error("Error en formulario crear empresas");
          },
          complete: function(){
            setTimeout(function(){
              top.$("#cargando").modal("hide");
            }, 1000);
          }
        });
      }
    });

    //boton de editar empresa
    $("#btn-editarEmpresa").on("click", function(){
      $.ajax({
        url: "acciones",
        type: "POST",
        cache: false,
        dataType: "json",
        data: {accion: "datosEmpresa", idEmpresa: $(this).val()},
        success: function(data){
          $("#idEmpresa").val(data.e_id);
          $("#nombreEditarEmpresa").val(data.e_nombre);
          $("#modalEditarEmpresa").modal("show");
        },
        error: function(){
          alertify.error("No se han traido los datos");
        }
      });
    });


    //Validamos los formularios
    $("#formEditarEmpresa").validate({
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

    //Formulario editar empresa
    $("#formEditarEmpresa").submit(function(event){
      event.preventDefault();
      if($("#formEditarEmpresa").valid()){
        top.$("#cargando").modal("show");
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          dataType: "json",
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            if (data.success) {
              $("#modalEditarEmpresa").modal("hide");
              alertify.success(data.msj);
            }else{
              alertify.error(data.msj);
            }
            cargarEmpresas();
          },
          error: function(){
            alertify.error("No se ha editado la empresa");
          },
          complete: function(){
            setTimeout(function(){
              top.$("#cargando").modal("hide");
            }, 1000);
          }
        });
      }
    });

    $("#btn-eliminarEmpresa").on("click", function(){
      top.$("#cargando").modal("show");
      $.ajax({
        url: "acciones",
        type: "POST",
        cache: false,
        dataType: "json",
        data: {accion: "eliminarEmpresa", idEmpresa: $(this).val()},
        success: function(data){
          if (data == 1) {
            $("#modalEliminar").modal("hide");
            alertify.success("Se ha eliminado correctamente");
          }else{
            alertify.error(data);            
          }
          cargarEmpresas();
        },
        error: function(){
          alertify.error("Error al eliminar.");
        },
        complete: function(){
          setTimeout(function(){
            top.$("#cargando").modal("hide");
          }, 1000);
        }
      });
    });

    $("#formActulizarCursos").submit(function(event){
      event.preventDefault();
      top.$("#cargando").modal("show");
      $.ajax({
        url: 'acciones',
        type: 'POST',
        cache: false,
        dataType: "json",
        contentType: false,
        processData: false,
        data: new FormData(this),
        success: function(data){
          if (data == 1) {
            alertify.success("Se han actualizado corractamente");
          }else{
            alertify.error(data);
          }
        },
        error: function(){
          alertify.error("Error al actualizar los cursos");
        },
        complete: function(){
          setTimeout(function(){
            top.$("#cargando").modal("hide");
          }, 1000);
        }
      });
    })
    
  });

  function cargarEmpresas(){
    $.ajax({
      url: "acciones",
      type: "POST",
      dataType: "json",
      cache: false,
      data: {accion: "listaEmpresas"},
      success: function(data){
        $("#tabla-empresas").dataTable().fnDestroy();
        $("#tbody-empresas").empty();
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#tbody-empresas").append(`
            <tr onClick="datosEmpresa(${data[i].e_id})">
              <td>${data[i].e_nombre}</td>
            </tr>          
          `);
        }
        
        definirdataTable("#tabla-empresas");

        //Mostramos el campo seleccionado en la tabla
        $("tr").on("click", function(){
          $("tr").removeClass("table-secondary");
          $("tr > td").removeClass("font-weight-bold");   
          $(this).addClass("table-secondary"); 
          $(this).children("td").addClass("font-weight-bold");   
        });
      },
      error: function(){
        alertify.error("No se han cargado la empresas.");
      }
    });
  }
  
  function datosEmpresa(id){
    //Habilitamos los botónes para modificar y eliminar la empresa
    $(".btn-edit").removeClass('disabled');
    $(".btn-edit").removeAttr("disabled");
    
    //Editar Empresa
    $("#btn-editarEmpresa").val(id);

    //Eliminar empresa
    $("#btn-eliminarEmpresa").val(id);

    $("#idActEmpresa").val(id);

    //Lista de cursos de la empresas
    checkCursos(id);
  }

  function checkCursos(id){
    $.ajax({
        url: "acciones",
        type: "POST",
        cache: false,
        dataType: "json",
        data: {accion: "checkCursos", idEmpresa: id},
        success: function(data){
          $("#checkCursos").empty();
          for (let i = 0; i < data.cantidad_registros; i++) {
            if (data[i].check) {
              $("#checkCursos").append(`
                <div class="custom-control custom-checkbox mt-1">
                  <input type="checkbox" name="cursos[]" value="${data[i].mc_id}" class="custom-control-input" id="curso${data[i].mc_id}" checked>
                  <label class="custom-control-label" for="curso${data[i].mc_id}">${data[i].mc_nombre}</label>
                </div>
              `);
            } else {  
              $("#checkCursos").append(`
                <div class="custom-control custom-checkbox mt-1">
                  <input type="checkbox" name="cursos[]" value="${data[i].mc_id}" class="custom-control-input" id="curso${data[i].mc_id}">
                  <label class="custom-control-label" for="curso${data[i].mc_id}">${data[i].mc_nombre}</label>
                </div>
              `);              
            }
          }
          $("#formActulizarCursos").removeClass("d-none");
        },
        error: function(data){
          console.log(data);
          alertify.error("Error al cargar los cursos.");
        },
      });
  }
</script>
</html>