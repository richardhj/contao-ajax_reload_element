<?php

use SimpleAjax\Event\SimpleAjax;

return [
    SimpleAjax::NAME => [
        [new AjaxReloadElement(), 'getModuleOrContentElement'],
    ],
];