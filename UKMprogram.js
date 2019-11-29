/* DASHBOARD */

function setState(clicked, state) {

    // Hvorvidt hendelsen skal vises i program-listen
    if (clicked.hasClass('toggleSynlig')) {
        clicked.parents('.hendelse').toggleClass('panel-danger', !state).toggleClass('panel-success', state);
    }
    clicked.attr('data-state', state ? 'on' : 'off');

    if (state) {
        clicked.find('.state-on').fadeIn();
        clicked.find('.state-off').hide();
    } else {
        clicked.find('.state-on').hide();
        clicked.find('.state-off').fadeIn();
    }
}
jQuery(document).on('click', '.toggleState', function() {
    var clicked = jQuery(this);
    var new_state_on = jQuery(this).attr('data-state') == 'off';

    setState(clicked, new_state_on);

    jQuery.post(
        ajaxurl, {
            action: 'UKMprogramV2_ajax',
            controller: 'save',
            save: jQuery(this).attr('data-controller'),
            state: new_state_on,
            hendelse: clicked.parents('.hendelse').attr('data-id')
        },
        function(response) {
            /* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
            if (response !== null && response.success) {
                // do nothing
            } else {
                setState(clicked, !new_state_on);
                alert('Beklager, en feil har oppstått.');
            }
        }
    );
});

/* HENDELSE */
jQuery(document).on('change', 'input[name="type"]', function() {

    var current = jQuery('input[name="type"]:checked').val();

    if (current == 'post') {
        jQuery('#posts').slideDown();
    } else {
        jQuery('#posts').slideUp()
    }

    if (current == 'category') {
        jQuery('#categories').slideDown();
    } else {
        jQuery('#categories').slideUp();
    }

    if (current == 'default') {
        jQuery('#oppmote').slideDown();
    } else {
        jQuery('#oppmote').slideUp();
    }
});


jQuery(document).on('change', '#angi_oppmote', function() {
    if (jQuery(this).val() == 'true') {
        jQuery('#oppmote_detaljer').slideDown();
    } else {
        jQuery('#oppmote_detaljer').slideUp();
    }
});

jQuery(document).ready(() => {
    jQuery('input[name="type"]').change();
    jQuery('#angi_oppmote').change();
});

/* GROVSORTERING */
jQuery(document).on('click', '#startSort', function() {
    var hendelser = [];

    jQuery('input[name="hendelser"]:checked').each(function(e) {
        hendelser.push(jQuery(this).val());
    });

    window.location.href = window.location.href + '&hendelser=' + hendelser.join('-');
});


function oppdaterListe(liste) {
    console.warn('SAVE LIST');
}

function showCounter(item, length) {
    console.warn('VIS EN COUNTER (antall hunder?)');
}

function receive_hidden(item) {
    console.warn('Receive hidden');
}

jQuery(document).ready(function() {
    /* SORTERING */
    jQuery('.detaljprogram, #innslagliste').sortable({
        connectWith: '.detaljprogram, #innslagliste',
        tolerance: 'pointer',
        items: 'li',
        start: function(event, ui) {
            item = ui.item;
            newList = oldList = ui.item.parent();
        },
        stop: function(event, ui) {
            //alert("Moved " + item.text() + " from " + oldList.attr('id') + " to " + newList.attr('id'));
            oppdaterListe(oldList.attr('id'));
            if (oldList.attr('id') != newList.attr('id')) {
                oppdaterListe(newList.attr('id'));
            }

            antall_hendelser = new Array();
            jQuery('ul.dash_forestilling li.dash_innslag').each(
                () => {
                    if (jQuery(this).attr('id') == ui.item.attr('id')) {
                        antall_hendelser.push(jQuery(this).attr('id'));
                    }
                }
            );
            showCounter(ui.item, antall_hendelser.length);
        },
        change: function(event, ui) {
            if (ui.sender) {
                newList = ui.placeholder.parent();
            }
        },
        receive: function(event, ui) {
            /*
            // mottaker av objektet er alle innslag (= fjern)
            if (jQuery(this).attr('id') == 'dash_alle') {
            	// Hvis objektet nå kun finnes i suppleringslisten (alle innslag) må det også legges til i "nye påmeldinger"
            	i_hendelse = new Array();
            	jQuery('ul.dash_forestilling li.dash_innslag').each(function () {
            		i_hendelse.push(jQuery(this).attr('id'));
            	});

            	// Innslaget er ikke i noen hendelse, legg til i nye
            	if (jQuery.inArray(jQuery(ui.item).attr('id'), i_hendelse) == -1) {
            		jQuery('#dash_supplering').append(ui.item.clone());
            	}

            	// Objektet finnes alltid i listen alle innslag, fjern derfor dropped
            	ui.item.remove();
            } else {
            	i_liste = new Array();
            	i_liste_cancel = false;
            	// Loop alle elementer i mottaker i tilfelle den allerede er der
            	jQuery(this).find('li').each(function () {
            		// Elementet finnes ?
            		if (jQuery.inArray(jQuery(this).attr('id'), i_liste) != -1) {
            			jQuery(this).parent().find('#' + jQuery(this).attr('id')).effect('pulsate', { times: 5 }, 200);
            			i_liste_cancel = true;
            		}
            		// Legg til elementet i listen
            		i_liste.push(jQuery(this).attr('id'));
            	});
            	visTeller = false;
            	// Hvis funnet, avbryt mottak
            	if (i_liste_cancel) {
            		jQuery(ui.sender).sortable('cancel');
            		// Elementet ble ikke funnet, og avsender er listen "alle innslag"
            	} else if (ui.sender.attr('id') == 'dash_alle') {
            		visTeller = true;
            		jQuery(ui.sender).append(ui.item.clone());
            	} else {
            		visTeller = true;
            	}
            }
            receive_hidden(jQuery(this).attr('id'));
            */
        }
    }).disableSelection();
});