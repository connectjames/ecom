'use strict';

(function(window, $) {
    window.Delivery = function($wrapper) {
        this.$wrapper = $wrapper;
        this.helper = new Helper($wrapper);

        this.helper.expandFirstTabDelivery();

        this.$wrapper.find('.js-save-delivery').on(
            'click',
            this.deliverySave.bind(this)
        );

        this.$wrapper.find('.js-save-new-dropshipper').on(
            'click',
            this.dropshipperNewSave.bind(this)
        );

        this.$wrapper.find('.js-dropshipper-save').on(
            'click',
            this.dropshipperSave.bind(this)
        );

        this.$wrapper.find('.js-dropshipper-delete').on(
            'click',
            this.dropshipperDelete.bind(this)
        );

        this.$wrapper.find('.js-save-new-surcharge').on(
            'click',
            this.surchargeNewSave.bind(this)
        );

        this.$wrapper.find('.js-surcharge-save').on(
            'click',
            this.surchargeSave.bind(this)
        );

        this.$wrapper.find('.js-surcharge-delete').on(
            'click',
            this.surchargeDelete.bind(this)
        );
    };

    $.extend(window.Delivery.prototype, {

        deliverySave: function(e) {
            e.preventDefault();

            var $myForm = $(e.currentTarget).closest('form');

            var $id = $myForm.data('id');

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each(function (index, node) {

                    $(node).addClass('field-error');

                    _toastr('Please check the field highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
                });
            } else {

                var prices = document.querySelectorAll('#' + $myForm.attr('name') + ' tr.js-weight-price'), allPrices= '';

                Array.prototype.forEach.call(prices, function (el) {

                    var weight = $(el).find('input[name="weight"]').val();
                    var price = $(el).find('input[name="price"]').val();

                    if (weight && price) {
                        allPrices += '"' + weight + '":' + price + ',';
                    }
                });

                allPrices = allPrices.substring(0,allPrices.length-1);

                $.ajax({
                    url: $myForm.attr('action') + '?delivery=' + '{' + allPrices + '}',
                    method: 'GET'
                }).done(function(data) {
                    var place = 'div.table-' + $id;

                    var result = $(data);

                    $(place).html(result);

                    _toastr('Delivery saved','top-full-width','success',false);
                });
            }
        },

        dropshipperSave: function(e) {
            e.preventDefault();

            var $tr = $(e.currentTarget).closest('tr');

            var $myForm = $tr.find('input[name="email"]').closest('form');

            var newValues = [];

            Array.prototype.forEach.call($('input[name="email"]'), function (el) {
                if ($tr.find('input[name="email"]').val() == el.value) {
                    newValues.push(el.value);
                }
            });

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each(function (index, node) {

                    $tr.find('input[name="email"]').addClass('field-error');

                    _toastr('Please check the field "Email" highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
                });
            } else if (!$tr.find('input[name="name"]').val()) {
                $tr.find('input[name="name"]').addClass('field-error');

                _toastr('Please check the field "Name" highlighted. Error: This field is required', 'top-full-width', 'warning', false);
            } else if (newValues.length > 1) {
                $tr.find('input[name="email"]').addClass('field-error');

                _toastr('Please check the field "Email" highlighted. Error: This field needs to be unique', 'top-full-width', 'warning', false);
            }

            else {

                $.ajaxSetup({
                    data: {name: $tr.find('input[name="name"]').val(), email: $tr.find('input[name="email"]').val()}
                });

                $.ajax({
                    url: $(e.currentTarget).data('url'),
                    method: 'GET'
                }).done(function () {

                    _toastr('Dropshipper details saved', 'top-full-width', 'success', false);
                });
            }
        },

        dropshipperNewSave: function(e) {
            e.preventDefault();

            var $tr = $('.js-dropshipper-form-email-new');

            var $myForm = $('form[name="dropshipper-form-email-new"]');

            var newValues = [];

            Array.prototype.forEach.call($('input[name="email"]'), function (el) {
                if ($tr.find('input[name="email"]').val() == el.value) {
                    newValues.push(el.value);
                }
            });

            if (!$myForm[0].checkValidity()) {

                $myForm.find(':invalid').each(function (index, node) {

                    $tr.find('input[name="email"]').addClass('field-error');

                    _toastr('Please check the field "Email" highlighted. Error: ' + node.validationMessage + '', 'top-full-width', 'warning', false);
                });
            } else if (!$tr.find('input[name="name"]').val()) {
                $tr.find('input[name="name"]').addClass('field-error');

                _toastr('Please check the field "Name" highlighted. Error: This field is required', 'top-full-width', 'warning', false);
            } else if (newValues.length > 1) {
                $tr.find('input[name="email"]').addClass('field-error');

                _toastr('Please check the field "Email" highlighted. Error: This field needs to be unique', 'top-full-width', 'warning', false);
            }
            else {
                var params = {
                    name: $tr.find('input[name="name"]').val(),
                    email: $tr.find('input[name="email"]').val()
                };

                var parameters = $.param(params);

                window.location.href = $(e.currentTarget).data('url') + '?' + parameters;
            }
        },

        dropshipperDelete: function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget).closest('tr');

            $.confirm({
                theme: 'supervan', // 'material', 'bootstrap'
                title: 'Confirm!',
                content: 'You are about to delete all the products associated with this dropshipper at the same than the dropshipper itself!',
                buttons: {
                    delete: {
                        text: 'Delete this dropshipper',
                        btnClass: 'btn-primary',
                        keys: ['enter'],
                        action: function(){
                            $(e.currentTarget).children('i').removeClass('fa-trash')
                                    .addClass('fa-spinner')
                                    .addClass('fa-spin');

                            $.ajax({
                                url: $(e.currentTarget).data('url'),
                                method: 'GET',
                                dataType: 'html'
                            }).done(function () {
                                $el.fadeOut();
                            })
                        }
                    },
                    cancel: function () {}
                }
            });
        },

        surchargeSave: function(e) {
            e.preventDefault();

            var $tr = $(e.currentTarget).closest('tr');

            if (!$tr.find('input[name="postcode"]').val()) {
                $tr.find('input[name="postcode"]').addClass('field-error');

                _toastr('Please check the field "Postcode" highlighted. Error: This field is required', 'top-full-width', 'warning', false);
            } else if (!$tr.find('input[name="amount"]').val()) {
                $tr.find('input[name="amount"]').addClass('field-error');

                _toastr('Please check the field "Amount" highlighted. Error: This field is required', 'top-full-width', 'warning', false);
            }
            else {

                $.ajaxSetup({
                    data: {postcode: $tr.find('input[name="postcode"]').val(), amount: $tr.find('input[name="amount"]').val()}
                });

                $.ajax({
                    url: $(e.currentTarget).data('url'),
                    method: 'GET'
                }).done(function () {

                    _toastr('Surcharge saved', 'top-full-width', 'success', false);
                });
            }
        },

        surchargeNewSave: function(e) {
            e.preventDefault();

            var $tr = $('.js-surcharge-new');

            if (!$tr.find('input[name="postcode"]').val()) {
                $tr.find('input[name="postcode"]').addClass('field-error');

                _toastr('Please check the field "Postcode" highlighted. Error: This field is required', 'top-full-width', 'warning', false);
            } else if (!$tr.find('input[name="amount"]').val()) {
                $tr.find('input[name="amount"]').addClass('field-error');

                _toastr('Please check the field "Amount" highlighted. Error: This field is required', 'top-full-width', 'warning', false);
            }
            else {
                var params = {
                    postcode: $tr.find('input[name="postcode"]').val(),
                    amount: $tr.find('input[name="amount"]').val()
                };

                var parameters = $.param(params);

                window.location.href = $(e.currentTarget).data('url') + '?' + parameters;
            }
        },

        surchargeDelete: function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget).closest('tr');

            $(e.currentTarget).children('i').removeClass('fa-trash')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET',
                dataType: 'html'
            }).done(function () {
                $el.fadeOut();
            })
        }
    });

    var Helper = function ($wrapper) {
        this.$wrapper = $wrapper;
    };

    $.extend(Helper.prototype, {

        expandFirstTabDelivery: function() {
            $('.js-delivery-tab').find('li a').first().click();
        }
    });
})(window, jQuery);