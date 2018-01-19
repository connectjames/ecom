'use strict';

(function(window, $) {
    window.BasketAdd = function($wrapper) {
        this.$wrapper = $wrapper;
        this.helper = new Helper($wrapper);
        this.$wrapper.find('.js-add-cart').on(
            'click',
            this.handleAddToBasket.bind(this)
        );

    };

    $.extend(window.BasketAdd.prototype, {

        handleAddToBasket: function(e) {
            e.preventDefault();

            var $topBasket = $('.quick-cart');
            var $productName = $(e.currentTarget).data('name');
            var $basketLink = $(e.currentTarget).data('basket');

            if ($(e.currentTarget).closest('div').find('input').val()) {
                $.ajax({
                    url: $(e.currentTarget).data('url') + '?quantity=' + $(e.currentTarget).closest('div').find('input').val(),
                    method: 'GET'
                }).done(function(data) {

                    $topBasket.html($(data));

                    window.Helper.prototype.confirmBox($productName, $basketLink);
                    window.Helper.prototype.basketClick();

                });
            } else {
                $.ajax({
                    url: $(e.currentTarget).data('url') + '?quantity=1',
                    method: 'GET'
                }).done(function(data) {

                    $topBasket.html($(data));

                    window.Helper.prototype.confirmBox($productName, $basketLink);
                    window.Helper.prototype.basketClick();

                });
            }
        }
    });

    window.Helper = function ($wrapper) {
        this.$wrapper = $wrapper;
    };

    $.extend(Helper.prototype, {

        basketClick: function() {
            var $basket = $('li.quick-cart>a');

            var $basketBox = $('li.quick-cart div.quick-cart-box');

            // Quick basket
            $basket.click(function (e) {
                e.preventDefault();

                if($basketBox.is(":visible")) {
                    $basketBox.fadeOut(300);
                } else {
                    $basketBox.fadeIn(300);

                    // close search if visible
                    if($('li.search .search-box').is(":visible")) {
                        $('.search-box').fadeOut(300);
                    }
                }
            });
            // close quick basket on body click
            if($basket.size() != 0) {
                $('li.quick-cart').on('click', function(e){
                    e.stopPropagation();
                });

                $('body').on('click', function() {
                    if ($basketBox.is(":visible")) {
                        $basketBox.fadeOut(300);
                    }
                });
            }
        },

        confirmBox: function(name, basketLink) {
            $.confirm({
                title: name + ' is now in your basket',
                content: 'Do you wish to continue Shopping or go to your Basket?',
                closeIcon: true,
                theme: 'material',
                animationBounce: 1.5,
                escapeKey: true,
                backgroundDismiss: true,
                columnClass: 'col-md-6 col-md-offset-3',
                icon: 'fa fa-basket',
                buttons: {
                    continueShopping: {
                        text: 'Continue Shopping',
                        btnClass: 'btn-default margin-top-10 pull-left',
                        keys: ['enter', 'shift'],
                        action: function(){
                        }
                    },
                    basket: {
                        text: 'Go to basket',
                        btnClass: 'btn-primary margin-top-10 pull-right',
                        action: function () {
                            window.location.href = basketLink;
                        }
                    }
                }
            });
        }
    });
})(window, jQuery);