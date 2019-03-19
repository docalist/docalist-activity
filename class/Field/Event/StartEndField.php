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
use Docalist\Type\DateTimeInterval;

/**
 * Champ "startend" pour les entités "event".
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class StartEndField extends DateTimeInterval
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema(): array
    {
        return [
            'name' => 'startend',
            'repeatable' => true,
            'relfilter' => 'type:work',
            'label' => __("Date de l'événement", 'docalist-activity'),
            'description' => __('Dates de début et de fin.', 'docalist-activity'),
        ];
    }
}
