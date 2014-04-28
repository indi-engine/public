<?php
echo $this->siteHeader();
$core = DOC . STD . '/core/' . Indi::ini()->view->scriptPath . '/' . Indi::uri()->section . '/' . (Indi::uri()->section == 'error' ? 'index' : Indi::uri('action')) . '.php';
$www  = preg_replace('/core(\/application)/', 'www$1', $core);
include(is_file($www) ? $www : $core);
echo $this->siteFooter();
