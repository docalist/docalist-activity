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
 * Champ "organization" pour les entités "work".
 *
 * Ce champ permet de créer des relations avec des organismes (structure parent, financeur, membres...)
 *
 * Chaque occurence du champ comporte deux sous-champs :
 * - `type` : type de relation,
 * - `value` : Post ID de la structure liée.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de relations disponibles
 * ("table:org-org-relation" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class OrganizationField extends TypedRelation
{
    public static function loadSchema()
    {
        return [
            'name' => 'organization',
            'label' => __('Organismes liés', 'docalist-activity'),
            'description' => __("Structures et organismes liés à l'activité.", 'docalist-activity'),
            'repeatable' => true,
            'fields' => [
                'type' => [
                    'label' => __('Relation', 'docalist-activity'),
                    'table' => 'table:org-org-relation',
                ],
                'value' => [
                    'label' => __('Structure liée', 'docalist-activity'),
                    'relfilter' => 'type:organization',
                ],
            ],
        ];
    }
}
