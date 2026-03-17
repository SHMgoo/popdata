<?php
/**
 *
 * @author David A. Benton Jr. - ASD
 */
define('HTTP_ACCESSIBLE', true); // define once
require('/vs/www/docs/main/.in/php_module/form/validate.php');

// This resets the program being executed by the server to the script name only, 
// which helps to protect against a Reflected Cross-Site Scripting attack by removing
// any extraneous information in the URL. 
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];

$field_definition = array(
  'feedback_comment'=>array(
    'type'=>'textarea',
    'label'=>'Feedback:',
    'req'=>true
  ),

	'feedback_email' => array(
		'type'=>'text',
		'label'=>'E-mail:',
		'req'=>false
  )
);

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST))
{
  $form = new validateAndMail( $field_definition, $_POST, 'cnmp.web.comments@census.gov', 'PopClock Comments' );
 
  if(isset($_POST["feedback_email"]) && trim($_POST["feedback_email"]) !== '')
  {
  	// force feedback email to be required
  	$field_definition['feedback_email']['req'] = true;
//  	$sendToUser = new validateAndSendConfirmation( $field_definition, $_POST, $_POST["feedback_email"], 'Your PopClock feedback on Census.gov','Popclock' );
  }
}
?><!--#include virtual="/main/template/inc/admin.inc"-->

<!--#set var="cb_site_title" value="Popclock Feedback" -->
<!--#set var="cb_page_title" value="Popclock Feedback" -->
<!--#set var="cb_description" value="Feedback Survey for Popclock website" -->
<!--#set var="cb_creator" value="US Census Bureau, Application Services Division" -->
<!--#set var="cb_page_contact" value="cnmp.web.comments@census.gov" -->
<!--#set var="cb_page_source" value="Center for New Media &amp; Promotions" -->

<!--#set var="cb_exclude_content_title" value="1" -->
<!--#set var="cb_page_columns" value="1" -->


<!--#include virtual="/main/template/inc/page_start.inc"-->

<!-- >>> SITE SPECIFIC HEADER START: Note, a default site defined CSS is already defined and included above -->
<script type="text/javascript" src="/main/.in/php_module/form/js/jQuery.formValidate.js"></script>
<script type="text/javascript" src="/main/javascript/home.js"></script>
<style type="text/css">
textarea {
    margin-bottom: 2em;
}
.betterButton {
	padding: 10px;
	height: auto;
	width: auto;
}
</style>
<!-- >>> SITE SPECIFIC HEADER STOP -->
<!--#include virtual="/main/template/inc/solid_header_exit_breadcrumb_start.inc"-->

<!-- >>> SITE SPECIFIC BREADCRUMBS START -->
<!-- The breadcrumb seperator must consist of a space, followed by &#8250;, followed by space. " &#8250; ". -->
 &#8250; <a href="../popclock">Popclock</a></h6>
<!-- >>> SITE SPECIFIC BREADCRUMBS STOP -->
<!--#include virtual="/main/template/inc/breadcrumbs_exit_topmenu_start.inc"-->
<!-- >>> SITE SPECIFIC TOP MENU START -->
<li><a href="../popclock">Popclock</a></li>
<!-- >>> SITE SPECIFIC TOP MENU STOP -->
<!--#include virtual="/main/template/inc/topmenu_exit_left_column_start.inc"-->
<!-- >>> SITE SPECIFIC LEFT COLUMN START -->
<!-- >>> SITE SPECIFIC LEFT COLUMN STOP -->
<!--#include virtual="/main/template/inc/left_column_exit_center_column_start.inc"-->
<!-- >>> SITE SPECIFIC CENTER COLUMN START -->

<div id="feedback-content">
	<?php
    if(isset($form))
    {
      //echo '<pre>'.var_export($form, true).'</pre>';
      echo $form->displaySubmitResult('redtext ui-corner-all', 'ui-corner-all');
    } else { 
    ?>
    
  <p>Thank you for visiting Popclock. Please provide any feedback to help us improve the site.</p>
    
  <form id="ASD-frm-004-ASD" action="<?=$_SERVER['PHP_SELF'];?>" method="post" title="Census site Feedback">
        <div>
            <span class="redtext">
            	CAUTION:&nbsp; Please do not include any personal information such as name, telephone number, Social Security Number, date of birth, or any other personally identifiable information (PII).
            </span>
            <br />
            <textarea id="feedback_comment" name="feedback_comment" class="validate lenDIV1000" style="border-width:1px;" rows="10" cols="130"><?=(isset($_POST['feedback_comment']))? htmlentities($_POST['feedback_comment']):'';?></textarea>
        </div>
        <p>
        	<label for="feedback_email">
            	E-mail (optional):
            </label>
        	<input type="text" maxlength="100" name="feedback_email" id="feedback_email" />
            <br />
        
        	<span class="smalltext">
            	If you would like a response, please provide your e-mail address.  E-mail information will only be used to contact you regarding your feedback and will not be used for any other purpose.
            </span>
        </p>
        
        <div>
        	<input type="submit" name="Submit" value="Submit" class="betterButton"/> | <input type="button" class="betterButton" name="Cancel" value="Cancel" onclick="window.location='../popclock/'"/>
        </div>
  </form>
    <?php
    }
    ?>
  <p>
	<a href="../popclock">Return to Popclock</a>
  </p>
</div>
<!-- Close feedback-content -->

<!-- >>> START CENTER COLUMN CONTENT -->
<!-- >>> SITE SPECIFIC CENTER COLUMN STOP -->
<!--#include virtual="/main/template/inc/center_column_exit_right_column_start.inc"-->
<!-- >>> SITE SPECIFIC RIGHT COLUMN START -->
<!-- >>> SITE SPECIFIC RIGHT COLUMN STOP -->
<!--#include virtual="/main/template/inc/right_column_exit_start_page_exit.inc"-->
