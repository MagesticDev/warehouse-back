<?php
	class TPL {
		public static function isMail($email, $pseudo, $titre, $contenu, $tpl){
			//function pour creer un template html
			$variables = array(
                'PSEUDO' => $pseudo,
                'DATE' => date('d/m/Y à H:i'),
                'TITRE' => $titre,
                'CONTENU' => $contenu,
                'URL' => $_SERVER['HTTP_HOST'],
                'EMAIL' => $email
            );
            
            $template = file_get_contents("template/".$tpl.'.html', FILE_USE_INCLUDE_PATH); // on charge le template html
			
			foreach($variables as $key => $value){
				$template = str_replace('{'.$key.'}', $value, $template);
			}
			
			return $template;

		}
	}
?>