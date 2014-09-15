<?php
echo $this->siteHeader();
$dir = DOC . STD . '/core/' . Indi::ini()->view->scriptPath . '/';
$core = $dir . Indi::trail()->view(null, true);
$coref  = preg_replace('/core(\/application)/', 'coref$1', $core);
$www  = preg_replace('/core(\/application)/', 'www$1', $core);
include(is_file($www) ? $www : (is_file($coref) ? $coref : $www));
echo $this->siteFooter();
