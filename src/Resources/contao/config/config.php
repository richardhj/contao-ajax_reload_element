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
 * Hooks
 *
 * Waiting for Contao 4.5 (see contao/core-bundle#1094) to get rid of this file
 */
$GLOBALS['TL_HOOKS']['parseTemplate'][] = ['richardhj.contao_ajax_reload_element.parse_template_listener', 'onParseTemplate'];
$GLOBALS['TL_HOOKS']['getPageLayout'][] = ['richardhj.contao_ajax_reload_element.get_page_layout_listener', 'onGetPageLayout'];
