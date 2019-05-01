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

	require_once($ruta_raiz . 'clases/funciones_generales.php');
  require_once($ruta_raiz . 'clases/sessionActiva.php');
  require_once($ruta_raiz . 'clases/librerias.php');

  //Traemos la session del usuario
  $session = new Session();
  $usuario = $session->get('usuario');

  $lib = new Libreria();

?>
<!DOCTYPE html>
<html>
<head>
	<?php  
    echo $lib->metaTagsRequired();
  ?>
	<title>Mandino</title>

	<?php  
    echo $lib->iconoPag();
    echo $lib->jquery();
    echo $lib->bootstrap();
    echo $lib->fontAwesome();
    echo $lib->jqueryValidate();
    echo $lib->alertify();
    echo $lib->mandino();
  ?>
</head>
<body>
	<?php include_once($ruta_raiz . 'navBar.php'); ?>
   <div class="container mt-4">
   	    <div class="text-center mb-5 row justify-content-center">
      <div class="col-8 col-md-5 col-lg-4">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/2/6-1.png">
      </div>
      <div class="col-12">
        <h3 class="mt-2 text-hyundai font-weight-bold">GESTIÓN HUMANA</h3>
      </div>
    </div>
    <p>
      <strong class="text-hyundai">CONSUMER ELECTRONICS GROUP S.A.S.</strong> fue constituida el 21 de diciembre de 2012, con el objetivo principal de ensamblar televisores, bajo la licencia de Hyundai Corporation, la compañía es distribuidor autorizado para Colombia y Latinoamérica de productos electrónicos y electrodomésticos para el hogar y el entretenimiento marca <strong class="text-hyundai">HYUNDAI</strong>.
    </p>
    <p>
      Consumer Electronics Group está ubicada en el Km 2 vía cerritos la Virginia en la ciudad de Pereira (Eje cafetero), lo que hace que tenga una localización estratégica y con conexión con las principales ciudades del país. <br>
      La empresa cuenta con dos líneas de Negocio: <br>
      <ul>
        <li>Línea marrón (tv, audio y video).</li>
        <li>Línea blanca ( aires acondicionados, neveras, congeladores y lavadoras)</li>
      </ul>

      Cobertura a nivel nacional en los canales Retail, Tradicional y Mayorista.
      <ul>
        <li>Eje-cafetero y Antioquia</li>
        <li>Centro-Oriente</li>
        <li>Pacifico</li>
        <li>Llanos</li>
        <li>Caribe</li>
      </ul>
    </p>
    <p>
      La especialidad de la compañía es el  servicio postventa con un excelente soporte técnico para la atención adecuada y oportuna de garantías y una línea de atención al cliente competente para responder a la necesidad de cada cliente.
    </p>
    <div class="card-deck">
      <div class="row justify-content-center">
        <div class="col-10 col-md-6 card">
          <div class="card-body">
            <h3 class="card-title text-center text-hyundai font-weight-bold">MISIÓN</h3>
            <p class="card-text"><strong class="text-hyundai">CONSUMER ELECTRONICS GROUP S.A.S</strong> es una empresa dedicada a la producción y comercialización de artículos electrónicos y electrodomésticos para el hogar, la industria y otros sectores, con los más altos estándares de calidad, tecnología, competitividad, y servicio; estamos ubicados en el Eje Cafetero, ubicación privilegiada como corredor logístico del centro, norte y suroccidente de Colombia.</p>
            <p class="card-text">Promovemos relaciones  de   confianza, la mejora continua de los procesos  y el compromiso  de nuestros colaboradores para la satisfacción de nuestros clientes, generando rentabilidad para el bienestar de todos nuestros grupos de interés, y en  permanente armonía con el medio ambiente.</p>
          </div>
        </div>
        <div class="col-10 col-md-6 card mt-4 mt-sm-0">
          <div class="card-body">
            <h3 class="card-title text-center text-hyundai font-weight-bold">VISIÓN</h3>
            <p class="card-text"><strong class="text-hyundai">CONSUMER ELECTRONICS GROUP S.A.S</strong> para el año 2020 será una empresa certificada y con reconocimiento de marca, que vive su cultura organizacional, enfocada permanentemente en la gestión, desarrollo e innovación de sus procesos, y apasionados por la satisfacción de nuestros clientes. </p>
          </div>
        </div>
      </div>
    </div>

    <div class="row justify-content-center mt-5">
      <div class="col-11">
        <div class="row justify-content-center">
          <div class="col-6 text-center order-2 order-md-1">
            <img class="w-100 w-md-75 w-lg-50" src="contenido/1/1/2/6-2.png">
          </div>
          <div class="col-12 col-md-6 align-self-center order-1 order-md-2">
            <h5 class="sub-titulo text-center text-md-left font-weight-bold">COMPETENCIAS DE LOS COLABORADORES</h5>
            <ul>
              <li>Trabajo en Equipo</li>
              <li>Orientación al Servicio</li>
              <li>Efectividad</li>
              <li>Innovación</li>
              <li>Gestión del Cambio</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-11 mt-4">
        <div class="row justify-content-center">
          <div class="col-12 col-md-6 align-self-center">
            <h5 class="sub-titulo text-center text-md-left font-weight-bold">COMPETENCIAS DE LOS LÍDERES</h5>
            <ul>
              <li>Toma de Decisiones estratégicas</li>
              <li>Desarrollo de sí mismo y de Otros</li>
              <li>Orientación al Logro</li>
            </ul>
          </div> 
          <div class="col-6 text-center">
            <img class="w-100 w-md-75 w-lg-50" src="contenido/1/1/2/6-3.png">
          </div>
        </div>  
      </div>
    </div>
    <h3 class="text-hyundai text-center mt-5 p-3 font-weight-bold">VALORES ORGANIZACIONALES</h3>
    <p><strong class="sub-titulo">Liderazgo:</strong> Intención de tomar el rol de líder en un equipo; implica un deseo de guiar a otros.</p>

    <p><strong class="sub-titulo">Respeto:</strong> Entender los deberes y derechos de cada una de las personas que hacen parte de la organización y actuar, siempre, partiendo de la consideración y valoración de la dignidad de la persona humana.</p>

    <p><strong class="sub-titulo">Honestidad:</strong> Actuar coherentemente por  el bien y el  interés general, actuar de manera clara y sincera en cada actividad de la empresa.</p>

    <p><strong class="sub-titulo">Lealtad:</strong> El trabajo en equipo, el amor por él, la lealtad y la transparencia frente a la organización, son características indispensables de nuestros colaboradores.</p>

    <p><strong class="sub-titulo">Responsabilidad:</strong> Capacidad que tiene todo individuo por tomar decisiones morales o racionales por sí mismo sin guía o autoridad superior.</p>

    <p><strong class="sub-titulo">Solidaridad:</strong> Participar colectivamente en la solución de problemas y en el cumplimiento de objetivos.</p>

    <h3 class="text-center text-hyundai mt-5 font-weight-bold">OBJETIVO DEL ÁREA DE GESTIÓN HUMANA</h3>
    <p>El objetivo de Gestión Humana es Desarrollar integralmente el recurso humano con base en procesos de mejoramiento continuo que garanticen el crecimiento del negocio, la satisfacción de los clientes y el bienestar de sus colaboradores.</p>
    <div class="row justify-content-center mt-5">
      <div class="col-12 col-md-6 align-self-center">
        <h5 class="sub-titulo text-center text-md-left font-weight-bold">SUBPROCESOS DEL ÁREA DE GESTIÓN HUMANA</h5>
        <ul>
          <li>Selección y Vinculación</li>
          <li>Inducción y Entrenamiento</li>
          <li>Nómina y seguridad Social</li>
          <li>Seguridad y Salud en el trabajo</li>
        </ul>  
      </div>
      <div class="col-6 col-md-6 text-center">
        <img class="w-100 w-md-50"  src="contenido/1/1/2/6-4.png" >
      </div>
    </div>

    <h3 class="text-center text-hyundai mt-5 font-weight-bold">TIPOS DE CONTRATO</h3>
    <div class="row mt-5">
      <div class="col-6">
        <!-- List group -->
        <div class="list-group" id="myList" role="tablist">
          <a class="list-group-item list-group-item-action active" data-toggle="list" href="#contrato-aprendiz" role="tab">Contrato de Aprendizaje</a>
          <a class="list-group-item list-group-item-action" data-toggle="list" href="#contrato-termino-fijo" role="tab">Contrato a termino fijo</a>
          <a class="list-group-item list-group-item-action" data-toggle="list" href="#contrato-termino-indefinido" role="tab">Contrato a termino indefinido</a>
          <a class="list-group-item list-group-item-action" data-toggle="list" href="#contrato-obra-labor" role="tab">Contrato por Obra o Labor</a>
        </div>
      </div>
      <div class="col-6 d-flex align-items-center">
        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane active" id="contrato-aprendiz" role="tabpanel">
            <ul>
              <li>Contrato especial de la legislación laboral.</li>
              <li>Cuota de sostenimiento.</li>
              <li>Duración de contrato de máximo 2 años.</li>
              <li>Afiliación a EPS y ARL.</li>
            </ul>
          </div>
          <div class="tab-pane" id="contrato-termino-fijo" role="tabpanel">
            <ul>
              <li>Duración especifica (3 meses, 1 año).</li>
              <li>Afiliaciones SGSS y Pago de Prestaciones Sociales.</li>
              <li>Manejo de prorrogas.</li>
              <li>Periodo de prueba de la quinta parte del contrato.</li>
            </ul>
          </div>
          <div class="tab-pane" id="contrato-termino-indefinido" role="tabpanel">
            <ul>
              <li>Duración indefinida.</li>
              <li>Afiliaciones SGSS y Pago de Prestaciones Sociales.</li>
              <li>Periodo de prueba de 2 meses.</li>
            </ul>
          </div>
          <div class="tab-pane" id="contrato-obra-labor" role="tabpanel">
            <ul>
              <li>Duración del contrato: hasta que termine la labor, máximo 6 meses.</li>
              <li>Prorroga.</li>
              <li>Afiliaciones a SGSS y Prestaciones Sociales.</li>
              <li>Relación Contractual.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row mt-5">
      <div class="col-12 col-md-6">
        <h5 class="text-hyundai sub-titulo font-weight-bold">¿QUE PAGA EL COLABORADOR?</h5>
        <ul>
          <li>4% EPS</li>
          <ul>
            <li>Elección del Colaborador</li>
          </ul>
          <li>4% Fondo de pensiones</li>
          <ul>
            <li>Elección del Colaborador</li>
          </ul>
        </ul>
      </div>
      <div class="col-12 col-md-6 mt-3 mt-md-0">
        <h5 class="text-hyundai sub-titulo font-weight-bold">¿QUE PAGA LA EMPRESA?</h5>
        <ul>
          <li>% ARL</li>
          <ul>
            <li>Elección de la empresa</li>
          </ul>
          <li>12% Fondo de pensiones</li>
          <ul>
            <li>Elección del Colaborador</li>
          </ul>
          <li>4% Caja de compensación</li>
          <ul>
            <li>Elección la Empresa</li>
          </ul>
        </ul>
      </div>
      <div class="col-12 mt-5">
        <h3 class="text-hyundai text-center font-weight-bold mb-3">RELOJ BIOMÉTRICO</h3>
        <div class="row">
          <div class="col-9 col-lg-6 align-self-center">
            <p>Para los colaboradores que se encuentren en la Oficina Principal en la Ciudad de Pereira   Se requiere realizar marcación  de entrada y salida para pago de la quincena (quincena vencida)</p>
          </div>
          <div class="col-3 col-lg-4">
            <img class="w-100 w-lg-50" src="contenido/1/1/2/6-5.png">
          </div>
        </div>
      </div>
      <div class="col-12 mt-5">
        <div class="row">
          <div class="col-12 col-lg-6">
            <h3 class="text-hyundai text-center font-weight-bold">BENEFICIOS PARA LOS COLABORADORES</h3>
            <ul>
              <li>Convenio de óptica plaza( 20% de descuento en monturas y lentes)</li>
              <li>Descuento  en productos de la empresa del 7% crédito, 10% contado</li>
              <li>Convenio EMI</li>
              <li>Convenio exequial Olivos y Ofrenda</li>
              <li>Cooperativa de ahorro</li>
              <li>Crédito de Libranza con Bancolombia</li>
              <li>Crédito de Libranza con Comfamiliar Rda.</li>
            </ul>
          </div>
          <div class="col-12 col-lg-4 mt-4 mt-lg-0">
            <h3 class="text-hyundai text-center font-weight-bold">HORARIOS DE ALIMENTACIÓN</h3>
            <ul>
              <li>Administrativo (45 minutos):  entre 12:00 pm  y 1:45 pm</li>
              <li>Producción:</li>
              <ul>
                <li>Turno 1: 25 min iniciando a las 9:00.</li>
                <li>Turno 2: 25 min iniciando a las  6:00 pm</li>
                <li>Turno 3: 25 min iniciando a las  2:00 am</li>
              </ul>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-12 mt-5">
        <h3 class="text-hyundai text-center font-weight-bold">REGLAMENTO INTERNO DE TRABAJO</h3>
        <p>Es el conjunto de normas que determinan las condiciones que deben sujetarse el empleador y sus trabajadores en la prestación del servicio. Además, las obligaciones que tendrán los trabajadores y el empleador dentro de la relación laboral.</p>
        <p>Lo puedes consultar en la Intranet o solicitarlo a nuestro departamento de gestión humana.</p>
        <a href="http://consumerelectronicsgroup.com/intranet/gestionhumana/">http://consumerelectronicsgroup.com/intranet/gestionhumana/</a>
      </div>
    </div>
   </div>
</body>
</html>