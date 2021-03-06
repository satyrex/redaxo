<?php

/**
 * Userinfo Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.org">www.redaxo.org</a>
 *
 * @package redaxo5
 */

$mypage = 'userinfo';

// im backend und eingeloggt?
if (rex::isBackend() && rex::getUser()) {
    if (rex_request('page', 'string') == 'be_dashboard') {
        if (!defined('REX_DASHBOARD_USERINFO_DEFAULT_LIMIT')) {
            define('REX_DASHBOARD_USERINFO_DEFAULT_LIMIT', 7);
        }

        require_once __DIR__ . '/functions/function_userinfo.php';

        $components = [
            'rex_articles_component',
            'rex_media_component',
            'rex_stats_component',
        ];

        foreach ($components as $compClass) {
            rex_extension::register(
                'DASHBOARD_COMPONENT',
                [new $compClass(), 'registerAsExtension']
            );
        }
    }
}
