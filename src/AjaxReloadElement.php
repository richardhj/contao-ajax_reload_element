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

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Controller;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Model;
use Contao\ModuleModel;
use Contao\Template;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class AjaxReloadElement
 */
class AjaxReloadElement
{

    const TYPE_MODULE  = 'mod';
    const TYPE_CONTENT = 'ce';
    const TYPE_ARTICLE = 'art';

    const ERROR_ELEMENT_NOT_FOUND           = 1;
    const ERROR_ELEMENT_AJAX_FETCH_DISABLED = 2;
    const ERROR_ELEMENT_TYPE_UNKNOWN        = 3;

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

        // Determine whether we have a module, a content element or an article by the vars given at this point
        $type = ('article' === $template->type)
            ? self::TYPE_ARTICLE
            : (('tl_article' === $template->ptable) ? self::TYPE_CONTENT : self::TYPE_MODULE);


        // cssID is parsed in all common templates
        // Use cssID for our attribute
        $template->cssID .= sprintf(
            ' data-ajax-reload-element="%s::%u"%s',
            $type,
            $template->id,
            ($template->ajaxReloadFormSubmit) ? ' data-ajax-reload-form-submit=""' : ''
        );
    }

    /**
     * We check for an ajax request on the getPageLayout hook, which is one of the first hooks being called. If so, and
     * the ajax request is directed to us, we send the generated module/content element as a JSON response.
     *
     * @internal param PageModel $page
     * @internal param LayoutModel $layout
     * @internal param PageRegular $pageHandler
     */
    public function processAjaxRequest()
    {
        if (false === Environment::get('isAjaxRequest')
            || !(null !== ($paramElement = Input::get('ajax_reload_element'))
                 || null !== ($paramElement = Input::post('ajax_reload_element')))
        ) {
            return;
        }

        $element       = null;
        $elementParser = [];
        $data          = [];
        list ($elementType, $elementId) = trimsplit('::', $paramElement);

        // Remove the get parameter from the url
        $requestUrl = UrlBuilder::fromUrl(Environment::get('request'));
        $requestUrl->unsetQueryParameter('ajax_reload_element');
        Environment::set('request', $requestUrl->getUrl());

        switch ($elementType) {
            case self::TYPE_MODULE:
                $element       = ModuleModel::findByPk($elementId);
                $elementParser = [Controller::class, 'getFrontendModule'];
                break;

            case self::TYPE_CONTENT:
                $element       = ContentModel::findByPk($elementId);
                $elementParser = [Controller::class, 'getContentElement'];
                break;

            case self::TYPE_ARTICLE:
                $element       = ArticleModel::findByPk($elementId);
                $elementParser = [Controller::class, 'getArticle'];
                break;

            default:
                $this->terminateWithError(self::ERROR_ELEMENT_TYPE_UNKNOWN);
                break;
        }

        $this->ensureModelIsNotNull($element, $paramElement);
        $this->ensureAjaxReloadIsAllowed($element);

        // Remove login error from session as it is not done in the module class anymore (see contao/core#7824)
        unset($_SESSION['LOGIN_ERROR']);

        $return = call_user_func($elementParser, $element);

        // Replace insert tags and then re-replace the request_token tag in case a form element has been loaded via insert tag
        $return = Controller::replaceInsertTags($return, false);
        $return = str_replace(['{{request_token}}', '[{]', '[}]'], [REQUEST_TOKEN, '{{', '}}'], $return);
        $return = Controller::replaceDynamicScriptTags($return); // see contao/core#4203

        $data['status'] = 'ok';
        $data['html']   = $return;

        $response = new JsonResponse($data);
        $response->send();
        exit;
    }

    /**
     * @param Model|null $model
     * @param string     $modelIdentifier
     */
    private function ensureModelIsNotNull($model, $modelIdentifier)
    {
        if (null !== $model) {
            return;
        }

        $this->terminateWithError(self::ERROR_ELEMENT_NOT_FOUND, $modelIdentifier);
    }

    /**
     * @param Model $model
     */
    private function ensureAjaxReloadIsAllowed(Model $model)
    {
        if ($model->allowAjaxReload) {
            return;
        }

        // Get element class like "ArticleModel" and cut off "Model"
        $elementType = (new ReflectionClass($model))->getShortName();
        $elementType = substr($elementType, 0, -5);

        $this->terminateWithError(self::ERROR_ELEMENT_AJAX_FETCH_DISABLED, [$elementType, $model->id]);
    }

    /**
     * @param int          $errorCode
     * @param array|string $args
     */
    private function terminateWithError($errorCode, $args = [])
    {
        $data['status']     = 'error';
        $data['error_code'] = $errorCode;
        $data['error']      = vsprintf($GLOBALS['TL_LANG']['ERR']['ajaxReloadElement'][$errorCode], (array)$args);

        $response = new JsonResponse($data);
        $response->send();
        exit;
    }
}
