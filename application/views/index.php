<?php
echo $this->siteHeader();
echo $this->render(($this->innerTpl ?: Indi::uri('section') . '/' . Indi::uri('action')) . '.php');
echo $this->siteFooter();
