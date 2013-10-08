<?php
#######################################################################
## HENTER INN ET DATEPICKER-TIDSPUNKT								 ##
#######################################################################
function getDatePickerTime($postname) {
	$vals = explode('.', $_POST[$postname.'_datepicker']);
	
	$v = array('d'=>$vals[0],
		  	   'm'=>$vals[1],
			   'y'=>$vals[2],
			   'h'=>$_POST[$postname.'_time'],
			   'i'=>$_POST[$postname.'_min']
				 );
	return mktime($v['h'],$v['i'],0,$v['m'],$v['d'],$v['y']);
}

function UKMprogram_save($info) {
	
	UKM_loader('api/forestilling.class');

	$_POST['c_start'] = getDatePickerTime('c_start');	
	unset($_POST['c_start_time']);
	unset($_POST['c_start_min']);
	unset($_POST['c_start_datepicker']);

	if($_POST['oppmote']=='false') {
		$_POST['c_delay'] = 0;
		$_POST['c_before'] = 0;
	}

	echo '<pre>';
	var_dump($_POST);
	echo '</pre>';
	
	$c = new forestilling($_POST['c_id']);
	$updates = array('c_name','c_place','c_start','c_visible_detail','c_visible_program','c_before','c_delay');
	foreach($updates as $field)
		$c->update($field);
}
?>