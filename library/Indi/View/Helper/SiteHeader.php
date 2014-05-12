<?php
class Indi_View_Helper_SiteHeader {

	public function siteHeader() {
		ob_start();?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <?=Indi::view()->siteFavicon()?>
        <title><?=Indi::view()->siteMetatag('title')?></title>
        <meta name="description" content="<?=Indi::view()->siteMetatag('description')?>">
        <meta name="keywords" content="<?=Indi::view()->siteMetatag('keywords')?>">
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <link rel="stylesheet" type="text/css" href="/css/adjust.css">
        <script src="/js/jquery-1.9.1.min.js"></script>
        <script src="/js/jquery-migrate-1.1.1.min.js"></script>
        <script src="/js/jquery.scrollTo-min.js"></script>
    </head>
    <body>
  <table class="main" width="1000" height="100%" align="center" border style="background-color: white;">
  <tr><td colspan="4" height="100">header will be here</td></tr>
  <tr>
	<td width="200" valign="top"><div id="authDepending"><?=$_SESSION['userId'] ? Indi::view()->userEntered() : Indi::view()->userLogin()?></div></td>
	<td valign="top">
	  <div>  
		
		<?return ob_get_clean();
	}
}