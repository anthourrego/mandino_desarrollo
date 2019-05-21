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

  //require_once($ruta_raiz . 'clases/usuario.php');
  require_once($ruta_raiz . 'clases/sessionActiva.php');
  require_once($ruta_raiz . 'clases/funciones_generales.php');
  require_once($ruta_raiz . 'clases/Conectar.php');

  $session = new Session();

  $usuario = $session->get("usuario");
?>

<div id="tabla-inicio">
  <table class="text-center table table-hover">
    <thead class="thead-dark">
      <tr>
        <th scope="col">Intento #</th>
        <th scope="col">Resultado</th>
        <th scope="col">Tiempo</th>
        <!--<th scope="col">Revisión</th>-->
      </tr>
    </thead>
    <tbody id="contenidoTabla">
      
    </tbody>
  </table>
  <div class="text-center">
    <button id="btn-realizar-examen" class="btn btn-primary rounded-pill"><i class="fas fa-file-signature"></i> Realizar Taller</button>
  </div>
</div>

<div id="taller">
  <form id="tallerAprendizaje">
    <div id="contenidoTaller">
      
    </div>
    <div class="text-center">
      <input class="btn btn-success rounded-pill" type="submit" name="enviar" id="enviarTaller" value="Finalizar">
    </div>
  </form>
</div>

<div id="revisionTaller">
  <button id="btn-volverRevision" class="btn btn-danger"><i class="fas fa-reply"></i> Vover</button>
  <div class="row d-flex justify-content-center text-center">
    <div class="col-2">
      <div class="bg-success rounded-circle mx-auto" style="width: 50px; height: 50px;"></div>
      <h5>Correcta</h5>
    </div>
    <div class="col-2">
      <div class="bg-warning rounded-circle mx-auto" style="width: 50px; height: 50px;"></div>
      <h5>Correción</h5>
    </div>
    <div class="col-2">
      <div class="bg-danger rounded-circle mx-auto" style="width: 50px; height: 50px;"></div>
      <h5>Incorrecta</h5>
    </div>
  </div>
  <hr>
  <div id="contenidoRevision">
    
  </div>
</div>

<script type="text/javascript">
  $(function(){
    $("#taller").hide();
    $("#revisionTaller").hide();
    listaTabla();

    $("#btn-realizar-examen").on("click", function(){
      $("#tabla-inicio").hide();
      
      $.ajax({
        type: "POST",
        url: "<?php echo($ruta_raiz) ?>modulos/taller/acciones",
        data: {taller: <?php echo $taller; ?>, accion: "contenidoTaller", leccion: <?php echo($_GET['less']); ?>, unidad: <?php echo($_GET['uni']); ?>},
        success: function(data){
          $("#contenidoTaller").html(data);
        },
        error: function(){
          alertify.error("Error");
        }
      });

      $("#taller").show();
    });

    $("#tallerAprendizaje").submit(function(event){
      event.preventDefault();
      $.ajax({
        type: "POST",
        url: "<?php echo($ruta_raiz) ?>modulos/taller/acciones",
        cache: false,
        contentType: false,
        processData: false,
        data: new FormData(this),
        success: function(data){
          if (data == "Ok") {
            location.reload();
            /*listaTabla();
            $("#taller").hide();
            $("#tabla-inicio").show();*/
          }else{
            alertify.error(data);
          }
        },
        error: function(){
          alertify.error("No se ha podido enviar el formulario");
        }
      });
    });

    $("#btn-volverRevision").on("click", function(){
      $("#revisionTaller").hide();
      $("#tabla-inicio").show();
    });

  });

  function revisionTaller(id){
    $("#tabla-inicio").hide();
    $("#revisionTaller").show();

    $.ajax({
      type: "POST",
      url: "<?php echo($ruta_raiz) ?>modulos/taller/acciones",
      data: {accion: "revisionTaller", id_mtu: id},
      success: function(data){
        $("#contenidoRevision").html(data);
      },
      error: function(){
        alertify.error("No se ha podido traer la revision");
      }
    })
  }

  function listaTabla(){
    $.ajax({
      type: "POST",
      url: "<?php echo($ruta_raiz) ?>modulos/taller/acciones",
      data: {accion: "registroTalleresIntentos", taller: <?php echo $taller; ?>, usu: <?php echo($usuario['id']); ?>},
      success: function(data){
        $("#contenidoTabla").html(data);
      },
      error: function(){
        alertify.error("Error al traer la lista");
      }
    });
  }
</script>