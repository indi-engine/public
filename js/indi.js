$(document).ready(function(){
    window.Indi = function(indi) {

        // Setup default value for indi.std property
        indi.std = '';

        //
        indi.lang = {
            I_YES: 'Да',
            I_NO: 'Нет',
            I_ERROR: 'Ошибка',
            I_MSG: 'Сообщение',
            I_BACK: 'Вернуться',
            I_SAVE: 'Сохранить',
            I_CLOSE: 'Закрыть',
            I_AUTH: 'Авторизация',
            I_ACTION_DELETE_CONFIRM_TITLE: 'Подтверждение',
            I_ACTION_DELETE_CONFIRM_MSG: 'Вы уверены что хотите удалить запись',
            name: 'ru'
        };

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
		indi.auth.vk = function(callback) {

            // Default callback
            callback = callback || function(data) {
                eval(data);
            }

            // Auth
			VK.Auth.login(function (response) {
				if (response.session) {
					VK.Api.call('getUserInfo', {}, function(r) {
						if (r.response) {
							MYid = r.response['user_id'];
							VK.Api.call('getProfiles', {uids: MYid, fields: 'nickname,photo_big',format: 'JSON'}, function(z) {
								$.post('/', {authType: 'vk', params: z.response[0]}, callback);
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
		indi.auth.fb = function(callback){

            // Default callback
            callback = callback || function(data) {
                eval(data);
            }

            // Auth
            FB.login(function (response) {
				if (response.authResponse) {
					FB.api('/me', function(response) {
						$.post('/', {authType: 'fb', params: response}, callback);
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

            var j = 0;
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

                    // If field is currently hidden - we duplicate erroк message for it to be shown within
                    // Ext.MessageBox, additionally
                    if (cmp.css('display') == 'none' && !cmp.attr('data-validetta-after')) wholeFormMsg.push(errorByFieldO[i]);
                    
                    // Else
                    else {
                    
                        // Mark field as invalid
                        if (cmp.attr('data-validetta-after')) {
                            $(cmp.attr('data-validetta-after')).markInvalid(certainFieldMsg);
                        } else {
                            cmp.first().markInvalid(certainFieldMsg);
                        }

                        // Focus field
                        if (!j) $.scrollTo(cmp.attr('data-validetta-after') || cmp);
                        
                        // Error bubble should be removed once field got focused again
                        if (!cmp.attr('has-focus-handler')) {
                            cmp.attr('has-focus-handler', 'true');
                            cmp.on('focus', null, function(){
                                $(this).clearInvalid();
                            });
                        }

                        // Increment visible invalid-fields counter
                        j++;
                    }
                    
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

        $.fn.ierror = function(error) {

            // If `error` arg is null/false/empty/zero - remove error message from element
            if (!error) $(this).siblings('.i-field-error').remove();

            // Else
            else {

                // Prepare error message element
                var span = $('<span class="i-field-error"/>'), after = $(this).attr('i-field-error-after');

                // Append span, containing error message
                span.text(error).insertAfter(after ? $(this).siblings(after) : $(this));
            }
        }

        /**
         * Detect json-stringified error messages, wrapped with <error/> tag, within the raw responseText,
         * convert each error to JSON-object, and return an array of such objects
         *
         * @param rt Response text, for trying to find errors in
         * @return {Array} Found errors
         */
        indi.serverErrorObjectA = function(rt, entitiesEncoded) {

            // If response text is empty - return false
            if (!rt.length) return [];

            // If `entitiesEncoded` arg is `true`, we decode back htmlentities
            if (entitiesEncoded) rt = rt.replace(/&lt;/g, '<').replace(/&gt;/g, '>');

            // Define variables
            var errorA = [], errorI;

            // Pick errors
            $('<response>'+rt+'</response>').find('error').each(function(){
                if (errorI = JSON.parse($(this).text())) errorA.push(errorI);
            });

            // Return errors
            return errorA;
        };

        /**
         * Ensure <error>-element will be stripped from response
         */
        $.ajaxSetup({
            converters: {
                'text json': function(str){
                    return JSON.parse(str.split('</error>').pop())
                }
            }
        });

        /**
         * Builds a string representation of a given error objects, suitable for use as Ext.MessageBox contents
         *
         * @param {Array} serverErrorObjectA
         * @return {Array}
         */
        indi.serverErrorStringA = function(serverErrorObjectA) {

            // Define auxilliary variables
            var errorSA = [], typeO = {1: 'PHP Fatal error', 2: 'PHP Warning', 4: 'PHP Parse error', 0: 'MySQL query', 3: 'MYSQL PDO'},
                type, seoA = serverErrorObjectA;

            // Convert each error message object to a string
            for (var i = 0; i < seoA.length; i++)
                errorSA.push(((type = typeO[seoA[i].code]) ? type + ': ' : '') + seoA[i].text + ' at ' +
                    seoA[i].file + ' on line ' + seoA[i].line);

            // Return error strings array
            return errorSA;
        };

        /**
         * Common function for handling ajax/iframe responses
         * It detects <error>...</error> elements in responseText prop of `response` arg,
         * show them along with trimming them from responseText. It also detects whether
         * the trimmed responseText can be decoded into JSON, and if so, does it have
         * `mismatch`, `confirm` and `success` props and if so - handle them certain ways
         * and return `success` prop that can be undefined, null, boolean or other value
         *
         * @param response
         * @return {Boolean}
         */
        indi.parseResponse = function(event, response, options) {

            var json, wholeFormMsg = [], mismatch, errorByFieldO, msg, form = options.form, trigger,
                certainFieldMsg, cmp, seoA = Indi.serverErrorObjectA(response.responseText), sesA,
                logger = console && (console.log || console.error), boxA = [], urlOwner = options;

            // Remove 'answer' param, if it exists within url
            urlOwner.url = urlOwner.url.replace(/\banswer=(ok|no|cancel)/, '');

            // todo: Hide loadmask

            // Try to detect error messages, wrapped in <error/> tag, within responseText
            if (seoA.length) {

                // Build array of error strings from error objects
                sesA = Indi.serverErrorStringA(seoA);

                // Write php-errors to the console, additionally
                try { if (logger) for (var i = 0; i < sesA.length; i++) logger(sesA[i]);} catch (e) {}

                // Show errors within a message box
                boxA.push({
                    title: 'Server error',
                    msg: sesA.join('<br><br>'),
                    buttons: 'Ext.Msg.OK',
                    icon: 'Ext.MessageBox.ERROR',
                    modal: true
                });

                // Strip errors from response
                response.responseText = response.responseText.split('</error>').pop();
            }

            // Parse response text as JSON, and if no success - return
            try { json = JSON.parse(response.responseText); } catch (e) {

                // If response status code is 401
                if (response.status == 401) {

                    // Shortcut to .xhr node within responseText
                    var xhrEl = $(response.responseText).find('.xhr');

                    // Show errors within a message box
                    boxA.push({
                        title: indi.lang.I_AUTH,
                        msg: xhrEl.length ? xhrEl.html() : response.responseText,
                        modal: true,
                        create: function(e) {
                            $(e.target).find('[indi-auth-sn]').removeAttr('onclick').click(function(){
                                var sn = $(this).attr('indi-auth-sn');

                                // Try to auth using certain social network
                                Indi.auth[sn](function(){

                                    // Once auth succeeded - retry original request
                                    if (form) form.submit(); else $.ajax(options);
                                });

                                // Destroy current dialog
                                $(e.target).dialog('destroy');

                                // Return false
                                return false;
                            });
                        }
                    });
                }

                // Ensure second box will be shown after first box closed
                if (boxA[1]) boxA[0].fn = function() { indi.mbox(boxA[1]); }

                // Show box
                if (boxA.length) indi.mbox(boxA[0]);

                // Assign empty json object
                response.responseJson = {};

                // Return success as true or false
                return boxA.length ? false : true;
            }

            // The the info about invalid fields from the response, and mark the as invalid
            if ('mismatch' in json && $.isPlainObject(json.mismatch)) {

                // Shortcut to json.mismatch
                mismatch = json.mismatch;

                // Error messages storage
                errorByFieldO = mismatch.errors;

                // Detect whether errors are related to current form fields, or related to fields of some other entry,
                // that is set up to be automatically updated (as a trigger operation, queuing after the primary one)
                trigger = form ? (mismatch.entity ? mismatch.entity.table + '-' + (mismatch.entity.entry || 0) != options.row : false) : true;

                // Collect all messages for them to be bit later displayed within Ext.MessageBox
                Object.keys(errorByFieldO).forEach(function(i){

                    // If mismatch key starts with a '#' symbol, we assume that message, assigned
                    // under such key - is not related to any certain field within form, so we
                    // collect al such messages for them to be bit later displayed within Ext.MessageBox
                    if (i.substring(0, 1) == '#' || trigger) wholeFormMsg.push(errorByFieldO[i]);

                    // Else if mismatch key doesn't start with a '#' symbol, we assume that message, assigned
                    // under such key - is related to some certain field within form, so we get that field's
                    // component and mark it as invalid
                    else if (form && (cmp = (form.find('[name="' + i + '[]"]')[0] || form.find('[name="' + i + '"]')[0]))) {

                        // Get the mismatch message
                        certainFieldMsg = errorByFieldO[i];

                        // If mismatch message is a string - cut off field title mention from message
                        /*if (Ext.isString(certainFieldMsg))
                            certainFieldMsg = certainFieldMsg.replace('"' + cmp.fieldLabel + '"', '').replace(/""/g, '');*/

                        // Mark field as invalid
                        $(cmp).ierror(certainFieldMsg);

                        // If field is currently hidden - we duplicate error message for it to be shown within
                        // Ext.MessageBox, additionally
                        if ($(cmp).hidden) wholeFormMsg.push(errorByFieldO[i]);

                        // Else mismatch message is related to field, that currently, for some reason, is not available
                        // within the form - push that message to the wholeFormMsg array
                    } else wholeFormMsg.push(errorByFieldO[i]);
                });

                // If we collected at least one error message, that is related to the whole form rather than
                // some certain field - use an Ext.MessageBox to display it
                if (wholeFormMsg.length) {

                    msg = (wholeFormMsg.length > 1 || trigger ? '&raquo; ' : '') + wholeFormMsg.join('<br>&raquo; ');

                    // If this is a mismatch, caused by background php-triggers
                    if (trigger) msg = 'При выполнении вашего запроса, одна из автоматически производимых операций, в частности над записью типа "'
                        + mismatch.entity.title + '"'
                        + (parseInt(mismatch.entity.entry) ? ' [id#' + mismatch.entity.entry + ']' : '')
                        + ' - выдала следующие ошибки: <br><br>' + msg;

                    // Show message box
                    boxA.push({
                        title: indi.lang.I_ERROR,
                        msg: msg,
                        buttons: 'Ext.MessageBox.OK',
                        icon: 'Ext.MessageBox.ERROR',
                        modal: true
                    });
                }

            // Else if `confirm` prop is set - show it within Ext.MessageBox
            } else if ('confirm' in json) boxA.push({
                title: indi.lang.I_MSG,
                msg: json.msg,
                buttons: 'Ext.Msg.OKCANCEL',
                icon: 'Ext.Msg.QUESTION',
                modal: true,
                fn: function(answer) {

                    // Append new answer param
                    urlOwner.url = urlOwner.url.split('?')[0] + '?answer=' + answer
                        + (urlOwner.url.split('?')[1] ? '&' + urlOwner.url.split('?')[1] : '');

                    // If answer is 'ok' show load mask
                    //if (answer == 'ok') Indi.loadmask.show();

                    // Make new request
                    if (form) form.submit(); else $.ajax(options);
                }

            // Else if `success` prop is set
            }); else if ('success' in json && 'msg' in json) {

                // If `msg` prop is set - show it within Ext.MessageBox
                boxA.push({
                    title: indi.lang[json.success ? 'I_MSG' : 'I_ERROR'],
                    msg: json.msg,
                    buttons: 'Ext.Msg.OK',
                    icon: "Ext.Msg[json.success ? 'INFO' : 'WARNING']",
                    modal: true
                });
            }

            // Assign json
            response.responseJson = json;

            // If no boxes should be shown - return
            if (!boxA.length) return json.success;

            // Ensure second box will be shown after first box closed
            if (boxA[1]) boxA[0].fn = function() { indi.mbox(boxA[1]); }

            // Show first box
            indi.mbox(boxA[0]);

            // Return
            return json.success;
        };

        /**
         * Show dialog box
         *
         * @param cfg
         */
        indi.mbox = function(cfg) {
            var buttonS = (cfg.buttons || '').split('.').pop(), buttonA = [], i, possible = ['OK', 'CANCEL', 'YES', 'NO'];

            // Build buttons array
            for (i in possible)
                if (buttonS.match(new RegExp(possible[i])))
                    buttonA.push({
                        text: possible[i],
                        click: function(e) {
                            var answer = $(e.target).text().toLowerCase();
                            $(this).dialog('destroy');
                            if (cfg.fn) cfg.fn.call(this, answer);
                        }
                    });

            if ($.fn.dialog) {

                // Show message box
                $('<div id="dialog" title="'+cfg.title+'">'+cfg.msg+'</div>').dialog({
                    dialogClass: "no-close",
                    buttons: buttonA,
                    modal: cfg.modal,
                    width: 'auto',
                    maxWidth: '50%',
                    create: cfg.create
                });

            } else {
                if (buttonS == 'OKCANCEL') {
                    if (confirm(cfg.msg)) {
                        if (cfg.fn) cfg.fn.call(this, 'ok');
                    } else {
                        if (cfg.fn) cfg.fn.call(this, 'cancel');
                    }
                } else {
                    alert(cfg.msg);
                    if (cfg.fn) cfg.fn.call(this);
                }
            }
        }

        $.fn.iform = function(options) { $(this).each(function(){

            // Check that we deal only with forms
            if ($(this).prop('tagName') != 'FORM') return indi.mbox({msg: 'Это не форма'});

            // If `config` arg is a string - we assume it's a form selector
            if (typeof options == 'function') options = {onSuccess: options};

            // Default options
            var defaults = {
                submit: '.i-submit'
            }

            // Apply default options
            options = $.extend({}, defaults, options);

            // Make sure that form will be submitted once submit button/link/etc is clicked
            $(this).find(options.submit).click(function(){

                // Submit form
                $(this).parents('form').submit();

                // Return false (for case if <a>-element is used as submit button)
                return false;
            });

            // Remove error message once field is focused
            $(this).find('input, select, textarea').focus(function(){
                $(this).ierror(false);
            });

            // Bind handler for `submit` event
            $(this).submit(function(){

                // Remove previous submit-target iframe
                $(this).find('iframe[name^="i-form-target"]').remove();

                // Generate random name for form target iframe
                var name = 'i-form-target-' + Math.ceil(Math.random() * Math.pow(10, 5));

                // Append iframe
                $(this).append('<iframe name="' + name + '"></iframe>');

                // Set form target
                $(this).attr('target', name);

                // Bind handler on form target iframe's `load` event
                $(this).find('iframe[name="' + name + '"]').load(function(){
                    var doc, contentNode, frame = this, success, response = {responseText: '', responseXML: null},
                        form = $(frame).parents('form');

                    // Try to pick responseText
                    try {

                        // If iframe's document element is accessible
                        if (doc = frame.contentWindow.document || frame.contentDocument || window.frames[$(frame).attr('name')].document) {
                            if (doc.body) {

                                // Response sent as Content-Type: text/json or text/plain. Browser will embed in a <pre> element
                                // Note: The statement below tests the result of an assignment.
                                if ((contentNode = doc.body.firstChild) && /pre/i.test(contentNode.tagName))
                                    response.responseText = contentNode.innerText;

                                // Response sent as Content-Type: text/html. We must still support JSON response wrapped in textarea.
                                // Note: The statement below tests the result of an assignment.
                                else if (contentNode = doc.getElementsByTagName('textarea')[0])
                                    response.responseText = contentNode.value;

                                // Response sent as Content-Type: text/html with no wrapping. Scrape JSON response out of text
                                else response.responseText = doc.body.innerHTML;
                            }

                            //in IE the document may still have a body even if returns XML.
                            response.responseXML = doc.XMLDocument || doc;
                        }
                    } catch (e) {}

                    // Remove error messages
                    form.find('.i-field-error').remove();

                    // Parse response and detect success/failure
                    success = Indi.parseResponse(null, response, {
                        form: form,
                        url: form.attr('action'),
                        row: form.attr('data-row')
                    });

                    // Call onSuccess fn, passing response.responseJson as a direct argument
                    if (success && typeof options.onSuccess == 'function')
                        options.onSuccess.apply(form[0], [response.responseJson]);
                });
            });
        }); }

        indi.form = function(config) {
            var defaults = {
                form: 'form',
                action: null,
                submit: '.i-submit',
                listeners: {
                    beforeSubmit: function() {

                    }
                }
            }, options = $.extend({}, defaults);

            if (typeof config == 'string') options.form = config;
            else if (typeof config == 'object') options = $.extend({}, defaults, config);

            var f = $(options.form); if (f.prop('tagName') != 'FORM') return indi.mbox({msg: 'Это не форма'});

            f.find(options.submit).click(function(){
                f.submit();
            });

            f.submit(function(){
                //indi.mbox({msg: 'Попытка отправить форму'});
                if (f.find('input[type="file"]').length) {
                    f.append('<iframe name="i-submit-iframe"></iframe>');
                    f.attr('target', 'i-submit-iframe');
                    $('iframe[name="i-submit-iframe"]').load(function(){

                        // Get selector of a form, that current iframe is a target for
                        var formS = 'form[target="' + $(this).attr('name') +'"]';

                        // Remove previous errors
                        $(formS).find('.validetta-bubble').remove();

                        // JSON-decode iframe contents
                        var result = JSON.parse($(this).contents().find('body').text());

                    });
                } else {
                    $.ajax({
                        url: options.action || f.attr('action'),
                        method: options.method || f.attr('method'),
                        data: f.serialize(),
                        context: f
                    });
                }
                return false;
            });
        }

        // Post-process response to pick and show errors or other messages
        $(document).ajaxComplete(indi.parseResponse);

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




