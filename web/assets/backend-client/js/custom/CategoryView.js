'use strict';

(function(window, $) {
    window.CategoryView = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'change',
            '#js-display input',
            this.handleDisplayChange.bind(this)
        )
    };

    $.extend(window.CategoryView.prototype, {

        handleDisplayChange: function (e) {

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET'
            }).success(function() {

                _toastr('Category display status changed','top-full-width','success',false);
            });
        }
    });
})(window, jQuery);