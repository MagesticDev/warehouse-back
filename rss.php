<?php 

//on définit l'encodage de la page
header('Content-type: application/rss+xml; charset=iso-8859-1');

echo '<?xml version="1.0" encoding="ISO-8859-1"?>'; 

?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<atom:link href="https://magestic.eu/rss.xml" rel="self" type="application/rss+xml" />
		<title>Magestic</title>
		<link>https://magestic.eu/</link>
		<description>Les derni&egrave;res nouvelles de Magestic</description>
		
		<?php
		
			date_default_timezone_set('Europe/Paris');
		
			include('classes/UTILS.class.php');
			include('classes/MYSQL.class.php');


			$query_news = mysql::query('SELECT sujets.id_sujet, sujets.titre, reponses.id, reponses.contenu, reponses.time
										FROM sujets INNER JOIN reponses ON sujets.id_sujet=reponses.sujet_id
										WHERE reponses.first=\'1\' AND sujets.id_forum=\'16\'
										ORDER BY reponses.time DESC
										LIMIT 10
									');								
								
			while ($donnees_news = mysql::FetchAssoc($query_news)) {
				echo '<item>
	<title>'.str_replace('’', "'", html_entity_decode(stripslashes($donnees_news['titre']))).'</title>
	<link>https://magestic.eu/Forum/section-les_nouveautees-16</link>
	<guid isPermaLink="false">s'.$donnees_news['id_sujet'].'</guid>
	<description><![CDATA[
		'.str_replace('’', "'", $donnees_news['contenu']).'
	]]></description>
	<pubDate>'.date('D, j M Y H:i:s', $donnees_news['time']).' +2000</pubDate>
</item>';
			}
		
		?>
		
	</channel>
</rss>