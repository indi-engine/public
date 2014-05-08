<?php
class Indi_View_Helper_InitVkontakte {

	public function initVkontakte() {

        // If no vkontakte API id was found - return
        if (!($apiId = Indi::ini()->social->vk->apiId)) return;

        // Include the vkontakte javascipt SDK
        ob_start();?>
<script async src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript" charset="windows-1251"></script>
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?45"></script>
<script type="text/javascript">VK.init({apiId: <?=$apiId?>});</script>
<div id="vk_auth"></div>
		<?$xhtml = ob_get_clean();
		return $xhtml;
	}
}