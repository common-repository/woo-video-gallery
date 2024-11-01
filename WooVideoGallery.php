<?php
/**
 * Plugin Name: Woo Video Gallery 
 * Plugin URI: 
 * Description: Include video gallery in product pages of your WooCommerce online store. It supports embedded videos from websites like youtube or vimeo.
 * Author: Lujayn Infoways
 * Author URI: http://www.lujayninfoways.com/
 * Version: 1.0.5
 * License: GPLv2 or later
 */
namespace WooVideoGallery;

defined('ABSPATH') or die("No script kiddies please!");
include 'autoload.php';

class WooVideoGallery {

  public static function init() {
    //Activation / Deactivation hooks
    register_activation_hook(__FILE__, array(__CLASS__, 'check_plugin_activated'));
    register_uninstall_hook(__FILE__, array(__CLASS__, 'unInstall'));

    //Verify dependencies
    add_action('admin_init', array(__CLASS__, 'check_plugin_activated'));
    add_action('wp_head', array(__CLASS__, 'print_ajax_url'));

    //Woocommerce integration
    add_action('woocommerce_init', array(__CLASS__, 'init_woo_integration'));
	add_action('admin_footer-post.php',
      array('\\WooVideoGallery\\WooBackend', 'popups_addedit_video'));
    add_action('admin_footer-post-new.php',
      array('\\WooVideoGallery\\WooBackend', 'popups_addedit_video'));
    add_action('wp_ajax_oembed_video',
      array('\\WooVideoGallery\\WooBackend', 'oembed_video'));

    //Settings
    add_action('admin_init',
      array('\\WooVideoGallery\\Setting', 'register_my_settings'));
    add_action('admin_menu',
      array('\\WooVideoGallery\\Setting', 'setting_page'));
    $plugin = plugin_basename( __FILE__ );
    add_filter("plugin_action_links_$plugin",
      array('\\WooVideoGallery\\Setting', 'add_setting_link'));
    add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_script'));
    add_action('plugins_loaded', array(__CLASS__, 'plugin_textdomain'));

    add_action('admin_notices', array(__CLASS__, 'review_notice'));
    add_action('wp_ajax_save_review', array(__CLASS__, 'save_review'));
  }

/******************************************************************************/
/*           Activation / Deactivation hooks. Verify dependencies             */
/******************************************************************************/

  public static function init_woo_integration() {
    $version = get_option('wo_di_configversion');
    if ($version == false) {
      self::activate_plugin();
    }
    // backend stuff
    add_action('woocommerce_product_write_panel_tabs',
      array('\\WooVideoGallery\\WooBackend', 'product_tab'));
    add_action('woocommerce_product_write_panels',
      array('\\WooVideoGallery\\WooBackend', 'product_tab_content'));
    add_action('woocommerce_process_product_meta',
      array('\\WooVideoGallery\\WooBackend', 'product_savedata'), 10, 2);

    // frontend stuff
    add_filter('woocommerce_product_tabs',
      array('\\WooVideoGallery\\WooFrontend','video_product_tabs'), 25);
    add_action('woocommerce_product_tab_panels',
     array('\\WooVideoGallery\\WooFrontend', 'video_product_tabs_panel'), 25);
  }

  /**
   * Check woocommerce dependency
   */
  public static function check_plugin_activated() {
    $plugin = is_plugin_active("woocommerce/woocommerce.php");
    if (!$plugin) {
      deactivate_plugins(plugin_basename(__FILE__));
      add_action('admin_notices', array(__CLASS__, 'disabled_notice'));
      if (isset($_GET['activate']))
        unset($_GET['activate']);
    }
    else {
      self::activate_plugin();
    }
  }

  public static function print_ajax_url() {
    ?>
    <script>
      var ajaxurl = '<?= admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
  }

  /**
   * Things to do when the plugin is activated
   */
  private static function activate_plugin() {
    $version = get_option('wo_di_configversion');

    //Update database to version 2.0 if necessary
    if(is_bool($version) && $version == false):
      $products = get_posts(array(
        'post_type'      => array('product', 'product_variation'),
        'posts_per_page' => -1,
        'fields'         => 'ids'
      ));
	
      foreach ($products as $id):
        $video_type = get_post_meta($id, 'wo_di_video_type', true);
        $arrJson = array();
        $size = 0;
       
        //Update new options
        update_post_meta($id, 'wo_di_num_of_videos', $size);
        update_post_meta($id, 'wo_di_video_productvideos', json_encode($arrJson));

        //Delete old options
        delete_post_meta($id, 'wo_di_video_type');
        for ($i = 0; $i <= 2; $i++) {
          delete_post_meta($id, 'wo_di_video_product'.$i);
        }
        delete_post_meta($id, 'wo_di_video_product_html5');
        delete_post_meta($id, 'heightvideo_woo');
        delete_post_meta($id, 'widthvideo_woo');
      endforeach;
      
      delete_option( 'videoheight' );
      delete_option( 'videowidth' );
    endif;
    update_option('wo_di_configversion', 2);
  }

  /**
   * Message information when the plugin was deactivated
   */
  public static function disabled_notice() {
    global $current_screen;
    if ($current_screen->parent_base == 'plugins'):
      ?>
      <div class="error" style="padding: 8px 8px;">
        <strong>
          <?= __('Woo Video Gallery requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> activated in order to work. Please install and activate <a href="' . admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce') . '" target="_blank">WooCommerce</a> first.','video_gallery') ?>
        </strong>
      </div>
      <?php
    endif;
  }

  /**
   * Things to do when the plugin is deactivated
   */
  public static function unInstall() {
    $products = get_posts(array(
      'post_type'      => array('product', 'product_variation'),
      'posts_per_page' => -1,
      'fields'         => 'ids'
    ));
    //Delete post meta related with the plugin
    foreach ($products as $id) {
      delete_post_meta($id, 'wo_di_video_productvideos');
      delete_post_meta($id, 'wo_di_num_of_videos');
      delete_post_meta($id, 'wo_di_editormce_video');
      delete_option('wo_di_configversion');
      delete_option('wo_di_video_hidetab');
      delete_option('wo_di_config_videoheight');
      delete_option('wo_di_config_videowidth');
      delete_option('wo_di_config_video_tabname');
      delete_option('wo_di_video_sizeforcing');
      delete_option('wo_di_config_video_tabposition');
    }
  }

/******************************************************************************/
/*                               Other settings                               */
/******************************************************************************/

  public static function admin_script($hook) {
    wp_register_script('admin-notice', plugins_url('js/admin_notice.js', __FILE__), array('jquery'));
    wp_enqueue_script('admin-notice');

    //check if a product page is displayed (creation or edition)
    global $post;
    if(empty($post->post_type) || 'product' != $post->post_type || ($hook != 'post.php' && $hook != 'post-new.php'))
      return;

    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script("jquery-ui-dialog");
    wp_enqueue_script("jquery-ui-sortable");
    wp_enqueue_script('tiny_mce');

    wp_register_script('jquery-validates', plugins_url('js/jquery.validates.min.js', __FILE__), array('jquery'));
    wp_enqueue_script('jquery-validates');

    wp_enqueue_media();

    wp_register_style('woohv-style', plugins_url('css/my_style.css', __FILE__));
    wp_enqueue_style('woohv-style' );

    wp_register_script('woohv-script', plugins_url('js/js-scripts.js', __FILE__), array('jquery'));
    wp_enqueue_script('woohv-script' );

    wp_register_script('my-uploads', plugins_url('js/button_action.js', __FILE__), array('jquery', 'media-upload', 'thickbox'));
    wp_enqueue_script('my-uploads');
  }

  /**
   * Set up localization
   */
  public static function plugin_textdomain() {
    load_plugin_textdomain(
      'video_gallery',
      false,
      dirname(plugin_basename(__FILE__)) . '/languages'
    );
  }

  /**
   * Print admin notice to ask for plugin review
   */
  public static function review_notice() {
    //verify option to check if user already dismiss or post the review
    $userId = get_current_user_id();
    $meta = get_user_meta($userId, 'woo_review', true);
    if (empty($meta) || false == $meta): ?>
      <div id="review-notice" class="notice notice-info">
        <p>
          Help others to make good choices when they are seeking for plugins, please add a review in Woo Video Gallery and help us to create confidence in more people.
        </p>
        <p>
          <a id="post-review" href="https://wordpress.org/support/view/plugin-reviews/woo-video-gallery#postform" class="button-primary" target="_blank">Post review</a>
          <a id="skip-review" class="button-secondary" href="">Dismiss</a>
        </p>
      </div>
    <?php endif;
  }

  /**
   * Save that current user already made a review or doesn't want to make it
   */
  public static function save_review() {
    $userId = get_current_user_id();
    update_user_meta($userId, 'woo_review', true);
  }
}

WooVideoGallery::init();
