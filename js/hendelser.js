var hendelseContainer = function($) {
    return function(selector) {
        var emitter = new UKMresources.emitter(selector);
        //emitter.enableDebug();
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
                return collection.set(id, new supply(id)).get(id);
            },
            createHendelse: function(id) {
                //console.log('REGISTER HENDELSE');
                return collection.set(id, new hendelse(id)).get(id);
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
                            self.createSupply($(this).attr('id'))
                                .on('received', self.emitReceived)
                                .on('init', self.emitInit)
                                .on('lost', self.emitLost);
                        } else {
                            self.createHendelse($(this).attr('id'))
                                .on('received', self.emitReceived)
                                .on('init', self.emitInit)
                                .on('lost', self.emitLost);
                        }
                    }
                });
                self.bind();
            },
            emitInit: function(_type, _id, _object) {
                return emitter.emit('init:' + _type, [_id, _object]);
            },
            emitLost: function(_liste_id, _innslag_id) {
                return emitter.emit('lost', [_liste_id, _innslag_id]);
            },
            emitReceived: function(_liste_id, _innslag_id) {
                return emitter.emit('received', [_liste_id, _innslag_id]);
            },
            on: function(event, callback) {
                return emitter.on(event, callback);
            },
            once: function(event, callback) {
                return emitter.once(event, callback);
            }
        }

        self.init();
        return self;
    }
}(jQuery);