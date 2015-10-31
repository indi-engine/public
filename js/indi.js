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
        indi.timeleft = function(to){
            var interval = Date.parse(to) - new Date - 1000 * 60 * 0, r = {
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




