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
    <h3 class="text-center text-hyundai font-weight-bold mt-4">SISTEMA INTEGRADO DE GESTIÓN</h3>
    <div class="row d-flex align-items-center">
      <div class="col-8">
        <h5 class="sub-titulo font-weight-bold mt-4">CONCEPTO GENERAL</h5>
        <ul>
          <li>Es una herramienta para unificar los sistemas de gestión de la organización de distintos ámbitos en uno sólo, recogiéndolos en una base documental única, además permiten controlar distintas facetas en una empresa como optimizar recursos, reducir costes y mejorar la productividad.</li>
          <li>Permite la articulación de diferentes requisitos en un solo sistema.</li>
          <li>Facilita la gestión, planeación, control y el mejoramiento continuo de las organizaciones.</li>
        </ul>
      </div>
      <div class="col-4 text-center">
        <img class="w-75" src="../../almacenamiento/contenido/1/1/4/15-3.png">
      </div>
    </div>
    <hr>
    <h5 class="sub-titulo font-weight-bold">BENEFICIOS EN IMPLEMENTAR</h5>
    <div class="row d-flex align-items-center">
      <div class="col-4">
        <div class="card">
          <div class="card-body">
            <p class="card-text"><b class="sub-titulo2">USUARIOS:</b> Asegura a todos los clientes, proveedores, colaboradores y otras partes interesadas, que la organización desarrolla su actividad cumpliendo la legislación  según la metodología de mejora continua.</p>
          </div>
        </div>
        <div class="card mt-4">
          <div class="card-body">
            <p class="card-text"><b class="sub-titulo2">CLARIDAD Y CONTROL EN LA GESTIÓN:</b> Su importancia radica en que los resultados se alcanzan con más eficiencia cuando las actividades y los recursos relacionados se gestionan como un proceso.</p>
          </div>
        </div>
      </div>
      <div class="col-4">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-4.png">
      </div>
      <div class="col-4">
        <div class="card mt-4">
          <div class="card-body">
            <p class="card-text"><b class="sub-titulo2">MEJORAMIENTO INTERNO:</b> Mejora el control de la empresa, así como las actividades y procesos. </p>
          </div>
        </div>
        <div class="card mt-4">
          <div class="card-body">
            <p class="card-text"><b class="sub-titulo2">EVALUACIÓN DEL RENDIMIENTO:</b> Refuerza la búsqueda por optimizar la satisfacción del cliente y partes interesadas.</p>
          </div>
        </div>
        <div class="card mt-4">
          <div class="card-body">
            <p class="card-text"><b class="sub-titulo2">LOGRO DE OBJETIVOS COMUNES:</b> La imagen de la empresa se ve mejorada ante la sociedad y crea un valor agregado diferencial a la competencia. </p>
          </div>
        </div>
      </div>
    </div>

    <hr>

    <h3 class="text-center text-hyundai font-weight-bold mt-5">SGC (sistema gestión de calidad) 9001-2015</h3>
    <div class="row d-flex align-items-center">
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo">CONCEPTO GENERAL</h5>
        <ul>
          <li>El sistema de gestión de la calidad es la gestión de servicios que se ofrecen, y que incluye planear, controlar, y mejorar, aquellos elementos de una organización, que de alguna manera afectan o influyen en la satisfacción del cliente y en el logro de los resultados deseados por la organización.</li>
        </ul>
        <h5 class="font-weight-bold sub-titulo">BENEFICIOS</h5>
        <ul>
          <li>Ayuda a mejorar la credibilidad e imagen de la empresa.</li>
          <li>Ayuda a satisfacer al cliente.</li>
          <li>Integra los procesos.</li>
          <li>Mejora la toma de decisiones basada en pruebas.</li>
          <li>Extiende la cultura de mejora continua.</li>
        </ul>
      </div>
      <div class="col-6">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-2.png">
      </div>
    </div>
    <hr>
    <h3 class="text-center text-hyundai font-weight-bold mt-5">NTC ISO 14001 2015</h3>
    <div class="row d-flex align-items-center">
      <div class="col-6">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-5.png">
      </div>
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo">CONCEPTO GENERAL</h5>
        <ul>
          <li>El propósito des SGA (Sistema de Gestión Ambiental) es proteger el medio ambiente y responder a las condiciones ambientales cambiantes, en equilibrio con las necesidades socioeconómicas.</li>
        </ul>
        <h5 class="font-weight-bold sub-titulo">BENEFICIOS</h5>
        <ul>
          <li>Aumento de la eficacia ambiental.</li>
          <li>Facilita el cumplimiento de la legislación vigente y la política ambiental de la organización.</li>
          <li>Se anticipa a los problemas ambientales que nos podamos encontrar, previniendo que aparezcan estos.</li>
          <li>Ayuda a la organización a disminuir le contaminación emitida por esta.</li>
        </ul>
      </div>
    </div>
    <hr>
    <h3 class="text-center text-hyundai font-weight-bold mt-5">NTC OHSAS 18001 2007</h3>
    <div class="row mt-3">
      <div class="col-4">
        <h5 class="font-weight-bold sub-titulo">CONCEPTO GENERAL</h5>
        <ul>
          <li>La norma OHSAS 18001 establece los requisitos mínimos de las mejores prácticas en gestión de seguridad y salud en el trabajo, destinados a permitir que una organización controle sus riesgos para la SST y mejore su desempeño.</li>
        </ul>
      </div>
      <div class="col-4">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-6.png">
      </div>
      <div class="col-4 align-self-end">
        <h5 class="font-weight-bold sub-titulo">BENEFICIOS</h5>
        <ul>
          <li>Crear las mejores condiciones de trabajo posibles en toda su organización.</li>
          <li>Identificar los riesgos y establecer controles de gestión.</li>
          <li>Reducir el número de accidentes laborales y bajas por enfermedad para disminuir los costes y tiempos de inactividad ligados a ellos.</li>
          <li>Comprometer y motivar al personal con unas condiciones laborales mejores y más seguras.</li>
        </ul>
      </div>
    </div>

    <hr>
    <h3 class="text-center text-hyundai font-weight-bold mt-5">BASC V5 – 2017</h3>
    <div class="row mt-3">
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo">BENEFICIOS</h5>
        <ul>
          <li>Es una alianza empresarial internacional que promueve un comercio seguro en cooperación con gobiernos y organismos internacionales. Facilita y agiliza el comercio internacional mediante el establecimiento, la administración de estándares y procedimientos globales de seguridad aplicada a la cadena logística del comercio internacional.</li>
        </ul>
      </div>
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo">BENEFICIOS</h5>
        <ul>
          <li>Reconocimiento y mayor contacto con autoridades vinculadas al comercio exterior.</li>
          <li>Mayor confianza por parte de las autoridades.</li>
          <li>Disminución de costos y riesgos derivados del control a sus procesos.</li>
        </ul>
      </div>
    </div>
    <hr>
    <h3 class="text-center text-hyundai font-weight-bold mt-5">ENFOQUE A PROCESOS</h3>
    <div class="row d-flex align-items-center mt-3">
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo">CONCEPTO GENERAL</h5>
        <p>El enfoque basado en procesos consiste en la identificación y gestión sistemática de los procesos desarrollados en la organización y en particular las interacciones entre tales procesos.</p>
        <p>El propósito final de la gestión por procesos es asegurar que todos los procesos de una organización se desarrollen de forma coordinada, mejorando la efectividad y la satisfacción de todas las partes interesadas (clientes, accionistas, personal, proveedores y la sociedad en general).</p>
      </div>
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo">COMO PODEMOS GENERAL VALOR DESDE LOS PROCESOS</h5>
        <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-7.png">
      </div>
    </div>
    
    <h5 class="font-weight-bold sub-titulo mt-4">Clasificación de procesos</h5>
    <p>El mapa de macro procesos y procesos de Consumer Electronics está dividido en 3 grandes grupos:</p>
    <div class="text-center">
      <img class="w-50" src="../../almacenamiento/contenido/1/1/4/15-8.png">
    </div>
    
    <h5 class="font-weight-bold sub-titulo mt-4">Clasificación de procesos</h5>
    <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-3.jpg">

    <h5 class="font-weight-bold sub-titulo mt-4">ALCANCE DEL SISTEMTA INTEGRADO DE GESTIÓN</h5>
    <div class="row mt-4 border">
      <div class="col-6 text-center border pt-3">
        <b>Sistema integrado de gestión (ISO 9001: 2015, ISO 14001:2015 y OHSAS 18001:2007):</b>
        <p>Ensamble de televisores y comercialización y distribución de electrodomésticos.</p>
        <img class="w-75" src="../../almacenamiento/contenido/1/1/4/15-9.png">
      </div>
      <div class="col-6 text-center border pt-3">
        <b>BASC V4:2012, Sectores elegibles importador y exportador:</b>
        <p>Importación y exportación de electrodomésticos.</p>
        <img class="w-50" src="../../almacenamiento/contenido/1/1/4/15-10.png">
      </div>
    </div>

    <h3 class="text-center text-hyundai font-weight-bold mt-5">POLITICA DEL SISTEMA INTEGRADO DE GESTIÓN</h3>
    <div class="row d-flex align-items-center">
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo mt-4">Concepto general</h5>
        <ul>
          <li>La política de una empresa, es un breve documento que demuestra el compromiso de la alta dirección y de todos los colaboradores de implantar un sistema de gestión orientado a la integración de procesos, a la mejora continua y al cumplimiento de requisitos aplicables a la organización.</li>
        </ul>
        <h5 class="font-weight-bold sub-titulo mt-4">Política del sistema integrado de gestión</h5>
        <p>Consumer Electronics Group S.AS, implementa, mantiene y mejora continuamente los sistemas de gestión, a través de la integración de los procesos, el cumplimiento de los requisitos legales y otros aplicables a la organización, comprometiéndose con:</p>
        <ul>
          <li>La satisfacción al cliente y las partes interesadas.</li>
          <li>La identificación y el control de aspectos e impactos significativos al medio ambiente con el fin de protegerlo y prevenir la contaminación.</li>
          <li>La identificación oportuna de peligros para el control de los riesgos, la prevención de lesiones y enfermedades laborales, así como la protección y promoción de la seguridad y salud en el trabajo.</li>
          <li>La prevención de actividades ilícitas en la cadena de suministro, tales como narcotráfico, terrorismo, lavado de activos, contrabando o robo.</li>
        </ul>
        <p>Para lograr lo anterior, desarrollamos integralmente el recurso humano y fomentamos la participación de las partes interesadas, proporcionando los recursos necesarios para la sostenibilidad de nuestro Sistema Integrado de Gestión. </p>
      </div>
      <div class="col-6">
        <img class="w-50" src="../../almacenamiento/contenido/1/1/4/15-1.png">
        <img class="w-50" src="../../almacenamiento/contenido/1/1/4/15-11.png">
      </div>
    </div>

    <h3 class="text-center text-hyundai font-weight-bold mt-5">OBJETIVOS ESTRATÉGICOS</h3>
    <ol>
      <li>Desarrollar integralmente el recurso humano con base en procesos de mejoramiento continuo que garanticen el crecimiento del negocio, la satisfacción de los clientes y el bienestar de sus colaboradores.</li>
      <li>Implementar procesos efectivos e innovadores que garanticen la competitividad del negocio.</li>
      <li>Estructurar ofertas de valor efectivas para el mercado que fortalezcan la preferencia de la empresa en los clientes.</li>
      <li>Fortalecer la orientación al servicio como factor diferenciador de nuestros equipos de trabajo frente a la competencia.</li>
      <li>Alcanzar niveles de rentabilidad que garanticen la sostenibilidad y el desarrollo continuo del negocio.</li>
      <li>Implementar un sistema de gestión integrado, que consolide un modelo de cultura por procesos, contribuya a la preservación del medio ambiente mediante la prevención de los impactos ambientales que se generen, fortalezca las condiciones de trabajo y el comportamiento para mejorar la salud y seguridad de los colaboradores, y establezca acciones para la prevención de actividades ilícitas en la cadena de suministro.</li>
    </ol>

    <h3 class="text-center text-hyundai font-weight-bold mt-5">ESTRUCTURA DOCUMENTAL</h3>
    <h5 class="font-weight-bold sub-titulo mt-4">1.CODIFICACIÓN</h5>
    <p>Los documentos que pertenezcan al sistema integrado de gestión de Consumer Electronics Group S.A.S cuentan con un modelo general de encabezado y cuerpo del documento. </p>
    <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-12.png">

    <ul class="mt-4">
      <li><b class="sub-titulo2">LOGO:</b> el uso del logo debe cumplir los lineamientos del manual de marca de Consumer Electronics Group S.A.S.</li>
      <li><b class="sub-titulo2">CÓDIGO DEL DOCUMENTO:</b> consta de un código alfanumérico así:</li>
      <ul>
        <li>Código del macroproceso. <b>Ver tabla 1</b>. Código del macroproceso</li>
        <li>Letra que identifica el tipo de documento. <b>Ver tabla 2</b>. Tipos de documentos</li>
        <li>Consecutivo. Consta de tres (3) dígitos.</li>
      </ul>

      <li><b class="sub-titulo2">NOMBRE DEL DOCUMENTO:</b> debe describir a qué tipo de documento está haciendo referencia guardando coherencia con el contenido.</li>

      <li><b class="sub-titulo2">VIGENCIA DEL DOCUMENTO:</b> debe contener la siguiente información:</li>
      <ul>
        <li><b class="sub-titulo2">VERSIÓN:</b> debe identificarse el número de la versión actual del documento.</li>
        <li><b class="sub-titulo2">FECHA:</b> se debe poner la fecha correspondiente a la aprobación del documento con el fin de evitar el uso de documentos obsoletos;</li>
      </ul>
    </ul>

    <p><b>tabla 1.</b> Código del macroproceso</p>
    <table class="table">
      <thead>
        <tr class="text-center">
          <th>MACROPROCESO</th>
          <th>CODIGO DE INDENTIFICACIÓN</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Gestión del Direccionamiento</td>
          <td>E01</td>
        </tr>
        <tr>
          <td>Sistema integrado de gestión</td>
          <td>E02</td>
        </tr>
        <tr>
          <td>Gestión de la cadena de suministros</td>
          <td>V01</td>
        </tr>
        <tr>
          <td>Gestión de Producción</td>
          <td>V02</td>
        </tr>
        <tr>
          <td>Comercialización</td>
          <td>V03</td>
        </tr>
        <tr>
          <td>Servicio postventa</td>
          <td>V04</td>
        </tr>
        <tr>
          <td>Administración del talento humano</td>
          <td>A01</td>
        </tr>
        <tr>
          <td>Infraestructura</td>
          <td>A02</td>
        </tr>
        <tr>
          <td>Gestión Financiera</td>
          <td>A03</td>
        </tr>
      </tbody>
    </table>

    <h5 class="font-weight-bold sub-titulo mt-4">1.2 TIPOS DE DOCUMENTOS</h5>
    <p>En Consumer Electronics Group, tenemos (5) tipos de documentos, el cual se relaciona a continuación:</p>
    <p><b>tabla 2.</b> Tipos de documentos</p>
    <img class="w-75" src="../../almacenamiento/contenido/1/1/4/15-13.jpg">

    <h5 class="font-weight-bold sub-titulo mt-4">1.3REGISTROS</h5>
    <p>Los formatos se deben diligenciar evitando tachones, enmendaduras, letra ilegible, diligenciamiento en lápiz o lapicero borrable, en papel químico; todo lo anterior con el fin de preservar la legibilidad de los registros de la organización</p>

    <h5 class="font-weight-bold sub-titulo mt-4">1.4 CONTROL DE CAMBIOS</h5>
    <img src="../../almacenamiento/contenido/1/1/4/15-14.png">

    <h3 class="text-center text-hyundai font-weight-bold mt-5">RESPONSABILIDADES Y AUTORIDADES EN EL SIG</h3>
    <p>Todos los líderes de procesos y colaboradores de la organización deben de cumplir con responsabilidades y cuentan con autoridad de acuerdo a los roles que se han establecido en el SIG.</p>
    <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-15.jpg">

    <h3 class="text-center text-hyundai font-weight-bold mt-5">INTRANET</h3>
    <ol>
      <li>La documentación del SIG se encuentra disponible en la Intranet de la organización este acceso se hace por medio de contraseña asignada a cada colaborador de la empresa.</li>
      <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-16.jpg">
      <li class="mt-3">Para acceder a la documentación, se debe realizar a través de la siguiente forma:</li>
      <ul class="mb-3">
        <li>Una vez se registre con el USUARIO Y CONTRASEÑA</li>
        <li>Ingresar al SIG</li>
      </ul>
      <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-17.jpg">
      <li class="mt-3 mb-3">Todos los formatos, procedimientos, manuales y otros que esten codificados como se explico en la sección No. 7 de ESTRUCTURA DOCUMENTAL lo puedes encontrar en documentos generales y mapa de macroprocesos</li>
      <img class="w-100" src="../../almacenamiento/contenido/1/1/4/15-18.jpg">
      <ul class="mt-3">
        <li><b class="sub-titulo2">DOCUMENTOS GENERALES:</b> En esta sección, están los documentos que son de consulta publica para varios procesos como, por ejemplo:</li>
        <ul>
          <li>Formato de solicitud de permiso</li>
          <li>Formato de legalización de anticipos</li>
          <li>Entre otros</li>
        </ul>
        <li><b class="sub-titulo2">MAPA DE MACROPROCESOS:</b> En esta sección, deberás de dar clic al macroproceso que pertenezcas y según el despliegue de procesos de nuevo dar clic.</li>
      </ul>
      <img class="w-100 mt-3" src="../../almacenamiento/contenido/1/1/4/15-19.jpg">
      <li class="mt-3">Y a continuación, encontraras la siguiente estructura por cada proceso:</li>
      <img class="w-100 mt-3" src="../../almacenamiento/contenido/1/1/4/15-20.jpg">
      <ul class="mt-3">
        <li><b class="sub-titulo2">CARACTERIZACIÓN:</b> La caracterización es la identificación de todos los factores y actividades que intervienen en el proceso y que se deben controlar.</li>
        <li><b class="sub-titulo2">DOCUMENTOS:</b> Son los procedimientos, instructivos y/o documentos del proceso, es decir, es el paso a paso de como se realiza una actividad.</li>
        <li><b class="sub-titulo2">FORMATOS:</b> Es el registro y trazabilidad como evidencia que se realizo una actividad especifica.</li>
      </ul>
    </ol>

   </div>


</body>
</html>