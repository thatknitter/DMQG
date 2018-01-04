<?php 

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('You are not allowed to call this page directly.'); 
}

if ( !empty( $_POST ) && ! wp_verify_nonce( $_REQUEST['wp_create_nonce'], 'subscriber-nonce' ) ) {
	die('<p>Security check failed.</p>');
}

elp_cls_common::elp_check_latest_update();

// Form submitted, check the data
//$search = isset($_GET['search']) ? $_GET['search'] : 'A,B,C';
//$search_sts = isset($_GET['sts']) ? $_GET['sts'] : '';
//$search_count = isset($_GET['cnt']) ? $_GET['cnt'] : '1';

$search 		= isset($_POST['searchquery']) ? sanitize_text_field($_POST['searchquery']) : 'ALL';
$search_sts 	= isset($_POST['searchquery_sts']) ? sanitize_text_field($_POST['searchquery_sts']) : '';
$search_count 	= isset($_POST['searchquery_cnt']) ? sanitize_text_field($_POST['searchquery_cnt']) : '1';
$search_group 	= isset($_POST['searchquery_group']) ? sanitize_text_field($_POST['searchquery_group']) : '';

// ------------ Security check----------------------------------------------------------------
//if(!is_numeric($search_count)) { die('<p>Security check failed. Are you sure you want to do this?</p>'); }
//if(!is_numeric($search_count)) { die('<p>Security check failed. Are you sure you want to do this?</p>'); }
//$search_nocomma = str_replace(",", "", $search);
//if(preg_match('/[^a-z_\-0-9]/i', $search_nocomma))
//{
//	die('<p>Security check failed. Are you sure you want to do this?</p>');
//}
//if($search_sts != '' && $search_sts != 'Confirmed' 
//	&& $search_sts != 'Unconfirmed' && $search_sts != 'Unsubscribed' && $search_sts != 'Single Opt In')
//{
//	die('<p>Security check failed. Are you sure you want to do this?</p>');
//}
// ------------ Security check----------------------------------------------------------------

if (isset($_POST['frm_elp_display']) && $_POST['frm_elp_display'] == 'yes')
{
	$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
	
	$elp_success = '';
	$elp_success_msg = FALSE;
	if (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] != 'delete' 
			&& $_POST['frm_elp_bulkaction'] != 'resend' && $_POST['frm_elp_bulkaction'] != 'groupupdate' 
				&& $_POST['frm_elp_bulkaction'] != 'search_sts' && $_POST['frm_elp_bulkaction'] != 'search_cnt' && $_POST['frm_elp_bulkaction'] != 'search_group')
	{	
		// First check if ID exist with requested ID
		$result = elp_cls_dbquery::elp_view_subscriber_count($did);
		if ($result != '1')
		{
			?>
			<div class="error fade">
			  <p><strong><?php _e('Oops, selected details doesnt exist.', 'email-posts-to-subscribers'); ?></strong></p>
			</div>
			<?php
		}
		else
		{
			// Form submitted, check the action
			if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
			{
				//	Just security thingy that wordpress offers us
				check_admin_referer('elp_form_show');
				
				//	Delete selected record from the table
				elp_cls_dbquery::elp_view_subscriber_delete($did);
				
				//	Set success message
				$elp_success_msg = TRUE;
				$elp_success = __('Selected record was successfully deleted.', 'email-posts-to-subscribers');
			}
			
			if (isset($_GET['ac']) && $_GET['ac'] == 'resend' && isset($_GET['did']) && $_GET['did'] != '')
			{
				$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
				$setting = array();
				$setting = elp_cls_dbquery2::elp_setting_select(1);
				if($setting['elp_c_optinoption'] <> "Double Opt In")
				{
					?>
					<div class="error fade">
					  <p><strong><?php _e('To send confirmation mail, Please change the Opt-in option to Double Opt In.', 'email-posts-to-subscribers'); ?></strong></p>
					</div>
					<?php
				}
				else
				{
					elp_cls_sendmail::elp_prepare_optin("single", $did, "");
					elp_cls_dbquery::elp_view_subscriber_upd_status("Unconfirmed", $did);
					$elp_success_msg = TRUE;
					$elp_success  = __('Confirmation email resent successfully.', 'email-posts-to-subscribers');
				}
			}
		}
	}
	else
	{
		check_admin_referer('elp_form_show');
		
		if (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] == 'delete')
		{
			$chk_delete = isset($_POST['chk_delete']) ? $_POST['chk_delete'] : '';
			
			if(!empty($chk_delete))
			{			
				$count = count($chk_delete);
				for($i=0; $i<$count; $i++)
				{
					
					$del_id = $chk_delete[$i];
					elp_cls_dbquery::elp_view_subscriber_delete($del_id);
					
				}
				
				//	Set success message
				$elp_success_msg = TRUE;
				$elp_success = __('Selected record was successfully deleted.', 'email-posts-to-subscribers');
			}
			else
			{
				?>
				<div class="error fade">
				  <p><strong><?php _e('Oops, No record was selected.', 'email-posts-to-subscribers'); ?></strong></p>
				</div>
				<?php
			}
		}
		elseif (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] == 'resend')
		{
			$chk_delete = isset($_POST['chk_delete']) ? $_POST['chk_delete'] : '';
			
			$setting = array();
			$setting = elp_cls_dbquery2::elp_setting_select(1);
			if($setting['elp_c_optinoption'] <> "Double Opt In")
			{
				?>
				<div class="error fade">
				  <p><strong><?php _e('To send confirmation mail, Please change the Opt-in option to Double Opt In.', 'email-posts-to-subscribers'); ?></strong></p>
				</div>
				<?php
			}
			else
			{
				if(!empty($chk_delete))
				{			
					$count = count($chk_delete);
					
					$idlist = "";
					for($i = 0; $i<$count; $i++)
					{
						$del_id = $chk_delete[$i];
					
						if($i < 1)
						{
							$idlist = $del_id;
						}
						else
						{
							$idlist = $idlist . ", " . $del_id;
						}
					}
					elp_cls_sendmail::elp_prepare_optin("group", 0, $idlist);
					elp_cls_dbquery::elp_view_subscriber_upd_status("Unconfirmed", $idlist);
					$elp_success_msg = TRUE;
					$elp_success = __('Confirmation email(s) resent successfully.', 'email-posts-to-subscribers');
				}
				else
				{
					?>
					<div class="error fade">
					  <p><strong><?php _e('Oops, No record was selected.', 'email-posts-to-subscribers'); ?></strong></p>
					</div>
					<?php
				}
			}
		}
		elseif (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] == 'search_sts')
		{
			// Nothing
		}
		elseif (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] == 'search_cnt')
		{
			// Nothing
		}
		elseif (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] == 'search_group')
		{
			// Nothing
		}
		elseif (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] == 'groupupdate')
		{
			$chk_delete = isset($_POST['chk_delete']) ? $_POST['chk_delete'] : '';
			if(!empty($chk_delete)) 
			{			
				$elp_email_group = isset($_POST['elp_email_group']) ? $_POST['elp_email_group'] : '';
				if ($elp_email_group != "") 
				{
					$count = count($chk_delete);
					$idlist = "";
					for($i = 0; $i < $count; $i++) 
					{
						$del_id = $chk_delete[$i];
						if($i < 1) 
						{
							$idlist = $del_id;
						} 
						else 
						{
							$idlist = $idlist . ", " . $del_id;
						}
					}
					
					elp_cls_dbquery::elp_view_subscriber_upd_group($elp_email_group, $idlist);
					$elp_success_msg = TRUE;
					$elp_success = __( 'Selected subscribers group updated.', 'email-posts-to-subscribers' );
				} 
				else 
				{
					?><div class="error fade"><p><strong><?php _e( 'Oops, No record was selected.', 'email-posts-to-subscribers' ); ?></strong></p></div><?php
				}
			} 
			else 
			{
				?><div class="error fade"><p><strong><?php _e( 'Oops, No record was selected.', 'email-posts-to-subscribers' ); ?></strong></p></div><?php
			}
		}
	}
	
	if ($elp_success_msg == TRUE)
	{
		?>
		<div class="updated fade">
		  <p><strong><?php echo $elp_success; ?></strong></p>
		</div>
		<?php
	}
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>subscribers/view-subscriber.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
  <form name="frm_elp_display" method="post" onsubmit="return _elp_bulkaction()">
  <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
  <div class="tool-box">
  <h3><?php _e('View subscriber', 'email-posts-to-subscribers'); ?> 
  <a class="add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=add"><?php _e('Add New', 'email-posts-to-subscribers'); ?></a>
  <a class="add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=import"><?php _e('Import', 'email-posts-to-subscribers'); ?></a> 
  <a class="add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=export"><?php _e('Export', 'email-posts-to-subscribers'); ?></a> 
  <a class="add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=sync"><?php _e('Sync', 'email-posts-to-subscribers'); ?></a> 
  <a class="add-new-h2" target="_blank" href="<?php echo ELP_FAV; ?>"><?php _e('Help', 'email-posts-to-subscribers'); ?></a> 
  </h3>
	<?php
	$myData = array();
	$offset = 0;
	$limit = 200;
	if ($search_count == 0)
	{
		$limit = 9999;
	}
	
	if ($search_count > 1)
	{
		$offset = $search_count;
	}
	
	if ($search_count == 1001)
	{
		$limit = 1000;
	}
	elseif ($search_count == 2001)
	{
		$limit = 3000;
	}
	elseif ($search_count == 5001)
	{
		$limit = 5000;
	}
	
	$myData = elp_cls_dbquery::elp_view_subscriber_search2($search, 0, $search_sts, $offset, $limit, $search_group);
	?>
	<div class="tablenav top">
			<input type="button" class="button action <?php if($search == "A,B,C"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('A,B,C')" id="" value="A,B,C" />
			<input type="button" class="button action <?php if($search == "D,E,F"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('D,E,F')" id="" value="D,E,F" />
			<input type="button" class="button action <?php if($search == "G,H,I"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('G,H,I')" id="" value="G,H,I" />
			<input type="button" class="button action <?php if($search == "J,K,L"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('J,K,L')" id="" value="J,K,L" />
			<input type="button" class="button action <?php if($search == "M,N,O"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('M,N,O')" id="" value="M,N,O" />
			<input type="button" class="button action <?php if($search == "P,Q,R"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('P,Q,R')" id="" value="P,Q,R" />
			<input type="button" class="button action <?php if($search == "S,T,U"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('S,T,U')" id="" value="S,T,U" />
			<input type="button" class="button action <?php if($search == "V,W,X,Y,Z"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('V,W,X,Y,Z')" id="" value="V,W,X,Y,Z" />
			<input type="button" class="button action <?php if($search == "0,1,2,3,4,5,6,7,8,9"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('0,1,2,3,4,5,6,7,8,9')" id="" value="0 to 9" />
			<input type="button" class="button action <?php if($search == "ALL"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_search_sub_action('ALL')" id="" value="ALL" />		
    </div>
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th class="check-column" scope="col" style="padding: 8px 2px;">
			<input type="checkbox" name="elp_checkall" id="elp_checkall" onClick="_elp_checkall('frm_elp_display', 'chk_delete[]', this.checked);" /></th>
            <th scope="col"><?php _e('Sno', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Email', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Name', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Group', 'email-posts-to-subscribers'); ?></th>
            <th scope="col"><?php _e('Subscribed', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Action', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th class="check-column" scope="col" style="padding: 8px 2px;">
			<input type="checkbox" name="elp_checkall" id="elp_checkall" onClick="_elp_checkall('frm_elp_display', 'chk_delete[]', this.checked);" /></th>
            <th scope="col"><?php _e('Sno', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Email address', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Name', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Group', 'email-posts-to-subscribers'); ?></th>
            <th scope="col"><?php _e('Subscribed', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Action', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </tfoot>
        <tbody>
          <?php 
			$i = 0;
			$displayisthere = FALSE;
			if(count($myData) > 0)
			{
				if ($offset == 0)
				{
					$i = 1;
				}
				else
				{
					$i = $offset;
				}
				foreach ($myData as $data)
				{					
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td align="left"><input name="chk_delete[]" id="chk_delete[]" type="checkbox" value="<?php echo $data['elp_email_id'] ?>" /></td>
					<td><?php echo $i; ?></td>
					<td><?php echo $data['elp_email_mail']; ?></td>
					<td><?php echo $data['elp_email_name']; ?></td>     
					<td><?php echo elp_cls_common::elp_disp_status($data['elp_email_status']); ?></td>
					<td><?php echo $data['elp_email_group']; ?></td>  
					<td><?php echo date_format(date_create($data['elp_email_created']), 'Y-m-d'); ?></td>
					<td><div> 
					<span class="edit">
						<a target="_blank" title="Edit" href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=edit&did=<?php echo $data['elp_email_id']; ?>">
					<?php _e('Edit', 'email-posts-to-subscribers'); ?></a> | </span> 
					<span class="trash">
					<a onClick="javascript:_elp_delete('<?php echo $data['elp_email_id']; ?>','<?php echo $search; ?>')" href="javascript:void(0);">
					<?php _e('Delete', 'email-posts-to-subscribers'); ?></a>
					</span>
					<?php
					if($data['elp_email_status'] != "Confirmed")
					{
						?>
						<span class="edit"> 
						| <a onClick="javascript:_elp_resend('<?php echo $data['elp_email_id']; ?>','<?php echo $search; ?>')" href="javascript:void(0);">
						<?php _e('Resend Confirmation', 'email-posts-to-subscribers'); ?></a>
						</span> 
						<?php
					}
					?>
					</div>
					</td>
					</tr>
					<?php
					$i = $i+1;
				} 
			}
			else
			{
				?>
				<tr>
					<td colspan="8" align="center"><?php _e('No records available. Please use the above alphabet search button to search.', 'email-posts-to-subscribers'); ?></td>
				</tr>
				<?php 
			}
			?>
        </tbody>
      </table>
      <?php wp_nonce_field('elp_form_show'); ?>
      <input type="hidden" name="frm_elp_display" id="frm_elp_display" value="yes"/>
	  <input type="hidden" name="frm_elp_bulkaction" id="frm_elp_bulkaction" value=""/>
	  <input name="searchquery" id="searchquery" type="hidden" value="<?php echo $search; ?>" />
	  <input name="searchquery_sts" id="searchquery_sts" type="hidden" value="<?php echo $search_sts; ?>" />
	  <input name="searchquery_cnt" id="searchquery_cnt" type="hidden" value="<?php echo $search_count; ?>" />
	  <input type="hidden" name="searchquery_group" id="searchquery_group" value="<?php echo $search_group; ?>" />
	  <?php $nonce = wp_create_nonce( 'subscriber-nonce' ); ?>
	  <input type="hidden" name="wp_create_nonce" id="wp_create_nonce" value="<?php echo $nonce; ?>"/>
	<div style="padding-top:5px;"></div>
    <div class="tablenav">
		<div class="alignleft">
			<select name="bulk_action" id="bulk_action" onchange="return _elp_action_visible(this.value)">
				<option value=""><?php _e('Bulk Actions', 'email-posts-to-subscribers'); ?></option>
				<option value="delete"><?php _e('Delete', 'email-posts-to-subscribers'); ?></option>
				<option value="resend"><?php _e('Resend Confirmation', 'email-posts-to-subscribers'); ?></option>
				<option value="groupupdate"><?php _e( 'Update Subscribers Group', 'email-posts-to-subscribers' ); ?></option>
			</select>
			<select name="elp_email_group" id="elp_email_group" disabled="disabled">
				<option value=''><?php _e( 'Select Group', 'email-posts-to-subscribers' ); ?></option>
				<?php
				$groups = array();
				$groups = elp_cls_dbquery::elp_view_subscriber_group();
				if(count($groups) > 0) {
					$i = 1;
					foreach ($groups as $group) {
						?><option value='<?php echo $group["elp_email_group"]; ?>'>
							<?php echo $group["elp_email_group"]; ?>
						</option><?php
					}
				}
				?>
			</select>
			<input type="submit" value="<?php _e('Apply', 'email-posts-to-subscribers'); ?>" class="button action" id="doaction" name="doaction">
		</div>
		<div class="alignright">
		   <select name="search_email_group" id="search_email_group" onchange="return _elp_search_group_action(this.value)">
				<option value=''><?php _e( 'All Groups', 'email-posts-to-subscribers' ); ?></option>
				<?php
				$groups = array();
				$groups = elp_cls_dbquery::elp_view_subscriber_group();
				if(count($groups) > 0) 
				{
					$i = 1;
					foreach ($groups as $group) 
					{
						?>
						<option value='<?php echo $group["elp_email_group"]; ?>' <?php if($group["elp_email_group"] == $search_group) { echo 'selected="selected"' ; } ?>>
							<?php echo $group["elp_email_group"]; ?>
						</option>
						<?php
					}
				}
				?>
			</select>
			<select name="search_sts_action" id="search_sts_action" onchange="return _elp_search_sts_action(this.value)">
				<option value=""><?php _e('View all status', 'email-posts-to-subscribers'); ?></option>
				<option value="Confirmed" <?php if($search_sts=='Confirmed') { echo 'selected="selected"' ; } ?>><?php _e('Confirmed', 'email-posts-to-subscribers'); ?></option>
				<option value="Unconfirmed" <?php if($search_sts=='Unconfirmed') { echo 'selected="selected"' ; } ?>><?php _e('Unconfirmed', 'email-posts-to-subscribers'); ?></option>
				<option value="Unsubscribed" <?php if($search_sts=='Unsubscribed') { echo 'selected="selected"' ; } ?>><?php _e('Unsubscribed', 'email-posts-to-subscribers'); ?></option>
				<option value="Single Opt In" <?php if($search_sts=='Single Opt In') { echo 'selected="selected"' ; } ?>><?php _e('Single Opt In', 'email-posts-to-subscribers'); ?></option>
			</select>
			<select name="search_count_action" id="search_count_action" onchange="return _elp_search_count_action(this.value)">
				<option value="1" <?php if($search_count=='1') { echo 'selected="selected"' ; } ?>>1 to 200 emails</option>
				<option value="201" <?php if($search_count=='201') { echo 'selected="selected"' ; } ?>>201 to 400 emails</option>
				<option value="401" <?php if($search_count=='401') { echo 'selected="selected"' ; } ?>>401 to 600 emails</option>
				<option value="601" <?php if($search_count=='601') { echo 'selected="selected"' ; } ?>>601 to 800 emails</option>
				<option value="801" <?php if($search_count=='801') { echo 'selected="selected"' ; } ?>>801 to 1000 emails</option>
				<option value="0" <?php if($search_count=='0') { echo 'selected="selected"' ; } ?>>display all</option>
			</select>
		</div>
    </div>
	</form>
    <p class="description"><?php echo ELP_OFFICIAL; ?></p>
  </div>
</div>