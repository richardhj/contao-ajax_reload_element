<?php
/**
 * AjaxReloadElement extension for Contao Open Source CMS
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package AjaxReloadElement
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
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
