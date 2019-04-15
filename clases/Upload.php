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

	class Upload{
		private $data;
		private $data_sucess;
		private $form_field;
		private $extenciones_img;
		private $ruta_save;
		private $file_name;
		
		public function __construct($config,$data){
			global $ruta_raiz;
			
			$this->data=@$data;	
			$this->form_field=$config['file_selector'];
			$this->extenciones_img=array("image/jpeg","image/png");
			$this->ruta_save=$ruta_raiz.$config['ruta_save'];
			$this->file_name=@$config['file_name'];
		}	
		
		private function reArrayFiles($file_post) {
		
		    $file_ary = array();
		    $file_count = count($file_post['name']);
		    $file_keys = array_keys($file_post);
			
			for ($i=0; $i<$file_count; $i++){
				foreach ($file_keys as $key){
			       $file_ary[$i][$key] = $file_post[$key][$i];
				}
			}			
		    return $file_ary;
		}
		
		public function save_img(){
		  $form_field=$this->form_field;
		  $submited_files_v = $this->data[$form_field]; 
		   
		  $file_ary = $this->reArrayFiles($submited_files_v);
			$contador=0;
		  foreach ($file_ary as $submited_files) {	   
			   
				//  $submited_files_tmp_name = $submited_files["tmp_name"];
				$files_data = array();
				//SAVE
				if (in_array($submited_files["type"], $this->extenciones_img)){  
	        $size_data =  getimagesize($submited_files["tmp_name"]);
	        $files_data["name"] = $submited_files["name"];
	        $files_data["tmp_name"] = $submited_files["tmp_name"];
	        $files_data["type"] = $submited_files["type"];    
	        $files_data["size-w"] = $size_data[0];
	        $files_data["size-h"] = $size_data[1];
	        $files_data["size"] = $submited_files["size"];
	        $files_data["error"] = $submited_files["error"];   				
								
							
					$vext_img=explode('.', $files_data["name"] );
					$ext_img=$vext_img[count($vext_img)-1];	
					$post_fijo_img='';
					if($contador){
						$post_fijo_img='_'.$contador;
					}
					$nombre_imagen=$this->file_name.$post_fijo_img.'.'.$ext_img;	
						
					$resultado = @move_uploaded_file($files_data["tmp_name"], $this->ruta_save.$nombre_imagen);		
						
					$contador++;						
				}
						
			} //fin foreach
		}


		public function save_file(){
			$form_field=$this->form_field;
			$submited_files_v = $this->data[$form_field]; 
		   
			$file_ary = $this->reArrayFiles($submited_files_v);
			
			$nombres_archivos=array();
			$contador_array_files=0;
		   
		  foreach ($file_ary as $submited_files) {	   
			  $files_data = array();
					
			  $files_data["name"] = $submited_files["name"];
			  $files_data["tmp_name"] = $submited_files["tmp_name"];
				
				$vext_file=explode('.', $files_data["name"] );
				
				$extension_archivo=$vext_file[count($vext_file)-1];	
				$nombre_archivo_sin_extension=str_replace('.'.$extension_archivo,'', $files_data["name"]);

				$nombre_temporal_save=$nombre_archivo_sin_extension;
				$postfijo_nombre_temporal='';
				$postfijo_nombre_temporal_contador=0;
				while (file_exists($this->ruta_save.$nombre_temporal_save.$postfijo_nombre_temporal.'.'.$extension_archivo)) {
					$postfijo_nombre_temporal_contador++;
					$postfijo_nombre_temporal='_'.$postfijo_nombre_temporal_contador;	
				}		
					
				$nombre_final_archivo=$nombre_archivo_sin_extension.$postfijo_nombre_temporal.'.'.$extension_archivo;		
				
				$resultado = @move_uploaded_file($files_data["tmp_name"], $this->ruta_save.$nombre_final_archivo);
				
				if(file_exists($this->ruta_save.$nombre_final_archivo)){
					$nombres_archivos[$contador_array_files]['nombre_save']=$nombre_final_archivo;
					$nombres_archivos[$contador_array_files]['nombre_original']=$files_data["name"];
					$nombres_archivos[$contador_array_files]['extension']=$extension_archivo;
					$contador_array_files++;
				}						
					
					
			} //fin foreach
				
			return($nombres_archivos);
		}
	}

?>