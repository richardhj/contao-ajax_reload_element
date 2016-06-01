<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'AjaxReloadElement' => 'system/modules/zz_ajax_reload_element/classes/AjaxReloadElement.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'j_ajaxform' => 'system/modules/zz_ajax_reload_element/templates',
));
