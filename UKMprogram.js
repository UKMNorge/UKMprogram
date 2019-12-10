/* GROVSORTERING */
jQuery(document).on('click', '#startSort', function() {
    var hendelser = [];

    jQuery('input[name="hendelser"]:checked').each(function(e) {
        hendelser.push(jQuery(this).val());
    });

    window.location.href = window.location.href + '&hendelser=' + hendelser.join('-');
});

var supply = function($) {
    return function(id) {
        var emitter = new UKMresources.emitter(id);
        var jQuerySelector = '#' + id;

        var helper = {
            toggle: function(antall_innslag) {
                //console.log('toggleHelpers for ' + id + ': ' + antall_innslag);
                //console.log(self.object());
                if (antall_innslag == 0) {
                    self.object().find('.helper.harInnslag').hide();
                    self.object().find('.helper.ingenInnslag').fadeIn();
                    if (self.getSupplyType() != 'delete') {
                        self.object().find('ol.ui-sortable').hide();
                    }
                } else {
                    self.object().find('.helper.ingenInnslag').hide();
                    self.object().find('.helper.harInnslag').fadeIn();
                    self.object().find('ol.ui-sortable').show();
                }
            },
        }

        var self = {
            supplyType: null,
            init: function() {
                //console.info('Initier supplylist ' + id);
                self.supplyType = $(jQuerySelector).attr('data-supply');
                self.bind();
            },
            add: function(item) {
                self.object().find('.ui-sortable').append(item);
                emitter.emit('change', item);
            },
            receive: function(item) {
                if (self.getSupplyType() == 'delete') {
                    item.remove();
                    return true;
                }
                return false;
            },
            remove: function(innslag_id) {
                //console.log(id + ' fjern:' + innslag_id);
                self.object().find('.innslag[data-id="' + innslag_id + '"]').slideUp(200, function() {
                    jQuery(this).remove();
                    emitter.emit('change', null);
                });
                emitter.emit('change', null);
            },
            lost: function(item) {
                emitter.emit('change', null);
            },
            getInnslag: function() {
                return self.object().find('li.innslag');
            },
            bind: function() {
                emitter.on('change', self.handle);
                $(document).ready(function() {
                    helper.toggle(self.getInnslag().length);
                });
            },
            handle: function(item) {
                //console.warn('HANDLE supplyList: ' + id);
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
                //console.log('getObject: ' + jQuerySelector);
                return $(jQuerySelector);
            },
            type: function() {
                return 'supply';
            },
            getType: function() {
                return self.type();
            },
            getSupplyType: function() {
                return self.supplyType;
            },
            getId: function() {
                return id;
            }
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
                //console.log('counter(' + id + ').update()');
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
                //console.log('counter(' + id + ').render()');
                counter.find('.antall_innslag').html(counter.innslag);
                counter.find('.varighet').html(UKMresources.tid(counter.duration).useMilitary().render());
            },
            object: function() {
                //console.log('counter(' + id + ').object() => jQuery(' + counter.jQuerySelector + ')');
                return $(counter.jQuerySelector);
            },
            find: function(selector) {
                //console.log('counter(' + counter.jQuerySelector + ').find(' + selector + ')');
                return counter.object().find(selector);
            },
            hide: function() {
                //console.log('counter(' + id + ').hide()');
                counter.object().slideUp();
            },
            show: function() {
                //console.log('counter(' + id + ').show()');
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
                //console.log('helper(' + id + ').object() => jQuery(' + helper.jQuerySelector + ')');
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
                //console.log('helper(' + id + ').hide()');
                helper.object().slideUp(200);
            },
            show: function() {
                //console.log('helper(' + id + ').show()');
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

        var saver = {
            lastSave: [],
            save: function() {
                if (saver.timer) {
                    clearTimeout(saver.timer);
                    saver.reset();
                }
                var rekkefolge = [];
                self.object().find('.ui-sortable li.innslag').each(function() {
                    if (jQuery(this).attr('data-id')) {
                        rekkefolge.push(jQuery(this).attr('data-id'));
                    }
                });

                if (saver.lastSave == JSON.stringify(rekkefolge)) {
                    //console.info('Lagrer ikke' + id);
                    return true;
                }

                saver.lastSave = JSON.stringify(rekkefolge);

                self.object().find('.saveSpinner').fadeIn();
                $.post(
                    ajaxurl, {
                        action: 'UKMprogramV2_ajax',
                        controller: 'save',
                        save: 'rekkefolge',
                        hendelse: self.object().attr('data-id'),
                        innslag: rekkefolge.join(',')
                    },
                    function(response) {
                        /* HANDLING GJENNOMFØRT. HÅNDTER RESPONS */
                        if (response !== null && response.success) {
                            saver.done();
                        } else {
                            saver.failed();
                        }
                    }
                ).fail(function(response) {
                    saver.failed();
                });
            },
            done: function() {
                self.object().find('.saveSpinner').hide();
                self.object().find('.saveStatus').fadeIn();
                saver.timer = setTimeout(
                    saver.complete,
                    1500
                );
            },
            complete: function() {
                self.object().find('.saveSpinner').hide();
                self.object().find('.saveStatus').fadeOut();
            },
            reset: function() {
                self.object().find('.saveSpinner').hide();
                self.object().find('.saveStatus').hide();
            },
            failed: function() {
                saver.reset();
                alert('Beklager, klarte ikke å lagre rekkefølgen for ' + self.object().find('.hendelseNavn').html());
            }
        }

        /**
         * PUBLIC FUNCTIONS
         */
        var self = {
            init: function() {
                //console.info('Initier ' + id);
                self.bind();
                // Registrere innslag i listen
                self.object().find('li.innslag').each(function() {
                    if ($(this).attr('data-id')) {
                        self.register($(this));
                    }
                });
                counter.update();
            },
            receive: function(item) {
                var innslag_id = self.getIdFromItem(item);
                if (innslag.includes(innslag_id)) {
                    //console.log('Innslag ' + innslag_id + ' already in list ' + id);
                    return false;
                }
                innslag.push(innslag_id);
                //console.group(id + ' mottok: ' + innslag_id);
                //console.log(item);
                //console.groupEnd();
                emitter.emit('change');
                return true;
            },
            register: function(item) {
                innslag.push(self.getIdFromItem(item));
            },
            lost: function(item) {
                innslag.splice(innslag.indexOf(self.getIdFromItem(item)), 1);
                //console.log(id + ' lost: ')
                //console.log(item);
                emitter.emit('change');
            },
            getIdFromItem: function(item) {
                return $(item).attr('data-id');
            },
            getInnslag: function() {
                return $(jQuerySelector).find('li.innslag');
            },
            bind: function() {
                //emitter.on('change', self.save);
                // save triggered by sortable.stop()
            },
            save: function() {
                saver.save();
            },
            toggle: function(property, state, item) {
                states.toggle(property, state, item);
            },
            object: function() {
                return $(jQuerySelector);
            },
            type: function() {
                return 'hendelse';
            },
            getType: function() {
                return self.type();
            },
            getSupplyType: function() {
                return false;
            },
            getId: function() {
                return id;
            }
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
                //console.log('hendelser:get(' + id + ')')
                if (!collection.has(id)) {
                    console.error('Fant ikke hendelse/supply:' + id);
                    return false;
                }
                //console.log(collection.get(id));
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
                return $(list).parents($(list).hasClass('supplyList') ? '.supplyContainer' : '.hendelse');
            },
            createSupply: function(id) {
                //console.log('REGISTER SUPPLY');
                collection.set(id, new supply(id));
            },
            createHendelse: function(id) {
                //console.log('REGISTER HENDELSE');
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
                        //console.error('Kan ikke registrere hendelse/supply uten ID');
                        //console.log($(this));
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
    var trans = {
        connectWith: '.detaljprogram, .supplyList',
        tolerance: 'pointer',
        items: 'li',
        dropOnEmpty: true,
        delay: 100,
        /**
         * Start av sortering
         * 
         * Resetter array med hendelser som skal lagres når
         * sorteringen er helt ferdig
         * 
         * @param {*} event 
         * @param {*} ui 
         */
        start: function(event, ui) {
            trans.setCurrent(event, ui);
            trans.resetSave();
        },
        /**
         * Innslaget holdes over en liste
         * 
         * Brukes for å markere at slette-listen registrere hover-event
         * da slette-listen er litt ustabil på init
         * 
         * @param {*} event 
         * @param {*} ui 
         */
        over: function(event, ui) {
            if (hendelser.hasFromSortableList(jQuery(event.target))) {
                if (hendelser.getFromSortableList(jQuery(event.target)).getSupplyType() == 'delete') {
                    hendelser.getFromSortableList(jQuery(event.target)).object().addClass('over');
                }
            }
        },
        /**
         * Innslaget er trukket ut av en liste (men ikke sluppet)
         * 
         * Fjern hover-klassen på slette-listen
         * Vis slette-listen hvis innslaget dras ut av en hendelse-liste
         * 
         * @param {*} event 
         * @param {*} ui 
         */
        out: function(event, ui) {
            trans.setCurrent(event, ui);
            hendelser.get('supplyDelete').object().removeClass('over');

            if (hendelser.getFromSortableList(trans.getSender()).getType() == 'hendelse') {
                hendelser.get('supplyDelete').object().show(
                    0,
                    function() {
                        jQuery(this).find('.supplyList').sortable('enable');
                    }
                );
            }
        },
        /**
         * Stop sortering
         * 
         * Endringen er gjennomført, og alle innslag er på riktig sted.
         * Gjennomfør lagring
         * 
         * @param {*} event 
         * @param {*} ui 
         */
        stop: function(event, ui) {
            // Skjul slett-boksen
            jQuery('#supplyDelete').slideUp(200);
            trans.doSave();
        },
        /**
         * Før vi stopper, lagre påvirkede hendelser
         * 
         * For å sikre at alle innslag er i listen de skal før lagring
         * trigges, lager denne en liste med påvirkede hendelser, som
         * stop() går gjennom og faktisk lagrer.
         * 
         * I beforeStop har vi tilgang på getSender() og getRecipient() 
         * med riktig data, mens i stop() vet vi at alle innslag er
         * mottatt, og eventuelt returnert riktig.
         * 
         * @param {*} event 
         * @param {*} ui 
         */
        beforeStop: function(event, ui) {
            var avsender = false;
            // Hvis avsender finnes og er hendelse. Legg til for lagring
            if (hendelser.hasFromSortableList(trans.getSender())) {
                avsender = trans.getHendelse(trans.getSender());
                if (avsender.getType() == 'hendelse') {
                    trans.save(avsender);
                }
            }

            // Hvis mottaker finnes og er hendelse. Legg til for lagring
            if (hendelser.hasFromSortableList(trans.getRecipient())) {
                var mottaker = trans.getHendelse(trans.getRecipient());
                if (mottaker.getType() == 'hendelse' && (!avsender || (avsender.getId() != mottaker.getId()))) {
                    trans.save(mottaker);
                }
            }
        },
        /**
         * Innslaget er på vei inn i en liste
         * 
         * Prøver, feiler og håndterer
         * 
         * @param {*} event 
         * @param {*} ui 
         */
        receive: function(event, ui) {
            var addToFordeling = false;
            trans.setCurrent(event, ui);

            var innslagSelector = '.innslag[data-id="' + trans.getItemId() + '"]';

            // Supply-lister er forskjellig fra hendelser
            if (trans.getHendelse(trans.getRecipient()).getType() == 'supply') {
                // Slett-listen tar i mot og sletter alle innslag fra hendelser
                if (trans.getHendelse(trans.getRecipient()).getSupplyType() == 'delete' && trans.getHendelse(trans.getSender()).getType() == 'hendelse') {
                    // jQuery(innslagSelector).length: 2 == 1, som vil si at innslaget ikke er med i en hendelse
                    // Hvis innslaget ikke er med i en hendelse, må det legges til i fordelingslisten
                    if (jQuery(innslagSelector).length == 2) {
                        addToFordeling = true;
                    }
                }
                // Det er ikke mulig å trekke et innslag inn til en supply-liste
                else {
                    trans.cancel();
                    return false;
                }
            }

            // Prøv å sende innslaget
            var result = trans.getHendelse(trans.getRecipient()).receive(trans.getItem());

            // Mottaker godtok ikke innslaget (sannsynligvis allerede i lista)
            if (!result) {
                // cache mottaker, fordi cancel() endrer alt
                var realRecipient = trans.getRecipient();
                trans.cancel();

                // Shake begge innslag
                trans.getItem().effect('shake'); // dragged item
                trans.getHendelse(realRecipient).object().find(innslagSelector).effect('shake'); // item in target

                return true;
            }

            // Hvis innslaget eksisterer i ikke fordelt-listen er det på tide å fjerne den nå
            if (hendelser.has('supplyFordeling') && hendelser.get('supplyFordeling').object().find(trans.getItemId())) {
                hendelser.get('supplyFordeling').remove(trans.getItemId());
            }

            // Hvis avsender er supply-list::alle, klon innslaget, så det ikke forsvinner
            // fra supply-listen
            if (trans.getHendelse(trans.getSender()).getSupplyType() == 'alle') {
                trans.getItem().clone().insertAfter(trans.getItem());
                trans.getSender().sortable('cancel');
                return trans.getItem();
            }

            // Oppdater hendelse-objektet som mistet ett innslag
            trans.getHendelse(trans.getSender()).lost(trans.getItem());

            // Innslaget har blitt fjernet, og skal legges til fordelingslisten igjen
            if (hendelser.has('supplyFordeling') && addToFordeling) {
                hendelser.get('supplyFordeling').add(trans.getItem());
            }
        },
        getItemId: function() {
            return trans.getItem().attr('data-id');
        },
        getItem: function() {
            return trans.getCurrentUI().item;
        },
        getRecipient: function() {
            return trans.getItem().parents('ol');
        },
        getSender: function() {
            return trans.sender;
        },
        setCurrent: function(event, ui) {
            trans.currentEvent = event;
            trans.currentUI = ui;
            trans.sender = jQuery(trans.getCurrentUI().sender);
        },
        getCurrentEvent: function() {
            return trans.currentEvent;
        },
        getCurrentUI: function() {
            return trans.currentUI;
        },
        getHendelse: function(sortable) {
            return hendelser.getFromSortableList(sortable);
        },
        cancel: function() {
            trans.getSender().sortable('cancel');
        },
        save: function(hendelse) {
            trans.saveHendelser.push(hendelse);
        },
        doSave: function() {
            for (i = 0; i < trans.saveHendelser.length; i++) {
                trans.saveHendelser[i].save();
            }
        },
        resetSave: function() {
            trans.saveHendelser = [];
        }

    };
    jQuery('.detaljprogram, .supplyList').sortable(trans).disableSelection();
    jQuery('.detaljprogram, .supplyList').sortable('refresh');
});