<?php
/**
 * Plugin Name: Christ Community Podcast Feed Creator
 * Plugin URI: http://tristanmason.com
 * Description: Creates an iTunes-friendly and Google Play-friendly podcast feed from the Sermons posts. Adds Podcast meta box with duration & file size to the Sermons custom post type. The podcast feed is published at /feed/podcast
 * Version: 1.0.0
 * Author: Tristan Mason
 * Author URI: http://tristanmason.com
 * License: GPL2
 */

// Add the podcast RSS feed at /feed/podcast

add_action('init', 'podcast_rss');
function podcast_rss(){
  add_feed('podcast', 'podcast_rss_func');
}

// Use the RSS template podcast-rss-template.php in this plugin's folder

function podcast_rss_func(){
  require_once( dirname( __FILE__ ) . '/podcast-rss-template.php' );
}

// Add post excerpt support to the Vamtam sermon custom post type

function wpcodex_add_excerpt_support_for_cpt() {
 add_post_type_support( 'wpv_sermon', 'excerpt' );
}
add_action( 'init', 'wpcodex_add_excerpt_support_for_cpt' );

// Enqueue the filesize finder js only on sermons post editor

function wpse_cpt_enqueue( $hook_suffix ){
    $cpt = 'wpv_sermon';

    if( in_array($hook_suffix, array('post.php', 'post-new.php') ) ){
        $screen = get_current_screen();

        if( is_object( $screen ) && $cpt == $screen->post_type ){
            wp_enqueue_script( 'podcast_filesize_js', plugin_dir_url( __FILE__ ) . 'js/podcast-filesize-finder.js', array('jquery'), '1.0.0', true );
        }
    }
}

add_action( 'admin_enqueue_scripts', 'wpse_cpt_enqueue');

// Receive AJAX request and respond with file size

function podcast_filesize_ajax_handler() {
  	$mp3url = $_POST['mp3url'];
	$path = parse_url($mp3url, PHP_URL_PATH);
	$mp3Location = '/home/aplaceto/www/www' . $path;
	$mp3Size = filesize($mp3Location);
	echo $mp3Size;
 	wp_die();
}
add_action( 'wp_ajax_podcast_filesize_approval_action', 'podcast_filesize_ajax_handler' );

// Add custom meta box for podcast file size & duration

function podcast_add_meta_box( $post ){
	add_meta_box( 'podcast_meta_box', __( 'Podcast', 'podcast_meta_plugin' ), 'podcast_meta_box_fn', 'wpv_sermon', 'side', 'low' );
}
add_action( 'add_meta_boxes_wpv_sermon', 'podcast_add_meta_box' );

// Callback function for meta box

function podcast_meta_box_fn( $post ){
	// Nonce for security
	wp_nonce_field( basename( __FILE__ ), 'podcast_meta_box_nonce' );

	// retrieve the _mp3_file_size current value
	$mp3_size = get_post_meta( $post->ID, '_mp3_file_size', true );

	// retrieve the _mp3_duration current value
	$mp3_duration = get_post_meta( $post->ID, '_mp3_duration', true );

	// Markup
	?>

	<div class='inside'>
		<p>
			<label for="filesize"><?php _e( '<strong>File Size</strong> (ex: 12745985)', 'podcast_meta_box_fn' ); ?></label>
		</p>
		<input type="text" id="filesizeField" name="filesize" value="<?php echo $mp3_size; ?>" /><button type="button" id="findSize">Find</button><br /><span style="font-size:10px;">"Find" uses the Sermon Options Audio Link</a></span>

		<p>
			<label for="duration"><?php _e( '<strong>Duration</strong> (ex: 32:45)', 'podcast_meta_box_fn' ); ?></label>
		</p>
		<input type="text" name="duration" value="<?php echo $mp3_duration; ?>" />

	</div>

	<?php
}

// Save podcast meta box data when saving post

function podcast_save_meta_boxes_data( $post_id ){
	// verify meta box nonce
	if ( !isset( $_POST['podcast_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['podcast_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	// Store filesize string
	if ( isset( $_REQUEST['filesize'] ) ) {
		update_post_meta( $post_id, '_mp3_file_size', sanitize_text_field( $_POST['filesize'] ) );
	}

	// Store duration string
	if ( isset( $_REQUEST['duration'] ) ) {
		update_post_meta( $post_id, '_mp3_duration', sanitize_text_field( $_POST['duration'] ) );
	}
}
add_action( 'save_post_wpv_sermon', 'podcast_save_meta_boxes_data', 10, 2 );

?>