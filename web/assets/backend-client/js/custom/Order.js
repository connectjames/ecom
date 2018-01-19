'use strict';

(function(window, $) {
    window.Order = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-table-checkbox input',
            this.handleToggleCheckboxes.bind(this)
        );

        this.$wrapper.find('#js-search-client form').on(
            'submit',
            this.handleSearchValue.bind(this)
        );

        this.$wrapper.find('#print-invoice').on(
            'click',
            this.printInvoice.bind(this)
        );

        this.$wrapper.find('#print-dispatch-note').on(
            'click',
            this.printDispatchNote.bind(this)
        );

        this.$wrapper.find('#print-all').on(
            'click',
            this.printInvoiceAndDispatchNote.bind(this)
        );

        this.$wrapper.find('.js-status input').on(
            'change',
            this.handleOrderStatusChange.bind(this)
        );

    };

    $.extend(window.Order.prototype, {

        handleToggleCheckboxes: function(e) {
            var checkboxes = document.getElementsByName('orders');

            for(var i=0, n=checkboxes.length;i<n;i++) {

                checkboxes[i].checked = e.currentTarget.checked;
            }
        },

        handleSearchValue: function(e) {
            e.preventDefault();

            window.location.href = this.$wrapper.data('url') +  '?' + 'search=ord.lastName&searchValue=%' + this.$wrapper.find('#js-search-client form').children('input').val() + '%';
        },

        printInvoice: function (e) {
            var checkboxes = document.querySelectorAll('input[name="orders"]:checked'), values = [];

            Array.prototype.forEach.call(checkboxes, function (el) {

                values.push(el.value);
            });

            if (values.length) {

                window.location.href = this.$wrapper.find('#print-invoice').data('url') + '?ordersId=' + values.toString();
            } else {

                _toastr('Please check at least one checkbox','top-full-width','warning',false);
            }
        },

        printDispatchNote: function (e) {
            var checkboxes = document.querySelectorAll('input[name="orders"]:checked'), values = [];

            Array.prototype.forEach.call(checkboxes, function (el) {

                values.push(el.value);
            });

            if (values.length) {

                window.location.href = this.$wrapper.find('#print-dispatch-note').data('url') + '?ordersId=' + values.toString();
            } else {

                _toastr('Please check at least one checkbox','top-full-width','warning',false);
            }
        },

        printInvoiceAndDispatchNote: function (e) {
            var checkboxes = document.querySelectorAll('input[name="orders"]:checked'), values = [];

            Array.prototype.forEach.call(checkboxes, function (el) {

                values.push(el.value);
            });

            if (values.length) {

                window.location.href = this.$wrapper.find('#print-all').data('url') + '?ordersId=' + values.toString();
            } else {

                _toastr('Please check at least one checkbox','top-full-width','warning',false);
            }
        },

        handleOrderStatusChange: function (e) {
            $.ajax({
                url: $(e.currentTarget).data('url') + '&status=' + ($(e.currentTarget).is(':checked') ? 2 : 1),
                method: 'GET'
            }).done(function() {

                _toastr('Order status changed','top-full-width','success',false);
            });
        }
    });
})(window, jQuery);