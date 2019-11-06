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

  if ($permisos->validarPermiso($usuario['id'], 'reportes') == 0) {
    header('Location: ' . $ruta_raiz . 'modulos/cursos/cursos');
  }

?>

<!DOCTYPE html>
<html>
<head>
  <title>Reportes</title>
  <?php  
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->datatables();
    echo $lib->jqueryValidate();
    echo $lib->alertify();
    echo $lib->bootstrapSelect();
    echo $lib->mandino();
  ?>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
  <!-- Contenido -->
  <div class="container mt-4 bg-white pt-3 pb-3 border rounded">
    <form id="form_reporte" class="form-row">
      <input type="hidden" name="accion" value="reporteUsuario">
      <div class="col-12 col-md-3">
        <label for="ciudad">Empresa</label>
        <select class="selectpicker form-control" required name="empresa" id="empresa" data-live-search="true" data-size="5" title="Seleccione una empresa"></select>
      </div>
      <div class="col-12 col-md-3">
        <label for="ciudad">Ciudades</label>
        <select class="selectpicker form-control" required name="ciudad[]" id="ciudad" data-live-search="true" data-size="5" title="Seleccione una ciudad" multiple data-selected-text-format="count > 3" disabled></select>
      </div>
      <div class="col-12 col-md-3">
        <label for="cursos">Cursos</label>
        <select class="selectpicker form-control" required name="cursos[]" id="cursos" data-live-search="true" data-size="5" title="Seleccione los cursos" multiple data-selected-text-format="count > 2" disabled></select>
      </div>
      <div class="text-center col-12 col-md-3 align-self-end">
        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Consultar</button>
      </div>
    </form>
  </div>

  <div class="container-fluid mt-4">
    <table id="tabla_reporte" class="table table-hover table-sm d-none">
      <thead>
        <th>Pruebas</th>
      </thead>
    </table>
  </div>
</body>
<?php 
  echo $lib->cambioPantalla();
?>
<script type="text/javascript">
  $(function(){
    //Select lista empresas
    $.ajax({
      url: '<?php echo($ruta_raiz) ?>modulos/configuracion/empresas/acciones',
      type: "POST",
      dataType: "json",
      cache: false,
      data: {accion: "listaEmpresas"},
      success: function(data){
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#empresa").append(`
            <option value="${data[i].e_id}">${data[i].e_nombre}</option>
          `);
        }
        $("#empresa").selectpicker('refresh');
      },
      error: function(){
        alertify.error("No se ha cargado la lista de empresas.");
      }
    });

    $("#empresa").on("change", function(){
      listaCursos();
      listaCiudades();
    });

    //Validaciones del reporte
    $("#form_reporte").validate({
      debug: true,
      rules: {
        empresa: "required",
        ciudad: "required",
        cursos: "required"
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

    $("#form_reporte").submit(function(event){
      event.preventDefault();
      if ($("#form_reporte").valid()) {
        top.$("#cargando").modal("show");
        $.ajax({
          url: 'acciones',
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          data: new FormData(this),
          success: function(data){
            $("#tabla_reporte").removeClass("d-none");
            $("#tabla_reporte").dataTable().fnDestroy();
            $("#tabla_reporte").html(data);
            definirdataTableExport('#tabla_reporte');
          },
          error: function(){
            alertify.error("Error al traer los datos");
          },
          complete: function(){
            setTimeout(function() {
              top.$("#cargando").modal("hide");
            }, 1000);
          }
        });
      }
    })
  });

  function listaCiudades(){
    //Select de cuidades
    $.ajax({
      url: "acciones",
      type: "GET",
      dataType: "json",
      cache: false,
      data: {
        accion: "listaCuidadesHabilitadas",
        empresa: $("#empresa").val()
      },
      success: function(data){
        $("#ciudad").empty();
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#ciudad").append(`
            <option value="${data[i].fk_ciudad}">${data[i].m_nombre}</option>
          `);
        }
      },
      error: function(){
        alertify.error("No se ha cargado las ciudades");
      },
      complete: function(){
        $("#ciudad").removeAttr("disabled", false); 
        $("#ciudad").selectpicker('refresh');
        $("#ciudad").focus().select();
      }
    });
  }

  function listaCursos(){
    //Select de cursos
    $.ajax({
      url: "acciones",
      type: "GET",
      dataType: "json",
      cache: false, 
      data: {
        accion: "listaCursosEmpresas",
        empresa: $("#empresa").val()
      },
      success: function(data){
        $("#cursos").empty();

        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#cursos").append(`
            <option value="${data[i].mc_id}">${data[i].mc_nombre}</option>
          `);
        }
      },
      error: function(){
        alertify.error("Error al traer los cursos.");
      },
      complete: function(){
        $("#cursos").prop("disabled", false); 
        $("#cursos").selectpicker('refresh'); 
      }
    });
  }
</script>
</html>