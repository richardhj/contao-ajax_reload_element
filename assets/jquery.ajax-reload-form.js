/* AjaxReloadElement for Contao Open Source CMS, (c) 2016-2017 Richard Henkenjohann */
(function ($) {
    $.fn.ajaxReloadForm = function (options) {
        options = $.extend({
            selector: 'div[data-ajax-reload-form-submit] form',
            reloadCssClass: 'ajax-reload-element-overlay'
        }, options);

        $(document).on('submit', options.selector, function (event) {
            var form, element, buildUrl;

            form = $(this);
            if ('post' !== form.attr('method').toLowerCase()) {
                return;
            }

            event.preventDefault();

            element = $(this).closest('[class^="ce_"],[class^="mod_"]');
            element.addClass(options.reloadCssClass);

            buildUrl = function (base, params) {
                var sep = (base.indexOf('?') > -1) ? '&' : '?';
                return base + sep + params;
            };

            $.ajax({
                method: 'POST',
                url: buildUrl(location.href, jQuery.param({
                    ajax_reload_element: element.attr('data-ajax-reload-element')
                })),
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
