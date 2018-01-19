'use strict';

(function(window, $) {
    window.Checkout = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.find('.js-disabled').on(
            'click',
            this.handleDisabled.bind(this)
        );

        this.$wrapper.find('.js-submit').on(
            'click',
            this.handleSagePaySubmit.bind(this)
        );
    };

    $.extend(window.Checkout.prototype, {


        handleDisabled: function(e) {
            e.preventDefault();
        },

        handleSagePaySubmit: function(e) {
            e.preventDefault();

            var $faFa = $(e.currentTarget).children('span');

            $faFa.removeClass('fa-mail-forward')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            $('.cc-loading').append('Please do not refresh your window!');

            var $urlMerchant = $(e.currentTarget).data('url-merchant');
            var $urlPlaceOrder = $(e.currentTarget).data('url-place-order');

            $.ajax({
                url: $urlMerchant,
                method: 'GET'
            }).done(function(data) {

                var myCard = $('.card-js');

                var expiry = "";

                if (myCard.CardJs('expiryMonth').length < 2) {
                    expiry = 0 + myCard.CardJs('expiryMonth') + myCard.CardJs('expiryYear');
                } else {
                    expiry = myCard.CardJs('expiryMonth') + myCard.CardJs('expiryYear');
                }

                sagepayOwnForm({ merchantSessionKey: data })
                    .tokeniseCardDetails({
                        cardDetails: {
                            cardholderName: myCard.CardJs('name'),
                            cardNumber: myCard.CardJs('cardNumber').replace(/ /g,''),
                            expiryDate: expiry,
                            securityCode: myCard.CardJs('cvc')
                        },
                        onTokenised : function(result) {
                            if (result.success) {
                                window.location.href = $urlPlaceOrder + '?cardIdentifier=' + result.cardIdentifier;
                            } else {
                                var i;
                                for (i = 0; i < result.errors.length; ++i) {
                                    _toastr(result.errors[i].message, 'top-full-width', 'warning', false);
                                }
                            }
                        }
                    })
                ;
            });
        }

    });

})(window, jQuery);