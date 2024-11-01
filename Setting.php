<?php
namespace WooVideoGallery;

class Setting {
  /**
   * Function to register plugin settings
   * attached to: admin_init action
   */
  public static function register_my_settings() {
    register_setting( 'dimension_group', 'wo_di_config_videowidth', 'intval' );
    //register_setting( 'dimension_group', 'wo_di_config_videoheight', 'intval' );
   /* register_setting( 'dimension_group', 'wo_di_config_video_tab_name' );
    register_setting( 'dimension_group', 'wo_di_config_video_tab_position', 'intval' );
    register_setting( 'dimension_group', 'wo_di_video_hide_tab', 'intval' );
    register_setting( 'dimension_group', 'wo_di_video_size_forcing', 'intval' );
    register_setting( 'dimension_group', 'wo_di_video_disable_iframe', 'intval' );
    register_setting( 'dimension_group', 'wo_di_config_video_description', 'intval' );*/
  }

  /**
   * Function to add a plugin configuration page
   * attached to: admin_menu action
   */
  public static function setting_page() {
    add_options_page(
      'Woo Video Gallery Settings',
      'Woo Video Gallery',
      'manage_options',
      'video-gallery-settings',
      array(__CLASS__, 'setting_page_content')
    );
  }

  /**
   * Function to add settngs link in plugins page
   * attached to: plugin_action_links_<plugin> filter
   */
  public static function add_setting_link( $links ) {
    ob_start();
    ?>
    <a href="options-general.php?page=video-gallery-settings">Settings</a>
    <?php
    $settings_link = ob_get_contents();
    ob_end_clean();
    array_push( $links, $settings_link );
    ob_start();
    ?>
    <?php
 
    ob_end_clean();
    array_push( $links);
    return $links;
  }

  /**
   * Function to create the content of the configuration page
   * callback in: settings_page
   */
  public static function setting_page_content() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
      <?php screen_icon(); ?>
      <h2>Woo Video Gallery Settings</h2>
      <form class="video_gallery" method="post" action="options.php" onsubmit="return check_form_settings();">
        <?php
        settings_fields('dimension_group');
        do_settings_fields('dimension_group','video-gallery-settings')
        ?>
        <p>
          <strong>
            <?= __('Configure the default video dimensions')?>:
          </strong>
        </p>
        <table class="form-table">
          
            <th scope="row">
              <?= __('Width')?>:
            </th>
            <td>
				<input type="text" name="wo_di_config_videowidth" id="wo_di_config_videowidth" value="<?= get_option('wo_di_config_videowidth'); ?>" />
         <?php echo "Note:Please put width more than 450 for better view!"; ?>
            </td>
            
          </tr>
          
          
        </table>
        <span id="span_errors"></span>
        <?php submit_button(); ?>
        
      </form>

     
    </div>
   <?php
  }
}
