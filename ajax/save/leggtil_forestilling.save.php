<?php
function UKMprogram_save($info) {
	require_once('UKM/monstring.class.php');
	
	$m = new monstring(get_option('pl_id'));
	
	$sql = new SQLins('smartukm_concert');
	$sql->add('c_name','Ny hendelse');
	$sql->add('pl_id',get_option('pl_id'));
	$sql->add('c_start', $m->g('pl_start')+3600);
	$sql->run();
		
	die(get_bloginfo('url').'/wp-admin/admin.php?page=UKMprogram_admin&tab=1&c_id='.$sql->insid());
}
?>