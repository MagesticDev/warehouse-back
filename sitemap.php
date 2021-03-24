<?php

if(preg_match('#sitemap.php#i', $_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], '/?') === 0) {
    header("Status: 301 Moved Permanently", false, 301);
	header("Location: /sitemap.xml");
	exit;
}

header("Content-type: text/xml");
include('classes/UTILS.class.php');
include('classes/CACHE.class.php');
include('classes/MYSQL.class.php');

if(CACHE::is('sitemap', 43200)) { //43200 = 12h
	echo CACHE::get('sitemap');
} else {
	$echo = '<?xml version="1.0" encoding="UTF-8"?>
                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                    <url>
                        <loc>https://magestic.eu/</loc>
                        <changefreq>daily</changefreq>
                        <priority>1.00</priority>
                    </url>
                    <url>
                        <loc>https://magestic.eu/Accueil</loc>  
                        <changefreq>daily</changefreq>
                        <priority>0.95</priority>
                    </url>
                    <url>
                        <loc>https://magestic.eu/Game</loc>
                        <changefreq>daily</changefreq>
                        <priority>0.95</priority>
                    </url>
                    <url>
                        <loc>https://magestic.eu/Forum</loc>
                        <changefreq>daily</changefreq>
                        <priority>0.95</priority>
                    </url>';
	
                //news
                $query =  MYSQL::query("SELECT id_sujet FROM sujets WHERE id_forum=16 ORDER BY time DESC LIMIT 10");
                while($row = mysqli_fetch_object($query)) {
                    $echo .= '	<url>
                    <loc>https://magestic.eu/Forum/section-news-16/sujet-news-'.$row->id_sujet.'</loc>
                    <changefreq>daily</changefreq>
                    <priority>0.85</priority>
                </url>';
                }
	mysqli_free_result($query);

	$echo .= '</urlset>';
	
	CACHE::set('sitemap', $echo);
	
	echo $echo;
}

?>