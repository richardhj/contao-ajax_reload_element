<?php
/**
 * AjaxReloadElement extension for Contao Open Source CMS
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package AjaxReloadElement
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


use Haste\Http\Response\JsonResponse;
use Haste\Input\Input;


/**
 * Class AjaxReloadElement
 */
class AjaxReloadElement extends \Controller
{

	/**
	 * Add the html attribute to allowed elements
	 *
	 * @param \Template $objTemplate
	 */
	public function parseTemplate($objTemplate)
	{
		if (!($objTemplate instanceof \FrontendTemplate) || !$objTemplate->allowAjaxReload)
		{
			return;
		}

		// Determine whether we have a module or a content element by the vars given at this point
		if (\Module::findClass($objTemplate->type))
		{
			$strType = 'mod';
		}
		elseif (\ContentElement::findClass($objTemplate->type))
		{
			$strType = 'ce';
		}
		else
		{
			return;
		}

		// Some elements use the auto_item which we need to pass
		$strAutoItem = Input::getAutoItem('auto_item');

		// cssID is parsed in all common templates
		// Use cssID for our attribute
		$objTemplate->cssID .= sprintf(
			' data-ajax-reload-element="%s::%u"%s%s',
			$strType,
			$objTemplate->id,
			strlen($strAutoItem) ? sprintf(' data-ajax-reload-auto-item="%s"', $strAutoItem) : '',
			($objTemplate->ajaxReloadFormSubmit) ? ' data-ajax-reload-form-submit=""' : ''
		);
	}


	/**
	 * Return the demanded frontend module or content element parsed as html string
	 *
     * Required GET data:
	 * * action: "reload-element"
	 * * element: "ce::id" or "mod::id" (replace 'id' with the element's id)
	 * * page: "id" (optionally, replace 'id' with the current page's id)
	 * * auto_item: (an optional auto_item which will be set before fetching the element)
     * * requestUri: (optionally uri to fetch the query params from)
	 */
	public function getModuleOrContentElement()
	{
		if (!\Environment::get('isAjaxRequest') || Input::get('action') != 'reload-element')
		{
			return;
		}

		global $objPage;

		// Set page object as it may be needed for the language e.g.
		if (!$objPage && (int)Input::get('page'))
		{
			$objPage = \PageModel::findWithDetails((int)Input::get('page'));
		}
		
		$GLOBALS['TL_LANGUAGE'] = (null !== $objPage) ? $objPage->language : $GLOBALS['TL_LANGUAGE'];

		list ($strElementType, $intElementId) = trimsplit('::', Input::get('element'));
		$strError = '';
		$return = '';

		// Authenticate front end user, e.g. for insert tags
		if (FE_USER_LOGGED_IN)
		{
			/** @noinspection PhpUndefinedMethodInspection */
			$this->import('FrontendUser', 'User');
			/** @var \FrontendUser $this ->User */
			$this->User->authenticate();
		}

		// Load default language file
		\System::loadLanguageFile('default');

		// Set a given auto_item to fetch the correct version of a module or content element
		if (($strAutoItem = Input::get('auto_item')))
		{
			Input::setGet('auto_item', $strAutoItem);
		}

        list(, $query) = explode('?', Input::get('requestUri', true), 2);
		parse_str($query, $params);

        foreach ($params as $k => $v) {
		    Input::setGet($k, $v);
		}

		switch ($strElementType)
		{
			case 'mod':
				/** @type \Model $objModule */
				$objModule = \ModuleModel::findByPk($intElementId);

				if (null === $objModule)
				{
					$strError = sprintf('Could not find module ID %s', $intElementId);

					continue;
				}

				if (!$objModule->allowAjaxReload)
				{
					$strError = sprintf('Module ID %u is not allowed to fetch', $intElementId);

					continue;
				}

				$return = \Controller::getFrontendModule($objModule);
				break;

			case 'ce':
				/** @type \Model $objContent */
				$objContent = ContentModel::findByPk($intElementId);

				if (null === $objContent)
				{
					$strError = sprintf('Could not find content element ID %s', $intElementId);

					continue;
				}

				if (!$objContent->allowAjaxReload)
				{
					$strError = sprintf('Content element ID %u is not allowed to fetch', $intElementId);

					continue;
				}

				$return = \Controller::getContentElement($objContent);
				break;

			default:
				$strError = 'Could not determine whether the element is a module or content element';
				break;
		}

        // Remove login error from session as it is not done in the module class anymore (see contao/core#7824)
        unset($_SESSION['LOGIN_ERROR']);

        // Replace insert tags and then re-replace the request_token tag in case a form element has been loaded via insert tag
        $return = $this->replaceInsertTags($return, false);
        $return = str_replace(array('{{request_token}}', '[{]', '[}]'), array(REQUEST_TOKEN, '{{', '}}'), $return);
        $return = $this->replaceDynamicScriptTags($return); // see contao/core#4203

		$arrResponse = array();

		if ($strError)
		{
			$arrResponse['status'] = 'error';
			$arrResponse['error'] = $strError;
		}
		else
		{
			$arrResponse['status'] = 'ok';
			$arrResponse['html'] = $return;
		}

		$objResponse = new JsonResponse($arrResponse);
		$objResponse->send();
	}
}
