<?php

class WYSIWYG {

	public static function editeur($nom, $contenu) {
		
		$tplW = new Template();
		$tplW->setFile('wysiwyg', '_wysiwyg.html'); 
		
		$tplW->values(Array(
			'NAME' => $nom,
			'CONTENU' => self::MICode($contenu),
		));
		
		return $tplW->construire('wysiwyg');
	}
	
	public static function MICode($texte, $isAdmin = false) {

		self::BBCode($texte);	
		
		
		
		if($isAdmin) {
			$filtre = new ZendFilterStripTags(
				Array('b', 'strong', 'i', 'u', 'span', 'font', 'div', 'a', 'img', 'br', 'strike', 'fieldset', 'legend', 'table', 'tr', 'th', 'td', 'object', 'param', 'ol', 'li', 'ul', 'p', 'sub', 'sup', 'iframe', 'hr','blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'address', 'pre', 'tt', 'big', 'small', 'code', 'del', 'ins', 'cite', 'q', 's', 'em'), 
				Array('style', 'size', 'href', 'src', 'color', 'type', 'data', 'width', 'height', 'id', 'name', 'value', 'allowfullscreen','frameborder', 'class'), 
				false
			);
		} else {
			$filtre = new ZendFilterStripTags(
				Array('b', 'strong', 'i', 'u', 'span', 'font', 'div', 'a', 'img', 'br', 'strike', 'fieldset', 'legend', 'table', 'tr', 'th', 'td', 'object', 'param', 'ol', 'li', 'ul', 'p', 'sub', 'sup', 'iframe', 'hr', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'address', 'pre', 'big', 'small', 'tt', 'code', 'del', 'ins', 'cite', 'q', 's', 'em'), 
				Array('style', 'size', 'href', 'src', 'color', 'type', 'data', 'width', 'height', 'id', 'name', 'value', 'allowfullscreen', 'frameborder', 'class'), 
				false
			);
		}
		
	
	
		
		$texte = preg_replace(Array("#\<([\s]*)p([\s]*)\>#i", "#\<\/([\s]*)p([\s]*)\>#i"), Array(' ', '<br />'), $texte);

		$texte = $filtre->filter($texte);
	
		$texte = str_replace("\t", "", $texte);

		//liens : pas de js
		$texte = preg_replace(	"#<[\s]*a[\s]+href[\s]*=[\s]*?('|\")(?!http://|ftp://|https://)(.+?)('|\")?[\s]*>(.+?)<[\s]*/[\s]*a[\s]*>#i",
								"&lt;a href=\"$2\"&gt;$4&lt;/a&gt;", 
								$texte);
								
		
					

		return $texte;
	}

	public static function BBCode(&$texte) {
		$texte = preg_replace("@\[b\](.+?)\[\/b\]@i", "<b>$1</b>", $texte);
		$texte = preg_replace("@\[barre\](.+?)\[\/barre\]@i", "<strike>$1</strike>", $texte);
		$texte = preg_replace("@\[i\](.+?)\[\/i\]@i", "<i>$1</i>", $texte);
		$texte = preg_replace("@\[u\](.+?)\[\/u\]@i", "<u>$1</u>", $texte);
		$texte = preg_replace("@\[url\]http(.+?)\[\/url\]@i", "<a href=\"http$1\">[Link]</a>", $texte);
		$texte = preg_replace("@\[url=http(.+?)\](.+?)\[\/url\]@i", "<a href=\"http$1\">$2</a>", $texte);
		$texte = preg_replace("@\[color=#(.+?)\](.+?)\[\/color\]@i", "<font color=\"#$1\">$2</font>", $texte);
		$texte = preg_replace("@\[surligne=#(.+?)\](.+?)\[\/surligne\]@i", "<span style=\"background-color: #$1;\">$2</span>", $texte);
		$texte = preg_replace("@\[taille=(.+?)\](.+?)\[\/taille\]@i", "<font size=\"$1\">$2</font>", $texte);
		$texte = str_replace("[separation]", "<br /><img src=\"images/hr.jpg\"><br />", $texte);
		
		//traitement des images (et gestion de la taille)
		$nb = preg_match_all('`\[img\](\S+[a-zA-Z0-9]/?)\[\/img\]`U', $texte, $elements); 
		for($i = 0 ; $i < $nb ; ++$i) {
		
			$el = str_replace('[/img]', '', str_replace('[img]', '', $elements[0][$i]));
		
			//pour IE, y'a pas encore de solution. J'utilisais avant la fonction 
			//getimagesize, mais les pages �tait trop lourdes et lentes
			/*if(utilise_ie()) {
				$tailleimg = ' style="max-width:330px; height:auto;"';
				//$tailleimg = ' width="330px"';
			} else {*/
				$tailleimg = ' style="max-width:330px; height:auto;"';
			//}
		
			$texte = str_replace($elements[0][$i], '<a href="'.$el.'"><img src="'.$el.'" alt="Image introuvable" border="0"'.$tailleimg.' /></a>', $texte);
		}
		
		return $texte;
	}

}

?>