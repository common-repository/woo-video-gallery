<?php

namespace WooVideoGallery;

class WooFrontend {

  const VIDEO_WIDTH = '100%';


   
  public static function video_product_tabs( $tabs ) {
	


    global $post, $product;

    $tab_option = get_option('wo_di_config_video_tabposition');
    $index = (($tab_option == false && $tab_option != 0) || $tab_option == "") ? 1 : $tab_option;

    $aux = array();

    foreach ($tabs as $key => $row)
      $aux[$key] = $row['priority'];

    array_multisort($aux, SORT_ASC, $tabs);

 

    $acum_priority = 0;
    $priority_video = 0;
    $current = 0;

    foreach ($tabs as $key => $row) {
      if ($current != $index) {
        $tabs[$key]["priority"] = $acum_priority;
      }
      else {
        $tabs[$key]["priority"] = $acum_priority + 5;
        $priority_video = $acum_priority;
        $acum_priority += 5;
      }

      $acum_priority += 5;
      $current++;
    }

    if($index >= $current)
      $priority_video = $acum_priority; 

   
    return $tabs;
  }

}
?>


<?php global $product;
$videos = json_decode(get_post_meta($product->id, 'wo_di_video_productvideos', true));
 $width_config = get_option('wo_di_config_videowidth');
        //$height_configs = get_option('wo_di_config_videoheight');
        //$height_config = $height_configs * 0.5;
      if($width_config == 0 ) {
		  $width_config = '450';
		 // $height_config = '200';
		  }
if(!$videos == 0){


	?> 

	<div class="umain">
	<div class="sub" style="width:<?php echo $width_config; ?>px;">
		<?php global $product;
      $videos = json_decode(get_post_meta($product->id, 'wo_di_video_productvideos', true));
     $test = $videos[0];
  $test1 = $test->width;
 // $test2 = $test->height;
 
     $testurl = $test->url;
     
     $width_config = get_option('wo_di_config_videowidth');
     //print_r($width_config);
  if (!empty($width_config) && $width_config != 0) {
                  $width = $width_config;
                }
                else {//REVIEW
                  $width = '450';
                }
		  if($test->type == oEmbed) {
	 global $wp_embed;
	 if($test->active == 1) { 
		 
		echo $wp_embed->run_shortcode('[embed width="' . $width . '"]' . $testurl . '[/embed]');         
            }    	  
		
		} 
				  ?>
	</div>
	</div>

 <div id="content">
	  <div href="#" class="yvgallery__controls-prev">
                   <img src="<?php echo  plugins_url('images/prev.png', __FILE__); ?>" alt="" />
                </div>
<div class="yvgallery-wrap">
	<div class="yvgallery clearfix">
	<?php
	
//custom video:
	global $product;
      $videos = json_decode(get_post_meta($product->id, 'wo_di_video_productvideos', true));
            $i=0;
         
              foreach ($videos as $video){	
             
           if ($video->active == 1):
            if (!empty($video->title) && ($video->type != "Embedded" ||
                ($video->type == "Embedded" && $disable_iframe == 0))) {
              ?>
              <h3><?//= $video->title ?></h3>
              <?php
		  }
		  endif;
		  ?>
		  <?php if($video->active == 1) { ?>
		  <div class="yvgallery__item" id="dv<?php echo $i; ?>"> 
        <?php 
            
	 if ($video->type == oEmbed) {
		  
                global $wp_embed;
$video = $video->url;
if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $match)) {
    $image = $match[1];

?>
<img  style="height:70px;width:80px;" class="yvgallery__img"  src="http://img.youtube.com/vi/<?php echo $image; ?>/0.jpg" />
<?php }else {?>
<?php

$pieces = explode("/", $video);
$id = end($pieces);

?>
<img style="height:70px;width:80px;" class="yvgallery__img"  src="https://i.vimeocdn.com/video/<?php echo $id; ?>.jpg" /> <?php } ?>
<?php }?>
            </div>
          <?php } ?>
	<?php  $i++; }

//custom video:
?>
</div>
</div>
<div href="#" class="yvgallery__controls-next">
                   <img src="<?php echo  plugins_url('images/next.png', __FILE__); ?>" alt="" />
                </div>
</div>

<?php } ?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
	
    // Only run everything once the page has completely loaded
    $(window).load(function(){

        // Set general variables
        // ====================================================================
        var totalWidth = 0;

        // Total width is calculated by looping through each yvgallery item and
        // adding up each width and storing that in `totalWidth`
        $(".yvgallery__item").each(function(){
            totalWidth = totalWidth + $(this).outerWidth(true);
        });

        // The maxScrollPosition is the furthest point the items should
        // ever scroll to. We always want the viewport to be full of images.
        var maxScrollPosition = totalWidth - $(".yvgallery-wrap").outerWidth();

        // This is the core function that animates to the target item
        // ====================================================================
        function toGalleryItem($targetItem){
            // Make sure the target item exists, otherwise do nothing
            if($targetItem.length){

                // The new position is just to the left of the targetItem
                var newPosition = $targetItem.position().left;

                // If the new position isn't greater than the maximum width
                if(newPosition <= maxScrollPosition){

                    // Add active class to the target item
                    $targetItem.addClass("yvgallery__item--active");

                    // Remove the Active class from all other items
                    $targetItem.siblings().removeClass("yvgallery__item--active");

                    // Animate .yvgallery element to the correct left position.
                    $(".yvgallery").animate({
                        left : - newPosition
                    });
                } else {
                    // Animate .yvgallery element to the correct left position.
                    $(".yvgallery").animate({
                        left : - maxScrollPosition
                    });
                };
            };
        };

        // Basic HTML manipulation
        // ====================================================================
        // Set the yvgallery width to the totalWidth. This allows all items to
        // be on one line.
        $(".yvgallery").width(totalWidth);

        // Add active class to the first yvgallery item
        $(".yvgallery__item:first").addClass("yvgallery__item--active");

        // When the prev button is clicked
        // ====================================================================
        $(".yvgallery__controls-prev").click(function(){
            // Set target item to the item before the active item
            var $targetItem = $(".yvgallery__item--active").prev();
            toGalleryItem($targetItem);
        });

        // When the next button is clicked
        // ====================================================================
        $(".yvgallery__controls-next").click(function(){
            // Set target item to the item after the active item
            var $targetItem = $(".yvgallery__item--active").next();
            toGalleryItem($targetItem);
        });
    });
    </script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<?php
 global $product;
  $videos = json_decode(get_post_meta($product->id, 'wo_di_video_productvideos', true));
  $i=0;
  $width_config = get_option('wo_di_config_videowidth');
        //$height_config = get_option('wo_di_config_videoheight');
        /*$disable_iframe = get_option('wo_di_video_disable_iframe');
        $size_forcing = get_option('wo_di_video_size_forcing');*/
        
            $width = 0;
            //$height = 0;

          if (!empty($width_config) && $width_config != 0) {
                  $width = $width_config;
                }
                else {//REVIEW
                  $width = '450';
                }
    
                
  foreach ($videos as $video){	 
	global $wp_embed;
	if($video->type == oEmbed){
		$url = url;
		}
	elseif($video->type == Embedded)
		{ $url = embebido; }

	?>
<script>
	
jQuery(document).ready(function(){
	
		jQuery('#dv<?php echo $i; ?>').click(function(){
			jQuery(".sub").show();			
            jQuery(".sub").html('<?php echo $wp_embed->run_shortcode('[embed width="' . $width . '"]' . $video->$url .'[/embed]'); ?>');   
	
    });
	
});
 
</script>
<?php  $i++; } ?>

<style>
/*
    css-plus by Jamy Golden
*/
#content {margin-top:20px;}
.yvgallery-wrap { margin: 0 auto; overflow: hidden; width: 365px; display:block;float: left;margin:0 26px;margin-bottom: 16px;}
.yvgallery { position: relative; left: 0; top: 0; display: inline-flex;}
.yvgallery__item { float: left; list-style: none; margin-right: 3px; display:inline;width:90px}
.yvgallery__img { display: block; border: 4px solid #40331b; height: 155px; width:auto; }
.yvgallery__item #dv5{margin:0}
.yvgallery__controls { margin-top: 10px; clear:both;}
.yvgallery__controls-prev { cursor: pointer; float:left; margin-top:5% ; margin-left:1%}
.yvgallery__controls-next { cursor: pointer;float:left; margin-top:4.4% }

/*
    For clearfix information visit:
    http://nicolasgallagher.com/micro-clearfix-hack/
*/
.clearfix:before, .clearfix:after { content: " "; display: table; }
.clearfix:after { clear: both; }
.clearfix { *zoom: 1;}
.sub {overflow:hidden;margin-left:5px;}
.player .title header .headers h1{display:none !important}

</style> 

