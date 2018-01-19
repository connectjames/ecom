'use strict';

(function(window, $) {
    window.Client = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('#js-search form').on(
            'submit',
            this.handleSearchValue.bind(this)
        );

        this.$wrapper.find('.js-admin input').on(
            'change',
            this.handleAdminStatusChange.bind(this)
        );

        this.$wrapper.find('.js-password').on(
            'click',
            this.handlePasswordReset.bind(this)
        );

    };

    $.extend(window.Client.prototype, {

        handleSearchValue: function(e) {
            e.preventDefault();

            window.location.href = this.$wrapper.data('url') + '?search=user.lastName&searchValue=%' + this.$wrapper.find('#js-search form').children('input').val() + '%';
        },

        handleAdminStatusChange: function (e) {
            $.ajax({
                url: $(e.currentTarget).data('url') + '&admin=' + ($(e.currentTarget).is(':checked') ? 2 : 1),
                method: 'GET'
            }).done(function() {

                _toastr('User admin status changed','top-full-width','success',false);
            });
        },

        handlePasswordReset: function () {
            $.ajax({
                url: $(e.currentTarget).attr('href'),
                method: 'GET'
            }).done(function() {

                _toastr('An email has been sent through to the client email address','top-full-width','success',false);
            });
        }
    });
})(window, jQuery);