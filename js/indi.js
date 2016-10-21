$(document).ready(function(){
    window.Indi = function(indi) {

        // Setup default value for indi.std property
        indi.std = '';

        /**
         * Quotes string that later will be used in regular expression.
         *
         * @param str
         * @param delimiter
         * @return {String}
         */
        indi.pregQuote = function(str, delimiter) {
            return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
        }

		/**
		 * Empty object for social networks auth functions
		 */
		indi.auth = {};

        /**
         * Calculate the time, left until certain datetime
         *
         * @param to
         * @return {Object}
         */
        indi.timeleft = function(to, ago, append){
            var interval = ago ? (new Date - Date.parse(to)) : (Date.parse(to) - new Date + (append || 0) * 60 * 1000), r = {
                days: Math.floor(interval/(60*60*1000*24)*1),
                hours: Math.floor((interval%(60*60*1000*24))/(60*60*1000)*1),
                minutes: Math.floor(((interval%(60*60*1000*24))%(60*60*1000))/(60*1000)*1),
                seconds: Math.floor((((interval%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1)
            };

            // Get total time
            r.none = r.days + r.hours + r.minutes + r.seconds ? false : true;

            // Get string representation
            r.str = (r.days ? r.days + 'д ' : '')
                + (r.hours ? ((r.hours + '').length == 1 ? '0' : '') + r.hours + ':' : '')
                + ((r.minutes + '').length == 1 ? '0' : '') + r.minutes + ':'
                + ((r.seconds + '').length == 1 ? '0' : '') + r.seconds;

            // Return
            return r;
        }

        /**
		 * Auth using vkontakte
		 */
		indi.auth.vk = function() {
			VK.Auth.login(function (response) {
				if (response.session) {
					VK.Api.call('getUserInfo', {}, function(r) {
						if (r.response) {
							MYid=r.response['user_id'];
							VK.Api.call('getProfiles', {uids: MYid, fields: 'nickname,photo_big',format: 'JSON'}, function(z) {
								$.post('/', {authType: 'vk', params: z.response[0]}, function(data) {
									eval(data);
								});
							});
						} 
					});	
				}
			});
			return false;
		}
		
		/**
		 * Auth using facebook
		 */
		indi.auth.fb = function(){
			FB.login(function (response) {
				if (response.authResponse) {
					FB.api('/me', function(response) {
						$.post('/', {authType: 'fb', params: response}, function(data){
							eval(data);
						});
					});
				}
			});
			return false;
		}
			
		/**
		 * Logout
		 */
		indi.auth.logout = function(){
			$.post('/', {logout: true}, function(response){
			   window.location = Indi.std + '/';
			});
            return false;
		}
        
        /**
         * Convert the query string to the object, containing param-value pairs, and return it as a whole, 
         * or the value of a certain key, if `param` argument is given
         * 
         * @param param
         * @return {Object/String}
         */
        indi.get = function(param) {
            
            // Setup auxilliary variables
            var pairA = document.location.search.substr(1).split('&'), pairI, getO = {};
            
            // Build getO object
            for (var i = 0; i < pairA.length; i++) {
                
                // Get the param-value pair
                pairI = pairA[i].split('=');
                
                // Append to `getO` object as a value under certain property
                getO[pairI[0]] = pairI[1];
            }
            
            // Return whole object or a certain param
            return param ? getO[param] : getO;
        }

		// If there is no <script> element in dom, that has 'std' attribute - return
        if (!$('script[std]').length) return indi;

        // Make extjs injections, if needed
        $(function(){
            $('[i-load]').each(function(){
                $(this).html(
                    '<div class="x-border-box x-strict x-viewport">' +
                        '<div id="ext-container-body" class="x-body x-webkit x-chrome x-reset x-border-layout-ct x-container"></div>' +
                    '</div>'
                )
                $(this).find('.x-body').load($(this).attr('i-load'));
            });
        });


        indi.actionfailed = function(result, formS) {
            var action = {}, cmp, certainFieldMsg, wholeFormMsg = [], mismatch, errorByFieldO, trigger, msg;

            // Parse response text
            action.result = result;

            // If no info about invalid fields got from the response - return
            if (!action.result || !action.result.mismatch) return;

            // Shortcut to action.result.mismatch
            mismatch = action.result.mismatch;

            // Error messages storage
            errorByFieldO = mismatch.errors;

            // Detect are error related to current form fields, or related to fields of some other entry,
            // that is set up to be automatically updated (as a trigger operation, queuing after the primary one)
            trigger = mismatch.entity.title != $(formS).attr('data-model-title')
                || ((mismatch.entity.entry || '') != $(formS).attr('data-entry-id'));

            Object.keys(errorByFieldO).forEach(function(i){

                // If mismatch key starts with a '#' symbol, we assume that message, assigned
                // under such key - is not related to any certain field within form, so we
                // collect al such messages for them to be bit later displayed within Ext.MessageBox
                if (i.substring(0, 1) == '#' || trigger) wholeFormMsg.push(errorByFieldO[i]);

                // Else if mismatch key doesn't start with a '#' symbol, we assume that message, assigned
                // under such key - is related to some certain field within form, so we get that field's
                // component and mark it as invalid
                else if (((cmp = $(formS + ' [name="' + i + '"]')) && cmp.length) || ((cmp = $(formS + ' [name="' + i + '[]"]')) && cmp.length)) {

                    // Get the mismatch message
                    certainFieldMsg = errorByFieldO[i];

                    // If mismatch message is a string
                    if (typeof certainFieldMsg == 'string')

                    // Cut off field title mention from message
                        certainFieldMsg = certainFieldMsg.replace('"' + cmp.attr('placeholder') + '"', '').replace(/""/g, '');

                    // Mark field as invalid
                    cmp.first().markInvalid(certainFieldMsg);

                    // Error bubble should be removed once field got focused again
                    cmp.on('focus', null, function(){
                        $(this).clearInvalid();
                    });

                    // If field is currently hidden - we duplicate erroк message for it to be shown within
                    // Ext.MessageBox, additionally
                    if (cmp.hidden) wholeFormMsg.push(errorByFieldO[i]);

                    // Else mismatch message is related to field, that currently, for some reason, is not available
                    // within the form - push that message to the wholeFormMsg array
                } else wholeFormMsg.push(errorByFieldO[i]);
            });

            // If we collected at least one error message, that is related to the whole form rather than
            // some certain field - use an Ext.MessageBox to display it
            if (wholeFormMsg.length) {

                msg = (wholeFormMsg.length > 1 || trigger ? '» ' : '') + wholeFormMsg.join('<br><br>» ');

                // If this is a mismatch, caused by background php-triggers
                if (trigger) msg = 'При выполнении вашего запроса, одна из автоматически производимых операций, в частности над записью типа "'
                    + mismatch.entity.title + '"'
                    + (parseInt(mismatch.entity.entry) ? ' [id#' + mismatch.entity.entry + ']' : '')
                    + ' - выдала следующие ошибки: <br><br>' + msg;

                // Show message box
                /*Ext.MessageBox.show({
                 title: Indi.lang.I_ERROR,
                 msg: msg,
                 buttons: Ext.MessageBox.OK,
                 icon: Ext.MessageBox.ERROR
                 });*/
                alert(msg.replace(/<br>/g, "\n"));
            }
        }

        $.fn.markInvalid = function(message) {
            var span = $('<span class="validetta-bubble validetta-bubble--bottom" style="margin-left: 13px;"/>');
            if ($(this).attr('data-validetta-after')) {
                span.text(message).insertAfter($(this).siblings($(this).attr('data-validetta-after')));
            } else {
                span.text(message).insertAfter($(this));
            }
        }
        $.fn.clearInvalid = function() {
            $(this).siblings('.validetta-bubble').remove();
        }


        // If 'std' attribute is not empty - setup additional ajax config
        if (!((indi.std = $('script[std]').attr('std')).length == 0))
            $.ajaxSetup({

                // Setup 'beforeSend' function
                beforeSend: function(xhr, options) {

                    // If ajax url's first character is '/', but the second is not '/'
                    // and url does not already starting with value of indi.std property
                    if(options.url.match(/^\//) && !options.url.match(/^\/{2}/)
                        && !options.url.match(new RegExp('^(' + indi.pregQuote(indi.std) +')+\\b')))

                    // Prepend ajax url with a value of indi.std property
                    options.url = indi.std + options.url;
                }
            });

        return indi;
    }(window.Indi || {});

});




