<?php

$Basedir = __DIR__;

$effect_id = rex_request('effect_id', 'int');
$type_id = rex_request('type_id', 'int');
$func = rex_request('func', 'string');

// ---- validate type_id
$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'media_manager_type WHERE id=' . $type_id);
if ($sql->getRows() != 1) {
    unset($type_id);
}
$typeName = $sql->getValue('name');


$info = '';
$warning = '';

//-------------- delete cache on effect changes or deletion
if ((rex_post('func') != '' || $func == 'delete')
     && $type_id > 0
) {
    $counter = rex_media_manager::deleteCacheByType($type_id);
//  $info = rex_i18n::msg('media_manager_cache_files_removed', $counter);
}

//-------------- delete effect
if ($func == 'delete' && $effect_id > 0) {
    $sql = rex_sql::factory();
//  $sql->setDebug();
    $sql->setTable(rex::getTablePrefix() . 'media_manager_type_effect');
    $sql->setWhere(['id' => $effect_id]);

    try {
        $sql->delete();
        $info = rex_i18n::msg('media_manager_effect_deleted') ;
    } catch (rex_sql_exception $e) {
        $warning = $sql->getError();
    }
    $func = '';
}

if ($info != '') {
    echo rex_view::info($info);
}

if ($warning != '') {
    echo rex_view::warning($warning);
}


echo '<div class="rex-addon-output-v2">';
if ($func == '' && $type_id > 0) {
    echo rex_view::contentBlock(rex_i18n::msg('media_manager_effect_list_header', htmlspecialchars($typeName)));

    $query = 'SELECT * FROM ' . rex::getTablePrefix() . 'media_manager_type_effect WHERE type_id=' . $type_id . ' ORDER BY priority';

    $list = rex_list::factory($query);
    $list->addParam('effects', 1);

    $list->setNoRowsMessage(rex_i18n::msg('media_manager_effect_no_effects'));
    $list->setCaption(rex_i18n::msg('media_manager_effect_caption', $typeName));
    $list->addTableColumnGroup([40, '*', 40, 130, 130]);

    $list->removeColumn('id');
    $list->removeColumn('type_id');
    $list->removeColumn('parameters');
    $list->removeColumn('updatedate');
    $list->removeColumn('updateuser');
    $list->removeColumn('createdate');
    $list->removeColumn('createuser');
    $list->setColumnLabel('effect', rex_i18n::msg('media_manager_type_name'));
    $list->setColumnLabel('priority', rex_i18n::msg('media_manager_type_priority'));

    // icon column
    $thIcon = '<a class="rex-i-element rex-i-generic-add" href="' . $list->getUrl(['type_id' => $type_id, 'func' => 'add']) . '"><span class="rex-i-element-text">' . rex_i18n::msg('media_manager_effect_create') . '</span></a>';
    $tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###id###</span></span>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-icon">###VALUE###</th>', '<td class="rex-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'type_id' => $type_id, 'effect_id' => '###id###']);

    // functions column spans 2 data-columns
    $funcs = rex_i18n::msg('media_manager_effect_functions');
    $list->addColumn($funcs, rex_i18n::msg('media_manager_effect_edit'), -1, ['<th colspan="2">###VALUE###</th>', '<td>###VALUE###</td>']);
    $list->setColumnParams($funcs, ['func' => 'edit', 'type_id' => $type_id, 'effect_id' => '###id###']);

    $delete = 'deleteCol';
    $list->addColumn($delete, rex_i18n::msg('media_manager_effect_delete'), -1, ['', '<td>###VALUE###</td>']);
    $list->setColumnParams($delete, ['type_id' => $type_id, 'effect_id' => '###id###', 'func' => 'delete']);
    $list->addLinkAttribute($delete, 'data-confirm', rex_i18n::msg('delete') . ' ?');

    $list->show();
} elseif ($func == 'add' && $type_id > 0 ||
                $func == 'edit' && $effect_id > 0 && $type_id > 0
) {
    $effectNames = rex_media_manager::getSupportedEffectNames();

    if ($func == 'edit') {
        $formLabel = rex_i18n::msg('media_manager_effect_edit_header', htmlspecialchars($typeName));
    } elseif ($func == 'add') {
        $formLabel = rex_i18n::msg('media_manager_effect_create_header', htmlspecialchars($typeName));
    }

    $form = rex_form::factory(rex::getTablePrefix() . 'media_manager_type_effect', $formLabel, 'id=' . $effect_id);

    // image_type_id for reference to save into the db
    $form->addHiddenField('type_id', $type_id);

    // effect name als SELECT
    $field = $form->addSelectField('effect');
    $field->setLabel(rex_i18n::msg('media_manager_effect_name'));
    $select = $field->getSelect();
    $select->addOptions($effectNames, true);
    $select->setSize(1);

    $script = '
    <script type="text/javascript">
    <!--

    (function($) {
        var currentShown = null;
        $("#' . $field->getAttribute('id') . '").change(function(){
            if(currentShown) currentShown.hide();

            var effectParamsId = "#rex-rex_effect_"+ jQuery(this).val();
            currentShown = $(effectParamsId);
            currentShown.show();
        }).change();
    })(jQuery);

    //--></script>';

    // effect prio
    $field = $form->addPrioField('priority');
    $field->setLabel(rex_i18n::msg('media_manager_effect_priority'));
    $field->setLabelField('effect');
    $field->setWhereCondition('type_id = ' . $type_id);

    // effect parameters
    $fieldContainer = $form->addContainerField('parameters');
    $fieldContainer->setAttribute('style', 'display: none');
    $fieldContainer->setSuffix($script);

    $effects = rex_media_manager::getSupportedEffects();

    foreach ($effects as $effectClass => $effectFile) {
        require_once $effectFile;
        $effectObj = new $effectClass();
        $effectParams = $effectObj->getParams();
        $group = $effectClass;

        if (empty($effectParams)) {
            continue;
        }

        foreach ($effectParams as $param) {
            $name = $effectClass . '_' . $param['name'];
            $value = isset($param['default']) ? $param['default'] : null;
            $attributes = [];
            if (isset($param['attributes'])) {
                $attributes = $param['attributes'];
            }

            switch ($param['type']) {
                case 'int' :
                case 'float' :
                case 'string' :
                    {
                        $type = 'text';
                        $field = $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes);
                        $field->setLabel($param['label']);
                        $field->setAttribute('id', "media_manager $name $type");
                        if (!empty($param['notice'])) {
                            $field->setNotice($param['notice']);
                        }
                        if (!empty($param['prefix'])) {
                            $field->setPrefix($param['prefix']);
                        }
                        if (!empty($param['suffix'])) {
                            $field->setSuffix($param['suffix']);
                        }
                        break;
                    }
                case 'select' :
                    {
                        $type = $param['type'];
                        $field = $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes);
                        $field->setLabel($param['label']);
                        $field->setAttribute('id', "media_manager $name $type");
                        if (!empty($param['notice'])) {
                            $field->setNotice($param['notice']);
                        }
                        if (!empty($param['prefix'])) {
                            $field->setPrefix($param['prefix']);
                        }
                        if (!empty($param['suffix'])) {
                            $field->setSuffix($param['suffix']);
                        }

                        $select = $field->getSelect();
                        if (!isset($attributes['multiple'])) {
                            $select->setSize(1);
                        }
                        $select->addOptions($param['options'], true);
                        break;
                    }
                case 'media' :
                    {
                        $type = $param['type'];
                        $field = $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes);
                        $field->setLabel($param['label']);
                        $field->setAttribute('id', "media_manager $name $type");
                        if (!empty($param['notice'])) {
                            $field->setNotice($param['notice']);
                        }
                        if (!empty($param['prefix'])) {
                            $field->setPrefix($param['prefix']);
                        }
                        if (!empty($param['suffix'])) {
                            $field->setSuffix($param['suffix']);
                        }
                        break;
                    }
                default:
                    {
                    throw new rex_exception('Unexpected param type "' . $param['type'] . '"');
                    }
            }
        }
    }

    // parameters for url redirects
    $form->addParam('type_id', $type_id);
    $form->addParam('effects', 1);
    if ($func == 'edit') {
        $form->addParam('effect_id', $effect_id);
    }
    $form->show();
}

echo '</div>';
