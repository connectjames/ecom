'use strict';

(function(window, $) {
    window.NewViewCatProd = function($wrapper) {
        this.$wrapper = $wrapper;
        this.helper = new Helper($wrapper);

        this.$wrapper.find('form input[name$="name"]').on(
            'keyup',
            this.handleCharLimitNameDescription.bind(this)
        );

        this.$wrapper.find('form input[name="category_form[sub]"]').on(
            'keyup',
            this.handleCharLimitSubDescription.bind(this)
        );

        this.$wrapper.find('form input[name$="metaTitle"]').on(
            'keyup',
            this.handleCharLimitTitleDescription.bind(this)
        );

        this.$wrapper.find('form input[name$="metaDescription"]').on(
            'keyup',
            this.handleCharLimitMetaDescription.bind(this)
        );

        this.$wrapper.find('form .js-spec-num-char').on(
            'keyup',
            this.handleCharLimitSpecification.bind(this)
        );

        this.$wrapper.find('.js-remove-product').on(
            'click',
            this.handleRemoveProduct.bind(this)
        );

        this.$wrapper.find('.js-add-product').on(
            'click',
            this.handleAddProduct.bind(this)
        );

        this.$wrapper.find('.js-remove-category').on(
            'click',
            this.handleRemoveCategory.bind(this)
        );

        this.$wrapper.find('.js-add-category').on(
            'click',
            this.handleAddPCategory.bind(this)
        );
    };

    $.extend(window.NewViewCatProd.prototype, {

        handleCharLimitNameDescription: function(e) {
            var nameField = e.currentTarget;

            var len = nameField.value.length;

            if (len >= 100) {

                nameField.value = nameField.value.substring(0, 100);
            } else {

                $('#charNumName').text('(' + (100 - len) + ' characters left)');
            }
        },

        handleCharLimitSubDescription: function(e) {
            var subField = e.currentTarget;

            var len = subField.value.length;

            if (len >= 100) {

                subField.value = subField.value.substring(0, 100);
            } else {

                $('#charNumSub').text('(' + (100 - len) + ' characters left)');
            }
        },

        handleCharLimitTitleDescription: function(e) {
            var metaTitleField = e.currentTarget;

            var len = metaTitleField.value.length;

            if (len >= 55) {

                metaTitleField.value = metaTitleField.value.substring(0, 55);
            } else {

                $('#charNumTitle').text('(' + (55 - len) + ' characters left)');
            }
        },

        handleCharLimitMetaDescription: function(e) {
            var metaDescriptionField = e.currentTarget;

            var len = metaDescriptionField.value.length;

            if (len >= 160) {

                metaDescriptionField.value = metaDescriptionField.value.substring(0, 160);
            } else {

                $('#charNumDesc').text('(' + (160 - len) + ' characters left)');
            }
        },

        handleCharLimitSpecification: function(e) {
            var SpecField = e.currentTarget;

            var len = SpecField.value.length;

            if (len >= 255) {

                SpecField.value = SpecField.value.substring(0, 255);
            } else {

                $(e.currentTarget).closest('div').find('.charNumSpec').text('(' + (255 - len) + ' characters left)');
            }
        },

        handleRemoveProduct: function(e) {
            e.preventDefault();

            $(e.currentTarget).closest('.js-product-item')
                .fadeOut()
                .remove();
        },

        handleAddProduct: function(e) {
            e.preventDefault();

            // Get the data-prototype explained earlier
            var prototype = $(e.currentTarget).closest('tbody').data('prototype');
            // get the new index
            var index = $(e.currentTarget).closest('tbody').data('index');
            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, index);
            // increase the index with one for the next item
            $(e.currentTarget).closest('tbody').data('index', index + 1);
            // Display the form in the page before the "new" link
            $(e.currentTarget).before(newForm);
        },

        handleRemoveCategory: function(e) {
            e.preventDefault();

            $(e.currentTarget).closest('.js-category-item')
                .fadeOut()
                .remove();
        },

        handleAddPCategory: function(e) {
            e.preventDefault();

            // Get the data-prototype explained earlier
            var prototype = $(e.currentTarget).closest('tbody').data('prototype');
            // get the new index
            var index = $(e.currentTarget).closest('tbody').data('index');
            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, index);
            // increase the index with one for the next item
            $(e.currentTarget).closest('tbody').data('index', index + 1);
            // Display the form in the page before the "new" link
            $(e.currentTarget).before(newForm);
        }

    });

    window.Helper = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.pageUrlLink.bind(this)
    };

    $.extend(Helper.prototype, {

        pageUrlLink: function() {
            return this.$wrapper.find('form').data('url');
        }
    });
})(window, jQuery);
