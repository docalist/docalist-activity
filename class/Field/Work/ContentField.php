<?php
/**
 * This file is part of Docalist Activity.
 *
 * Copyright (C) 2017-2019 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Docalist\Activity\Field\Work;

use Docalist\Data\Field\ContentField as BaseContentField;

/**
 * Champ "content" pour les entités "work".
 *
 * Cette classe hérite simplement du champ standard de docalist-data et modifie les paramètres par défaut.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class ContentField extends BaseContentField
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema(): array
    {
        return [
            'label' => __('Présentation', 'docalist-activity'),
            'description' => __("Présentation de l'activité...", 'docalist-activity'),
        ];
    }
}
