'use strict';

(function(window, $) {
    window.SelectDropdown = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('.js-select-dropdown .js-dropdown-1').on(
            'change',
            this.handleFirstDropdownChange.bind(this)
        );

        this.$wrapper.find('.js-select-dropdown .js-dropdown-2').on(
            'change',
            this.handleSecondDropdownChange.bind(this)
        );
    };

    $.extend(window.SelectDropdown.prototype, {

        handleFirstDropdownChange: function(e) {
            $.getJSON( "assets/frontend-client/json/select-spill-kits-direct.json", function( data ) {

                var items = ['\<option value="">--- Type ---</option>'];
                $.each( data, function( key, val ) {
                    if (val['select'] == $(e.currentTarget).val()) {
                        $.each( val['children'], function( key, val ) {
                            items.push('\<option value="1"\>' + val['select'] + '\</option\>');

                            var place = '.js-dropdown-2';

                            $(place).html(items.join( "" ));

                            $(place).removeAttr('disabled');
                        });
                    }
                });

            });
        }
    });

})(window, jQuery);