<?php
class Indi_View_Helper_InitMymailru {

	public function initMymailru() {

        // If my.mail.ru application id or private key was not found - return
        if (!($appId = Indi::ini()->social->mm->appId) || !($privateKey = Indi::ini()->social->mm->privateKey)) return;

        // Start output buffering
        ob_start();

        // Prepare initialization javascript
        ?><script type="text/javascript" src="http://cdn.connect.mail.ru/js/loader.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
	            if (typeof(mailru) == 'undefined') return;
                mailru.loader.require('api', function(){
                    mailru.connect.init('<?=$appId?>', '<?=$privateKey?>');
                    mailru.events.listen(mailru.connect.events.login, function(session){
                        mailru.common.users.getInfo(function(result){
			                $.post('./', {authType: 'mm', params: result[0]}, function(data){
				                window.location.reload();
			                })
		                });
                    });
                    mailru.events.listen(mailru.connect.events.logout, function(){
                    });
                    mailru.connect.getLoginStatus(function(result){
                        if (result.is_app_user != 1) {
		                    $('<a class="mrc__connectButton" style="display: none !important;">вход@mail.ru</a>').appendTo('body');
                            mailru.connect.initButton();
	                        $('.mrc__connectButton').hide();
                        }
                    });
                });
	        });
        </script><?

        // Return buffered contents
		return ob_get_clean();
	}
}