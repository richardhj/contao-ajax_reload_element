<?php

/**
 * This file is part of richardhj/contao-ajax_reload_element.
 *
 * Copyright (c) 2016-2018 Richard Henkenjohann
 *
 * @package   richardhj/contao-ajax_reload_element
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2016-2018 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-ajax_reload_element/blob/master/LICENSE LGPL-3.0
 */

namespace Richardhj\ContaoAjaxReloadElementBundle\EventListener;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Controller as ContaoController;
use Contao\CoreBundle\Image\PictureFactory;
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
class AjaxReloadElementListener
{

    const TYPE_MODULE  = 'mod';
    const TYPE_CONTENT = 'ce';
    const TYPE_ARTICLE = 'art';

    const ERROR_ELEMENT_NOT_FOUND        = 1;
    const ERROR_ELEMENT_AJAX_NOT_ALLOWED = 2;
    const ERROR_ELEMENT_TYPE_UNKNOWN     = 3;


    /**
     * @var PictureFactory
     */
    private $pictureFactory;


    public function __construct(PictureFactory $pictureFactory)
    {
        $this->pictureFactory = $pictureFactory; 
    }


    /**
     * Add the html attribute to allowed elements
     *
     * @param Template $template
     */
    public function onParseTemplate($template)
    {
        if (!($template instanceof FrontendTemplate) || !$template->allowAjaxReload) {
            return;
        }

        // Determine whether we have a module, a content element or an article by the vars given at this point
        $type = ('article' === $template->type)
            ? self::TYPE_ARTICLE
            : (\in_array($template->ptable, ['tl_article', 'tl_news', 'tl_calendar_events'], true) ? self::TYPE_CONTENT : self::TYPE_MODULE);

        // cssID is parsed in all common templates
        // Use cssID for our attribute
        $template->cssID .= sprintf(
            ' data-ajax-reload-element="%s::%u"%s',
            $type,
            $template->id,
            $template->ajaxReloadFormSubmit ? ' data-ajax-reload-form-submit=""' : ''
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
    public function onGetPageLayout($page, $layout)
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
                $elementParser = [ContaoController::class, 'getFrontendModule'];
                break;

            case self::TYPE_CONTENT:
                $element       = ContentModel::findByPk($elementId);
                $elementParser = [ContaoController::class, 'getContentElement'];
                break;

            case self::TYPE_ARTICLE:
                $element       = ArticleModel::findByPk($elementId);
                $elementParser = [ContaoController::class, 'getArticle'];
                break;

            default:
                $this->terminateWithError(self::ERROR_ELEMENT_TYPE_UNKNOWN);
                break;
        }

        $this->ensureModelIsNotNull($element, $paramElement);
        $this->ensureAjaxReloadIsAllowed($element);

        // Remove login error from session as it is not done in the module class anymore (see contao/core#7824)
        unset($_SESSION['LOGIN_ERROR']);

        // Set theme and layout related information in the page object (see #10)
        $theme = $layout->getRelated('pid');
        $this->pictureFactory->setDefaultDensities($theme->defaultImageDensities);
		$page->layoutId = $layout->id;
		$page->template = $layout->template ?: 'fe_page';
		$page->templateGroup = $theme->templates;
		list($strFormat, $strVariant) = explode('_', $layout->doctype);
		$page->outputFormat = $strFormat;
		$page->outputVariant = $strVariant;

        // Parse the element
        $return = $elementParser($element);

        // Replace insert tags and then re-replace the request_token tag in case a form element has been loaded via insert tag
        $return = ContaoController::replaceInsertTags($return, false);
        $return = str_replace(['{{request_token}}', '[{]', '[}]'], [REQUEST_TOKEN, '{{', '}}'], $return);
        $return = ContaoController::replaceDynamicScriptTags($return); // see contao/core#4203

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

        $this->terminateWithError(self::ERROR_ELEMENT_AJAX_NOT_ALLOWED, [$elementType, $model->id]);
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
