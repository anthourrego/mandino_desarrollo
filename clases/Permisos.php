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

	require_once($ruta_raiz . "clases/funciones_generales.php");
	require_once($ruta_raiz . "clases/Conectar.php");
	require_once($ruta_raiz . "clases/Session.php");

	class Permisos extends Bd{

		function __construct(){
			parent::__construct(); //Inicializamos la Bd
		}

		function validarPermisoPadre($user, $permiso){
			$this->conectar();

			$permisos = $this->consulta("SELECT * FROM mandino_permisos INNER JOIN mandino_permisos_usuarios ON mandino_permisos.mp_id = mandino_permisos_usuarios.fk_mp WHERE mandino_permisos.fk_mp = :fk_mp AND mandino_permisos_usuarios.fk_u = :fk_u", array(":fk_mp" => $permiso, ":fk_u" => $user));

			$this->desconectar();

			if ($permisos['cantidad_registros'] > 0) {
				return 1;
			}else{
				return 0;
			}
		}

		function validarPermiso($user, $permiso){
			$this->conectar();

			$permisos = $this->consulta("SELECT * FROM mandino_permisos_usuarios INNER JOIN mandino_permisos ON mandino_permisos_usuarios.fk_mp = mandino_permisos.mp_id WHERE fk_u = :fk_u AND mp_nombre = :mp_nombre", array(":mp_nombre" => $permiso, ":fk_u" => $user));

			$this->desconectar();
		
			if ($permisos['cantidad_registros'] == 1) {
				return 1;
			}else{
				return 0;
			}
		}
	}
?>