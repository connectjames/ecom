'use strict';

(function(window, $) {
    window.ClientOrdView = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('#js-invoice').on(
            'click',
            this.sendInvoiceClient.bind(this)
        );

        this.$wrapper.find('#js-status select').on(
            'change',
            this.handleStatusChange.bind(this)
        );

        this.$wrapper.find('.js-reset-password').on(
            'click',
            this.handlePasswordReset.bind(this)
        );

        this.$wrapper.find('.js-admin input').on(
            'change',
            this.handleAdminStatusChange.bind(this)
        );

        this.$wrapper.find('.js-status input').on(
            'change',
            this.handleOrderStatusChange.bind(this)
        );

        this.$wrapper.find('#js-invoice-details').on(
            'click',
            this.UpdateInvoiceDetails.bind(this)
        );

        this.$wrapper.find('.js-delivery-details').on(
            'click',
            this.UpdateDeliveryDetails.bind(this)
        );

    };

    $.extend(window.ClientOrdView.prototype, {

        sendInvoiceClient: function () {
            $.ajax({
                url: this.$wrapper.find('#js-invoice').data('url'),
                method: 'GET'
            }).done(function() {

                _toastr('Order resent to client','top-full-width','success',false);
            });
        },

        handleStatusChange: function () {
            $.ajax({
                url: this.$wrapper.find('#js-status select').data('url') + '&admin=' + this.$wrapper.find('#js-status select').val(),
                method: 'GET'
            }).done(function() {

                _toastr('Status of this order updated','top-full-width','success',false);
            });
        },

        handlePasswordReset: function () {
            $.ajax({
                url: this.$wrapper.find('.js-reset-password').attr('href'),
                method: 'GET'
            }).done(function() {

                _toastr('An email has been sent through to the client email address','top-full-width','success',false);
            });
        },

        handleAdminStatusChange: function () {
            $.ajax({
                url: this.$wrapper.find('.js-admin input').data('url') + '&admin=' + (this.$wrapper.find('.js-admin input').is(':checked') ? 2 : 1),
                method: 'GET'
            }).done(function() {

                _toastr('User admin status changed','top-full-width','success',false);
            });
        },

        handleOrderStatusChange: function () {
            $.ajax({
                url: this.$wrapper.find('.js-status input').data('url') + '&status=' + (this.$wrapper.find('.js-status input').is(':checked') ? 2 : 1),
                method: 'GET'
            }).done(function() {

                _toastr('Order status changed','top-full-width','success',false);
            });
        },

        UpdateInvoiceDetails: function (e) {
            var $myForm = $('#invoice-form');

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each( function( index, node ) {

                    $('#' + node.id + '').addClass('field-error');

                    _toastr('Please check the field "' + $("label[for='"+node.id+"']").text() + '" highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
                });
            } else {

                $.ajax({
                    url: this.$wrapper.find('#js-invoice-details').data('url') + '?email=' + $('input[name="email"]').val() + '&invoiceAddress={"firstName":"' + $("input[name=firstName-i]").val() + '","lastName":"' + $("input[name=lastName-i]").val() + '","company":"' + $("input[name=company-i]").val() + '","address1":"' + $("input[name=address1-i]").val() + '","address2":"' + $("input[name=address2-i]").val() + '","city":"' + $("input[name=city-i]").val() + '","postcode":"' + $("input[name=postcode-i]").val() + '","phone":"' + $("input[name=phone-i]").val() + '"}',
                    method: 'GET'
                }).done(function () {

                    _toastr('Invoice address updated', 'top-full-width', 'success', false);
                });
            }
        },

        UpdateDeliveryDetails: function (e) {
            var $myForm = $(e.currentTarget).closest('form');
            var $arrayPosition = $(e.currentTarget).data('id');
            var $urlPath = $(e.currentTarget).data('url');
            var $arrayPositionUnique = $(e.currentTarget).data('id') + 1;

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each( function( index, node ) {

                    $('#' + node.id + '').addClass('field-error');

                    _toastr('Please check the field "' + $("label[for='"+node.id+"']").text() + '" highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
                });
            } else {

                $.ajax({
                    url: $urlPath + '?arrayPosition=' + $arrayPosition + '&deliveryAddress={"firstName":"' + $('input[name=firstName-d-' + $arrayPositionUnique + ']').val() + '","lastName":"' + $('input[name=lastName-d-' + $arrayPositionUnique + ']').val() + '","company":"' + $('input[name=company-d-' + $arrayPositionUnique + ']').val() + '","address1":"' + $('input[name=address1-d-' + $arrayPositionUnique + ']').val() + '","address2":"' + $('input[name=address2-d-' + $arrayPositionUnique + ']').val() + '","city":"' + $('input[name=city-d-' + $arrayPositionUnique + ']').val() + '","postcode":"' + $('input[name=postcode-d-' + $arrayPositionUnique + ']').val() + '","phone":"' + $('input[name=phone-d-' + $arrayPositionUnique + ']').val() + '"}',
                    method: 'GET'
                }).done(function () {

                    _toastr('Delivery address updated', 'top-full-width', 'success', false);
                });
            }
        }
    });
})(window, jQuery);