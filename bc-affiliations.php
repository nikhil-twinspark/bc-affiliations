<?php
/**
 * Plugin Name:       BC Affiliations
 * Plugin URI:        https://github.com/nikhil-twinspark/bc-affiliations
 * Description:       A simple plugin for creating custom post types for displaying affiliations.
 * Version:           1.0.0
 * Author:            Blue Corona
 * Author URI:        #
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bc-affiliations
 * Domain Path:       /languages
 */

 if ( ! defined( 'WPINC' ) ) {
     die;
 }

define( 'BC_AFFILIATION_VERSION', '1.0.0' );
define( 'BCAFFILIATIONDOMAIN', 'bc-affiliations' );
define( 'BCAFFILIATIONPATH', plugin_dir_path( __FILE__ ) );

require_once( BCAFFILIATIONPATH . '/post-types/register.php' );
add_action( 'init', 'bc_affiliation_register_affiliation_type' );

require_once( BCAFFILIATIONPATH . '/custom-fields/register.php' );

function bc_affiliation_rewrite_flush() {
    bc_affiliation_register_affiliation_type();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bc_affiliation_rewrite_flush' );

// plugin uninstallation
register_uninstall_hook( BCAFFILIATIONPATH, 'bc_affiliation_uninstall' );
function bc_affiliation_uninstall() {
    // Removes the directory not the data
}

// Add Conditionally css & js for specific pages
add_action('admin_enqueue_scripts', 'bc_affiliation_include_css_js');
function bc_affiliation_include_css_js($hook){
    $current_screen = get_current_screen();
    if ( $current_screen->post_type == 'bc_affiliations') {
        // Include CSS Libs
        wp_register_style('bc-affiliation-plugin-css', plugins_url('assests/css/bootstrap.min.css', __FILE__), array(), '1.0.0', 'all');
        wp_enqueue_style('bc-affiliation-plugin-css');

        wp_enqueue_script('bc-affiliation-image-upload-js', plugin_dir_url(__FILE__).'assests/js/bc-image-upload.js', array( 'jquery'));
    } 
}

add_shortcode( 'bc-affiliation', 'bc_affiliation_shortcode' );
function bc_affiliation_shortcode( $atts , $content = null ) {
    $args  = array( 'post_type' => 'bc_affiliations', 'posts_per_page' => -1, 'order'=> 'DESC','post_status'  => 'publish');

    if(isset($atts['withoutgrayscale']) && $atts['withoutgrayscale'] == '1' ) { ?>
        <style type="text/css">
        /*.bc_affliations_img {-webkit-filter: grayscale(0) !important;filter: grayscale(0)!important;}
        .bc_affliations_img:hover {-webkit-filter: grayscale(0) !important;filter: grayscale(0)!important;}*/

        .bc_affliations_img {
            filter: url("data:image/svg+xml;utf8,&lt;svg xmlns=\'http://www.w3.org/2000/svg\'&gt;&lt;filter id=\'grayscale\'&gt;&lt;feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/&gt;&lt;/filter&gt;&lt;/svg&gt;#grayscale"); /* Firefox 10+, Firefox on Android */
            filter: gray; /* IE6-9 */
            -webkit-filter: grayscale(0%) !important; /* Chrome 19+, Safari 6+, Safari 6+ iOS */
        }
        .bc_affliations_img:hover {
            filter: url("data:image/svg+xml;utf8,&lt;svg xmlns=\'http://www.w3.org/2000/svg\'&gt;&lt;filter id=\'grayscale\'&gt;&lt;feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/&gt;&lt;/filter&gt;&lt;/svg&gt;#grayscale"); /* Firefox 10+, Firefox on Android */
            filter: gray; /* IE6-9 */
            -webkit-filter: grayscale(0%) !important; /* Chrome 19+, Safari 6+, Safari 6+ iOS */
        }



        </style>
    <?php }
    $query = new WP_Query( $args );
        if ( $query->have_posts() ) :
        while($query->have_posts()) : $query->the_post();

        $name = get_post_meta( get_the_ID(), 'affiliation_name', true );
        $link = get_post_meta( get_the_ID(), 'affiliation_link', true );
        $image = get_post_meta( get_the_ID(), 'affiliation_custom_image', true );
    ?>
        <div class="swiper-slide">
            <div class='text-center'>
                <a href="<?= $link?>" target="_blank"><img class="img-fluid bc_affliations_img" alt="bbblogo" src="<?= $image;?>"></a>
            </div>
        </div>
    
    <?php
    endwhile; 
    wp_reset_query();
    endif;
    ?>

<?php }

// Admin notice for displaying shortcode on index page
add_action('admin_notices', 'bc_affiliation_general_admin_notice');
function bc_affiliation_general_admin_notice(){
    global $pagenow;
    global $post;
    if ($pagenow == 'edit.php' &&  (isset($post->post_type) ? $post->post_type : null) == 'bc_affiliations') { 
     echo '<div class="notice notice-success is-dismissible">
            <p><b>Shortcode Example</b> All : [bc-affiliation] Without Grayscale : [bc-affiliation withoutgrayscale="1"]</p>
        </div>';
    }
}
