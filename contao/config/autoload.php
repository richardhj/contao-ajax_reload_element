<?php

/**
 * This file is part of richardhj/contao-ajax_reload_element.
 *
 * Copyright (c) 2016-2017 Richard Henkenjohann
 *
 * @package   richardhj/contao-ajax_reload_element
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2016-2017 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-ajax_reload_element/blob/master/LICENSE LGPL-3.0
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
