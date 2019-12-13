var hendelse = function($) {
    return function(id) {
        var emitter = new UKMresources.emitter(id);
        //emitter.enableDebug();

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
                var program = $(jQuerySelector).attr('data-synlighet');
                $(jQuerySelector).toggleClass('panel-danger', !newState).toggleClass('panel-' + program, newState);
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
                emitter.emit('init', ['hendelse', id, self]);
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
                emitter.emit('received', [id, self.getIdFromItem(item)]);
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
                emitter.emit('lost', [id, self.getIdFromItem(item)]);
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
            },
            on: function(event, callback) {
                return emitter.on(event, callback);
            },
            once: function(event, callback) {
                return emitter.once(event, callback);
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