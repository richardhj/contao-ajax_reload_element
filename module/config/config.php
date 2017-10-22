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
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseTemplate'][] = [AjaxReloadElement::class, 'parseTemplate'];
$GLOBALS['TL_HOOKS']['getPageLayout'][] = [AjaxReloadElement::class, 'processAjaxRequest'];
