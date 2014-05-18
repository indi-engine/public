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

        // If there is no <script> element in dom, that has 'std' attribute - return
        if(!$('script[std]').length) return;

        // If 'std' attribute is empty - return
        if ((indi.std = $('script[std]').attr('std')).length == 0) return;

        // Setup additional ajax config
        $.ajaxSetup({

            // Setup 'beforeSend' function
            beforeSend: function(xhr1, options) {

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




