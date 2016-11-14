(function($){

    var settings = {};

    var methods = {

        init : function(options)
        {
            var key = this.selector;

            settings[key] = $.extend( {
                addSelector: '[data-addfield="collection"]',
                removeSelector: '[data-removefield="collection"]',
                wrapperSelector: 'ul.bc-collection',
                elementWrapperTag: 'li'
            }, options);

            return this.each(function(index, element) {

                var $element = $(element);

                $($element, document).on('click', settings[key]['addSelector'], function(event) {

                    event.preventDefault();

                    var $link = $(event.target);

                    if(!$link.is(settings[key]['addSelector'])) {
                        $link.parents(settings[key]['addSelector']).eq(0);
                    }

                    var $collection = $('#'+ $link.attr('data-collection'));
                    var $wrapper = $collection.find(settings[key]['wrapperSelector']);
                    var count = $wrapper.children(settings[key]['elementWrapperTag']).size();

                    var newWidget = $link.attr('data-prototype') ? $link.attr('data-prototype') : $collection.attr('data-prototype');

                    // Check if an element with this ID already exists.
                    // If it does, increase the count by one and try again
                    var newName = newWidget.match(/id="(.*?)"/);
                    while ($('#' + newName[1].replace(/__name__/g, count)).size() > 0) {
                        count++;
                    }
                    newWidget = newWidget.replace(/__name__/g, count);
                    newWidget = newWidget.replace(/__id__/g, newName[1].replace(/__name__/g, count));

                    var elementWrapper = document.createElement(settings[key]['elementWrapperTag']);
                    var $elementWrapper = $(elementWrapper);

                    $elementWrapper.html(newWidget);

                    $.when(
                        $elementWrapper.appendTo($wrapper)
                    ).done(function() {
                        $(document).trigger('saxulum-collection.add', [$(settings[key]['elementWrapperTag'], $wrapper).last()]);
                    });
                });

                $($element, document).on('click', settings[key]['removeSelector'], function(event) {

                    event.preventDefault();

                    var $link = $(event.target);

                    if(!$link.is(settings[key]['removeSelector'])) {
                        $link.parents(settings[key]['removeSelector']).eq(0);
                    }

                    var $elementWrapper =  $link.parents(settings[key]['elementWrapperTag']).eq(0);

                    $.when(
                        $(document).trigger('saxulum-collection.remove', [$elementWrapper])
                    ).done(function() {
                        $elementWrapper.remove();
                    });
                });
            });
        }
    };

    $.fn.saxulumCollection = function(method) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jquery.saxulum-collection.js' );
        }
    };

})(jQuery);
