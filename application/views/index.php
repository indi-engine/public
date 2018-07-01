<?php
echo $this->siteHeader();
echo $this->render(Indi::uri('section') . '/' . Indi::uri('action') . '.php');
echo $this->siteFooter();
