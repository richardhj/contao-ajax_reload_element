[![Latest Version on Packagist](http://img.shields.io/packagist/v/richardhj/contao-ajax_reload_element.svg)](https://packagist.org/packages/richardhj/contao-ajax_reload_element)
[![Dependency Status](https://www.versioneye.com/php/richardhj:contao-ajax_reload_element/badge.svg)](https://www.versioneye.com/php/richardhj:contao-ajax_reload_element)

# AjaxRelaodElement for Contao Open Source CMS

With AjaxReloadElement you have the possibility to fetch a particular front end module or content element via ajax. All you have to do is to tick the box „allow ajax reload“ for the module/element and include the JavaScript.
If you are using jQuery you can use something like this.
```html
<script>
	$(".mod_my_module a.reloadThisElementOnClick").click(function (event) {

		event.preventDefault();

		var element = $(this).closest('[class^="ce_"],[class^="mod_"]');
		element.addClass('ajax-reload-element-overlay');

		$.ajax({
			method: 'GET',
			url: 'SimpleAjaxFrontend.php',
			data: {
				action: 'reload-element',
				element: element.attr('data-ajax-reload-element'),
				auto_item: (typeof element.attr('data-ajax-reload-auto-item') != typeof undefined) ? element.attr('data-ajax-reload-auto-item') : '',
				REQUEST_TOKEN: '<?= \RequestToken::get() ?>'
			}
		})
			.done(function (response, status, xhr) {
				
				if (response.status == 'ok') {
					element.replaceWith(response.html);
				}
				else {
					location.reload();
				}
			});
	});
</script>
```

## Out of the box: Ajax forms
For all modules that integrate forms you can tick the box for using ajax forms. Additionally the template „j_ajaxforms“ has to be included in the page layout. Instead of reloading the entire page, forms will update itself.
This feature is supported for all forms that are programmed in Contao-style (third party) or Contao core modules like change password, personal data, login form etc.