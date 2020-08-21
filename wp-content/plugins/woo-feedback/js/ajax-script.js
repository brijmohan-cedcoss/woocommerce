jQuery(document).ready( function($) {

    $(document).on('submit', '#feedback_form', function(e){
        e.preventDefault();
       // alert( 'hello' );
        var first_field = $('#first_field').val();
        var first_field_attr = $('#first_field').attr('name');
        //alert(first_field_attr);
         //alert(first_field);
        var second_field = $('#second_field'). val();
        var second_field_attr = $('#second_field').attr('name');
       // alert(second_field_attr);
       // alert(second_field);
        var third_field = $('#third_field').val();
        var third_field_attr = $('#third_field').attr('name');
       // alert(third_field_attr);
       // alert(third_field);
        var fourth_field = $('#fourth_field').val();
        var fourth_field_attr = $('#fourth_field').attr('name');
        //alert(fourth_field_attr);
       // alert(fourth_field);
        var query = $('#user_query').val();
        //var query_attr =
       // alert(query);
     
        $.ajax({
            url : ajax_script_obj.ajaxurl,
            method : 'post',
            data : {
                'action'         : 'ajax_request_call',
                '1st_field'      : first_field,
                '1st_field_attr' : first_field_attr,
                '2nd_field'      : second_field,
                '2nd_field_attr' : second_field_attr,
                '3rd_field'      : third_field,
                '3rd_field_attr' : third_field_attr,
                '4th_field'      : fourth_field,
                '4th_field_attr' : fourth_field_attr,
                'message'        : query,
                'nonce'          : ajax_script_obj.nonce
            },

            success: function(data) {
                console.log(data);
                alert(data);
                $('#feedback_form').trigger('reset');
            },

            error  : function(errorThrown) {
                console.log(errorThrown);
            }
        });
    });
});