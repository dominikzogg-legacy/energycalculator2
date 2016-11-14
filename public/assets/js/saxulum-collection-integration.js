(function($){
    var addAjaxSelect = function($selector){
        $selector.select2({
            multiple: $selector.attr('multiple'),
            ajax: {
                url: $selector.attr('data-route'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            templateSelection: function formatRepoSelection (result) {
                if(result.amount) {
                    var $formGroup = $selector.closest('div[class="form-groups"]');
                    if($formGroup.length == 1) {
                        $formGroup.find('input[id*="amount"]').val(result.amount);
                    }
                }

                return result.text;
            }
        });
    };
    var addSelect = function($selector) {
        $selector.select2({
            multiple: $selector.attr('multiple')
        });
    };
    $(document).ready(function(){
        $('form').saxulumCollection('init', {});
        $('select:not([data-ajax])').each(function(i, element){
            addSelect($(element));
        });
        $('select[data-ajax]').each(function(i, element){
            addAjaxSelect($(element));
        });
    });
    $(document).on('saxulum-collection.add', function(e, $element){
        $('select:not([data-ajax])', $element).each(function(i, element){
            addSelect($(element));
        });
        $('select[data-ajax]', $element).each(function(i, element){
            addAjaxSelect($(element));
        });
    });
})(jQuery);
