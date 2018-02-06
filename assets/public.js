jQuery(document).ready(function($) {
    if( LWModals.modal_selector ){
        // if( LWModals.modal_type == 'fancybox3' ){
            $( LWModals.modal_selector ).each(function(index, el) {
                $(this).attr('data-fancybox', $(this).attr('rel') );
            });

            var fancyModal = $( LWModals.modal_selector ).fancybox({
                animationEffect : LWModals.openCloseEffect,
                transitionEffect : LWModals.nextPrevEffect,
            });
        // }
    }

    $('[data-modal-id]').on('click', function(event) {
        var modal_id = +$(this).attr( 'data-modal-id' );
        if( modal_id >= 1 ) {
            $.ajax({
                type: 'POST',
                url: LWM_Settings.ajax_url,
                data: {
                    action: 'increase_click_count',
                    nonce: LWM_Settings.nonce,
                    modal_id: modal_id
                },
                success: function(response){
                    //alert('Получено с сервера: ' + response);
                }
            }).fail(function() {
                console.log('Warning: Ajax Fatal Error!');
            });
        }
    });
});