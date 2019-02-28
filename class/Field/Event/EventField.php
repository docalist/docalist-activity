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

use Docalist\Data\Type\TypedRelation;

/**
 * Champ "event" pour les entités "event".
 *
 * Ce champ permet de créer des relations avec d'autres événements.
 *
 * Chaque occurence du champ comporte deux sous-champs :
 * - `type` : type de relation,
 * - `value` : Post ID de l'événement lié.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de relations disponibles
 * ("table:event-event-relation" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class EventField extends TypedRelation
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema()
    {
        return [
            'name' => 'event',
            'label' => __('Événement lié', 'docalist-activity'),
            'description' => __("Événements liés à cet événement.", 'docalist-activity'),
            'repeatable' => true,
            'fields' => [
                'type' => [
                    'label' => __('Type de relation', 'docalist-activity'),
                    'table' => 'table:event-event-relation',
                ],
                'value' => [
                    'label' => __('Événement lié', 'docalist-activity'),
                    'relfilter' => 'type:event',
                ],
            ],
        ];
    }
}
