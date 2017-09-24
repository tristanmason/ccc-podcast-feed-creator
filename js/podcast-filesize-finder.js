// Ajax get the file size of podcast on button click

jQuery( document ).ready( function( $ ) {
	$('#findSize').click(function() {

		var mp3url = $( '#wpv-sermon-audio' ).val();

      		$.ajax({
        		method: "POST",
        		url: ajaxurl,
        		data: { 'action': 'podcast_filesize_approval_action', 'mp3url': mp3url }
      		})

      		.done(function( data ) {
        		$('#filesizeField').val(data);
      		})

      		.fail(function( data ) {
			$('#filesizeField').val("error");
      		});

	});
} );