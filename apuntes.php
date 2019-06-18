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
<div class="row mt-4 d-flex align-items-center">
      <div class="col-6">
        <h5 class="font-weight-bold sub-titulo">¿QUÉ ES EL MERCADEO?</h5>
        <p>
          El mercadeo es un proceso administrativo y conjunto de técnicas que permite a las empresas o instituciones, la adquisición, creación, producción, distribución, promoción y ventas de ideas comerciales, productos como bienes o servicios que logren satisfacer los objetivos de la organización.  
        </p>
      </div>
      <div class="col-6 text-center">
        <img class="w-50" src="../../almacenamiento/contenido/1/1/14/43-1.jpg">
      </div>
    </div>
    <p class="text-muted text-justify font-italic mt-4">
      “El marketing es una función de la organización y un conjunto de procesos para crear, comunicar y entregar valor a los clientes, y para manejar las relaciones con estos últimos, de manera que beneficien a toda la organización” 
    </p>
    <p class="text-right text-muted">-American Marketing Association-</p>
    <h5 class="sub-titulo font-weight-bold mt-4">MISIÓN</h5>
    <p>
     El departamento de mercadeo de Consumer Electronics Group cuenta con una misión interna la cual consiste en:
    </p>
    <p>
     Planear y ejecutar estrategias de comunicación que permitan posicionar la marca Hyundai en el mercado colombiano, a través de la comercialización de productos de valor para nuestros mercados meta, haciendo uso de herramientas publicitarias en medios tradicionales (masivos, no masivos) y digitales.
    </p>
    <p>
     El área de mercadeo esta 100% ligada a las estrategias promocionales de la compañía, cada colaborador debe mantener actualizado de las promociones y estrategias desarrolladas por la marca en conjunto con los clientes de retail (Éxito, Alkosto, Cencosud, etc.) y especializados (Innovar, Electrojaponesa, Lagobo, etc.) para garantizar la comunicación de dichas estrategias en piso.
    </p>
    <p>
     Para esta función el departamento de mercadeo tiene las siguientes áreas que lo conforman: 
    </p>
    <ul>
     <li>Comunicación y Diseño</li>
     <li>Social Media</li>
     <li>Formación y Desarrollo</li>
     <li>Trade Marketing </li>
    </ul>

    <h3 class="text-center text-hyundai font-weight-bold mt-4">ÁREA DE COMUNICACIÓN Y DISEÑO</h3>
    <p>
     Es el equipo encargado de crear la publicidad de los productos, en pro a ser competitivos y atractivos en el mercado. 
    </p>
    <p>
     En el área de diseño se define la síntesis de la pieza o arte a partir de elementos como el público a quien va dirigido el producto, los beneficios básicos que ofrece, la forma y el momento de su utilización; adicionalmente, su identificación con una categoría de producto ya existente y relación del nuevo producto con otras mercancías de la empresa que ya están en el mercado.
    </p>

    <h3 class="text-center text-hyundai font-weight-bold mt-4">MANUAL DE IDENTIDAD CORPORATIVA HYUNDAI</h3>
    <p class="text-center">(Marca líder coreana respaldada en Colombia por Consumer Electronics Group)</p>
    <img class="w-100" src="../../almacenamiento/contenido/1/1/14/43-2.jpg">
    <img class="w-100 mt-4" src="../../almacenamiento/contenido/1/1/14/43-3.jpg">
    <div class="row d-flex align-items-center">
      <div class="col-4">
        <p class="text-center">Más de la mitad de la población mundial utiliza Internet (3.750 millones) en su día a día. <br> ¡WE ARE SOCIAL!</p>
      </div>
      <div class="col-8">
        <img class="w-100 mt-4" src="../../almacenamiento/contenido/1/1/14/43-4.jpg"> 
      </div>
    </div>

    <h3 class="text-center text-hyundai font-weight-bold mt-4">ÁREA DE SOCIAL MEDIA</h3>
    <p class="mt-3 text-center">El departamento de Social Media Marketing es la encargada de la creación estratégica del canal de comunicación con la audiencia 100% digital.</p>
    <p>Social Media significa la construcción de un negocio a través de muchos medios diferentes como videos virales, redes sociales y blogs, con el fin de dar exposición a una empresa.</p>
    <p>A diferencia de los medios tradicionales donde los lectores o los espectadores son participantes pasivos, la red social es un lugar donde los clientes participan activamente e intercambian información, compartiendo experiencia, dando su opinión y comentarios basados en su comprensión.</p>

    <div class="row d-flex align-items-center">
      <div class="col-6">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/14/43-5.jpg">
      </div>
      <div class="col-6">
        <div class="card bg-light mb-3">
          <div class="card-body">
            <p class="card-text">Un usuario normal ocupa seis horas diarias desde algún dispositivo o servicio con conexión a internet. <i class="text-muted">- GlobalWebIndex -</i></p>
          </div>
        </div>
        <div class="card bg-light mb-3">
          <div class="card-body">
            <p class="card-text">Facebook sigue siendo la red social más popular. Sus usuarios representan más del 62% de los clientes totales de redes sociales en el mundo. <i class="text-muted">- ilifebelt.com -</i></p>
          </div>
        </div>
      </div>
    </div>

    <h5 class="sub-titulo font-weight-bold mt-4">¿SABES LO QUE HACE UN COMMUNITY MANAGER POR LA COMPAÑÍA?</h5>
    <ul>
      <li><h6 class="sub-titulo2 font-weight-bold">ES LA VOZ DE LA EMPRESA EN LA RED.</h6></li>
      <p>Ofrece por medio de sus publicaciones una comunicación más cercana con los usuarios de manera instantánea y de acuerdo a los principios de la marca. Es el embajador de la marca en Internet.</p>
      <li><h6 class="sub-titulo2 font-weight-bold">CONSTRUYE LO QUE LA GENTE QUIERE VER Y OÍR.</h6></li>
      <p>El community manager se asegurará de que el mensaje llegue de manera impecable al consumidor o cliente.</p>
      <li><h6 class="sub-titulo2 font-weight-bold">CREA UNA COMUNIDAD EN TORNO A LA MARCA.</h6></li>
      <p>Fideliza seguidores a través de concursos, contenidos y campañas con el fin de que se conviertan en clientes finales de nuestro producto. </p>
    </ul>
    <h3 class="text-hyundai font-weight-bold mt-4 text-center">ÁREA FORMACION Y DESARROLLO</h3>
    <p>El área de <b>Formación y Desarrollo</b> ha estado presente desde hace muchos años en la compañía, siendo visible en capacitaciones impartidas para el personal de venta y talleres de servicio de manera presencial en las diferentes ciudades nacionales e internacionales, donde actualmente tenemos existencia. </p>
    <p>Desde el año 2018 se empezó a desarrollar el proyecto de la plataforma de educación virtual <b class="text-hyundai">MANDINO</b>, la cual está encargada de realizar contenido escrito y audiovisual para ser compartido mediante la plataforma. Para esto, contamos con la disponibilidad del personal más calificado para instruir a todos los colaboradores de la compañía. </p>
    <p>Este departamento cuenta con un abastecimiento completo de  equipos de audio y video, estudio de grabación, chroma key para la toma de fotografías, equipo de iluminación  y un personal idóneo (educadora bilingüe, locutora y productor audiovisual) para generar capacitaciones de manera virtual con el fin de ser  más efectivos e irse adaptando al ritmo vertiginoso en el que avanza la tecnología, para así llevar a tiempo la información a la fuerza de ventas y generar clientes satisfechos por la atención brindada en el punto de venta. </p>
    <h3 class="text-hyundai font-weight-bold mt-4 text-center">ÁREA DE TRADE MARKETING</h3>
    <div class="row d-flex align-items-center">
      <div class="col-8">
        <h5 class="sub-titulo font-weight-bold">HISTORIA</h5>
        <p>Nace formalmente en Estados Unidos a finales de los ochenta como una necesidad de crear una colaboración más estrecha entre los grandes distribuidores minoristas, los fabricantes y productores de bienes. Esto con el fin de coordinar mejor diferentes actividades dentro del punto de venta y así lograr la satisfacción del cliente, en constantes mejoras y a su vez la rentabilidad para ambas partes.</p>
      </div>
      <div class="col-4 text-center">
        <img class="w-75" src="../../almacenamiento/contenido/1/1/14/43-6.jpg">
      </div>
    </div>
    <h5 class="sub-titulo font-weight-bold">IMPORTANCIA</h5>
    <p>
      Aparte de la necesidad de planear y llevar a cabo acciones de forma conjunta, la importancia del trade marketing radica en que entre el 70% y el 80% de las decisiones de compra que toman las personas se hacen en el punto de venta. Asimismo, si consideramos que el nivel de rotación de los productos depende en gran medida de las actividades que se lleven a cabo dentro del punto y que esto afecta de forma directa los intereses tanto del fabricante como del distribuidor, es evidente que las marcas deben poner en marcha diferentes estrategias y tácticas que en conjunto con los canales de distribución, les permitan mantener un nivel de ventas óptimo que sea significativo para el distribuidor.
    </p>
    <p>
      El fabricante o productor distribuye sus productos a través de diferentes intermediarios o canales a través de venta directa mediante establecimientos propios, o a través de una combinación de ambas. El trade marketing es en definitiva uno de los aspectos más importantes que se deben considerar no sólo para influir en la decisión de compra de las personas y tomar ventaja sobre otras marcas, sino que también, juega un papel importante en la construcción de marca siendo así más importante y crítico, cuando recién se está intentando posicionar un producto en el mercado y ganar la preferencia del cliente.
    </p>
    <p>
      Ahora bien, no es lo mismo realizar la distribución de un producto a través de terceros que de forma directa por medio de establecimientos propios o de internet. En estos casos las marcas están en condiciones de desplegar con mayor libertad y autonomía diferentes acciones para influenciar en el comportamiento de compra de las personas mediante la creación de estímulos en tiendas físicas o virtuales, teniendo además la ventaja de ejercer un control total sobre cada uno de los elementos que les permiten incidir en el comportamiento de los compradores. Sin embargo, aunque esta es una opción válida  que ofrece muchas ventajas para los negocios, presenta algunos inconvenientes como el costo que implica desarrollar una infraestructura propia para llegar al cliente directamente (por ejemplo invertir en establecimientos comerciales e incurrir en gastos importantes asociados), y en el caso de internet, que es un canal cuyas cifras de compra en un país como Colombia aún demuestran que no se ha desarrollado completamente, lo cual es especialmente crítico para algunas categorías en las que los hábitos de compra de las personas todavía se encuentran muy ligados a los canales minoristas tradicionales.
    </p>
    <p>
      Teniendo en cuenta las razones anteriores, se dejan en evidencia la importancia que tiene la distribución a manos de terceros y también muestran la dependencia de los fabricantes hacia estos. Para el fabricante esto representa una pérdida de poder frente al distribuidor en el que muchas veces debe estar dispuesto a otorgar ciertas concesiones a fin de que los bienes que produce puedan llegar al consumidor final y facilitar su compra, algo que siempre representa un desafío para las marcas pues no tienen control total de cada uno de los elementos que influyen en la decisión de compra de las personas y a su vez enfrentarse cara a cara con otras marcas contrincantes o sustitutas.
    </p>
    <p>
      Los objetivos principales del trade marketing son impulsar y acelerar el consumo y las ventas, mejorar la rotación del producto en el punto de venta, planificar y coordinar promociones; desarrollar merchandising y branding, y generar tráfico.
    </p>
    <h3 class="text-hyundai font-weight-bold mt-4 text-center">ACTIVIDADES CLAVES DE LAS QUE SE ENCARGA EL TRADE MARKETING</h3>
    <p>
      Es un conjunto de técnicas que buscan principalmente incentivar la demanda por parte del cliente final y aportar los máximos beneficios tanto al fabricante como al distribuidor. El trade marketing busca principalmente generar una serie de estímulos actuando directamente en el lugar donde concentra la oferta y la demanda para influir en la decisión de compra de las personas. De esta manera debe planear y ejecutar una serie de acciones encaminadas a lograr este objetivo.
    </p>
    <p>
      Sin embargo, también debemos considerar que el trade marketing no apunta exclusivamente a mejorar la rentabilidad y los beneficios de los principales agentes que intervienen en la entrega de los productos al cliente final, sino también a lograr la satisfacción plena del cliente a partir de la colaboración mutua entre fabricantes y distribuidores. Teniendo en cuenta esto, las siguientes son algunas de las actividades de las que se encarga el trade marketing para lograr sus propósitos:
    </p>
    <ul>
      <li>Cuidar la exhibición de los productos y llevar a cabo tareas relacionadas con <i>merchandising</i> que permitan crear estímulos favorables e influir en la decisión de compra de las personas.</li>
      <li>Realizar actividades de impulso a través de promotores que den a conocer el producto a los clientes y les brinden información sobre el mismo (actividades de impulso)</li>
      <li>Llevar a cabo activaciones y eventos promocionales que ayuden a crear experiencias de compra agradables para los clientes y les ayuden a las marcas a crear vínculos emocionales con ellos (fidelización).</li>
      <li>Crear incentivos para lograr prueba de producto (promociones, descuentos o concursos). Los incentivos que no afectan precios y agregados comerciales los determina comercial.</li>
      <li>A partir de la colaboración con los distintos canales, obtener información sobre el mercado y los hábitos de consumo de los clientes. Así permite hacer mejoras en los productos al obtener retroalimentación del mercado y tener un mejor conocimiento de la demanda que ayuda a hacer una mejor gestión de los inventarios.</li>
      <li>Establece mecanismos para aprovechar la compra por impulso (la cual tiene una incidencia importante en las ventas de los distribuidores minoristas) e incrementa la transacción promedio de los compradores a través de la creación de combos o paquetes.</li>
      <li>Se encarga de manejar las políticas de precios establecidas para los canales.</li>
    </ul>
    <h5 class="text-center sub-titulo font-weight-bold">¡A LA HORA DE COMPRAR, EL TRADE MARKETING PUEDE SER MÁS DECISIVO QUE LA PUBLICIDAD!</h5>
    <h5 class="font-weight-bold sub-titulo mt-4">CONCLUSIÓN </h5>
    <p>EL objetivo principal del trade marketing, debe estar enfocado en generar una experiencia de compra memorable, compuesta por una correcta exposición y distribución de productos en piso y una asesoría especializada que cumpla con las expectativas de los clientes, los satisfaga y fidelice.</p>
    <h3 class="text-hyundai font-weight-bold mt-4 text-center">SUB ÁREA - TRABAJO ADMINISTRATIVO PROMOTORES DE MERCADEO</h3>
    <h3 class="text-hyundai font}acciones.php text-center">RESPONSABILIDADES DEL PROMOTOR CONSUMER ELECTRONICS GROUP</h3>
    <ul>
      <li>Velar por la correcta exhibición y administración de nuestros productos en los puntos de venta. Tales como: marcación correcta de PVP, así como de una correcta presentación personal para impactar a los clientes.</li>
      <li>Etiquetar los productos con su respectiva referencia, precio y material POP actualizado.</li>
      <li>Velar por stock de nuestros productos y llevar inventario de productos que estén en modalidad VMI en los almacenes de nuestros clientes.</li>
      <li>Verificar que todo el POP que tenga una norma de ley (Ejemplo: retiq), se encuentre actualizado y sin falta alguna en los productos (con el fin de evitar demandas, pues todo es vigilado por la SIC).</li>
      <li>Hacer su ingreso formal al almacén y que este quede consignado en el registro que cada cliente lleva. Además, diligenciar el “Formato de Planilla de Asistencia” suministrado por la compañía, para el respectivo pago de nomina</li>
      <li>Cumplir con la ruta y horarios establecidos contractualmente por su jefe inmediato o por la compañía.</li>
      <li>Garantizar el buen uso y manejo de la dotación enviada y herramientas (uniformes, splitter o cajas de videos, controles normales, android y smart, cables HDMI, cables de video, USB, módems, router, canguros, maletines y demás).</li>
      <li>Verificar y reportar en cada tienda el estado de los productos (incompletos o con daños).</li>
      <li>Programarse para recibir mercancía, por medio de las transportadoras que utiliza la empresa, estar pendientes al llamado de estas para coordinar fecha y hora de entrega.</li>
    </ul>

    <h3 class="font-weight-bold text-center text-hyundai">INFORME SEMANAL PROMOTORES</h3>
    <h5 class="sub-titulo font-weight-bold">OBJETIVO</h5>
    <p>Conocer cuáles fueron las novedades de la competencia, eventos realizados y activaciones de marca.</p>
    <ul>
      <li>El informe semanal de promotores se debe reportar los días lunes antes de las 10 pm a los jefes inmediatos.</li>
      <li>Debe ir argumentadas las preguntas que se relacionan en el informe.</li>
      <li>Se recomienda utilizar el método de las 5 w+1h (esta es una fórmula para conocer la historia completa de algo).</li>
    </ul>
    <p>Ejemplo:</p>
    <p>¿Cuáles fueron las novedades o activaciones de la competencia?</p>
    <h5 class="sub-titulo font-weight-bold">WHAT? – ¿QUÉ?</h5>
    <p>Hace referencia a <b>QUE</b> acontecimientos, acciones e ideas realizo la competencia o qué tipo de evento se realizó para nuestra marca.</p>

    <h5 class="sub-titulo font-weight-bold">WHO? – ¿QUIÉN?</h5>
    <p>Hace referencia a las personas a las que nos dirigimos para poder ajustar el mensaje.</p>

    <h5 class="sub-titulo font-weight-bold">WHERE? – ¿DÓNDE?</h5>
    <p>Hace referencia al lugar donde se presentó la novedad o activación.</p>

    <h5 class="sub-titulo font-weight-bold">WHEN? – ¿CUÁNDO?</h5>
    <p>Hace referencia a la fecha en la cual se hizo el evento.</p>
    
    <h5 class="sub-titulo font-weight-bold">WHY? – ¿POR QUÉ?</h5>
    <p>Explica las razones por las cuales la competencia realizo la actividad.</p>

    <h5 class="sub-titulo font-weight-bold">EJEMPLO:</h5>
    <ul>
      <li>Introducción de nuevo producto al mercado.</li>
      <li>Posicionamiento de marca.</li>
      <li>Aniversario de la marca.</li>
      <li>Descuentos.</li>
      <li>Búsqueda de incremento de ventas.</li>
      <li>Entre otras.</li>
    </ul>

    <h5 class="sub-titulo font-weight-bold">HOW? – ¿CÓMO?</h5>
    <p>Esta es la pregunta más importante siendo base de la estructura de la historia que se quiere contar. Hace referencia al <b>CÓMO</b> se realizó la actividad de la competencia o secuencia de sucesos que se hicieron visibles en el punto de venta.</p>

    <h3 class="text-center font-weight-bold text-hyundai">ACTA DE ENTREGA DE OBSEQUIOS</h3>
    <p>El acta de entrega de obsequios fue creada con el fin de relacionar los obsequios entregados a los clientes en activaciones de marca en punto de venta.</p>
    <p>El promotor debe llenar todos los espacios de fecha, cantidad de obsequios entregados, punto de venta de la actividad, nombre del cliente, identificación, teléfono, descripción del obsequio entregado, serial del producto, fecha y numero de la factura y firma.</p>
    <p>Si el evento es una activación de marca con material de apoyo y los suvenires están siendo obsequiados a los clientes que no tienen compra, llenar la planilla de igual manera, en este caso tendrá campos que no requieren ser llenados como serial, cantidad, fecha y número de la factura.</p>
    <div class="row d-flex align-items-center">
      <div class="col-2">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/14/43-8.png">
      </div>
      <div class="col-8">
        <h5 class="sub-titulo font-weight-bold">PARA TENER EN CUENTA:</h5>
        <ul>
          <li>Cada planilla enviada a la regional debe tener la política de protección de    datos en el inferior del registro.</li>
          <li>Cada vez que solicites obsequios promocionales para activación de marca, debes hacerlo por medio de tu coordinador o jefe inmediato.</li>
          <li>La solicitud de artículos promocionales debe ir respaldada por una planilla, cada evento en una planilla de entrega diferente. <b>NO SE DEBE MEZCLAR DOS EVENTOS EN UN MISMO DOCUMENTO.</b> </li>          <li>En la parte superior de cada planilla debes relacionar el nombre del promotor encargado  y el tipo de evento que estas realizando.</li>
        </ul>
      </div>
    </div>

    <h3 class="text-center text-hyundai font-weight-bold">PLANILLA DE ASISTENCIA</h3>
    <p>La planilla de asistencia es un documento generado por Consumer Electronics Group para el seguimiento de las visitas que realizan los promotores a los PDV.</p>
    <p>Las planillas de control deben ser enviadas a la regional en la ciudad de Pereira los días 10 de cada mes, con el fin de revisar las visitas y generar el pago de dominicales trabajados.</p>
    <div class="row d-flex align-items-center">
      <div class="col-2">
        <img class="w-100" src="../../almacenamiento/contenido/1/1/14/43-8.png">
        
      </div>
      <div class="col-8">
        <h5 class="sub-titulo font-weight-bold">PARA TENER EN CUENTA:</h5>
        <ul>
          <li>Las planillas deben llegar limpias y en perfecto estado a la regional.</li>
          <li>Las planillas no deben ser modificadas </li>
          <li>Deben estar debidamente firmadas día a día por el coordinador de piso del almacén visitado por el promotor y diligenciadas en lapicero.</li>
          <li>No deben utilizar resaltador para tachar todo el recuadro de los días compensatorios. </li>
          <li>No deben colocar varios días en un mismo recuadro.</li>
        </ul>
      </div>
    </div>

    <h3 class="text-center text-hyundai font-weight-bold mt-4">HORARIO DE TRABAJO PARA PROMOTORES DE CONSUMER ELECTRONICS GROUP</h3>
    <p>
      Los colaboradores que ingresen a Consumer Electronics Group como promotores de mercadeo cuentan con el siguiente horario de trabajo:
    </p>
    <ul>
      <li>Lunes a Miércoles de 10 de la mañana a 7 de la noche.</li>
      <li>De Jueves a Sábado de 11 de la mañana a 8 de la noche.</li>
      <li>Los horarios anteriormente mencionados se ajustan según la necesidad de la empresa.</li>
    </ul>
    <h3 class="text-center text-hyundai font-weight-bold mt-4">PROTOCOLO DE UNIFORME CONSUMER ELECTRONICS GROUP</h3>
    <p>Consumer Electronics Group cuenta con un protocolo de vestuario con el fin de mantener toda la uniformidad de los colaboradores de la compañía, aplica también para los promotores de la marca SIMPLY.</p>
    <ul>
      <li>La camisa tanto para hombres como mujeres deberá ir por dentro del pantalón.</li>
      <li>Los zapatos deben ir brillados y siempre limpios.</li>
      <li>Camisa y pantalón planchados. </li>
      <li>Cabello sostenido en la parte de adelante con visibilidad de la cara.</li>
      <li>Maquillaje de uñas en colores pasteles. </li>
    </ul>
    <p>Elementos de seguridad como casco, guantes y botas punteras deben ser portadas siempre y cuando el almacén visitado lo exija, cuando se ingrese a una bodega en la manipulación o traslado de los productos de la compañía. </p>
   </div>
</body>
</html>