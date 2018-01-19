'use strict';

(function(window, $) {
    window.CategoryB = function($wrapper) {
        this.$wrapper = $wrapper;
        this.helper = new Helper($wrapper);

        this.helper.nestable();

        this.$wrapper.on(
            'change',
            '.js-display input',
            this.handleDisplayChange.bind(this)
        );

        this.$wrapper.find('#save-categories').on(
            'click',
            this.saveCategoriesOrdering.bind(this)
        );
    };

    $.extend(window.CategoryB.prototype, {

        handleDisplayChange: function (e) {
            var $display = $(e.currentTarget).closest('.js-display').data('id');

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET'
            }).success(function(data) {

                var place = '#js-category-' + $display;

                var result = $(data).find(place).html();

                $(place).html(result);

                _toastr('Category display status changed','top-full-width','success',false);
            });
        },

        saveCategoriesOrdering: function (e) {
            var $nestable = jQuery('#nestable');

            var updateOutput = function (e) {
                var list = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };

            // Nestable list 1
            $nestable.find('#nestable-categories').nestable({
                group: 1,
                maxDepth: 3
            }).on('change', updateOutput);

            // output initial serialised data
            updateOutput($nestable.find('#nestable-categories').data('output', $nestable.find('#nestable_output')));

            var urlMenu = $(e.currentTarget).data('url') + '?categoriesMenu=' + $('#nestable_output').val();

            $.ajax({
                url: urlMenu,
                method: 'GET',
                dataType: 'html'
            }).done(function() {
                _toastr('Menu updated','top-full-width','success',false);
                nestable();
            });
        }
    });

    var Helper = function ($wrapper) {
        this.$wrapper = $wrapper;
    };

    $.extend(Helper.prototype, {

        nestable: function() {
            var $nestable = jQuery('#nestable');

            if(jQuery().nestable) {

                var updateOutput = function (e) {
                    var list = e.length ? e : $(e.target),
                        output = list.data('output');
                    if (window.JSON) {
                        output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
                    } else {
                        output.val('JSON browser support required for this demo.');
                    }
                };

                // Nestable list 1
                $nestable.find('#nestable-categories').nestable({
                    group: 1,
                    maxDepth: 3
                }).on('change', updateOutput);

                // output initial serialised data
                updateOutput($nestable.find('#nestable-categories').data('output', $nestable.find('#nestable_output')));

                // Expand All
                jQuery('button[data-action=expand-all]').bind('click', function() {
                    jQuery('.dd').nestable('expandAll');
                });

                // Collapse All
                jQuery('button[data-action=collapse-all]').bind('click', function() {
                    jQuery('.dd').nestable('collapseAll');
                });

            }
        }
    });
})(window, jQuery);