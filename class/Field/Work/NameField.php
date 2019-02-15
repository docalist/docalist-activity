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

use Docalist\Type\TypedText;

/**
 * Champ "name" pour les entités "work".
 *
 * Ce champ permet d'indiquer les différents noms du work (nom usuel, sigle, ancien nom...)
 *
 * Chaque occurence du champ comporte deux sous-champs :
 * - `type` : type de nom,
 * - `value` : nom.
 *
 * Le sous-champ type est associé à une table d'autorité qui indique les types de noms disponibles
 * ("table:name-type" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class NameField extends TypedText
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema()
    {
        return [
            'name' => 'name',
            'label' => __('Nom', 'docalist-activity'),
            'description' => __(
                "Noms, sigles et appellations utilisés pour désigner l'activité.",
                'docalist-activity'
            ),
            'repeatable' => true,
            'fields' => [
                'type' => [
                    'label' => __('Type de nom', 'docalist-activity'),
                    'table' => 'table:name-type',
                ],
                'value' => [
                    'label' => __('Nom ou sigle', 'docalist-activity'),
                ],
            ],
        ];
    }
}
