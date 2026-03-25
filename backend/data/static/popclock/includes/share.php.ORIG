<?php
	require_once 'common.php';
	require_once 'config.php';
	
	foreach($_GET as $key=>$value){
		if($value != strip_tags($value)) {
			header('https://www.census.gov/popclock');
		}
	}


	// Strip out JS as a protection against reflected XSS. 
	foreach($_GET as $key=>$value){
	   $_GET[$key] = xss_cleaner($value);
	}
	 
	if ( ! isset($_GET['component']) ) :
		input_error('Component does not exist!');
	else:
		$component = $_GET['component'];

		if ($component == 'counter'):

		elseif ($component == 'pop_on_date'):

		elseif ($component == 'growth'):

		elseif ($component == 'pyramid'):

		elseif ($component == 'populous'):

		elseif ($component == 'density'):

		else:
			input_error('Bad component');

		endif;

	endif;
	
	
	if ( ! isset($_GET['component'])
			|| ! isset($_GET['share_pt_text'])
			|| ! isset($_GET['share_fb_text'])
			|| ! isset($_GET['share_fb_href'])
			|| ! isset($_GET['share_tw_text'])
			|| ! isset($_GET['share_em_text'])
			|| ! isset($_GET['share_url'])
			|| ! isset($_GET['share_image'])
			) {
		input_error('Missing share variables');
	}
	
	$share_pt_text = html_entity_decode($_GET['share_pt_text'], ENT_QUOTES);
	$share_fb_text = html_entity_decode($_GET['share_fb_text'], ENT_QUOTES);
	$share_fb_href = html_entity_decode($_GET['share_fb_href'], ENT_QUOTES);
	$share_tw_text = html_entity_decode($_GET['share_tw_text'], ENT_QUOTES);
	$share_em_text = html_entity_decode($_GET['share_em_text'], ENT_QUOTES);
	$share_url     = html_entity_decode($_GET['share_url'], ENT_QUOTES);
	$share_image   = html_entity_decode($_GET['share_image'], ENT_QUOTES);
	
	require_once 'libraries/share.php';

	$share = Share::get_instance();

	$email = $share->add('Email', 'Email');
	$twitter = $share->add('Twitter', 'Twitter');
	$facebook = $share->add('Facebook', 'Facebook');
	$pinterest = $share->add('Pinterest', 'Pinterest');
	
	
	function xss_cleaner($input_str) {	
		$return_str = str_replace( array('<','>',"'",'"',')','('), array('','','&apos;','','&#x29;','&#x28;'), $input_str );
		$return_str = str_ireplace( '%3Cscript', '', $return_str );
		return $return_str;
	}
?>
<link rel="stylesheet" href = "./js/jquery-ui-themes-1.10.4/themes/ui-lightness/jquery-ui.css">
<script src="./js/jquery-ui-1.10.4/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.4/ui/jquery-ui.js"></script>
<style>

      .ui-dialog {
       width: 340px !important;
      }

      .ui-dialog-titlebar {
          background:  #112e51 !important;
      }
      .ui-widget-header,.ui-state-default, ui-button {
            background:#b9cd6d;
            border: 1px solid #b9cd6d;
            color: #FFFFFF;
            font-weight: bold;
         }
      .ui-dialog-buttonset {
        width: 100%;
      }  


</style>

  <div id = "iframeDialog" direction="ltl"
      title = "Copy to clipboard: Ctrl+C, Enter"><textarea direction="ltl" id="iframeText" rows="1" cols="100" id="iframeText"></textarea>
  </div>
<script type="text/javascript">
   $("#iframeDialog").hide();
   function openInNewWindow() {
		
	    var str = " width="+'"'+window.innerWidth+'"'+" height="+'"'+window.innerHeight+'"'+"></iframe>";
/*
	    window.prompt(" Copy to clipboard: Ctrl+C, Enter " , "<iframe src="+'"'+window.location +'"'+ str); 
*/ 

          var fullStr = "<iframe src="+'"'+window.location +'"'+ str; 


       $(function() {
           $("#iframeDialog").dialog({
              autoOpen: true,
              modal: true,
              hide: "puff",
              show: "slide",
              buttons: {
                "OK": function() {
                 $(this).dialog("close");
             } 
             // "Cancel": function() {
             //    $(this).dialog("close");
             //   }
              }

           });
           $("#iframeText").text(fullStr); 
           $("#iframeText").focus(); 
           
        }); 

        $("#iframeText").focus(function() {
            var $this = $(this);
            $this.select();

           // Work around Chrome's little problem
           $this.mouseup(function() {
               // Prevent further mouseup intervention
               $this.unbind("mouseup");
               return false;
           });
       });
        $("#iframeText").scrollLeft(); 
    	
// 	    var str = " width="+'"'+window.innerWidth+'"'+" height="+'"'+window.innerHeight+'"'+"></iframe>";
// 	    window.prompt(" Copy to clipboard: Ctrl+C, Enter " , "<iframe src="+'"'+window.location +'"'+ str); 

	 }

   </script>
	<h4>Share This</h4>
	<div id="copy-notice">code copied</div>
	<nav>
		<a id="print-this" href="./print.php?component=<?php print($component); ?>&image=<?php echo $share_image; ?>" target="_blank" class="share">Print</a>
		<a id="download-this" href="./" class="share">Download</a>
		<div id="email-this" class="share"><?php echo $email->share($share_em_text, $share_url, $share_image)->get_button(array('to' => $config->share->email->to, 'subject' => $config->share->email->subject)); ?></div>
		<a   onclick="openInNewWindow();" ><img src="./css/images/icon-embed.png" /></a>
		<div id="tweet-this" class="share"><?php echo $twitter->share($share_tw_text, $share_url, $share_image)->get_button(); ?></div>
		<div id="post-this" class="share"><?php echo $facebook->share($share_fb_text, $share_url, $share_image)->get_button(array('data-href' => $share_fb_href)); ?></div>
<!-- 		<div id="pin-this" class="share"><?php echo $pinterest->share($share_pt_text, $share_url, $share_image)->get_button(); ?></div>  -->
	</nav>
	<?php echo $share->get_includes(); ?>
