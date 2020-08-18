jQuery(document).ready( function($) {

    $(document).on('click', '#submit_feedback', function(e){
        e.preventDefault();
        alert( 'hello' );
        var user_name = $('#user_name').val();
        alert(user_name);
        var user_email = $('#user_email').val();
        alert(user_email);
        var user_phone = $('#user_phone').val();
        alert(user_phone);
        var user_query = $('#user_query').val();
        alert(user_query);

        $.ajax({
            url : ajax_script_obj.ajaxurl,
            method : 'post',
            data : {
                'action' : 'ajax_request_call',
                'uname'  : user_name,
                'uemail' : user_email,
                'uphone' : user_phone,
                'uquery' : user_query,
                'nonce'  : ajax_script_obj.nonce
            },

            success: function(data) {
                alert('Feedback succesfully submitted');
            },

            error  : function(errorThrown) {
                console.log(errorThrown);
            }
        });
    });
});