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

namespace Docalist\Activity\Field\Event;

use Docalist\Data\Type\Relation;

/**
 * Champ "work" pour les entités "event".
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class WorkField extends Relation
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema()
    {
        return [
            'name' => 'work',
            'relfilter' => 'type:work',
            'label' => __('Activité parent', 'docalist-activity'),
            'description' => __('Post ID de la fiche activité parent', 'docalist-activity'),
        ];
    }
}
