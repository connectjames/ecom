'use strict';

(function(window, $) {
    window.Product = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('.js-table-checkbox input').on(
            'click',
            this.handleToggleCheckboxes.bind(this)
        );

        this.$wrapper.find('#js-search-client form').on(
            'submit',
            this.handleSearchValue.bind(this)
        );

        this.$wrapper.find('.js-price i').on(
            'click',
            this.handlePriceChange.bind(this)
        );

        this.$wrapper.on(
            'change',
            '.js-display input',
            this.handleDisplayChange.bind(this)
        );

        this.$wrapper.on(
            'change',
            '.js-featured input',
            this.handleFeaturedChange.bind(this)
        );

        this.$wrapper.find('#js-displays select[name="display"]').on(
            'change',
            this.handleBulkDisplayChange.bind(this)
        );
    };

    $.extend(window.Product.prototype, {

        handleToggleCheckboxes: function(e) {
            var checkboxes = document.getElementsByName('products');

            for(var i=0, n=checkboxes.length;i<n;i++) {

                checkboxes[i].checked = e.currentTarget.checked;
            }
        },

        handleSearchValue: function(e) {
            e.preventDefault();

            window.location.href = this.$wrapper.data('url') + '?search=prod.name&searchValue=%' + this.$wrapper.find('#js-search-client form').children('input').val() + '%';;
        },

        handlePriceChange: function (e) {
            e.preventDefault();

            $.ajax({
                url: $(e.currentTarget).data('url') + '&price=' + $(e.currentTarget).closest('div').find('input').val(),
                method: 'GET'
            }).success(function() {

                _toastr('Product price changed','top-full-width','success',false);
            });
        },

        handleDisplayChange: function (e) {
            var $display = $('.js-display').data('id');

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET'
            }).success(function(data) {

                var place = '#js-product-' + $display;

                var result = $(data).find(place).html();

                $(place).html(result);

                _toastr('Product display status changed','top-full-width','success',false);
            });
        },

        handleFeaturedChange: function (e) {
            var $featured = $('.js-featured').data('id');

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET'
            }).success(function(data) {

                var place = '#js-product-' + $featured;

                var result = $(data).find(place).html();

                $(place).html(result);

                _toastr('Product featured status changed','top-full-width','success',false);
            });
        },

        handleBulkDisplayChange: function (e) {
            var checkboxes = document.querySelectorAll('input[name="products"]:checked'), values = [];

            Array.prototype.forEach.call(checkboxes, function (el) {

                values.push(el.value);

                if (e.currentTarget.value == 1) {

                    $('.js-display[data-id="' + el.value + '"] input').prop('checked', false);
                } else {

                    $('.js-display[data-id="' + el.value + '"] input').prop('checked', true);
                }
            });

            if (values.length) {

                $.ajaxSetup({
                    data: { productsId: values.toString(), display: e.currentTarget.value }
                });

                $.ajax({
                    url: this.$wrapper.find('#js-displays').data('url'),
                    method: 'GET'
                }).success(function() {

                    _toastr('Products display status changed','top-full-width','success',false);
                });

                $(this).val(0)

            } else {

                _toastr('Please check at least one checkbox','top-full-width','warning',false);

                $(this).val(0)
            }
        }
    });
})(window, jQuery);