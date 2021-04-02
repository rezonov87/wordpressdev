console.log('load script from rezonov plugin');
jQuery(document).on('submit','form.form_go',function(){
    event.preventDefault();
    var data = {
        action: 'sendmail_form',
        email: jQuery('.email').val()
    };


    jQuery.post( my_ajax_object.ajax_url, data, function( response ){
        alert( 'Получено с сервера: ' + response );
    } );
    return false;
});
