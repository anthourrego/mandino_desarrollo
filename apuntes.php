<?php  
	$resp .= $sql['cantidad_registros'] . "<br>";
  for ($i=0; $i < $sql['cantidad_registros']; $i++) { 

    $sql2 = $db->consulta("SELECT * FROM mandino_permisos WHERE fk_mp = :fk_mp", array(":fk_mp" => $sql[$i]['mp_id']));

    $resp .= $sql2['cantidad_registros'];

    if($sql2['cantidad_registros'] > 0) {
      $resp .= "<li>" . $sql[$i]['mp_tag'] . "</li>";
      for ($j=0; $j <$sql2['cantidad_registros']; $j++) { 
        $resp .= "<ul>";
        $resp .= "<li>" . $sql2[$j]['mp_tag'] . "</li>";        
        $resp .= permisos($sql2[$j]['mp_id']); 
        $resp .= "</ul>";
      }
    }else{
      $resp .= "<li>" . $sql[$i]['mp_tag'] . "</li>";
    }
  }

?>