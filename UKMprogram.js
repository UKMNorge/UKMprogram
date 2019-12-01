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


function toggleIngenInnslag(item) {
    var liste = item.parents('.detaljprogram');

}

function receive_hidden(item) {
    console.warn('Receive hidden');
}

var supply = function($) {
    return function(id) {
        var emitter = new UKMresources.emitter(id);
        var jQuerySelector = '#' + id;

        var helper = {
            toggle: function(antall_innslag) {
                console.log('toggleHelpers for ' + id + ': ' + antall_innslag);
                console.log(self.object());
                if (antall_innslag == 0) {
                    self.object().find('.helper.harInnslag').hide();
                    self.object().find('.helper.ingenInnslag').fadeIn();
                    self.object().find('ol.ui-sortable').hide();
                } else {
                    self.object().find('.helper.ingenInnslag').hide();
                    self.object().find('.helper.harInnslag').fadeIn();
                    self.object().find('ol.ui-sortable').show();
                }
            },
        }

        var self = {
            init: function() {
                console.info('Initier supplylist ' + id);
                self.bind();
            },
            receive: function(item) {
                console.group(id + ' mottok:');
                console.log(item);
                console.groupEnd();
                emitter.emit('change', item);
            },
            remove: function(innslag_id) {
                console.log(id + ' fjern:' + innslag_id);
                self.object().find('.innslag[data-id="' + innslag_id + '"]').slideUp(200, function() {
                    jQuery(this).remove();
                    emitter.emit('change', null);
                });
            },
            lost: function(item) {
                emitter.emit('change');
            },
            getInnslag: function() {
                return self.object().find('li.innslag:visible');
            },
            bind: function() {
                emitter.on('change', self.handle);
                $(document).ready(function() {
                    helper.toggle(self.getInnslag().length);
                });
            },
            handle: function(item) {
                console.warn('HANDLE supplyList: ' + id);
                if (self.getInnslag().length == 0) {
                    self.object().removeClass('panel-warning').addClass('panel-success');
                    self.object().find('.icon .dashicons').removeClass('text-warning').addClass('text-success');
                } else {
                    self.object().removeClass('panel-success').addClass('panel-warning');
                    self.object().find('.icon .dashicons').removeClass('text-success').addClass('text-warning');
                }
                helper.toggle(self.getInnslag().length);
            },

            object: function() {
                console.log('getObject: ' + jQuerySelector);
                return $(jQuerySelector);
            },
        };

        self.init();

        return self;
    }
}(jQuery);

var hendelse = function($) {
    return function(id) {
        var emitter = new UKMresources.emitter(id);
        emitter.enableDebug();

        var innslag = [];
        var jQuerySelector = '#' + id;

        /**
         * COUNTER: HOLDER STYR PÅ ANTALL INNSLAG I LISTEN
         */
        var counter = {
            innslag: 0,
            duration: 0,
            jQuerySelector: jQuerySelector + ' .totals',

            bind: function() {
                emitter.on('change', counter.update);
            },

            update: function() {
                console.log('counter(' + id + ').update()');
                var innslag = self.getInnslag();
                counter.innslag = innslag.length;

                counter.duration = 0;
                innslag.each(function() {
                    counter.duration += parseInt($(this).attr('data-duration'));
                });

                counter.render();
            },
            render: function() {
                if (counter.innslag == 0) {
                    counter.hide();
                } else {
                    counter.show();
                }
                console.log('counter(' + id + ').render()');
                counter.find('.antall_innslag').html(counter.innslag);
                counter.find('.varighet').html(UKMresources.tid(counter.duration).useMilitary().render());
            },
            object: function() {
                console.log('counter(' + id + ').object() => jQuery(' + counter.jQuerySelector + ')');
                return $(counter.jQuerySelector);
            },
            find: function(selector) {
                console.log('counter(' + counter.jQuerySelector + ').find(' + selector + ')');
                return counter.object().find(selector);
            },
            hide: function() {
                console.log('counter(' + id + ').hide()');
                counter.object().slideUp();
            },
            show: function() {
                console.log('counter(' + id + ').show()');
                counter.object().fadeIn();
            }
        };

        /**
         * HJELPER FOR Å KOMME I GANG MED SORTERING
         */
        var helper = {
            jQuerySelector: jQuerySelector + ' .ingeninnslag',
            bind: function() {
                emitter.on('change', helper.toggle);
            },
            object: function() {
                console.log('helper(' + id + ').object() => jQuery(' + helper.jQuerySelector + ')');
                return $(helper.jQuerySelector);
            },
            toggle: function() {
                if (self.getInnslag().length > 0) {
                    helper.hide();
                } else {
                    helper.show();
                }
            },
            hide: function() {
                console.log('helper(' + id + ').hide()');
                helper.object().slideUp(200);
            },
            show: function() {
                console.log('helper(' + id + ').show()');
                helper.object().fadeIn();
            }
        }


        /**
         * HÅNDTER STATE-ENDRING
         */
        var states = {
            set: function(property, state, item) {
                emitter.emit('state:' + property, state);
                item.attr('data-state', state ? 'on' : 'off');

                if (state) {
                    item.find('.state-on').fadeIn();
                    item.find('.state-off').hide();
                } else {
                    item.find('.state-on').hide();
                    item.find('.state-off').fadeIn();
                }
            },
            toggle: function(property, state, item) {
                states.set(property, state, item);

                jQuery.post(
                    ajaxurl, {
                        action: 'UKMprogramV2_ajax',
                        controller: 'save',
                        save: property,
                        state: state,
                        hendelse: self.object().attr('data-id')
                    },
                    function(response) {
                        /* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
                        if (response !== null && response.success) {
                            // do nothing
                        } else {
                            states.set(property, !state, item);
                            alert('Beklager, en feil har oppstått.');
                        }
                    }
                );
            },
            bind: function() {}
        };

        /**
         * FUNKSJONER TILKNYTTET HEADEREN
         */
        var header = {
            toggleSynlig: function(newState) {
                $(jQuerySelector).toggleClass('panel-danger', !newState).toggleClass('panel-success', newState);
            },
            bind: function() {
                emitter.on('state:synligRammeprogram', header.toggleSynlig);
            }
        }

        /**
         * PUBLIC FUNCTIONS
         */
        var self = {
            init: function() {
                console.info('Initier ' + id);
                self.bind();
            },
            receive: function(item) {
                var innslag_id = $(item).attr('data-id');
                if (innslag.includes(innslag_id)) {
                    console.log('Innslag ' + innslag_id + ' already in list ' + id);
                    return false;
                }
                innslag.push(innslag_id);
                console.group(id + ' mottok: ' + innslag_id);
                console.log(item);
                console.groupEnd();
                emitter.emit('change');
                return true;
            },
            lost: function(item) {
                emitter.emit('change');
            },
            getInnslag: function() {
                return $(jQuerySelector).find('li.innslag');
            },
            bind: function() {
                emitter.on('change', self.save);
            },
            save: function() {
                console.warn('SAVE ' + id);
            },
            toggle: function(property, state, item) {
                states.toggle(property, state, item);
            },
            object: function() {
                return $(jQuerySelector);
            },
        };

        // INITIATE
        counter.bind();
        helper.bind();
        states.bind();
        header.bind();
        self.init();

        return self;
    }
}(jQuery);

var hendelseContainer = function($) {
    return function(selector) {

        var collection = new Map();

        var self = {
            has: function(id) {
                return collection.has(id);
            },
            get: function(id) {
                console.log('hendelser:get(' + id + ')')
                if (!collection.has(id)) {
                    console.error('Fant ikke hendelse/supply:' + id);
                    return false;
                }
                console.log(collection.get(id));
                return collection.get(id);
            },
            hasFromSortableList: function(list) {
                return self.has(self._findIdFromSortable(list));
            },
            getFromSortableList: function(list) {
                return self.get(self._findIdFromSortable(list));
            },
            _findIdFromSortable: function(list) {
                return self.getParentContainer(list).attr('id');
            },
            getParentContainer: function(list) {
                return $(list).parents($(list).hasClass('.supplyList') ? '.supplyContainer' : '.hendelse');
            },
            createSupply: function(id) {
                console.log('REGISTER SUPPLY');
                collection.set(id, new supply(id));
            },
            createHendelse: function(id) {
                console.log('REGISTER HENDELSE');
                collection.set(id, new hendelse(id));
            },
            bind: function() {
                $(document).on('click', '.toggleState', function(e) {
                    if (self.has($(this).parents('.hendelse').attr('id'))) {
                        self.get($(this).parents('.hendelse').attr('id'))
                            .toggle(
                                $(this).attr('data-controller'),
                                $(this).attr('data-state') == 'off', // sjekk om off, for å gi motsatt state som ny verdi
                                $(this)
                            );
                    }
                });
            },
            init: function() {
                $(selector).each(function() {
                    if (!$(this).attr('id')) {
                        console.error('Kan ikke registrere hendelse/supply uten ID');
                        console.log($(this));
                    } else {
                        if ($(this).hasClass('supplyContainer') || $(this).hasClass('supplyList')) {
                            self.createSupply($(this).attr('id'));
                        } else {
                            self.createHendelse($(this).attr('id'));
                        }
                    }
                });
                self.bind();
            }
        }

        self.init();
        return self;
    }
}(jQuery);

jQuery(document).ready(function() {
    var hendelser = hendelseContainer('.hendelse, .supplyContainer');
    /* SORTERING */
    jQuery('.detaljprogram, .supplyList').sortable({
        connectWith: '.detaljprogram, .supplyList',
        tolerance: 'pointer',
        items: 'li',
        start: function(event, ui) {
            item = ui.item;
            newList = ui.item.parent();
            oldList = ui.item.parent();
        },
        stop: function(event, ui) {
            // oldList er satt i start()
            // newList er oppdatert i change()
            if (hendelser.hasFromSortableList(oldList)) {
                hendelser.getFromSortableList(oldList).lost(item);
            }
        },
        change: function(event, ui) {
            if (ui.sender) {
                newList = ui.placeholder.parent();
            }
        },
        receive: function(event, ui) {
            var success = false;
            // Prøv å sende innslaget til lista
            if (hendelser.hasFromSortableList(newList)) {
                success = hendelser.getFromSortableList(newList).receive(item);
            }

            var innslagSelector = '.innslag[data-id="' + item.attr('data-id') + '"]';

            // Hvis lista nekter, shake it off.
            if (!success) {
                jQuery(ui.sender).sortable('cancel');
                hendelser.getFromSortableList(newList).object().find(innslagSelector).effect('shake');
                jQuery(item).effect('shake');
                return false;
            }

            // Når et innslag legges til i en hendelse, må det ut av "innslag som ikke er fordelt"
            if (hendelser.has('supplyFordeling')) {
                if (hendelser.get('supplyFordeling').object().find(innslagSelector)) {
                    hendelser.get('supplyFordeling').remove(jQuery(item).attr('data-id'));
                }
            }


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
})