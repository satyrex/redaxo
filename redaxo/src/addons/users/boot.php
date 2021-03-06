<?php

/** @var rex_addon $this */

rex_user::setRoleClass('rex_user_role');

rex_perm::register('users[]', rex_i18n::msg('user_management'));

rex_extension::register('COMPLEX_PERM_REMOVE_ITEM', 'rex_user_role::removeOrReplaceItem');
rex_extension::register('COMPLEX_PERM_REPLACE_ITEM', 'rex_user_role::removeOrReplaceItem');

if (rex::getUser() && rex::getUser()->isAdmin()) {
    $this->setProperty('pages1', [
        ['', rex_i18n::msg('users')],
        ['roles', rex_i18n::msg('roles')]
    ]);
}
