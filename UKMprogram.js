/* DASHBOARD */

function setState( clicked, state ) {

	// Hvorvidt hendelsen skal vises i program-listen
	if( clicked.hasClass('toggleSynlig') ) {
		clicked.parents('.hendelse').toggleClass('panel-danger', !state).toggleClass('panel-success', state);		
	}
	clicked.attr('data-state', state ? 'on' : 'off' );

	if( state ) {
		clicked.find('.state-on').fadeIn();
		clicked.find('.state-off').hide();
	} else {
		clicked.find('.state-on').hide();
		clicked.find('.state-off').fadeIn();
	}
}
jQuery(document).on('click', '.toggleState', function() {
	var clicked = $(this);
	var new_state_on = $(this).attr('data-state') == 'off';

	setState( clicked, new_state_on );

	jQuery.post(
		ajaxurl,
		{
			action: 'UKMprogram_ajax',
			controller: 'save',
			save: jQuery(this).attr('data-controller'),
			state: new_state_on,
			hendelse: clicked.parents('.hendelse').attr('data-id')
		},
		function( response ) {
			if( response !== null && response !== undefined ) {
                try {
                    response = JSON.parse( response );
                } catch( error ) {
                    response = null;
                }
            }
            
            /* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
            if( response !== null && response.success ) {
                // do nothing
            } else {
				setState( clicked, !new_state_on );
                alert('Beklager, en feil har oppstått.');
            }
		}
	);
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

/* GROVSORTERING */
$(document).on('click', '#startSort', function(){
	var hendelser = [];

	$('input[name="hendelser"]:checked').each( function(e) {
		hendelser.push( $(this).val() );
	} );

	window.location.href = window.location.href + '&hendelser='+ hendelser.join('-');
});