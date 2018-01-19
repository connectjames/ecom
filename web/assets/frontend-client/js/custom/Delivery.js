'use strict';

(function(window, $) {
    window.Delivery = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('.js-postcode').on(
            'click',
            this.handleUpdateDeliveryBasket.bind(this)
        );
    };

    $.extend(window.Delivery.prototype, {

        handleUpdateDeliveryBasket: function(e) {
            e.preventDefault();

            var $myForm = $(e.currentTarget).closest('form');
            var $faFa = $(e.currentTarget).children('span');
            var $postcode = $(e.currentTarget).closest('div').find('input').val();
            var $postcodeEntry = ($(e.currentTarget).closest('div').find('input').val()).toUpperCase();
            var $deliveryCalculatorUrl = $(e.currentTarget).data('url');

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each(function (index, node) {

                    $('#' + node.id + '').addClass('field-error');

                    _toastr('Please check the field "' + $("label[for='" + node.id + "']").text() + '" highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
                });
            } else {

                $(e.currentTarget).closest('div').find('input').removeClass('field-error');

                $faFa.removeClass('fa-truck')
                    .addClass('fa-spinner')
                    .addClass('fa-spin');

                $postcode.toUpperCase();
                if ($postcode.charAt(0) == " ") {
                    $postcode.substring(1);
                    if ($postcode.charAt(0) == " ") {
                        $postcode.substring(1);
                    }
                }
                $postcode = $postcode.substring(0, 4);
                if ($postcode.charAt(4) == " ") {
                    $postcode = $postcode.substring(0, 3);
                }
                $.ajax({
                    url: $deliveryCalculatorUrl + '?postcode=' + $postcode + '&postcodeEntry=' + $postcodeEntry,
                    method: 'GET'
                }).done(function (data) {

                    $faFa.removeClass('fa-spinner')
                        .removeClass('fa-spin')
                        .addClass('fa-truck');

                    var place = '.js-box-totals';

                    var result = $('<div />').append(data).find('.js-box-totals').html();

                    $(place).html(result);
                });
            }
        }
    });
})(window, jQuery);