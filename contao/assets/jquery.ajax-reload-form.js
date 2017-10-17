/* AjaxReloadElement for Contao Open Source CMS, (c) 2016-2017 Richard Henkenjohann */
(function ($) {
    $.fn.ajaxReloadForm = function (options) {
        options = $.extend({
            selector: 'div[data-ajax-reload-form-submit] form',
            page: 0,
            reloadCssClass: 'ajax-reload-element-overlay'
        }, options);

        $(document).on('submit', options.selector, function (event) {
            var form = $(this);
            if (form.attr('method').toLowerCase() !== 'post') {
                return;
            }

            event.preventDefault();

            var element = $(this).closest('[class^="ce_"],[class^="mod_"]');
            element.addClass(options.reloadCssClass);

            $.ajax({
                method: 'POST',
                url: 'SimpleAjaxFrontend.php?' + jQuery.param({
                    action: 'reload-element',
                    element: element.attr('data-ajax-reload-element'),
                    auto_item: (typeof element.attr('data-ajax-reload-auto-item') !== typeof undefined)
                        ? element.attr('data-ajax-reload-auto-item')
                        : '',
                    page: options.page
                }),
                data: form.serialize()
            })
                .done(function (response, status, xhr) {
                    if ('nocontent' !== status) {
                        if ('ok' === response.status) {
                            element.replaceWith(response.html);
                        }
                        else {
                            location.reload();
                        }
                    } else {
                        // The element processes a reload
                        if (xhr.getResponseHeader('X-Ajax-Location').indexOf(this.url) >= 0) {
                            // Trigger new ajax request
                            form.find('[name=FORM_SUBMIT]').val('');
                            form.submit();
                        }
                        // The element processes a redirect
                        else {
                            window.location.replace(xhr.getResponseHeader('X-Ajax-Location'));
                        }
                    }
                });
        });
    };
}(jQuery));
