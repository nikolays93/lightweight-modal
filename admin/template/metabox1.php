<?php

namespace NikolayS93\LWModal;

use NikolayS93\WPAdminForm\Form as Form;

add_filter( 'LWModal_libs', __NAMESPACE__ . '\add_fancybox_listitem', 10, 1 );
function add_fancybox_listitem( $libs ) {
    $libs['fancybox3']    = __('Fancybox 3', DOMAIN);
    // $libs['magnific']     = __('Magnific Popup', DOMAIN);
    // $libs['photoswipe']   = __('PhotoSwipe', DOMAIN);
    // $libs['lightgallery'] = __('LightGallery', DOMAIN); // https://sachinchoolur.github.io/lightgallery.js/

    return $libs;
}

$data = array(
    array(
        'type'    => 'select',
        'id'      => 'lib_name',
        'label'   => __('Libary', DOMAIN),
        'options' => apply_filters( 'LWModal_libs', array(
            '' => __('Do not use', DOMAIN),
        ) ),
        'value'   => 'fancybox3',
        'input_class' => 'button right',
    ),
    array(
        'type'      => 'text',
        'id'        => 'selector',
        'label'     => sprintf('<hr><%2$s>%1$s</%2$s><br>',
            __('jQuery Selector', DOMAIN),
            'strong'),
        'placeholder'   => '.fancybox, .zoom',
        'custom_attributes' => array(
            'onclick' => 'if(!this.value)this.value=jQuery(this).attr(\'placeholder\');focus()',
        ),
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_args][openCloseEffect',
        'label'   => sprintf('<%2$s>%1$s</%2$s><br>', __('Show effect', DOMAIN), 'strong'),
        'options' => array(
            'false'       => __('Without effect', DOMAIN),
            'zoom'        => __('Zoom', DOMAIN),
            'fade'        => __('Fade', DOMAIN),
            'zoom-in-out' => __('Zoom in out', DOMAIN),
        ),
        'default' => 'zoom',
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_args][nextPrevEffect',
        'label'   => sprintf('<%2$s>%1$s</%2$s><br>', __('Prev/Next effect', DOMAIN), 'strong'),
        'options' => array(
            'false'       => __('Without effect', DOMAIN),
            'fade'        => __('Fade', DOMAIN),
            'slide'       => __('Slide', DOMAIN),
            'circular'    => __('Circular', DOMAIN),
            'tube'        => __('Tube', DOMAIN),
            'zoom-in-out' => __('Zoom in out', DOMAIN),
            'rotate'      => __('Rotate', DOMAIN),
        ),
        'default' => 'fade',
    ),
    array(
        'type'    => 'html',
        'id'      => 'for_group',
        'value'   => __('To group objects, use the same <em>rel</em>', DOMAIN),
    ),
);

$form = new Form( $data, array(
    'is_table' => false,
) );
$form->display();

submit_button( 'Сохранить', 'primary right', 'save_changes' );

echo '<div class="clear"></div>';