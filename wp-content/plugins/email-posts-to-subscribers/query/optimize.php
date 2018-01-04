<?php
class elp_cls_optimize
{
	// Start - Sent details
	public static function elp_optimize_setdetails()
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		
		$total = elp_cls_dbquery2::elp_sentmail_count($id = 0);
		if ($total > 20)
		{
			$delete = $total - 20;
			$sSql = "DELETE FROM `".$prefix."elp_sentdetails` ORDER BY elp_sent_id ASC LIMIT ".$delete;
			$wpdb->query($sSql);
		}
		
		$sSql = "DELETE FROM `".$prefix."elp_deliverreport` WHERE elp_deliver_sentguid NOT IN";
		$sSql = $sSql . " (SELECT elp_sent_guid FROM `".$prefix."elp_sentdetails`)";
		$wpdb->query($sSql);
		return true;
	}
	// End - Sent details
}
?>