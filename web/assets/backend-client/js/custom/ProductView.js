'use strict';

(function(window, $) {
    window.ProductView = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'change',
            '#js-display input',
            this.handleDisplayChange.bind(this)
        );

        this.$wrapper.on(
            'change',
            '#js-featured input',
            this.handleFeaturedChange.bind(this)
        );
    };

    $.extend(window.ProductView.prototype, {

        handleDisplayChange: function (e) {

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET'
            }).success(function() {

                _toastr('Product display status changed','top-full-width','success',false);
            });
        },

        handleFeaturedChange: function (e) {

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET'
            }).success(function() {

                _toastr('Product featured status changed','top-full-width','success',false);
            });
        }
    });
})(window, jQuery);