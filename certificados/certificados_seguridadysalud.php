<?php  
  $max_salida=10; // Previene algun posible ciclo infinito limitando a 10 los ../
  $ruta_raiz=$ruta="";
  while($max_salida>0){
    if(is_file($ruta.".htaccess")){
      $ruta_raiz=$ruta; //Preserva la ruta superior encontrada
      break;
    }
    $ruta.="../";
    $max_salida--;
  }

  require_once($ruta_raiz . 'clases/sessionActiva.php');
  require_once($ruta_raiz . 'clases/Conectar.php');
  require_once($ruta_raiz . 'clases/funciones_generales.php');

  $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"); 

  $session = new Session();

  $usuario = $session->get("usuario");

  $db = new Bd();
  $db->conectar();

  $sql_usuario = $db->consulta("SELECT * FROM mandino_usuarios WHERE u_id = :u_id", array(":u_id" => $usuario['id'])); 
  
  $session->set('encabezado', '<img style="width: 745px;" src="'.$ruta_raiz.'img/certificados/encabezado1.jpg">');
  $session->set('pie','<br><br><br><h6 style="text-align: center; font-weight: bold;">Centro Industrial y Logístico de Pereira - km2 Vía Cerritos - La Virginia - Ent 5 Cafelia - PBX: 326 25 00 - EXT 123 - Pereira/Colombia <br> <span style="color: #1874c1;">www.consumerelectronicsgroup.com</span></h6>');  
  $session->set('html', '<h4 style="text-align: center; font-weight: bold; line-height: 26px;">DIRECTORA DE GESTIÓN HUMANA DE <br> CONSUMER ELECTRONICS GROUP S.A.S <br><br>CERTIFICA</h4>
      <p style="line-height: 26px; text-align: justify;">Que el sr(a) <b>' . strtoupper($sql_usuario[0]['u_nombre1']) . ' ' . strtoupper($sql_usuario[0]['u_nombre2']) . ' ' . strtoupper($sql_usuario[0]['u_apellido1']) . ' ' . strtoupper($sql_usuario[0]['u_apellido2']) . '</b> identificado con número de cédula <b>' . number_format($sql_usuario[0]['u_nro_documento']) . '</b> culminó y aprobó  exitosamente el módulo de seguridad y salud en el trabajo, en el cual se le dio a conocer las normas, reglamentos y procedimiento establecidos al interior de la organización, como también los peligros y riesgos en su trabajo, la prevención de accidentes y enfermedades laborales.</p>

      <p style="line-height: 26px; text-align: justify;">El presente documento se ha generado vía WEB, consideramos importante validarlo aquí estipulado con los colaboradores del área de formación y desarrollo.</p>
      <br>
      <p>Dada en Pereira, a los ' . date('d') . ' días del mes de ' . $meses[date('n')-1] . ' de ' . date('Y') .'. </p>
      <p>Cordialmente,</p>
      <br>
      <table style="padding-top: 10px;">
        <tr nobr="true">
          <td style="text-align: center;">
            <img src="'.$ruta_raiz.'img/certificados/firma_angela.png">
            Angela Maria Alvarez Rivas
            <br>
            <span style="font-size: 14px;">Directora Gestión Humana</span>
          </td>
          <td></td>
          <td style="text-align: center;">
            <img src="'.$ruta_raiz.'img/certificados/firma_fernanda.png">
              Nidia Fernanda Gallego Perez
            <br>
            <span style="font-size: 14px;">Coordinadora de Formación</span>
          </td>
        </tr>
      </table>');     
      
  $db->desconectar();

  $session->set('autor','Consumer Electronics Group S.A.S');                  
  $session->set('imprimir', 1);
  $session->set('tipo_salida', 'I');
  $session->set('marca_agua', 1);
  $session->set('ruta_marca_agua', $ruta_raiz.'img/certificados/marca_agua.png');
  $session->set('PDF_SAVE', 'Certificado Salud y Seguridad.pdf');
  require_once( $ruta_raiz . "clases/pdf.php");
?>