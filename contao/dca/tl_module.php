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
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'allowAjaxReload';

foreach ($GLOBALS['TL_DCA']['tl_module']['palettes'] as $name => $palette)
{
	if ($name == '__selector__')
	{
		continue;
	}

	$GLOBALS['TL_DCA']['tl_module']['palettes'][$name] .= ',allowAjaxReload';
}

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['allowAjaxReload'] = 'ajaxReloadFormSubmit';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['allowAjaxReload'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['allowAjaxReload'],
	'inputType' => 'checkbox',
	'eval'      => array
	(
		'submitOnChange' => true,
		'tl_class'       => 'clr w50 m12'
	),
	'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['ajaxReloadFormSubmit'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['ajaxReloadFormSubmit'],
	'inputType' => 'checkbox',
	'eval'      => array
	(
		'tl_class' => 'w50 m12'
	),
	'sql'       => "char(1) NOT NULL default ''"
);
