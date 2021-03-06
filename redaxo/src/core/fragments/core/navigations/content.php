<?php
/*
    Tabnavi  -> rex-navi-tab

    ->right = "text right from navi"
    ->left = "text left from navi"

    ->navigaion_left = left navi objekts
    ->navigaion_right = left navi objekts

*/

$navigations = [];

if (isset($this->navigation_left)) {
    $navigations['left'] = $this->navigation_left;

}

if (isset($this->navigation_right)) {
    $navigations['right'] = $this->navigation_right;

}

foreach ($navigations as $nav_key => $navigation) {

    foreach ($navigation as $navi) {
        if (isset($navi['active']) && $navi['active'] && isset($navi['children']) && count($navi['children']) > 0) {
            $navigations['children'] = $navi['children'];
        }
    }
}


foreach ($navigations as $nav_key => $navigation) {

    $li = [];
    foreach ($navigation as $navi) {

        $li_a = '';


        $attributes = [];

        if (isset($navi['itemClasses']) && is_array($navi['itemClasses']) && count($navi['itemClasses']) > 0 && isset($navi['itemClasses'][0]) && $navi['itemClasses'][0] != '') {
            $attributes['class'] = implode(' ', $navi['itemClasses']);
        }

        if (isset($navi['itemAttr']) && is_array($navi['itemAttr']) && count($navi['itemAttr']) > 0) {
            foreach ($navi['itemAttr'] as $key => $value) {
                if ($value != '') {
                    $attributes[$key] = $value;
                }
            }
        }

        $li_a .= '<li' . rex_string::buildAttributes($attributes) . '>';


        if (isset($navi['href']) && $navi['href'] != '') {

            $attributes = [];
            $attributes['href'] = $navi['href'];

            if (isset($navi['linkClasses']) && is_array($navi['linkClasses']) && count($navi['linkClasses']) > 0 && isset($navi['linkClasses'][0]) && $navi['linkClasses'][0] != '') {
                $attributes['class'] = implode(' ', $navi['linkClasses']);
            }

            if (isset($navi['linkAttr']) && is_array($navi['linkAttr']) && count($navi['linkAttr']) > 0) {
                foreach ($navi['linkAttr'] as $key => $value) {
                    if ($value != '') {
                        $attributes[$key] = $value;
                    }
                }
            }

            if ($nav_key != 'children') {
                if (isset($attributes['class'])) {
                    $attributes['class'] = trim('rex-navi-content-item ' . $attributes['class']);
                } else {
                    $attributes['class'] = 'rex-navi-content-item';
                }
            }

            $li_a .= '<a' . rex_string::buildAttributes($attributes) . '>';

        }

        $li_a .= $navi['title'];

        if (isset($navi['href']) && $navi['href'] != '') {
            $li_a .= '</a>';
        }

        $li_a .= '</li>';
        $li[] = $li_a;
    }


    $navigations[$nav_key] = implode($li);

}







echo '<div class="rex-navi-content">';

$right = '';
if (isset($navigations['right'])) {
    $right .= '<ul class="rex-navi-content-items">' . $navigations['right'] . '</ul>';
}

$gizmo = '';
if (isset($this->text_right) && $this->text_right != '') {
    $gizmo .= '<span class="rex-navi-content-text">' . $this->text_right . '</span>';
}
if (isset($this->right) && $this->right != '') {
    $gizmo = '<span class="rex-navi-content-gizmo">' . $this->right . $gizmo . '</span>';
}

$right .= $gizmo;

echo $right != '' ? '<div class="rex-navi-content-right">' . $right . '</div>' : '';


// left text
$gizmo = '';
if (isset($this->text_left) && $this->text_left != '') {
    $gizmo .= '<span class="rex-navi-content-text">' . $this->text_left . '</span>';
}
if (isset($this->left) && $this->left != '') {
    $gizmo = '<span class="rex-navi-content-gizmo">' . $gizmo . $this->left . '</span>';
}

echo $gizmo;

// left navi
if (isset($navigations['left'])) {
    echo '<ul class="rex-navi-content-items">';
    echo $navigations['left'];
    echo '</ul>';
}

echo '</div>';



if (isset($navigations['children'])) {
    echo '
        <div class="rex-navi-content-head">
            <ul class="rex-piped">' . $navigations['children'] . '</ul>
        </div>';
}
