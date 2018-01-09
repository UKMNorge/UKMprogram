visRedigering = false;
clickevents = false;

jQuery(document).ready(function(){
	
	initKnapper();
		
	/* MELD AV INNSLAG */
	jQuery('.innslag div.ikon_meld_av').click(function(){
		sikker = confirm('Er du sikker på at du vil melde av dette innslaget fra din mønstring?');
		if(sikker) {
			bid = jQuery(this).parents('.innslag').attr('id');
			var data = {action: 'UKMdeltakere_gui',
		 				save: 'meldav',
						i: bid
					   }
			jQuery.post(ajaxurl, data, function(response){
				var data = jQuery.parseJSON(response);
				if (data.result === true) {
					//doAdvarsler(data,container);
					jQuery('.innslag#'+bid).hide("highlight", {color: '#f3776f'}, 1000);
				}
				else
					alert('En feil oppstod ved sletting');
			});
		}
	});
	
	jQuery('.ikon_duplicates').click(function(){
		inspectDuplicates(jQuery(this));
	});
	
	showActive();
});

function inspectDuplicates(jqobj) {
	skjulAlleDetaljer();
	bid = jqobj.parents('li').attr('id');
	getDetaljer('duplicates','duplicates', bid,'detaljer_'+bid);
	jqobj.parents('li').addClass('grayBackground');
}

function skjulAlleDetaljer() {
	jQuery('li.innslag').removeClass('grayBackground');
	jQuery('.innslag .detaljer').html('');
	jQuery('.ikon_detaljer').show();
	jQuery('.ikon_detaljer_skjul').hide();
	clickevents = false;	
}

function innslagVisDetaljer(jqobj) {
	skjulAlleDetaljer();
	bid = jqobj.parents('li.innslag').attr('id');
	getDetaljer('innslag_detaljer','innslag', bid,'detaljer_'+bid);
	jQuery('li.innslag#'+bid).find('div.ikon_detaljer').hide();
	jQuery('li.innslag#'+bid).find('div.ikon_detaljer_skjul').show();
	jqobj.parents('li').addClass('grayBackground');
}

function innslagVisTittelloseDetaljer(jqobj) {
	skjulAlleDetaljer();
	bid = jqobj.parents('li').attr('id');
	getDetaljer('innslag_tittellos','innslag_tittellos', bid,'detaljer_'+bid);
	jQuery('li.innslag#'+bid).find('div.ikon_detaljer').hide();
	jQuery('li.innslag#'+bid).find('div.ikon_detaljer_skjul').show();
	jqobj.parents('li').addClass('grayBackground');
}

function getDetaljer(control,view,bid,container,id2){
	//jQuery('.innslag .detaljer').html('');
	jQuery('#'+container).html('<span class="loading"><img src="//ico.ukm.no/loading.gif" width="32" /><span>Laster inn...</span></span>');
	var data = {
		action: 'UKMdeltakere_gui',
		c: control,
		v: view,
		i: bid,
		i2: id2
	};
				
	jQuery.post(ajaxurl, data, function(response){
		jQuery('#'+container).html(response);
		
		if(visRedigering) {
			visRedigering=false;
			getDetaljer('innslag','innslag_rediger',bid,'redigerInnslag'+bid);
		}
		
		if(clickevents == false) {
			/* REDIGER PERSON */
			jQuery('.ikon_person_rediger').click(function(){
				getDetaljer('person_rediger','person_rediger', jQuery(this).parent().parent().attr('id'),'persondetaljer_'+jQuery(this).parent().parent().attr('id'), jQuery(this).parents('li.innslag').attr('id'));
			});
			
			jQuery('.bytt_kontaktperson').click(function(){
				var id = 'kontaktperson_'+jQuery(this).parent().attr('id');
				getDetaljer('bytt_kontaktperson', 'bytt_kontaktperson', jQuery(this).parent().attr('id'), 'kontaktperson_'+jQuery(this).parent().attr('id'), '');
				jQuery('#'+id).dialog({
					title : 'Bytt kontaktperson',
					closeText : 'Lukk',
					width : '400',
					modal : true
				});
				//alert(id);
			});
			
			jQuery('.legg_til_person').click(function(){
				var id = 'leggtilperson_'+jQuery(this).parent().attr('id');
				getDetaljer('leggtilperson', 'leggtilperson', jQuery(this).parent().attr('id'), 'leggtilperson_'+jQuery(this).parent().attr('id'), '');
				kontaktpersonMODAL = id;
				jQuery('#'+id).dialog({
					title : 'Legg til person',
					closeText : 'Lukk',
					width : '400',
					modal : true
				});

			});
		
			/* FJERN PERSON */
			jQuery('.person_remove').click(function(){
				var id = jQuery(this).parents('ul').attr('id');
				b_id = jQuery('#persondetaljer_'+id).parents('li.innslag').attr('id');
				var data = {action: 'UKMdeltakere_gui',
		 					save: 'fjernperson',
							b_id: b_id,
							p_id: id
						   }
				jQuery.post(ajaxurl, data, function(response){
					var data = jQuery.parseJSON(response);
					if (data.result === true) {
						doAdvarsler(data,container);
						updateDetaljer_rekalkuler_list_person(data);
						jQuery('ul#'+id).hide("highlight", {color: '#f3776f'}, 1000);
						if(data.reloadInnslag === true) {
							reloadInnslagInfo(data.b_id);
						}
					} else {
						alert('En feil oppstod ved sletting');
					}
				});
				//getDetaljer('redigertittel', 'redigertittel', id, 'redigertittel_'+id, jQuery(this).parents('li.innslag').attr('id'));	
			});
			
			/* VIDERESEND PERSON TIL SIN MØNSTRING */
			jQuery('.person_videresend').click(function(){
				var id = jQuery(this).parents('ul').attr('id');
				b_id = jQuery('#persondetaljer_'+id).parents('li.innslag').attr('id');
				var data = {action: 'UKMdeltakere_gui',
		 					save: 'videresendperson',
							b_id: b_id,
							p_id: id
						   }
				jQuery.post(ajaxurl, data, function(response){
					var data = jQuery.parseJSON(response);
					if (data.result === true) {
						doAdvarsler(data,container);
						updateDetaljer_rekalkuler_list_person(data);
						jQuery('ul#'+id).hide("highlight", {color: '#4cb45b'}, 1000);
						reloadInnslagInfo(data.b_id);
					} else {
						alert('En feil oppstod ved videresending');
					}
				});
				//getDetaljer('redigertittel', 'redigertittel', id, 'redigertittel_'+id, jQuery(this).parents('li.innslag').attr('id'));	
			});


			
			/* ENDRE TITTEL */
			jQuery('.title_icon').click(function(){
				var id = jQuery(this).parent().attr('id');
				getDetaljer('redigertittel', 'redigertittel', id, 'redigertittel_'+id, jQuery(this).parents('li.innslag').attr('id'));	
			});

			/* FJERN TITTEL */
			jQuery('.title_remove_icon').click(function(){
				var id = jQuery(this).parents('ul.innslag_titler').attr('id');
				b_id = jQuery('#'+id).parents('li.innslag').attr('id');
				var data = {action: 'UKMdeltakere_gui',
		 					save: 'fjerntittel',
							b_id: b_id,
							t_id: id
						   }
				jQuery.post(ajaxurl, data, function(response){
					var data = jQuery.parseJSON(response);
					if (data.result === true) {
						doAdvarsler(data,container);
						updateDetaljer_rekalkuler_list_tittel(data);
						jQuery('#'+id).hide("highlight", {color: '#f3776f'}, 1000);
						if(data.reloadInnslag === true) {
							reloadInnslagInfo(data.b_id);
						}
					} else {
						alert('En feil oppstod ved sletting');
					}
				});
			});

			/* VIDERESENDT TITTEL */
			jQuery('.title_videresend_icon').click(function(){
				var id = jQuery(this).parents('ul.innslag_titler').attr('id');
				b_id = jQuery('#'+id).parents('li.innslag').attr('id');
				var data = {action: 'UKMdeltakere_gui',
		 					save: 'videresendtittel',
							b_id: b_id,
							t_id: id
						   }
				jQuery.post(ajaxurl, data, function(response){
					var data = jQuery.parseJSON(response);
					if (data.result === true) {
						doAdvarsler(data,container);
						updateDetaljer_rekalkuler_list_tittel(data);
						jQuery('#'+id).hide("highlight", {color: '#f3776f'}, 1000);
						reloadInnslagInfo(data.b_id);
					} else {
						alert('En feil oppstod ved videresending');
					}
				});
			});

		
			jQuery('.legg_til_tittel').click(function(){
				var id = jQuery(this).parents('.innslag').attr('id');
				getDetaljer('leggtiltittel', 'leggtiltittel', id, 'leggtiltittel_'+id, '');
				kontaktpersonMODAL = 'leggtiltittel_'+id;
				jQuery('#leggtiltittel_'+id).dialog({
					title : 'Legg til tittel',
					closeText : 'Lukk',
					width : '400',
					modal : true
				});
			});
			
			jQuery('.legg_til_i_forestilling').click(function(){
				var id = jQuery(this).parents('.innslag').attr('id');
				getDetaljer('leggtiliforestilling', 'leggtiliforestilling', id, 'leggtiliforestilling_'+id, '');
				kontaktpersonMODAL = 'leggtiliforestilling_'+id;
				jQuery('#leggtiliforestilling_'+id).dialog({
					title : 'Legg til i forestilling',
					closeText : 'Lukk',
					width : '400',
					modal : true
				});
			});

			/* FJERN FRA FORESTILLING */
			jQuery('.forestillinger_remove_icon').click(function(){
				var id = jQuery(this).parents('ul.forestillinger').attr('id');
				b_id = jQuery('#'+id).parents('li.innslag').attr('id');
				var data = {action: 'UKMdeltakere_gui',
		 					save: 'fjernfraforestilling',
							b_id: b_id,
							c_id: id
						   }
				jQuery.post(ajaxurl, data, function(response){
					var data = jQuery.parseJSON(response);
					if (data.result === true) {
						doAdvarsler(data,container);
						jQuery('#'+id).hide("highlight", {color: '#f3776f'}, 1000);
					}
					else
						alert('En feil oppstod ved sletting');
				});
			});
				
			clickevents = true;
		}

		/* CANCEL DETALJER */
		jQuery('a.ajax_cancel').click(function(){
			jQuery(this).parents('li.innslag').find('div.detaljer').find('form').remove();
			return false;
/*
			jQuery('.innslag .detaljer').html('');
			jQuery('.innslag .detaljer').parent().removeClass('grayBackground');
			jQuery('.innslag .detaljer').parent().find('div.ikon_detaljer').show();
			jQuery('.innslag .detaljer').parent().find('div.ikon_detaljer_skjul').hide();
*/
		})
		jQuery('.ajax_cancel_modal').click(function(){
			jQuery('#'+kontaktpersonMODAL).dialog("close");
		})
		/* SUBMIT DETALJER */
		jQuery('input.ajax_save').click(function(){
			var data = jQuery(this).parents('form').serialize()
					 + '&action=UKMdeltakere_gui'
					 + '&save='+jQuery(this).attr('save')
					 + '&i='+jQuery(this).attr('bid');
					 
			var bid = jQuery(this).attr('bid');
			var container = jQuery(this).parent().parent().parent().attr('class');
			var view = jQuery(this).attr('save');
			
			jQuery(this).attr('disabled','disabled');
			jQuery(this).css('background-color', '#ccc');
			jQuery(this).css('color', '#666');
			jQuery(this).attr('value','Lagrer...');
					 
			jQuery.post(ajaxurl, data, function(response){
				//container.html('<div class="success">Skjemaet ble lagret.</div>');
				updateDetaljer(view,bid,container,response);
			});
		});
		
		/* SUBMIT DETALJER MODAL */
		jQuery('input.ajax_save_modal').click(function(){
			var data = jQuery(this).parents('form').serialize()
					 + '&action=UKMdeltakere_gui'
					 + '&save='+jQuery(this).attr('save')
					 + '&i='+jQuery(this).attr('bid');
					 
			var bid = jQuery(this).attr('bid');
			var container = jQuery(this).parent().parent().parent().attr('class');
			var view = jQuery(this).attr('save');
			
			jQuery(this).attr('disabled','disabled');
			jQuery(this).css('background-color', '#ccc');
			jQuery(this).css('color', '#666');
			jQuery(this).attr('value','Lagrer...');
			jQuery('#'+kontaktpersonMODAL).dialog("close");
					 
			jQuery.post(ajaxurl, data, function(response){
				//container.html('<div class="success">Skjemaet ble lagret.</div>');
				updateDetaljer(view,bid,container,response);
			});
		});
		
		/* GODKJENN INNSLAG */
		jQuery('.approveband').click(function(){
			bid = jQuery(this).parents('.innslag').attr('id');
			var data = {action: 'UKMdeltakere_gui',
		 				save: 'meldpa',
						i: bid
					   }
			jQuery.post(ajaxurl, data, function(response){
				var data = jQuery.parseJSON(response);
				if (data.result === true)
					jQuery('.innslag#'+bid).hide("highlight", {color: '#f3776f'}, 1000);
				else
					alert('En feil oppstod ved påmelding');
			});
		});
		
		/* BYTTE KONTAKTPERSON */
		jQuery('li.person_contact.clickable').click(function(){
			bid = jQuery(this).parents('.innslag').attr('id');
			var data = {action: 'UKMdeltakere_gui',
		 				save: 'bytt_kontaktperson',
						b_id: bid,
						p_id: jQuery(this).attr('data-pid')
					   }
			jQuery.post(ajaxurl, data, function(response){
				var data = jQuery.parseJSON(response);
				if (data.result === true) {
					jQuery('li.innslag#'+bid).find('div.kontaktperson').html(data.p_firstname+' ('+data.age+') <span class="phone">'+data.p_phone+'</span>');
					innslagVisDetaljer(jQuery('li.innslag#'+bid).find('div'));
				} else
					alert('En feil oppstod ved bytte av kontaktperson');
			});
		});
		
		/* LEGG TIL KONTAKTPERSON-FUNKSJONER */
				jQuery('#tidligere_registrerte_kontaktpersoner').bind('keyup change', filter_list);
		jQuery('#remove_choice').click(function(){
			jQuery(this).hide();
			jQuery('#person_liste input').each(function(){
				jQuery(this).prop('checked', false);
			});
			enable_new();
			clear_filter('#tidligere_registrerte_kontaktpersoner');
		});
		
		jQuery('#remove_new_data').click(function(){
			jQuery(this).hide();
			jQuery('.wrapper_new_person input').each(function(){
				jQuery(this).val('');
			});
			enable_old();
			clear_filter('#tidligere_registrerte_kontaktpersoner');
		});
		
		jQuery('#person_liste input').each(function(){
			jQuery(this).click(function(){
				jQuery('#remove_choice').show();
				disable_new();
			});
		});
		
		jQuery('.wrapper_new_person').children().each(function(){
			jQuery(this).change(function(){
				disable_old();
				jQuery('#remove_new_data').show();
			});
		});
		
		/**/
		
	});
}

function updateDetaljer(view,bid,container,response) {
	var data = jQuery.parseJSON(response);
	
	doAdvarsler(data,container);
	switch(view) {
		case 'innslag':
			if(data.reload) {
				jQuery('div.pameldte_missing').hide();
				jQuery('div.ukmdeltakere_wrapper').hide();
				jQuery('div.ukmdeltakere_wrapper').after('<span class="loading"><img src="/UKM/ico/loading.gif" width="32" /><span>Laster inn listen på nytt</span></span>');
				window.location.href=window.location.href.split("#")[0]+'#rediger_'+data.b_id;
				window.location.reload(); 
			}
				
			updateDetaljer_innslag(data,container);
			break;
		case 'person':
			updateDetaljer_deltaker(data,container);
			updateDetaljer_rekalkuler_list_person(data);
			break;
		case 'redigertittel':
			updateDetaljer_tittel(data,container);
			break;
		case 'leggtiltittel':
			updateDetaljer_visDetaljer(data,container);
			updateDetaljer_rekalkuler_list_tittel(data);
			break;
		case 'leggtilperson':
			if(data.error) {
				alert(data.error);
			}
			updateDetaljer_visDetaljer(data,container);
			updateDetaljer_rekalkuler_list_person(data);
			break;
		case 'leggtiliforestilling':
			updateDetaljer_visDetaljer(data,container);
			updateDetaljer_rekalkuler_list_tittel(data);
			break;
	}
}
function updateDetaljer_rekalkuler_list_tittel(data) {
	jQuery('li.innslag#'+data.b_id).find('div.tid').html(data.varighet);
	jQuery('li.innslag#'+data.b_id).find('div.titler').html(data.titler);
}
function updateDetaljer_rekalkuler_list_person(data) {
	jQuery('li.innslag#'+data.b_id +' > div.group').find('div.personer').html(data.personer);
	jQuery('li.innslag#'+data.b_id +' > div.group').find('div.snittalder').html('Snitt '+data.snitt +' år');
}

function updateDetaljer_visDetaljer(data,container) {
	jqobj = jQuery('li.innslag#'+data.innslag).find('div');
	innslagVisDetaljer(jqobj);
}

function updateDetaljer_tittel(data,container) {
	if(data.error) {
		alert(data.error);
		jQuery('.'+container).find('.ajax_save').removeAttr('disabled');
		jQuery('.'+container).find('.ajax_save').css('background-color', '');
		jQuery('.'+container).find('.ajax_save').css('color', '');
		jQuery('.'+container).find('.ajax_save').attr('value','Lagre');
	} else {
		var id = data.tittel;
		updateDetaljer_rekalkuler_list_tittel(data);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_tittel').html(data.ny_tittel);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_tittel').attr('title',data.ny_tittel_full);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_melodi_av').html(data.ny_melodi_av);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_melodi_av').attr('title',data.ny_melodi_av_full);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_tekst_av').html(data.ny_tekst_av);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_tekst_av').attr('title',data.ny_tekst_av);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_varighet').html(data.ny_varighet);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_varighet').attr('title',data.ny_varighet);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_koreografi').html(data.ny_koreografi);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_koreografi').attr('title',data.ny_koreografi_full);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_teknikk').html(data.ny_teknikk);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_teknikk').attr('title',data.ny_teknikk_full);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_type').html(data.ny_type);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_type').attr('title',data.ny_type_full);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_format').html(data.ny_format);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_format').attr('title',data.ny_format_full);

		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_beskrivelse').html(data.ny_beskrivelse);
		jQuery('.'+container).parents('li.innslag').find('ul.innslag_titler#'+data.tittel).find('li.title_beskrivelse').attr('title',data.ny_beskrivelse_full);

		jQuery('#edit_title_'+id).effect("highlight", {color: '#a0cf67'}, 700,function (){	
			jQuery('#edit_title_'+id).slideUp(function(){
				jQuery('#edit_title_'+id).remove();
			});
		});
	}

}

function updateDetaljer_deltaker(data,container) {
	if(data.error) {
		alert(data.error);
		jQuery('.'+container).find('.ajax_save').removeAttr('disabled');
		jQuery('.'+container).find('.ajax_save').css('background-color', '');
		jQuery('.'+container).find('.ajax_save').css('color', '');
		jQuery('.'+container).find('.ajax_save').attr('value','Lagre');
	} else {
		var id = data.p_id;
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_name').html(data.p_name + ' ('+data.p_alder+')');
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_name').attr('title',data.p_name_full);
		
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_cellnumber').html(data.p_phone);
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_cellnumber').attr('title',data.p_phone);
		
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_instrument').html(data.instrument);
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_instrument').attr('title',data.instrument);
		
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_email').html(data.p_email);
		jQuery('.'+container).parents('li.innslag').find('ul#'+data.p_id).find('li.person_email').attr('title',data.p_email);
		
		jQuery('#edit_person_'+id).effect("highlight", {color: '#a0cf67'}, 700,function (){	
			jQuery('#edit_person_'+id).slideUp(function(){
				jQuery('#edit_person_'+id).remove();
			});
		});
		
		if(data.tittellos) {
			updateDetaljer_innslag(data, container);
			jQuery('.'+container).parents('li.innslag').find('div.kontaktperson').html(data.p_name_full + ' ('+data.p_alder+') '
																					+ '<span class="phone"><span class="mobiltelefon">'+data.p_phone+'</span>');

			if(data.reload) {
				jQuery('div.pameldte_missing').hide();
				jQuery('div.ukmdeltakere_wrapper').hide();
				jQuery('div.ukmdeltakere_wrapper').after('<span class="loading"><img src="/UKM/ico/loading.gif" width="32" /><span>Laster inn listen på nytt</span></span>');
				window.location.href=window.location.href.split("#")[0]+'#rediger_'+data.b_id;
				window.location.reload(); 
			}

			//skjulAlleDetaljer();
//			alert('test');
		}
	}
}
function updateDetaljer_innslag(data,container) {
	
	jQuery('.'+container).parents('li.innslag').find('div.navn').html('<em>'+data['innslag']+'</em>');
	jQuery('.'+container).parents('li.innslag').find('div.kategori').html(data['kategori']);
	jQuery('div.kategori').css('text-transform','capitalize');
	jQuery('.'+container).parents('li.innslag').find('div.sjanger').html(data['sjanger']);
	jQuery('.'+container).effect("highlight", {color: '#a0cf67'}, 700,function (){	
		jQuery('.'+container).slideUp(function(){
			skjulAlleDetaljer();
			jQuery('.'+container).remove();
		});
	});
}

function initKnapper(){
	/* REDIGER INNSLAG */
	jQuery('.innslag div.ikon_rediger').click(function(){
		visRedigering=true;
		innslagVisDetaljer(jQuery(this));
	});
	
	/* REDIGER TITTELLØSE INNSLAG */
	jQuery('.innslag div.ikon_rediger_tittellose').click(function(){
		skjulAlleDetaljer();
		innslagVisTittelloseDetaljer(jQuery(this));
	});
	
	/* VIS DETALJER FOR INNSLAG */
	jQuery('.innslag div.ikon_detaljer').click(function(){
		skjulAlleDetaljer();
		innslagVisDetaljer(jQuery(this));
	});
	jQuery('.innslag div.ikon_detaljer_skjul').click(function(){
		skjulAlleDetaljer();
		jQuery('li.innslag#'+bid).find('div.ikon_detaljer').show();
		jQuery('li.innslag#'+bid).find('div.ikon_detaljer_skjul').hide();
	});
}

function reloadInnslagInfo(bid) {
	skjulAlleDetaljer();
	jQuery('li.innslag#'+bid).find('div.ikon_detaljer').click();
}

function reloadDetaljer(container) {
	bid = jQuery('.'+container).parents('li.innslag').attr('id');
	skjulAlleDetaljer();
	getDetaljer('innslag_detaljer','innslag', bid,'detaljer_'+bid);
	jQuery('li.innslag#'+bid).find('div.ikon_detaljer').hide();
	jQuery('li.innslag#'+bid).find('div.ikon_detaljer_skjul').show();
	jQuery('li.innslag#'+bid).addClass('grayBackground');
}

function doAdvarsler( data,container ) {
	if( data.advarsler != undefined && data.advarsler != "UNDEFINED" && data.advarsler.length > 1 ) {
		var html = lagAdvarsler(data.advarsler);
		
		if(!html.length > 0) {
			jQuery('li.innslag#'+data.b_id).find('div.detaljer').find('div.error_wrapper').html('');
			jQuery('li.innslag#'+data.b_id).find('div.ikon_alert').html('');				
		} 
		else {		
			jQuery('li.innslag#'+data.b_id).find('div.detaljer').find('div.error_wrapper').html('<h3>Advarsler</h3>'+html);
			jQuery('li.innslag#'+data.b_id).find('div.ikon_alert').html('<div align="center" style="font-size: 9px;"><img src="//ico.ukm.no/emblem-important-256.png" width="18" alt="&lt;p&gt;krever oppmerksomhet&lt;/p&gt;" style="border: 0px none; margin: 2px; margin-bottom: -2px; padding: 0px;" border="0"><br clear="all"><p>krever oppmerksomhet</p></div>');
		}
	}
	else {
		jQuery('li.innslag#'+data.b_id).find('div.detaljer').find('div.error_wrapper').html('');
		jQuery('li.innslag#'+data.b_id).find('div.ikon_alert').html('');
	}	
}

function lagAdvarsler( data ) {
	if( strpos( data, ', ' ) == false ) {
		var error_message = data;
		var error = '<div class="advarsler error">'+error_message+'</div>';
	}
	else {	
		var adv = data.split(', ');
		
		var error = '';
		
		for( var i = 0; i<adv.length; i++ ) {
			error = error + '<div class="advarsler error">'+adv[i]+'</div>';
		} 		

	}
	
	return error;
}

function strpos (haystack, needle, offset) {
  var i = (haystack+'').indexOf(needle, (offset || 0));
  return i === -1 ? false : i;
}

function showActive(){
	var url = window.location.hash, idx = url.indexOf("#")
	var hash = idx != -1 ? url.substring(idx+1) : "";
	
	id = hash.replace('innslag_','');
	if( (id*1) > 1) {
		innslagVisDetaljer(jQuery('li.innslag#'+id+' div.ikon_detaljer'));
	}
	id = hash.replace('rediger_','');
	if( (id*1) > 1) {
		jQuery('li.innslag#'+id+' div.ikon_rediger').click();
		jQuery('li.innslag#'+id+' div.ikon_rediger_tittellose').click();
	}
}