jQuery(document).ready(function($){


    var media_modal; //variable to handle the media library frame

    $('#upload_img').click(function(e){
        e.preventDefault();
        alert('hello');

        if( media_modal ) {
            media_modal.open();
            return;
        }

        media_modal = wp.media.frames.media_modal = wp.media({
            title : 'Upload an Image',
            button : { text : 'Select' },
            library : { type : 'image' },
        });

        media_modal.on( 'select', function(){

            var attachment  = media_modal.state().get('selection').first().toJSON();
            var img =  attachment.sizes.thumbnail || attachment.sizes.medium || attachment.sizes.full;
            console.log(attachment);
            console.log(img);
            $('#custom_image_field').val( attachment.url );
            $('#img_thumbnail').find('img').attr( 'src', img.url );
        });

        

         media_modal.open();
    });

    $(document).on('click', '#remove_img', function(e){
        alert('hey');
        $('#custom_image_field').val('');
        $('#img_thumbnail').find('img').attr( 'src', '' );
    });
});