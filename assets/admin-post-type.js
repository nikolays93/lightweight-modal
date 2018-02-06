jQuery(document).ready(function($) {
    $('#_trigger_type').on('change', function(event) {
        $('#_shortcode-wrap, #_onclick-wrap, #_onfocus-wrap, #_onload-wrap, #_trigger-wrap')
            .hide()
            .attr('disable', 'true');

        $( '#_' + $(this).val() + '-wrap' )
            .show()
            .removeAttr('disable');

        if( $(this).val() == 'onclose' || $(this).val() == 'onload' ) {
            $( '#_trigger-wrap' )
                .show()
                .removeAttr('disable');
        }
    }).trigger('change');
});