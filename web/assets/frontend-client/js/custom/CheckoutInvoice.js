'use strict';

(function(window, $) {
    window.CheckoutInvoice = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('.js-submit').on(
            'click',
            this.handleInvoiceSubmit.bind(this)
        );
    };

    $.extend(window.CheckoutInvoice.prototype, {

        handleInvoiceSubmit: function(e) {
            e.preventDefault();

            var $myForm = $(e.currentTarget).closest('form');

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each( function( index, node ) {

                    $('#' + node.id + '').addClass('field-error');

                    _toastr('Please check the field highlighted.', 'top-full-width', 'warning', false);
                });
            } else {
                $myForm.submit();
            }
        }
    });
})(window, jQuery);