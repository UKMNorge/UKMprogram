<?php
function UKMprogram_save($info) {
	require_once('UKM/monstring.class.php');
	
	$m = new monstring_v2(get_option('pl_id'));
    
    $start = $m->getStart();
    $start->modify('+1 hour');

	$sql = new SQLins('smartukm_concert');
	$sql->add('c_name','Ny hendelse');
	$sql->add('pl_id',get_option('pl_id'));
	$sql->add('c_start', $start->getTimestamp());
	$sql->run();
	
	do_action('UKMprogram_save', 'leggtil_forestilling', $sql->insid());
	die(get_bloginfo('url').'/wp-admin/admin.php?page=UKMprogram_admin&tab=1&c_id='.$sql->insid());
}
?>