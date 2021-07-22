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

use Richardhj\ContaoAjaxReloadElementBundle\EventListener\DataContainer\ModifyPalettesListener;

/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_article']['config']['onload_callback'][] = [ModifyPalettesListener::class, '__invoke'];

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_article']['fields']['allowAjaxReload'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['allowAjaxReload'],
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'clr w50',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];
