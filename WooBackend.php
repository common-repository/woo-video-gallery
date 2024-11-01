<?php
namespace WooVideoGallery;

class WooBackend {

  /**
   * Creates tab in the product creation/edition page
   * attached to: woocommerce_product_write_panel_tabs action
   */
  public static function product_tab() {
    ?>
    <li class='video_gallery'>
      <a href='#video-tab'><?= __('Video gallery','video_gallery') ?></a>
    </li>
    <?php
  }

  /**
   * Print the admin panel content
   * attached to: woocommerce_product_write_panels action
   */
  public static function product_tab_content() {
    ?>
    <div id="video-tab" class="panel woocommerce_options_panel">
      <?php self::video_tab_form(array(
        'id'          => '_tab_video',
        'label'       => __('Embed Code','video_gallery'),
        'placeholder' => __('Place your embedded video code here.','video_gallery'),
        'style'       => 'width:70%;height:21.5em;'
      )); ?>
    </div>
    <?php
  }

  /**
   * Update post meta when the product is saved
   * attached to: woocommerce_process_product_meta action
   */
  public static function product_savedata($post_id, $post) {
	  
    $num_of_videos = $_POST['wo_di_num_of_videos'];
    $arrJson = array();
    update_post_meta($post_id, 'wo_di_num_of_videos', $num_of_videos);
    if ($num_of_videos > 0) {
      $videotypes = $_POST['wo_di_videotypes'];
      $videotitles = $_POST['wo_di_videotitles'];
     
      $videoUrl = $_POST['wo_di_videourl'];
     
      $videowidth = $_POST['wo_di_videowidths'];
      $videoheight = $_POST['wo_di_videoheights'];
      $videoactive = $_POST['wo_di_videoactive'];
      //Organize every video information to set the post meta
      foreach ($videotypes as $key => $types) {
        $arrJson[] = array(
          "type"     => $types,
          "title"    => $videotitles[$key],
          "width"    => $videowidth[$key],
          "height"   => $videoheight[$key],
         
          'url'      => $videoUrl[$key],
         
          "active"   => $videoactive[$key]
        );
      }
    }
    //encode data
    $Data = '5.4.0' <= phpversion() ? json_encode($arrJson, JSON_UNESCAPED_UNICODE) : json_encode($arrJson);
    update_post_meta($post_id, 'wo_di_video_productvideos', $Data);
    //update text of tinymce editor
    $mce_editorcontent = $_POST['wo_di_editormce_video'];
    update_post_meta($post_id, 'wo_di_editormce_video', $mce_editorcontent);
  }

  /**
   * Popups for add and edit videos.
   * attached to: admin_footer-post.php and admin_footer-post-new.php actions
   */
  public static function popups_addedit_video() {
    global $post;
    if($post->post_type == "product"):
      $placeholder = __('Place your embedded video code here.','video_gallery');
    
      ?>
      <?php //When adding a video ?>
      <div id="dialog_form_addvideo" title="<?= __("Add Video", 'video_gallery') ?>" style="display: none;">
        <form id="wo_di_form_addvideo" action="<?= admin_url( 'admin-ajax.php' )?>" method="post" onsubmit="return false;">
          <fieldset>
            <div id="div_errores_addvideo"></div>
            <div class="options_group">
                <label for="wo_di_videotitle">
                <?= __("Title: ","video_gallery") ?>
              </label>
           <input class="wo_di_form_input" id="wo_di_videotitle" type="text"  value="" name="wo_di_videotitle" style="margin-left: 21px;">
           </div>
            <?php
            if (get_option('wo_di_video_sizeforcing') == 1) {
              $width = "value='" . get_option('wo_di_config_videowidth') . "' readonly";
              $height = "value='" . get_option('wo_di_config_videoheight') . "' readonly";
            }
            else {
              $width = "";
              $height = "";
            }
            ?>
         
            <div class="options_group">
              <label>
                <?= __("Source:","video_gallery") ?>
              </label>
              <input class="radio" id="wo_di_videooembed" type="radio" value="oembed" name="wo_di_tipovideo" checked="checked">
              <label class="radio" for="wo_di_videooembed">
                <?php echo __('Youtube/Vimeo Url', 'video_gallery') ?>
              </label>        
            </div>
            <hr/>
            <div class="video_option oembed_video">
              <p>
                <?= __('Type the URL of your video, supports URLs of videos in websites like Youtube or Vimeo.', 'video_gallery')?>
              </p>
              <input class="wo_di_form_input" type="url" id="video_texturl" name="video_texturl" value="">
            </div>  
          </fieldset>
        </form>
      </div>

      <?php //When editing a video ?>
      <div id="dialog_form_editvideo" title="<?= __("Edit Video", 'video_gallery') ?>" style="display: none;">
        <form id="wo_di_form_editvideo" action="<?= admin_url( 'admin-ajax.php' )?>" onsubmit="return false;" method="post">
          <fieldset>
            <div class="options_group">
             <label for="wo_di_video_titleedit">
                <?= __("Title: ","video_gallery") ?>
              </label>
              <input class="wo_di_form_input" id="wo_di_video_titleedit" type="text"  value="" name="wo_di_video_titleedit" style="margin-left: 21px;" />
            </div>
            <?php
            if (get_option('wo_di_video_sizeforcing') == 1) {
              $width = "value='" . get_option('wo_di_config_videowidth') . "' readonly";
              $height = "value='" . get_option('wo_di_config_videoheight') . "' readonly";
            }
            else {
              $width = "";
              $height = "";
            }
            ?>
         <!--   <div class="options_group">
              <label for="width_video_wooedit">
                <?//= __("Width","video_gallery")?>:
              </label>
              <input type="text" id="width_video_wooedit" name="width_video_wooedit" placeholder="<?//= get_option('wo_di_config_videowidth'); ?>" <?//= $width ?> class="dimension-input">
              <label for="height_video_wooedit">
                <?php //echo __("Height","video_gallery")?>:
              </label>
              <input type="text" id="height_video_wooedit" name="height_video_wooedit" placeholder="<?//= get_option('wo_di_config_videoheight'); ?>" <?//= $height ?> class="dimension-input">
            </div> -->
            <div class="options_group">
              <label>
                <?= __("Source:","video_gallery") ?>
              </label>
              <input class="radio" id="wo_di_video_oembededit" type="radio" value="oembed" name="wo_di_tipo_videoedit">
              <label class="radio" for="wo_di_video_oembededit">
                <?php echo __('Youtube/Vimeo Url', 'video_gallery') ?>
              </label> 
            </div>
            <hr/>
            <div class="video_option oembed_video" hidden>
              <p>
                <?= __('Type the URL of your video, supports URLs of videos in websites like Youtube or Vimeo.', 'video_gallery')?>
              </p>
              <input class="wo_di_form_input" type="url" id="video_texturl_edit" name="video_texturl_edit" value="">
            </div>
          </fieldset>
        </form>
      </div>

      <div id="dialog_previewvideo" title="<?= __("Preview Video", 'video_gallery') ?> ">
        <div id="contenedorvideo"></div> 
      </div> 
    <?php
    endif;
  }

  public static function oembed_video() {
	  
	  
    $videoUrl = isset($_POST['video_url'])? $_POST['video_url'] : '';
    $height = get_option('wo_di_config_videoheight');
    $height = (isset($_POST['height']) && !empty($_POST['height']))? $_POST['height'] : $height;
    $width = get_option('wo_di_config_videowidth');
    $width = (isset($_POST['width']) && !empty($_POST['width']))? $_POST['width'] : $width;
    global $wp_embed;
    if (isset($_POST['post_id']) && 0 != $_POST['post_id']) {
      global $post;
      $post =  get_post($_POST['post_id']);
      echo $wp_embed->run_shortcode("[embed width='{$width}' height='{$height}']{$videoUrl}[/embed]");
    }
    else {
      echo '';
    }
    wp_die();
  }
/******************************************************************************/
/*                                  Auxiliar                                  */
/******************************************************************************/

  /*
   * Build the admin panel content
   */
  private static function video_tab_form($field) {
    //$thepostid is created by woocommerce
    global $thepostid, $post;
    ?>
    <script type="text/javascript">
      var text_add_button = "<?= __('Add','video_gallery'); ?>";
      var text_edit_button = "<?= __('Edit','video_gallery'); ?>";
      var text_cancel_button = "<?= __('Cancel','video_gallery'); ?>";
      var text_close_button = "<?= __('Close','video_gallery'); ?>";
      var text_error_min_html = "<?= __('At least one video is required','video_gallery'); ?>";
      var text_error_insert_html = "<?= __('Embedded code is required','video_gallery'); ?>";
      var text_error_id = "<?= __('The name is required','video_gallery'); ?>";
      var text_error_dimension = "<?= __('height and width of the video is required','video_gallery'); ?>";
    </script>
    <?php

    if (!is_int($thepostid))
      $thepostid = $post->ID;
    if (!isset($field['placeholder'])) {
      $field['placeholder'] = '';
    }

 
    ?>
    
    <?php
    $num_of_videos = get_post_meta($thepostid, 'wo_di_num_of_videos', true);
    $tableBody = '';
    if (empty($num_of_videos)):
      $num_of_videos = 0;
    else:
      $videos = json_decode(get_post_meta($thepostid, 'wo_di_video_productvideos', true));
      //Set every video information
      for($i = 0; $i < $num_of_videos; $i++):
        $video = $videos[$i];
        $title = $video->title;
        $types = $video->type;
        $videoEmbebido = '';
        $videoMp4 = '';
        $videoOGG = '';
        $videoUrl = '';
        $height = $video->height;
        $width = $video->width;
        if($height == '' && $width == '') {
          $dimension = 'Default';
          $width = get_option('wo_di_config_videowidth');
          $height = get_option('wo_di_config_videoheight');
        }
        else {
          $dimension = $height .' X ' . $width;
        }

        $class = "class=''";
        $disable_iframe = get_option('wo_di_video_disableiframe');
        switch ($types) {
         
          case 'oEmbed':
            $videoUrl = $video->url;
            $dimension = '-';
            $formats = '-';
            break;
        }
        $checked = "";
        if ($video->active == 1) {
         $checked = "checked='checked'";
        }
        global $wp_embed;
        //Construct row for each video
        ob_start();
        ?>
        <tr id='wo_di_videoproduct_<?= $i ?>' <?= $class ?>>
          <td style="width:20px;">
            <span class='sort-button dashicons dashicons-sort' style="font-size:18px;" title="move"></span>
          </td>
          <td>
            <input type=hidden name='wo_di_videotitles[]' value='<?= $title ?>' />
            <span><?= $title ?></span>
          </td> 
          <td>
            <input type=hidden name='wo_di_videotypes[]' value='<?= $types ?>' />
            <span><?= $types ?></span>
          </td>
          <td>
            <input type=hidden name='wo_di_videoformats[]' value='<?= $formats ?>' />
            <span><?= $formats ?></span>
          </td>
     
        
          <input type=hidden name='wo_di_videourl[]' value='<?= $videoUrl ?>' />
          <input type=hidden name='wo_Oembed[]' value='<?= $wp_embed->run_shortcode("[embed width='{$width}' height='{$height}']{$videoUrl}[/embed]") ?>' />

          <td>
            <input type=hidden name='wo_di_videoactive[]' value='<?= $video->active ?>' />
            <input type='checkbox' value='active' <?php echo $checked; ?> onchange='update_inputactive(this)'/>
          </td>
          <td>
            <?php if ($types != "Embedded" || ($types == "Embedded" && $disable_iframe == 0)): ?>
              <span class='action-button dashicons dashicons-search float-right' onclick='previewvideo(this)' title='preview'></span>
              <span class='action-button dashicons dashicons-edit float-right' onclick='editrow(this)' title='edit'></span>
              <span class='action-button dashicons dashicons-trash float-right' onclick='deleterow(this)' title='delete'></span>
            <?php elseif ($types == "Embedded" && $disable_iframe == 1): ?>
              <span class='ui-icon ui-icon-circle-zoomout float-right' onclick='previewvideo(this)' style='visibility:hidden;'></span>
              <span class='ui-icon ui-icon-pencil float-right' onclick='editrow(this)'  style='visibility:hidden;'></span>
              <span class='ui-icon ui-icon-trash float-right' onclick='deleterow(this)'></span>
            <?php endif;?>
          </td>
        </tr>
        <?php
        $tableBody .= ob_get_contents();
        ob_end_clean();
      endfor;
    endif;
    //Print table with all the videos for the current product
    ?> 
    
    <div class='options_group'>
      <h4 class='wohvtitle'><?= __("Attached videos") ?></h4>
      <input id='wo_di_num_of_videos' name='wo_di_num_of_videos' type='hidden' value='<?= $num_of_videos ?>'/>
      <table id="wo_di_table_videoshtml" class="wp-list-table widefat wo_di_table_videos">
        <thead>
          <tr>
            <th></th>
           <th><?= __('Title', 'video_gallery') ?></th> 
            <th><?= __('Type', 'video_gallery') ?></th>
            <th><?= __('Formats', 'video_gallery') ?></th>
         
            <th><?= __('Active', 'video_gallery') ?></th>
            <th><?= __('Actions', 'video_gallery') ?></th>
          </tr>
        </thead>
        <tbody id="table-videosortable">
         <?= $tableBody ?>
        </tbody>
      </table>
      <button id="button_addvideo"><?= __("Add", 'video_gallery') ?></button>
    </div>
    <?php
    //Product description, this is part of the woocommerce.
    if (isset($field['description']) && $field['description']) {
      ?>
      <span class="description"><?= $field['description'] ?></span>
      <?php
    }
  }
}
