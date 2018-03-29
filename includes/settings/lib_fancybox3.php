<?php
$modal_types = array(
    ''          => 'Не использовать',
    // 'fancybox2' => 'Fancybox 2',
    'fancybox3' => 'Fancybox 3',
    // 'magnific'   => 'Magnific Popup',
    // 'photoswipe' => 'PhotoSwipe',
    // 'lightgallery' => https://sachinchoolur.github.io/lightgallery.js/
);

$props = array(
    array(
        'type'    => 'select',
        'id'      => 'lib_props][modal_type',
        'label'   => 'Библиотека',
        'options' => $modal_types,
        'value'   => 'fancybox3',
        'input_class' => 'button right',
    ),
    array(
        'type'      => 'text',
        'id'        => 'lib_props][modal_selector',
        'label'     => '<hr> <strong>jQuery Селектор</strong> <br>',
        // 'desc'      => 'Модальное окно (Галерея, всплывающее окно)',
        'placeholder'   => '.fancybox, .zoom',
        'custom_attributes' => array(
            'onclick' => 'if(!this.value)this.value=jQuery(this).attr(\'placeholder\');focus()',
        ),
        // 'input_class' => 'button right',
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_props][openCloseEffect',
        'label'   => 'Эффект открытия',
        'options' => array(
            'false'     => 'Без эффекта',
            'zoom'        => 'Увеличение от объекта',
            'fade'        => 'Угасание',
            'zoom-in-out' => 'Увеличение из вне',
        ),
        'default' => 'zoom',
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_props][nextPrevEffect',
        'label'   => 'Эффект перелистывания',
        'options' => array(
            'false'       => 'Без эффекта',
            'fade'        => 'Угасание',
            'slide'       => 'Скольжение',
            'circular'    => 'Циркуляция',
            'tube'        => 'Труба',
            'zoom-in-out' => 'Увеличение из вне',
            'rotate'      => 'Переворот',
        ),
        'default' => 'fade',
    ),
    array(
        'type'    => 'html',
        'id'      => 'for_group',
        'value'   => 'Для группировки объектов используйте одинаковый <em>rel</em>'
    ),
);

return apply_filters('LWModal_lib_props', $props, 'fancybox3');