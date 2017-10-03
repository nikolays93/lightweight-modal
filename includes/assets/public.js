jQuery(document).ready(function($) {
    if( SModals.modal_selector ){
        if( SModals.modal_type == 'fancybox3' ){
            $( SModals.modal_selector ).each(function(index, el) {
                $(this).attr('data-fancybox', $(this).attr('rel') );
            });

            var fancyModal = $( SModals.modal_selector ).fancybox({
                animationEffect : SModals.openCloseEffect,
                transitionEffect : SModals.nextPrevEffect,
            });
        }
        else if(SModals.modal_type) {
            var fancyModal = $( SModals.modal_selector ).fancybox({
                openEffect : SModals.openEffect,
                closeEffect : SModals.closeEffect,
                nextEffect : SModals.nextEffect,
                prevEffect : SModals.prevEffect,
                helpers: {
                    title : { type : 'inside' },
                    thumbs : SModals.thumb ? { width: 120, height: 80 } : false
                }
            });
        }

        $('[data-modal-id]').on('click', function(event) {
            var modal_id = +$(this).attr( 'data-modal-id' );
            if( modal_id >= 1 ) {
                $.ajax({
                    type: 'POST',
                    url: SM_Settings.ajax_url,
                    data: {
                        action: 'increase_click_count',
                        nonce: SM_Settings.nonce,
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
    }
});