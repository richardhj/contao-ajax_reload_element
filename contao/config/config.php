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
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('AjaxReloadElement', 'parseTemplate');
$GLOBALS['TL_HOOKS']['simpleAjaxFrontend'][] = array('AjaxReloadElement', 'getModuleOrContentElement');
