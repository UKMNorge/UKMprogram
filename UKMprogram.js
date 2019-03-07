/* DASHBOARD */
jQuery(document).on('click', '.toggleState', function() {

	var new_state_on = $(this).attr('data-state') == 'off';
	jQuery(this).attr('data-state', new_state_on ? 'on' : 'off' );

	// Hvorvidt hendelsen skal vises i program-listen
	if( $(this).hasClass('toggleSynlig') ) {
		$(this).parents('.hendelse').toggleClass('panel-danger', !new_state_on).toggleClass('panel-success', new_state_on);		
	}

	if( new_state_on ) {
		jQuery(this).find('.state-on').fadeIn();
		jQuery(this).find('.state-off').hide();
	} else {
		jQuery(this).find('.state-on').hide();
		jQuery(this).find('.state-off').fadeIn();
	}
});

/* HENDELSE */
$(document).on('change', 'input[name="type"]', function(){
	
	var current = $('input[name="type"]:checked').val();

	if( current == 'post' ) {
		$('#posts').slideDown();
	} else {
		$('#posts').slideUp()
	}

	if( current == 'category') {
		$('#categories').slideDown();
	} else {
		$('#categories').slideUp();
	}

	if( current == 'default' ) {
		$('#oppmote').slideDown();
	} else {
		$('#oppmote').slideUp();
	}
});


$(document).on('change', '#angi_oppmote', function(){
	if( $(this).val() == 'true' ) {
		$('#oppmote_detaljer').slideDown();
	} else {
		$('#oppmote_detaljer').slideUp();
	}
});

$(document).ready(()=>{
	$('input[name="type"]').change();
	$('#angi_oppmote').change();
});