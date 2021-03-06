// ------------------------------------------------------------ Drop Menu

jQuery(function($) {
    $('.rex-js-drop-button, .rex-js-drop .rex-js-close').click(function(){
        checkDrop($(this));
    });
});


function checkDrop($object) {
    var $menu = $object.closest('.rex-js-drop');

    if ($menu.hasClass('rex-open')) {
        closeDrop($menu);
    }
    else {
        openDrop($menu);
    }
}

function openDrop($object) {
    $object.addClass('rex-open');
}
function closeDrop($object) {
    $object.removeClass('rex-open');
}


// ------------------------------------------------------------ Context Menu

jQuery(function($) {
    $('.rex-js-context-menu-button, .rex-js-context-menu .rex-js-close').click(function(){
        checkContextMenu($(this));
    });
});

function checkContextMenu($object) {
    var $menu = $object.closest('.rex-js-context-menu');

    if ($menu.hasClass('rex-open')) {
        closeContextMenu($menu);
    }
    else {
        openContextMenu($menu);
    }
}

function openContextMenu($object) {
    $object.addClass('rex-open');
}
function closeContextMenu($object) {
    $object.removeClass('rex-open');
}
