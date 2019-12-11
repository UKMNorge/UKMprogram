/* GROVSORTERING */
jQuery(document).on('click', '#startSort', function() {
    var hendelser = [];

    jQuery('input[name="hendelser"]:checked').each(function(e) {
        hendelser.push(jQuery(this).val());
    });

    window.location.href = window.location.href + '&hendelser=' + hendelser.join('-');
});

jQuery(document).ready(function() {
    // Minst én supply-container må finnes for at appen starter
    if (jQuery('.supplyContainer').length) {
        var hendelser = hendelseContainer('.hendelse, .supplyContainer');
        alleInnslag.init(hendelser);

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
    }
});