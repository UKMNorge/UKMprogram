var alle_lister = new Array();
var visTeller = false;
var infoIkon = '<img src="http://ico.ukm.no/info-sirkel-256.png" alt="Klikk flere ganger!" title="Klikk flere ganger" class="dash_info clickable" width="16" style="float:right; margin-right: 4px;" />';
var searchIkon = '<img src="http://ico.ukm.no/search-256.png" alt="Klikk for å se hvilke hendelser innslaget deltar i" title="Klikk for å se hvilke hendelser innslaget deltar i" class="dash_search clickable" width="16" style="float:right; margin-right: 4px;" />'; 

jQuery(document).ready(function(){
	var i_liste_cancel = false;

	jQuery('#hide_all_details').click(function () {
		jQuery(".hideshow_details").click();
	});
	
	jQuery('li div.hideshow_details').live('click',function(){
		jQuery(this).parents('ul').find('li.dash_innslag').slideToggle();
		jQuery(this).find('div.hideshow_icons').toggle();
		status = jQuery(this).parents('ul').attr('data-show');
		jQuery(this).parents('ul').attr('data-show', status == 'true' ? 'false':'true');
		
	});

	jQuery('#changeData').click(function() {
		jQuery('li.dash_innslag img.dash_info').click();
	});

	jQuery('li div.detaljprogram').click(function(){
		id = jQuery(this).parents('li').attr('id');
		var data = 'action=UKMprogram_ajax'
				 + '&save=program'
				 + '&c_id='+id;
		jQuery('li#'+id+' div.detaljprogram span').html('Oppdaterer...');
		jQuery.post(ajaxurl, data, function(response){
			data = jQuery.parseJSON(response);
			jQuery('li#'+data.id+' div.detaljprogram span').html(data.text);
			jQuery('li#'+data.id+' div.detaljprogram img').attr('src', 
				jQuery('li#'+data.id+' div.detaljprogram img').attr('src').replace('light-green', data.icon).replace('red', data.icon)
			);
		});
	});

	jQuery('li div.rammeprogram').click(function(){
		id = jQuery(this).parents('li').attr('id');
		var data = 'action=UKMprogram_ajax'
				 + '&save=program'
				 + '&ramme=true'
				 + '&c_id='+id;
		jQuery('li#'+id+' div.rammeprogram span').html('Oppdaterer...');
		jQuery.post(ajaxurl, data, function(response){
			data = jQuery.parseJSON(response);
			jQuery('li#'+data.id+' div.rammeprogram span').html(data.text);
			jQuery('li#'+data.id+' div.rammeprogram img').attr('src', 
				jQuery('li#'+data.id+' div.rammeprogram img').attr('src').replace('light-green', data.icon).replace('red', data.icon)
			);
		});

	});

	jQuery('li.dash_innslag img.dash_search').live('click', function(){
		findID = jQuery(this).parents('li').attr('id');
		
		jQuery('ul.dash_forestilling li.dash_innslag').each(function(){
			if(jQuery(this).attr('id')==findID) {
				varighet = parseInt(jQuery(this).attr('data-tall'));
				if(varighet > 7)
					varighet = 7000;
				else if(varighet < 2)
					varighet = 2000;
				else
					varighet = varighet*1000;
				
				jQuery(this).effect('highlight', {color: '#1e4a45'}, varighet);	
			}
		});
	});	
	
	jQuery('li.dash_innslag img.dash_info').live('click', function(){
		showNextInfo(jQuery(this).parents('li'));
	});
	
	jQuery('li.dash_innslag').each(function(){
		showInfo(jQuery(this));
	});


	jQuery('#delete-concert').click(function(){
		if(confirm('Er du sikker på at du vil slette hendelsen og alle detaljer?')) {
			var data = 'action=UKMprogram_ajax'
					 + '&save=slett_forestilling';
			jQuery.post(ajaxurl, data, function(response){
				//alert(response);
				window.location.href = response;
			});
		}	
	});
	
	jQuery('#duplicate-concert').click(function(){
		if(confirm('Du vil nå lage en kopi av denne hendelsen. Kopien kan for eksempel brukes til teknisk prøve. Vil du lage en kopi?')) {
			var data = 'action=UKMprogram_ajax'
					 + '&save=dupliser_forestilling';
			jQuery.post(ajaxurl, data, function(response){
				window.location.href = response;
			});
		}	
	});

	// GROVSORTERING FORESTILLING DASHBOARD
	jQuery('.dash_forestilling, #dash_supplering,#dash_alle').sortable({
		connectWith: '.dash_forestilling, #dash_alle',
		tolerance: 'pointer',
		items: 'li:not(.forestilling_detaljer)',
		start: function(event, ui) {
            item = ui.item;
            newList = oldList = ui.item.parent();
        },
        stop: function(event, ui) {
            //alert("Moved " + item.text() + " from " + oldList.attr('id') + " to " + newList.attr('id'));
            oppdaterListe(oldList.attr('id'));
            if(oldList.attr('id')!=newList.attr('id'))
            	oppdaterListe(newList.attr('id'));
            	
			antall_hendelser = new Array();
			jQuery('ul.dash_forestilling li.dash_innslag').each(function(){
				if(jQuery(this).attr('id')==ui.item.attr('id'))
					antall_hendelser.push(jQuery(this).attr('id'));
			});
			showCounter(ui.item, antall_hendelser.length);
        },
        change: function(event, ui) {  
            if(ui.sender) newList = ui.placeholder.parent();
        },
        receive: function(event, ui){
			// mottaker av objektet er alle innslag (= fjern)
			if(jQuery(this).attr('id')=='dash_alle') {				
				// Hvis objektet nå kun finnes i suppleringslisten (alle innslag) må det også legges til i "nye påmeldinger"
				i_hendelse = new Array();
				jQuery('ul.dash_forestilling li.dash_innslag').each(function(){
					i_hendelse.push(jQuery(this).attr('id'));
				});
				
				// Innslaget er ikke i noen hendelse, legg til i nye
				if(jQuery.inArray(jQuery(ui.item).attr('id'), i_hendelse)==-1) {
					jQuery('#dash_supplering').append(ui.item.clone());
				}
				
				// Objektet finnes alltid i listen alle innslag, fjern derfor dropped
				ui.item.remove();
			} else {
	        	i_liste = new Array();
    	    	i_liste_cancel = false;
        		// Loop alle elementer i mottaker i tilfelle den allerede er der
        		jQuery(this).find('li').each(function(){
        			// Elementet finnes ?
					if(jQuery.inArray(jQuery(this).attr('id'), i_liste)!=-1) {
						jQuery(this).parent().find('#'+jQuery(this).attr('id')).effect('pulsate',{times: 5},200);
						i_liste_cancel = true;
					}
					// Legg til elementet i listen
					i_liste.push(jQuery(this).attr('id'));
				});
				visTeller=false;
				// Hvis funnet, avbryt mottak
				if(i_liste_cancel){
					jQuery(ui.sender).sortable('cancel');
				// Elementet ble ikke funnet, og avsender er listen "alle innslag"
				} else if(ui.sender.attr('id')=='dash_alle') {
					visTeller=true;
					jQuery(ui.sender).append(ui.item.clone());
				} else {
					visTeller=true;
				}
			}
		recieve_hidden(jQuery(this).attr('id'));
        }
	}).disableSelection();

	jQuery('#leggtilforestilling').click(function(){
		var data = 'action=UKMprogram_ajax'
				 + '&save=leggtil_forestilling';
		jQuery.post(ajaxurl, data, function(response){
			//alert(response);
			window.location.href = response;
		});
	});

	jQuery('#hugesubmit').click(function(){
		jQuery(this).find('#lagre').html('Lagrer...');
		
		var data = 'action=UKMprogram_ajax'
				 + '&save=lagre_forestilling'
				 + '&'+jQuery('#forestilling_form').serialize();
		jQuery.post(ajaxurl, data, function(response){
			jQuery('#hugesubmit #lagre').html('Lagret');
			window.location.href = '?page=UKMprogram_admin';
			//setTimeout(function(){
			//	jQuery('#hugesubmit #lagre').html('Lagre')
			//},2000);
		});
	});
	
	jQuery('.forestilling_detaljer .name').click(function(){
		window.location.href = '?page=UKMprogram_admin&c_id='+jQuery(this).parents('li').attr('id');
	});
    jQuery( "#program_rekkefolge" ).sortable({
      	update: function(event, ui) {
       		oppdaterRekkefolge(),
       		lagreRekkefolge(jQuery('#program_rekkefolge').sortable('toArray'))
       		},
       	handle: "div.drag"
    });
    jQuery( "#program_teknisk_rekkefolge" ).sortable({
      	update: function(event, ui) {
       		oppdaterRekkefolge(),
       		lagreTekniskRekkefolge(jQuery('#program_teknisk_rekkefolge').sortable('toArray'))
       		},
       	handle: "div.drag"
    });

	oppdaterRekkefolge();
	
	jQuery('#skjulSuppleringsliste').click(function(){
		jQuery('#visSuppleringsliste').show();
		jQuery('#skjulSuppleringsliste').hide();
		jQuery('#suppleringsliste').hide();
		jQuery('#supplering_filterboks').hide();
	});
	
	jQuery('#visSuppleringsliste').click(function(){
		jQuery('#supplering_type_innslag option:first-child').attr("selected", "selected");
		jQuery('#supplering_deltar_i option:first-child').attr("selected", "selected");

		jQuery('#visSuppleringsliste').hide();
		jQuery('#skjulSuppleringsliste').show();
		jQuery('#supplering_filterboks').slideDown();
	
		loadFilterList();
	});
	
	jQuery('#supplering_filtrer').click(function(){
		loadFilterList();
	})
	
	jQuery('div.ikon_meld_av_forestilling').live("click",function(){
		sikker = confirm('Vil du fjerne innslaget fra hendelsen (det meldes IKKE av mønstringen!) ?');
		if(sikker) {
			bid = jQuery(this).parents('.innslag').attr('id');
			var data = {action: 'UKMprogram_ajax',
		 				save: 'fjern_fra_forestilling',
						b_id: bid,
						c_id: jQuery('#innslagsrekkefolge').val()
					   }
			jQuery.post(ajaxurl, data, function(response){
				var data = jQuery.parseJSON(response);
				if (data.result === true) {
					jQuery('.innslag#'+bid).hide("highlight", {color: '#f3776f'}, 1000);
					setTimeout(function() {
						jQuery('.innslag#'+bid).remove();
						oppdaterRekkefolge();
					}, 1100);
				} else {
					alert('En feil oppstod ved fjerning');
				}
			});
		}
	});

	jQuery('#vis_detalj').click(function(){
		jQuery('#vis_ramme').attr('selected','selected');
		jQuery('#vis_ramme').attr('checked','checked');
	});



	jQuery('#skjul_ramme').click(function(){
		jQuery('#detaljprogram').slideUp();
	});

	jQuery('#vis_ramme').click(function(){
		jQuery('#detaljprogram').slideDown();
	});
	
	if(jQuery('#vis_ramme').attr('checked')=='checked') {
		jQuery('#detaljprogram').show();
	} else {
		jQuery('#detaljprogram').hide();
	}
	
	jQuery('#readmore').click(function() {
		jQuery('#forklaring_tekst').toggle();
		if (jQuery('#readmore').text() == 'Les mer')
			jQuery('#readmore').text('Skjul');
		else
			jQuery('#readmore').text('Les mer');
	});
	
	jQuery('#oppmote_ja').click(function(){
		jQuery('#oppmotedetaljer').slideDown();
	});
	jQuery('#oppmote_nei').click(function(){
		jQuery('#oppmotedetaljer').slideUp();
	});
	
	oppdaterOppmote();
	oppdaterDelay();

	jQuery( ".datepicker" ).datepicker( {
		minDate: jQuery('#datepicker_start').val(), 
		maxDate: jQuery('#datepicker_stop').val(), 
		dateFormat: 'dd.mm.yy'
	});
	
	jQuery('#c_start_time').change(function(){oppdaterOppmote(),oppdaterDelay()});
	jQuery('#c_start_min').change(function(){oppdaterOppmote(),oppdaterDelay()});
	jQuery('#c_before').change(function(){oppdaterDelay()});
	
//	alert(jQuery('#oppmote_nei').attr('checked'));
	if(jQuery('#oppmote_ja').attr('checked')=='checked')
		jQuery('#oppmotedetaljer').show();
	
    jQuery.datepicker.regional['no'] = {
		closeText: 'Lukk',
        prevText: '&laquo;Forrige',
		nextText: 'Neste&raquo;',
		currentText: 'I dag',
        monthNames: ['Januar','Februar','Mars','April','Mai','Juni',
        'Juli','August','September','Oktober','November','Desember'],
        monthNamesShort: ['Jan','Feb','Mar','Apr','Mai','Jun',
        'Jul','Aug','Sep','Okt','Nov','Des'],
		dayNamesShort: ['S&oslash;n','Man','Tir','Ons','Tor','Fre','L&oslash;r'],
		dayNames: ['S&oslash;ndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','L&oslash;rdag'],
		dayNamesMin: ['S&oslash;','Ma','Ti','On','To','Fr','L&oslash;'],
		weekHeader: 'Uke',
        dateFormat: 'yy-mm-dd',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
    jQuery.datepicker.setDefaults(jQuery.datepicker.regional['no']);
   
});


function substr_replace(str, replace, start, length) {
  // http://kevin.vanzonneveld.net
  // +   original by: Brett Zamir (http://brett-zamir.me)

  if (start < 0) { // start position in str
    start = start + str.length;
  }
  length = length !== undefined ? length : str.length;
  if (length < 0) {
    length = length + str.length - start;
  }
  return str.slice(0, start) + replace.substr(0, length) + replace.slice(length) + str.slice(start + length);
}

function shortString(str, length) {
	if(length == undefined || length == null)
		length = 20;

	if( str.length > length ) {
		separator = '...';
		separatorlength = separator.length ;
		maxlength = length-3;
		start = maxlength / 2 ;
		trunc =  str.length - maxlength;
		return substr_replace(str, separator, start, trunc);
	}
	return str;
}
function oppdaterListe(listeID) {
	var data = 'action=UKMprogram_ajax'
		 + '&save=grovsortering'
		 + '&forestilling='+listeID.replace('dash_forestilling_','')
		 + '&'+jQuery('#'+listeID).sortable('serialize');

	recieve_hidden(listeID);
	jQuery.post(ajaxurl, data, function(response){
		var data = jQuery.parseJSON(response);
		jQuery('#dash_forestilling_'+data.c_id+' div.antall').html(data.antall+' innslag');
		jQuery('#dash_forestilling_'+data.c_id+' div.varighet').html(data.varighet);
	});
}

function recieve_hidden(listeID) {
	if(jQuery('#'+listeID).attr('data-show') == 'false') {
		//alert('#'+listeID + ': skjul ny');
		setTimeout(function(){
			jQuery('#'+listeID).find('li.dash_innslag').hide();
		}, 1000);
	}
}

function showCounter(object, antall_hendelser) {
	object.attr('data-tall', antall_hendelser);
	object.attr('data-antall', 'Deltar i ' + antall_hendelser+ ' hendelser');
	if(!visTeller)
		return;
	temp_html = object.html();
	object.html(object.attr('data-antall') + infoIkon + searchIkon);
	object.effect("highlight", {}, 3000);
	
	setTimeout(function(){
		object.html(temp_html);
		showInfo(object);
	}, 2500);
}

function showNextInfo(object) {
	state = parseInt(object.attr('data-state'));
	state++;
	if(state > 5)
		state = 0;

	object.attr('data-state', state);
	showInfo(object);
}

function showInfo(object) {
	state = parseInt(object.attr('data-state'));
	switch(state) {
		case 0:	show = 'name';		break;
		case 1:	show = 'type';		break;
		case 2:	show = 'time';		break;
		case 3: show = 'antall';	break;
		case 4: show = 'kommune';	break;
		case 5: show = 'titler'; 	break;
		default:
			alert('out of range');
	}
	if(show == 'kommune' && jQuery(object).attr('data-kommune') == 'false') {
		showNextInfo(object);
	}
	jQuery(object).html(shortString(jQuery(object).attr('data-'+show), 23) + infoIkon + searchIkon);
	jQuery(object).attr('alt', jQuery(object).attr('data-'+show));
	jQuery(object).attr('title', jQuery(object).attr('data-'+show));
	if(state == 0)
		jQuery(object).css('font-weight', 'bold');
	else
		jQuery(object).css('font-weight','normal');
}

function oppdaterOppmote(){
	var c_start_time = jQuery('#c_start_time :selected').val();
	var c_start_min = jQuery('#c_start_min :selected').val();
	jQuery('#c_before').find('option').each(function(){
		if(jQuery(this).val()=='0') {
			jQuery(this).html('Når forestillingen begynner (kl. '
								+c_start_time +':'+(c_start_min<10?'0':'')+ c_start_min+')');
		} else {
			c_start_min -= 5;
			if(c_start_min < 0) {
				c_start_time--;
				c_start_min = 55;
			}
			
			jQuery(this).html(jQuery(this).val() 
							+ ' min før (kl. '+c_start_time +':'+ (c_start_min<10?'0':'')+c_start_min+')');
		}
	});
}

function oppdaterDelay(){
	delay_time = jQuery('#c_start_time :selected').val();
	delay_min = jQuery('#c_start_min :selected').val();
	oppmote = jQuery('#c_before :selected').val();
	delay_min = delay_min*1-oppmote*1;
	if(delay_min < 0) {
		delay_time--;
		delay_min = 60+delay_min;
	}
		
	jQuery('#c_delay :first').html('Alle møter samtidig (kl. '+delay_time + ':' + (delay_min<10?'0':'')+delay_min +')');
}

function loadFilterList() {
	jQuery('#suppleringsliste').html('Laster inn...');
	var data = {
		action: 'UKMprogram_ajax',
		c: 'suppleringsliste',
		v: 'suppleringsliste',
		c_id: jQuery('#innslagsrekkefolge').val(),
		type: jQuery('#supplering_type_innslag option:selected').val(),
		deltari: jQuery('#supplering_deltar_i option:selected').val()
	};
				
	jQuery.post(ajaxurl, data, function(response){
		jQuery('#suppleringsliste').html(response);
		jQuery('#suppleringsliste').slideDown();			
		initKnapper();
    	jQuery( "#program_supplering" ).sortable({
		  connectWith: '#program_rekkefolge',
		  handle: "div.add"
		});
	});
}
	
function oppdaterRekkefolge() {
	var program_counter = 0;
	jQuery('#program_rekkefolge').find('li.innslag').each(function(){
		jQuery(this).find('div.drag').show();
		jQuery(this).find('div.order').show();
		jQuery(this).find('div.add').hide();
		program_counter++;
		jQuery(this).find('div.order').html(program_counter);
	});
	jQuery('#program_teknisk_rekkefolge').find('li.innslag').each(function(){
		jQuery(this).find('div.drag').show();
		jQuery(this).find('div.order').show();
		program_counter++;
		jQuery(this).find('div.order').html(program_counter);
	});
}

function lagreTekniskRekkefolge(rekkefolge) {
	var data = 'order='+rekkefolge
				 + '&action=UKMprogram_ajax'
				 + '&c_id='+jQuery('#innslagsrekkefolge').val()
				 + '&save=lagre_teknisk_rekkefolge';
	jQuery.post(ajaxurl, data, function(response){
		//alert(response);
	});
}
	
function lagreRekkefolge(rekkefolge) {
	var data = 'order='+rekkefolge
				 + '&action=UKMprogram_ajax'
				 + '&c_id='+jQuery('#innslagsrekkefolge').val()
				 + '&save=lagre_rekkefolge';
	jQuery.post(ajaxurl, data, function(response){
		//alert(response);
	});
}