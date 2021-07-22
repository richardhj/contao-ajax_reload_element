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

namespace Richardhj\ContaoAjaxReloadElementBundle\EventListener\DataContainer;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;

/**
 * Class ModifyPalettesListener
 */
class ModifyPalettesListener
{
    public function __invoke(DataContainer $dc): void
    {
        foreach ($GLOBALS['TL_DCA'][$dc->table]['palettes'] as $name => $palette) {
            if (!\is_string($palette)) {
                continue;
            }

            if ('tl_content' === $dc->table && 'module' === $name) {
                continue;
            }

            PaletteManipulator::create()
                ->addField('allowAjaxReload', 'expert_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette($name, $dc->table)
            ;
        }
    }
}
