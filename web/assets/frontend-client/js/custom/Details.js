'use strict';

(function(window, $) {
    window.Details = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('.js-delivery-address select').on(
            'change',
            this.handleSelectDeliveryAddressFromDropDown.bind(this)
        );

        this.$wrapper.find('.js-delivery-details').on(
            'click',
            this.UpdateDeliveryDetails.bind(this)
        );
    };

    $.extend(window.Details.prototype, {

        handleSelectDeliveryAddressFromDropDown: function(e) {
            var $url = $(e.currentTarget).val();

            $.ajax({
                url: $url,
                method: 'GET'
            }).done(function(data) {

                var place = '.js-form-fields';

                $(place).html(data);
            });

        },

        UpdateDeliveryDetails: function (e) {
            e.preventDefault();

            var $myForm = $(e.currentTarget).closest('form');
            var $urlPath = $(e.currentTarget).data('url');
            var $id = $myForm.find('fieldset').data('id');

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each( function( index, node ) {

                    $('#' + node.id + '').addClass('field-error');

                    _toastr('Please check the field "' + $("label[for='"+node.id+"']").text() + '" highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
                });
            } else {

                $.ajax({
                    url: $urlPath + '?id=' + $id + '&deliveryAddress={"firstName":"' + $('input[name="user_edit_address_delivery_form[firstName]"]').val() + '","lastName":"' + $('input[name="user_edit_address_delivery_form[lastName]"]').val() + '","company":"' + $('input[name="user_edit_address_delivery_form[company]"]').val() + '","address1":"' + $('input[name="user_edit_address_delivery_form[address1]"]').val() + '","address2":"' + $('input[name="user_edit_address_delivery_form[address2]"]').val() + '","city":"' + $('input[name="user_edit_address_delivery_form[city]"]').val() + '","postcode":"' + $('input[name="user_edit_address_delivery_form[postcode]"]').val() + '","phone":"' + $('input[name="user_edit_address_delivery_form[phone]"]').val() + '"}',
                    method: 'GET'
                }).done(function () {

                    _toastr('Delivery address updated', 'top-full-width', 'success', false);
                });
            }
        }
    });

})(window, jQuery);