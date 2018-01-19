'use strict';

(function(window, $) {
    window.CheckoutDelivery = function($wrapper) {
        this.$wrapper = $wrapper;
        this.helper = new Helper($wrapper);

        this.$wrapper.find('.js-new-address input').on(
            'click',
            this.handleSelectNewAddress.bind(this)
        );

        this.$wrapper.find('.js-invoice input').on(
            'click',
            this.handleSelectInvoice.bind(this)
        );

        this.$wrapper.find('.js-saved-address input').on(
            'click',
            this.handleSelectSavedAddress.bind(this)
        );

        this.$wrapper.find('.js-saved-address select').on(
            'change',
            this.handleSelectSavedAddressFromDropDown.bind(this)
        );

        this.$wrapper.find('.js-submit').on(
            'click',
            this.handleDeliverySubmit.bind(this)
        );
    };

    $.extend(window.CheckoutDelivery.prototype, {

        handleSelectNewAddress: function(e) {
            $('.js-saved-address select').attr('disabled', 'disabled');

            var $url = $(e.currentTarget).data('url');

            $.ajax({
                url: $url,
                method: 'GET'
            }).done(function(data) {

                var place = '.js-form-fields';

                $(place).html(data);
            });
        },

        handleSelectInvoice: function(e) {
            $('.js-saved-address select').attr('disabled', 'disabled');

            var $url = $(e.currentTarget).data('url');

            $.ajax({
                url: $url,
                method: 'GET'
            }).done(function(data) {

                var place = '.js-form-fields';

                $(place).html(data);
            });
        },

        handleSelectSavedAddress: function(e) {
            $('.js-saved-address select').removeAttr('disabled');
        },

        handleSelectSavedAddressFromDropDown: function(e) {
            var $url = $(e.currentTarget).val();

            $.ajax({
                url: $url,
                method: 'GET'
            }).done(function(data) {

                var place = '.js-form-fields';

                $(place).html(data);
            });

        },

        handleDeliverySubmit: function(e) {
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

    window.Helper = function ($wrapper) {
        this.$wrapper = $wrapper;
    };

    $.extend(Helper.prototype, {
        // handleUpdateDeliveryCheckout: function() {
        //
        //     var $myForm = $('form.js-form-postcode');
        //     var $button = $('.js-postcode-checkout');
        //     var $input = $('#checkout_delivery_form_postcode');
        //     $myForm.find('input').val($input.val());
        //     var $faFa = $button.children('span');
        //     var $postcode = $myForm.find('input').val();
        //     var $postcodeEntry = ($myForm.find('input').val()).toUpperCase();
        //     var $deliveryCalculatorUrl = $button.data('url');
        //
        //     if (!$myForm[0].checkValidity()) {
        //
        //         $myForm.find(':invalid').each(function (index, node) {
        //
        //             $input.addClass('field-error');
        //
        //             _toastr('Please check the field "Postcode" highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
        //         });
        //     } else if ($postcode.length < 6 || $postcode.length > 8) {
        //
        //         $input.addClass('field-error');
        //
        //         _toastr('Please check the field "Postcode" highlighted. Error: Please lengthen this text in between 6 characters and 8 characters.', 'top-full-width', 'warning', false);
        //     } else {
        //
        //         $input.removeClass('field-error');
        //
        //         $faFa.removeClass('fa-truck')
        //             .addClass('fa-spinner')
        //             .addClass('fa-spin');
        //
        //         $postcode.toUpperCase();
        //         if ($postcode.charAt(0) == " ") {
        //             $postcode.substring(1);
        //             if ($postcode.charAt(0) == " ") {
        //                 $postcode.substring(1);
        //             }
        //         }
        //         $postcode = $postcode.substring(0, 4);
        //         if ($postcode.charAt(4) == " ") {
        //             $postcode = $postcode.substring(0, 3);
        //         }
        //         $.ajax({
        //             url: $deliveryCalculatorUrl + '?postcode=' + $postcode + '&postcodeEntry=' + $postcodeEntry,
        //             method: 'GET'
        //         }).done(function(data) {
        //
        //             $faFa.removeClass('fa-spinner')
        //                 .removeClass('fa-spin')
        //                 .addClass('fa-truck');
        //
        //             var place = '.js-box-totals';
        //
        //             var result = $('<div />').append(data).find('.js-box-totals').html();
        //
        //             $(place).html(result);
        //         });
        //     }
        // }
    });
})(window, jQuery);