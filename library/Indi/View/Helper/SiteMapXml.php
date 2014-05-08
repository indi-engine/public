<?php
class Indi_View_Helper_SiteMapXml {

    public function siteMapXml() {

        // Start output buffering
        ob_start();

        // Write the xml descriptor
        echo '<?xml version="1.0" encoding="UTF-8"?>';

        // Open the 'urlset' tag
        ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><?

        // Build the xml
        foreach(Indi::view()->tree as $item){
            ?><url><?
                ?><loc>http://<?=$_SERVER['HTTP_HOST']?><?=$item['href'] == '/index/'? '/' : $item['href']?></loc><?
                ?><changefreq>daily</changefreq><?
                ?><priority><?=$item['href'] == '/index/'?'1.0':'0.8'?></priority><?
            ?></url><?
        }

        // Close the 'urlset' tag
        ?></urlset><?

        // Get the buffered contents
        $xml = ob_get_clean();

        // If seo uri mode is turned on
        if (Indi::ini()->general->seoUri)

            // Transform the all the uri
            $xml = Indi_Uri::sys2seo($xml, false, '/<loc>(http\:\/\/' . $_SERVER['HTTP_HOST'] . ')([0-9a-z\/#]+)<\/loc>/');

        return $xml;
    }
}