<?php
class Indi_View_Helper_SiteHeader {
	public function siteHeader(){
		ob_start();?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <meta charset="utf-8">
        <?=view()->siteFavicon()?>
        <title><?=view()->siteMetatag('title')?></title>
		<meta name="title" content="<?=view()->siteMetatag('title')?>">
        <meta name="description" content="<?=view()->siteMetatag('description')?>">
        <meta name="keywords" content="<?=view()->siteMetatag('keywords')?>">
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <link rel="stylesheet" type="text/css" href="/css/adjust.css">
        <script src="/js/jquery-1.9.1.min.js"></script>
        <script src="/js/jquery.scrollTo-min.js"></script>
    </head>
    <body>
        <table class="main" width="1000" height="100%" align="center" border style="background-color: white;">
            <tr><td colspan="4" height="100">header will be here</td></tr>
            <tr>
	            <td width="200" valign="top"><div id="authDepending"><?//=$_SESSION['userId'] ? view()->userEntered() : view()->userLogin()?></div></td>
	            <td valign="top">
	                <div>
		<?return ob_get_clean();
	}
}