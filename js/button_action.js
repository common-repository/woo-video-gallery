
var text_add_button;
var text_edit_button;
var text_cancel_button;
var text_close_button;
var text_error_min_html;
var text_error_insert_html;
var text_error_id;
var text_error_dimension;
var type_of_action="";
var tr_edit;
var media_uploader = null;
var add_flag;

function update_inputactive(obj){
  var input=jQuery(obj.parentNode).find("input[name='wo_di_videoactive[]']");
  if(jQuery(obj).is(":checked")){
    jQuery(input).val(1);
  }else{
    jQuery(input).val(0);
  }
}

function clean_inputs_edit(){
  jQuery("#wo_di_video_titleedit").val("");
  jQuery("#height_video_wooedit").val("");
  jQuery("#width_video_wooedit").val("");
  jQuery("#video_texturl_edit").val("");
  jQuery("#_checkbox_url_edit").attr('checked', false);
}

function deleterow(obj){
  var i=obj.parentNode.parentNode;
  jQuery(obj.parentNode.parentNode).remove();
  var num_of_videos=jQuery("#wo_di_num_of_videos").val();
  num_of_videos--;
  jQuery("#wo_di_num_of_videos").val(num_of_videos);
  //jQuery("#wo_di_table_videoshtml").deleteRow(i);
}

function editrow(obj){
  clean_inputs_edit();
  tr_edit=obj.parentNode.parentNode;

  var type=jQuery(tr_edit).find("input[name='wo_di_videotypes[]']").val();
  var title=jQuery(tr_edit).find("input[name='wo_di_videotitles[]']").val();
  var id=jQuery(tr_edit).find("input[name='wo_di_videoids[]']").val();
  jQuery("#wo_di_video_titleedit").val(title);
  jQuery("#wo_di_video_idedit").val(id);
  jQuery('#wo_di_form_editvideo div.video-option').hide();

  add_flag=false;

  switch (type) {
    case 'oEmbed':
      jQuery('#wo_di_video_oembededit').attr('checked', true);
      var height=jQuery(tr_edit).find("input[name='wo_di_videoheights[]']").val();
      var width=jQuery(tr_edit).find("input[name='wo_di_videowidths[]']").val();
      var url=jQuery(tr_edit).find("input[name='wo_di_videourl[]']").val();

      jQuery('#height_video_wooedit').val(height);
      jQuery('#width_video_wooedit').val(width);
      jQuery('#video_texturl_edit').val(url);

      if(url != '') {
        jQuery('#_checkbox_url_edit').attr('checked', 'checked');
      }
      jQuery("#wo_di_form_editvideo div.oembed_video").show();
      break;
  }
  jQuery('#dialog_form_editvideo').dialog('open');
}

function previewvideo(obj){
  tr_edit=obj.parentNode.parentNode;

  var type=jQuery(tr_edit).find("input[name='wo_di_videotypes[]']").val();
  var title=jQuery(tr_edit).find("input[name='wo_di_videotitles[]']").val();
  var width= jQuery(tr_edit).find("input[name='wo_di_videowidths[]']").val();
  var height= jQuery(tr_edit).find("input[name='wo_di_videoheights[]']").val();
  var embebido;

  jQuery("#dialog_previewvideo").dialog('option', 'title', 'Preview Video - '+title);
 if (type == "oEmbed") {
    embebido=jQuery(tr_edit).find("input[name='wo_Oembed[]']").val();
    jQuery("#contenedorvideo").html(embebido);
  }
  jQuery( "#dialog_previewvideo" ).dialog( "open" );
}

var form_add_video;
var form_edit_video;

function initiate_rules(){
  //edit form

  jQuery.validator.addMethod(
    'insert_video_oembed_edit',
    function(value, element) {
      if(jQuery("#wo_di_video_oembededit").is(':checked')) {
        if(jQuery("#video_texturl_edit").val() == '') {
          return false;
        }
      }
      return true;
    },
    text_error_insert_html
  );

    jQuery.validator.addMethod(
        "insert_video_dimension_edit",
        function(value, element) {
          if(jQuery("#wo_di_video_servidor_edit").is(':checked')){
            if(jQuery("#height_video_wooedit").val()=="" && jQuery("#width_video_wooedit").val()==""){
              jQuery("#height_video_wooedit").removeClass("error");
              jQuery("#height_video_wooedit").siblings("p").remove();
              return true;
            }
            if(jQuery("#height_video_wooedit").val()!="" && jQuery("#width_video_wooedit").val()!=""){
              jQuery("#height_video_wooedit").removeClass("error");
              jQuery("#height_video_wooedit").siblings("p").remove();
              return true;
            }
            return false;
          }
          return true;
        },
        text_error_dimension
    );
   form_edit_video=jQuery('#wo_di_form_editvideo').validate({
      wrapper:"p",
    //errorLabelContainer :"div_errores_addvideo",
    rules:{
      wo_di_video_idedit :{
        required: true
      },
    wo_di_tipo_videoedit:{
      required: true
      },
      video_texturl_edit:{
        insert_video_oembed_edit: true
      },
      height_video_wooedit:{
        insert_video_dimension_edit:true
      }
    },
    messages: {
      wo_di_video_idedit: {
       required: text_error_id
      }
    }
  });

  //add form
 
  jQuery.validator.addMethod(
    'insert_video_oembed',
    function(value, element) {
      if(jQuery('#wo_di_videooembed').is(':checked')) {
        if(jQuery('#video_texturl').val() == '') {
          return false;
        }
      }
      return true;
    },
    text_error_insert_html
  );

    jQuery.validator.addMethod(
        "insert_video_dimension",
        function(value, element) {
          if(jQuery("#wo_di_video_servidor").is(':checked') || jQuery('#wo_di_videooembed').is(':checked')) {
            if(jQuery("#heightvideo_woo").val()=="" && jQuery("#widthvideo_woo").val()==""){
              jQuery("#heightvideo_woo").removeClass("error");
              jQuery("#heightvideo_woo").siblings("p").remove();
              return true;
            }
            if(jQuery("#heightvideo_woo").val()!="" && jQuery("#widthvideo_woo").val()!=""){
              jQuery("#heightvideo_woo").removeClass("error");
              jQuery("#heightvideo_woo").siblings("p").remove();
              return true;
            }
            return false;
          }
          return true;
        },
        text_error_dimension
    );

   form_add_video=jQuery('#wo_di_form_addvideo').validate({
     wrapper:"p",
    //errorLabelContainer :"div_errores_addvideo",
    rules:{
      wo_di_videoid :{
        required: true
      },
    wo_di_tipo_video:{
      required: true
      },
      video_texturl: {
        insert_video_oembed: true
      },
   
      heightvideo_woo:{
        insert_video_dimension:true
      }
    },
    messages: {
      wo_di_videoid: {
       required: text_error_id
      }
    }
  });
}

function open_media_uploader_video()
{
  media_uploader = wp.media({
    library: {type: 'video'},
    title: 'Add Video Source'
  });

  media_uploader.on("select", function(){
    var file = media_uploader.state().get('selection').first();
    var extension = file.changed.subtype;
    var video_url = file.changed.url;

    var win = window.dialogArguments || opener || parent || top;

  });
  media_uploader.open();
}

function oEmbedVideo(url, height, width) {
  var video = '';
  jQuery.ajax({
    url: ajaxurl,
    data: {
      action: 'oembed_video',
      video_url: url,
      height: height,
      width: width,
      post_id: urlParam('post')
    },
    method: 'POST',
    async: false,
    success: function (iframe) {
      video = iframe;
    }
  });
  return video;
}

function urlParam(name){
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  if (null != results)
    return results[1] || 0;
  return 0;
}

function oEmbedVideo(url, height, width) {
  var video = '';
  jQuery.ajax({
    url: ajaxurl,
    data: {
      action: 'oembed_video',
      video_url: url,
      height: height,
      width: width,
      post_id: urlParam('post')
    },
    method: 'POST',
    async: false,
    success: function (iframe) {
      video = iframe;
    }
  });
  return video;
}

function urlParam(name){
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  if (null != results)
    return results[1] || 0;
  return 0;
}


jQuery(document).ready(function()
    {
        initiate_rules();
        /*jQuery('#wo_di_upload_video_edit').click(function()
        {
            tb_show('Upload Video', 'media-upload.php?type=video&context=uploadVideo&tab=type&TB_iframe=true');
            return false;
        });*/

        jQuery('#wo_di_select_video_edit').click(function()
        {
            //tb_show('Select Video', 'media-upload.php?type=video&context=selectVideo&action_video=edit&tab=library&TB_iframe=true');
            open_media_uploader_video();

            return false;
        });

        /*jQuery('#wo_di_upload_video').click(function()
        {
            tb_show('Upload Video', 'media-upload.php?type=video&context=uploadVideo&tab=type&TB_iframe=true');
            return false;
        });*/

        jQuery('#wo_di_select_video').click(function()
        {
            //tb_show('Select Video', 'media-upload.php?type=video&context=selectVideo&action_video=add&tab=library&TB_iframe=true');
            open_media_uploader_video();
            return false;
        });

        tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "mceEditorVideoHtml",
        width : "100%",
        height : "300px"
        });
        //edit
         jQuery( "#dialog_form_editvideo").dialog({
        autoOpen: false,
        draggable: false ,
        height: 560,
        width: 565,
        modal: false,
        buttons: [
          {
            text: text_edit_button,
            click: function() {
            jQuery('#wo_di_form_editvideo').submit();
              if(form_edit_video.valid()) {
                var formats;
                var height;
                var width;
                var dimension;
                var video_embebido="";
                var video_url="";
                var video_mp4="";
                var video_ogg="";
                var video_webm="";
             
                if(jQuery('#wo_di_video_oembededit').is(':checked')) {
                  type = 'oEmbed';
                  height = jQuery("#height_video_wooedit").val();
                  width = jQuery("#width_video_wooedit").val();
                  if( height == '' && width == '') {
                   dimension = 'Default';
                  }else{
                   dimension = height + " X " + width;
                  }
                  video_url = jQuery('#video_texturl_edit').val();
                  formats="-";
                }
                var title=jQuery("#wo_di_video_titleedit").val();
                var id=jQuery("#wo_di_video_idedit").val();
                var input_ids=jQuery(tr_edit).find("input[name='wo_di_videoids[]']");
                jQuery(input_ids).val(id);
                jQuery(input_ids).next().html(id);
                var input_titles=jQuery(tr_edit).find("input[name='wo_di_videotitles[]']");
                jQuery(input_titles).val(title);
                jQuery(input_titles).next().html(title);
                var input_types=jQuery(tr_edit).find("input[name='wo_di_videotypes[]']");
                jQuery(input_types).val(type);
                jQuery(input_types).next().html(type);
                jQuery(tr_edit).find("input[name='wo_di_videoembebido[]']").val(video_embebido);
                var input_formats=jQuery(tr_edit).find("input[name='wo_di_videoformats[]']");
                jQuery(input_formats).val(formats);
                jQuery(input_formats).next().html(formats);
                jQuery(tr_edit).find("input[name='wo_di_videoheights[]']").val(height);
                var input_width=jQuery(tr_edit).find("input[name='wo_di_videowidths[]']");
                input_width.val(width);
                jQuery(input_width).next().html(dimension);
                jQuery(tr_edit).find("input[name='wo_di_videourl[]']").val(video_url);
             
                jQuery( this ).dialog( "close" );
              } else {
                form_edit_video.showErrors();
              }
            }
          },
          {
            text: text_cancel_button,
            click: function() {
            clean_inputs_edit();
            jQuery( this ).dialog( "close" );
            }
          }]
        });
      //add
        jQuery( "#dialog_form_addvideo").dialog({
        autoOpen: false,
        draggable: false ,
        height: 560,
        width: 565,
        modal: false,
        buttons: [
          {
          text : text_add_button,
          click: function() {
            var id=jQuery("#wo_di_videoid").val();
            jQuery('#wo_di_form_addvideo').submit();
            if(form_add_video.valid()) {
              var formats;
              var height;
              var width;
              var dimension;
              var video_embebido="";
              var videoUrl = '';
            
              var oEmbed="";
              var noClick = false;
             
              if(jQuery('#wo_di_videooembed').is(':checked')){
                type = 'oEmbed';
              
                videoUrl = jQuery('#video_texturl').val();
                oEmbed = oEmbedVideo(videoUrl, height, width);
                formats = '-';
                if ('' == oEmbed) {
                  noClick = true;
                }
              }
              var num_of_videos=jQuery("#wo_di_num_of_videos").val();
              num_of_videos++;
              var classColumn="class=''";
              /*if((num_of_videos%2)!=0){
                classColumn="class='alternate'";
              }*/

              var title=jQuery("#wo_di_videotitle").val();
              var video="<tr id='wo_di_videoproduct_"+num_of_videos+"' "+classColumn+">";
              //video+="<td><input type=hidden name='wo_di_videoids[]' value='"+id+"' /><span>"+id+"</span></td>";
              video+="<td style='width:20px;'><span class='sort-button dashicons dashicons-sort' style='font-size:18px;' title='move'></span></td>";
              video+="<td><input type=hidden name='wo_di_videotitles[]' value='"+title+"' /><span>"+title+"</span></td>";
              video+="<td><input type=hidden name='wo_di_videotypes[]' value='"+type+"' /><span>"+type+"</span></td>";
              video+="<td> <input type=hidden name='wo_di_videoformats[]' value='"+formats+"' /><span>"+formats+"</span></td>";
              video+="<input type=hidden name='wo_di_videoembebido[]'/ value='"+video_embebido+"' >";
              video+="<input type=hidden name='wo_di_videourl[]' value='" + videoUrl + "' />";
              video+="<td><input type=hidden name='wo_di_videoactive[]' value='1' /><input type='checkbox' checked='checked' onchange='update_inputactive(this)' /></td>";
              var previewButton ="<span class='action-button dashicons dashicons-search float-right' onclick='previewvideo(this)' title='preview'>";
              if (noClick) {
                previewButton ="<span class='action-button dashicons dashicons-search float-right' title='Preview available after saving the product for the first time'>";
              }
              video+="<td>" + previewButton + " </span> <span class='action-button dashicons dashicons-edit float-right' onclick='editrow(this)' title='edit'></span><span class='action-button dashicons dashicons-trash float-right' onclick='deleterow(this)' title='delete'></span></td>";
              jQuery("#wo_di_table_videoshtml").append(video);
              jQuery("#wo_di_num_of_videos").val(num_of_videos);
              jQuery( this ).dialog( "close" );

              //Clean modal
              jQuery('#wo_di_form_addvideo').find("input[type=text], input[type=url], textarea").val("");
              jQuery('#wo_di_videooembed').attr('checked', true);
              jQuery('#wo_di_form_addvideo div.video-option').hide();
              jQuery('#wo_di_form_addvideo div.oembed_video').show();
            } else {
              form_add_video.showErrors();
            }
          }
        }
        ,
        {
          text:text_cancel_button,
          click: function() {
            jQuery( this ).dialog( "close" );
          }
        }]}/*,
        },
        close: function() {
          jQuery( this ).dialog( "close" );
        }*/
      );

      jQuery( "#button_addvideo" )
      .button()
      .click(function(event) {
        event.preventDefault();
        jQuery( "#dialog_form_addvideo" ).dialog( "open" );
        add_flag=true;
        return false;
      });

      jQuery( "#table-videosortable" ).sortable();
      jQuery( "#table-videosortable" ).disableSelection();

      //preview
      jQuery( "#dialog_previewvideo").dialog({
        autoOpen: false,
        draggable: false ,
        width: 650,
        modal: false,
        buttons: [
          {
            text: text_close_button,
            click: function() {
            //clean_inputs_edit();
            jQuery("#contenedorvideo").html("");
            jQuery( this ).dialog( "close" );
            }
          }],
         close: function(){
           jQuery("#contenedorvideo").html("");
          }
        });
    });
