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
      <div class="col-12 col-md-4">
        <label for="ciudad">Ciudades</label>
        <select class="selectpicker form-control" name="ciudad[]" id="ciudad" data-live-search="true" data-size="5" title="Seleccione una ciudad" multiple data-selected-text-format="count > 3"></select>
      </div>
      <div class="col-12 col-md-4">
        <label for="cursos">Cursos</label>
        <select class="selectpicker form-control" name="cursos[]" id="cursos" data-live-search="true" data-size="5" title="Seleccione los cursos" multiple data-selected-text-format="count > 2"></select>
      </div>
      <div class="text-center col-12 col-md-4 align-self-end">
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
    //Select de cuidades
    $.ajax({
      url: "acciones",
      type: "GET",
      dataType: "json",
      cache: false,
      data: {
        accion: "listaCuidadesHabilitadas"
      },
      success: function(data){
        $("#ciudad").empty();
        for (let i = 0; i < data.cantidad_registros; i++) {
          $("#ciudad").append(`
            <option value="${data[i].fk_ciudad}">${data[i].m_nombre}</option>
          `);
        }
        $("#ciudad").selectpicker('refresh');
        $("#ciudad").focus().select();
      },
      error: function(){
        alertify.error("No se ha cargado las ciudades");
      }
    });

    //CheackBox de los cursos
    $.ajax({
      url: "acciones",
      type: "GET",
      dataType: "json",
      cache: false, 
      data: {
        accion: "listaCursos"
      },
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
        alertify.error("Error al traer los cursos.");
      }
    });

    //Validaciones del reporte
    $("#form_reporte").validate({
      debug: true,
      rules: {
        ciudad: "required"
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
</script>
</html>