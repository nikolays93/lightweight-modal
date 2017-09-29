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
}
});