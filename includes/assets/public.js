jQuery(document).ready(function($) {
    var selectors = $.parseJSON( smodals_opt.selectors );

    console.log(selectors);
    var ids = Object.keys(selectors);

    ids.forEach( function(item) {
        $('body').append('<div id="'+ selectors[item].slice(1) +'"></div>');
        $( selectors[item] ).on('click', function(event) {
            $( item ).html('opened');
        });
    } );
    // selectors.forEach( function(el, key){
    //     console.log(el, key);
    // } );
});