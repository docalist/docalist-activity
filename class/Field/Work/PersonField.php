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

use Docalist\Data\Type\TypedRelation;

/**
 * Champ "person" pour les entités "work".
 *
 * Ce champ permet de créer des relations avec des personnes (dirigeant, membre...)
 *
 * Chaque occurence du champ comporte deux sous-champs :
 * - `type` : type de relation,
 * - `value` : Post ID de la personne liée.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de relations disponibles
 * ("table:org-person-relation" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class PersonField extends TypedRelation
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema(): array
    {
        return [
            'name' => 'organization',
            'label' => __('Personnes liées', 'docalist-activity'),
            'description' => __("Personnes liées à l'activité.", 'docalist-activity'),
            'repeatable' => true,
            'fields' => [
                'type' => [
                    'label' => __('Rôle / fonction', 'docalist-activity'),
                    'table' => 'table:org-person-relation',
                ],
                'value' => [
                    'label' => __('Personne liée', 'docalist-activity'),
                    'relfilter' => 'type:person',
                ],
            ],
        ];
    }
}
