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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] .= ',allowAjaxReload';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_article']['fields']['allowAjaxReload'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['allowAjaxReload'],
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'clr w50 m12',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];
