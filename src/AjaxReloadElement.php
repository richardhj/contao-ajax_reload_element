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

namespace Richardhj\Contao\Ajax;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\Controller;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\FrontendUser;
use Contao\Input;
use Contao\Model;
use Contao\Module;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Contao\Template;
use SimpleAjax\Event\SimpleAjax as SimpleAjaxEvent;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class AjaxReloadElement
 */
class AjaxReloadElement
{

    const TYPE_MODULE  = 'mod';
    const TYPE_CONTENT = 'ce';

    /**
     * Add the html attribute to allowed elements
     *
     * @param Template $template
     */
    public function parseTemplate($template)
    {
        if (!($template instanceof FrontendTemplate) || !$template->allowAjaxReload) {
            return;
        }

        // Determine whether we have a module or a content element by the vars given at this point
        if (Module::findClass($template->type)) {
            $type = self::TYPE_MODULE;
        } elseif (ContentElement::findClass($template->type)) {
            $type = self::TYPE_CONTENT;
        } else {
            return;
        }

        // Some elements use the auto_item which we need to pass
        $autoItem = Input::get('auto_item');

        // cssID is parsed in all common templates
        // Use cssID for our attribute
        $template->cssID .= sprintf(
            ' data-ajax-reload-element="%s::%u"%s%s',
            $type,
            $template->id,
            strlen($autoItem) ? sprintf(' data-ajax-reload-auto-item="%s"', $autoItem) : '',
            ($template->ajaxReloadFormSubmit) ? ' data-ajax-reload-form-submit=""' : ''
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
     */
    public function getModuleOrContentElement(SimpleAjaxEvent $event)
    {
        if (!Environment::get('isAjaxRequest')
            || Input::get('action') != 'reload-element'
            || false === $event->isIncludeFrontendExclusive()) {
            return;
        }

        // Set page object as it may be needed for the language e.g.
        $page = $GLOBALS['objPage'];
        if (!$page && (int)Input::get('page')) {
            $page = PageModel::findWithDetails((int)Input::get('page'));
        }

        $GLOBALS['TL_LANGUAGE'] = (null !== $page) ? $page->language : $GLOBALS['TL_LANGUAGE'];

        list ($elementType, $elementId) = trimsplit('::', Input::get('element'));
        $error  = '';
        $return = '';

        // Authenticate front end user, e.g. for insert tags
        if (FE_USER_LOGGED_IN) {
            FrontendUser::getInstance()->authenticate();
        }

        // Load default language file
        System::loadLanguageFile('default');

        // Set a given auto_item to fetch the correct version of a module or content element
        if (($autoItem = Input::get('auto_item'))) {
            Input::setGet('auto_item', $autoItem);
        }

        switch ($elementType) {
            case self::TYPE_MODULE:
                /** @type Model $module */
                $module = ModuleModel::findByPk($elementId);

                if (null === $module) {
                    $error = sprintf('Could not find module ID %s', $elementId);

                    continue;
                }

                if (!$module->allowAjaxReload) {
                    $error = sprintf('Module ID %u is not allowed to fetch', $elementId);

                    continue;
                }

                $return = Controller::getFrontendModule($module);
                break;

            case self::TYPE_CONTENT:
                /** @type Model $contentElement */
                $contentElement = ContentModel::findByPk($elementId);

                if (null === $contentElement) {
                    $error = sprintf('Could not find content element ID %s', $elementId);

                    continue;
                }

                if (!$contentElement->allowAjaxReload) {
                    $error = sprintf('Content element ID %u is not allowed to fetch', $elementId);

                    continue;
                }

                $return = Controller::getContentElement($contentElement);
                break;

            default:
                $error = 'Could not determine whether the element is a module or content element';
                break;
        }

        // Remove login error from session as it is not done in the module class anymore (see contao/core#7824)
        unset($_SESSION['LOGIN_ERROR']);

        // Replace insert tags and then re-replace the request_token tag in case a form element has been loaded via insert tag
        $return = Controller::replaceInsertTags($return, false);
        $return = str_replace(['{{request_token}}', '[{]', '[}]'], [REQUEST_TOKEN, '{{', '}}'], $return);
        $return = Controller::replaceDynamicScriptTags($return); // see contao/core#4203

        $data = [];

        if ($error) {
            $data['status'] = 'error';
            $data['error']  = $error;
        } else {
            $data['status'] = 'ok';
            $data['html']   = $return;
        }

        $response = new JsonResponse($data);
        $response->send();
    }
}
