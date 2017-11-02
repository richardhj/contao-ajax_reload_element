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

use Richardhj\Contao\Ajax\AjaxReloadElement;


/**
 * Fields
 */
$GLOBALS['TL_LANG']['ERR']['ajaxReloadElement'][AjaxReloadElement::ERROR_ELEMENT_NOT_FOUND]           = 'Could not find element %s.';
$GLOBALS['TL_LANG']['ERR']['ajaxReloadElement'][AjaxReloadElement::ERROR_ELEMENT_AJAX_FETCH_DISABLED] = '%s ID %u is not allowed to fetch.';
$GLOBALS['TL_LANG']['ERR']['ajaxReloadElement'][AjaxReloadElement::ERROR_ELEMENT_TYPE_UNKNOWN]        = 'Could not determine whether the element is a module or content element';