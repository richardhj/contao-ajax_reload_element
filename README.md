# AjaxReloadElement for Contao Open Source CMS

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]]()

AjaxReloadElement gives you the possibility to fetch a particular front end module or content element via an ajax
request.

**Scroll down to see some nice animated screenshots 😎**

## Usage

You need to tick the box «Allow ajax reload» for the module/element in the back end input mask and include a JavaScript.

### Basic/custom usage

If you are using jQuery you can use something like this. Modify this code snippet for your purposes. (Create a 
`j_myajaxreload.html5` template and include it in the layout.)

This code snippet will replace the HTML node `.mod_my_module` when clicking on `a.reloadThisElementOnClick`.

```html
<script>
    $(".mod_my_module a.reloadThisElementOnClick").click(function (event) {
        var element;
        
        // Don't follow the link
        event.preventDefault();
        
        // This is the elements div container like ".mod_my_module". "Allow ajax reload" has to be ticket for this element in the backend
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

### Ajax Calendar

Add this to the `cal_default.html5` template. Don't forget to activate ajax reload on the module.

```html
<script>
    $(".calendar .head.previous a, .calendar .head.next a").click(function (event) {
        var element;
        // Get url of next/previous month
        var $url = window.location.origin + '/' + $(this).attr('href');
        // Don't follow the link
        event.preventDefault();
        // This is the element's div container like ".mod_my_module". "Allow ajax reload" has to be enabled for this module in the back end
        element = $(this).closest('[class^="ce_"],[class^="mod_"]');
        // Add a css class to this element. An overlay and spinning icon can be set via css.
        element.addClass('ajax-reload-element-overlay');
        
        $.ajax({
            method: 'POST',
            url: $url,
            data: {
                // The data- attribute is set automatically
                ajax_reload_element: element.attr('data-ajax-reload-element')
            }
        })
        .done(function (response, status, xhr) {
            if ('ok' === response.status) {
                // Replace the DOM
                element.replaceWith(response.html);
            } else {
                // Reload the page as fallback
                location.reload();
            }
        });
    });
</script>
```

### Ajax forms

This one comes out of the box.

For all modules that integrate forms, you can tick the box «Update a form via ajax». Additionally, the template
"j_ajaxforms" has to be included in the page layout. Instead of reloading the entire page, forms will update itself.

This feature is supported for all Contao core forms like *change password,* *personal data,* *login form* etc. and forms
from third-party apps that are programmed in Contao style.

#### Demonstration

When the login was successful, the redirect processed in the login form will be followed.

![Demonstration with Contao's core login form](https://cloud.githubusercontent.com/assets/1284725/15799602/20d59fc8-2a62-11e6-8c22-2d1d971aeb20.gif)

### Modal editing

This one is a bit more advanced.

First of all, this is the list of requirements for this plugin:

1. [jquery-ui.js](https://jqueryui.com/download/) (with at least the `Dialog` widget)
2. [jquery.dialogOptions.js](https://github.com/jasonday/jQuery-UI-Dialog-extended) (can be optional, if you adjust the script)
3. [jQuery.modal-editing.js](https://gist.github.com/richardhj/27345239b7326e98658a8a4dff599736) (the jQuery plugin written for this extension)

Then we create a template called `j_modal_editing.js` and include it in the page layout:

```php
<?php

$GLOBALS['TL_JAVASCRIPT'][] = 'files/js/jquery-ui.min.js';
$GLOBALS['TL_JAVASCRIPT'][] = 'files/js/jquery.dialogOptions.js';
$GLOBALS['TL_JAVASCRIPT'][] = 'files/js/jquery.modal-editing.js';

?>

<script>
    $(function () {
        $(document).modalEditing({
            container: '.mm-list-participants',
            trigger: '.item a',
            element: 'mod::24',
            closeText: 'Schließen', /* If you want to internationalize the label, you can use (with Haste installed): <?= Haste\Util\Format::dcaLabel('default', 'close'); ?>*/
            title: 'Edit element'
        });
        $(document).modalEditing({
            container: '.mm-list-participants',
            trigger: '.addUrl a',
            element: 'mod::24',
            closeText: 'Close',
            title: 'Add element'
        });
    });
</script>
```

This code snippet is tailored to a MetaModel frontend editing. You set the id of the editing form as the `element`
option. In addition, you enable the ajax form as stated above (see paragraph «Ajax forms»).

#### Demonstration

![Demonstration of the modal editing script](https://user-images.githubusercontent.com/1284725/31863229-4013be20-b74b-11e7-890b-d1fa5f105f11.gif)

[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-ajax_reload_element.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/contao-ajax_reload_element
