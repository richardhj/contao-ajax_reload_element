[![Latest Version on Packagist](http://img.shields.io/packagist/v/richardhj/contao-ajax_reload_element.svg)](https://packagist.org/packages/richardhj/contao-ajax_reload_element)
[![Dependency Status](https://www.versioneye.com/php/richardhj:contao-ajax_reload_element/badge.svg)](https://www.versioneye.com/php/richardhj:contao-ajax_reload_element)

# AjaxReloadElement for Contao Open Source CMS

With AjaxReloadElement you have the possibility to fetch a particular front end module or content element via ajax. All you have to do is to tick the box „allow ajax reload“ for the module/element and include a JavaScript.
If you are using jQuery you can use something like this. (Create a `j_….html5` template and include it in the layout.)
```html
<script>
    $(".mod_my_module a.reloadThisElementOnClick").click(function (event) {
        var element;
        
        // Don't follow the link
        event.preventDefault();
        
        // This is the elements div container like ".mod_my_module"
        element = $(this).closest('[class^="ce_"],[class^="mod_"]');
        // Add a css class to this element. An overlay and spinning icon can be set via css
        element.addClass('ajax-reload-element-overlay');
        
        $.ajax({
            method: 'POST',
            url: location.href,
            data: {
                // The data- attribute is set automatically
                ajax_reload_element: element.attr('data-ajax-reload-element')
            }
        })
            .done(function (response, status, xhr) {
                if ('ok' === response.status) {
                    // Replace the DOM
                    element.replaceWith(response.html);
                }
                else {
                    // Reload the page as fallback
                    location.reload();
                }
            });
    });

</script>
```

## Out of the box: Ajax forms
For all modules that integrate forms you can tick the box "Update a form via ajax". Additionally the template "j_ajaxforms" has to be included in the page layout. Instead of reloading the entire page, forms will update itself.
This feature is supported for all Contao core forms like change password, personal data, login form etc and forms from third partys that are programmed in Contao style.

### Demonstration
A redirect processed in the login form will be followed too.
![Demonstration with Contao's core login form](https://cloud.githubusercontent.com/assets/1284725/15799602/20d59fc8-2a62-11e6-8c22-2d1d971aeb20.gif)
