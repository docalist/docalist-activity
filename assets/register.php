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

namespace Docalist\Activity;

// Les scripts suivants ne sont dispos que dans le back-office
add_action('admin_init', function () {
    $base = DOCALIST_ACTIVITY_URL;

    wp_register_style(
        'docalist-activity-edit-work',
        "$base/assets/edit-work.css",
        [],
        '190214'
    );
});
