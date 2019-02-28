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
 * Champ "place" pour les entités "event".
 *
 * Ce champ permet de créer des relations avec des lieux.
 *
 * Chaque occurence du champ comporte deux sous-champs :
 * - `type` : type de relation,
 * - `value` : Post ID du lieu lié.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de relations disponibles
 * ("table:event-place-relation" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class PlaceField extends TypedRelation
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema()
    {
        return [
            'name' => 'place',
            'label' => __('Lieu', 'docalist-activity'),
            'description' => __("Lieu(x) où se déroulera l'événement.", 'docalist-activity'),
            'repeatable' => true,
            'fields' => [
                'type' => [
                    'label' => __('Type de lieu', 'docalist-activity'),
                    'table' => 'table:event-place-relation',
                ],
                'value' => [
                    'label' => __('Lieu', 'docalist-activity'),
                    'relfilter' => 'type:place',
                ],
            ],
        ];
    }
}
