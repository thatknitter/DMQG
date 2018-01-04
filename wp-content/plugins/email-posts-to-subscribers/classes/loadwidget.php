<?php
class elp_cls_widget
{
	public static function elp_widget_int($arr)
	{
		$elp_name = "";
		$elp_desc = "";
		require_once(ELP_DIR.'widget'.DIRECTORY_SEPARATOR.'widget.php');
	}
}

function elp_shortcode( $atts ) 
{
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	
	//[email-posts-subscribers namefield="YES" desc="" group=""]
	$elp_name = isset($atts['namefield']) ? $atts['namefield'] : 'YES';
	$elp_desc = isset($atts['desc']) ? $atts['desc'] : '';
	$elp_group = isset($atts['group']) ? $atts['group'] : '';
	
	//echo "1." . $elp_name . "<br>";
	//echo "2." . $elp_desc . "<br>";
	//echo "3." . $elp_group . "<br>";
	
	$elp_name = trim($elp_name);
	$elp_desc = trim($elp_desc);
	$elp_group = trim($elp_group);
	
	$url = "'" . home_url() . "'";
	$elp = "";
		
	global $elp_includes;
	if (!isset($elp_includes) || $elp_includes !== true) 
	{ 
		$elp_includes = true;
		$elp = $elp . '<link rel="stylesheet" media="screen" type="text/css" href="'.ELP_URL.'widget/widget.css" />';
		$elp = $elp . '<script language="javascript" type="text/javascript" src="'.ELP_URL.'widget/widget.js"></script>';
	} 
		
	$elp = $elp . '<div>';
	if( $elp_desc <> "" )
	{
		$elp = $elp . '<div class="elp_caption">'.$elp_desc.'</div>';
	}
	$elp = $elp . '<div class="elp_msg"><span id="elp_msg"></span></div>';
	if( $elp_name == "YES" )
	{
		$elp = $elp . '<div class="elp_lablebox">'.__('Name', 'email-posts-to-subscribers').'</div>';
		$elp = $elp . '<div class="elp_textbox">';
			$elp = $elp . '<input class="elp_textbox_class" name="elp_txt_name" id="elp_txt_name" value="" maxlength="225" type="text">';
		$elp = $elp . '</div>';
	}
	$elp = $elp . '<div class="elp_lablebox">'.__('Email *', 'email-posts-to-subscribers').'</div>';
	$elp = $elp . '<div class="elp_textbox">';
		$elp = $elp . '<input class="elp_textbox_class" name="elp_txt_email" id="elp_txt_email" onkeypress="if(event.keyCode==13) elp_submit_page('.$url.')" value="" maxlength="225" type="text">';
	$elp = $elp . '</div>';
	$elp = $elp . '<div class="elp_button">';
		$elp = $elp . '<input class="elp_textbox_button" name="elp_txt_button" id="elp_txt_button" onClick="return elp_submit_page('.$url.')" value="'.__('Subscribe', 'email-posts-to-subscribers').'" type="button">';
	$elp = $elp . '</div>';
	if( $elp_name != "YES" )
	{
		$elp = $elp . '<input name="elp_txt_name" id="elp_txt_name" value="" type="hidden">';
	}
	$elp = $elp . '<input name="elp_txt_group" id="elp_txt_group" value="'.$elp_group.'" type="hidden">';
	$elp = $elp . '</div>';
	return $elp;
}

function elp_subbox( $elp_name = "YES", $elp_desc = "" )
{
	$arr = array();
	$arr["elp_title"] 	= "";
	$arr["elp_desc"] 	= $elp_desc;
	$arr["elp_name"] 	= $elp_name;	
	require_once(ELP_DIR.'widget'.DIRECTORY_SEPARATOR.'widget.php');
}
?>