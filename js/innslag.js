var alleInnslag = function($) {
    var collection = new Map();
    var emitter = new UKMresources.emitter('alleInnslag');

    var innslag = function(id, type, hendelser) {
        var self = {
            getId: function() {
                return id;
            },
            getType: function() {
                return type;
            },
            getHendelser: function() {
                return hendelser;
            },
            getAntallHendelser: function() {
                return hendelser.length;
            },
            fjernHendelse: function(hendelse_id) {
                hendelser.splice(hendelser.indexOf(hendelse_id), 1);
            },
            leggTilHendelse: function(hendelse_id) {
                hendelser.push(hendelse_id);
            },
            getSelector: function() {
                return '.innslag[data-id="' + self.getId() + '"]';
            }
        }
        return self;
    }

    function received(liste_id, innslag_id) {
        //console.log('GOT ' + innslag_id + ' @ ' + liste_id);
        collection.get(innslag_id).leggTilHendelse(liste_id.replace('hendelse_', ''));
        emitter.emit('change', [collection.get(innslag_id)]);
    }

    function lost(liste_id, innslag_id) {
        //console.log('LOST ' + innslag_id + ' @ ' + liste_id);
        collection.get(innslag_id).fjernHendelse(liste_id.replace('hendelse_', ''));
        emitter.emit('change', [collection.get(innslag_id)]);
    }

    var self = {
        init: function(hendelser) {
            var liste = hendelser.get('supplyAlle');
            jQuery(liste.getInnslag()).each(function(element) {
                self.register(jQuery(this));
            });
            hendelser.on('received', received);
            hendelser.on('lost', lost);
        },
        bind: function(id, liste) {
            if (id == 'supplyAlle') {
                liste.on('change', self.change);
            }
            if (id == 'supplyDelete') {
                liste.on('change', self.change);
            }
            // on alleInit: iterer og sett opp data
        },
        change: function(list, innslag_id) {
            //console.log('Change ' + innslag_id + ' @ ', list);
        },
        register: function(innslag_item) {
            var hendelser = innslag_item.attr('data-hendelser');
            if (hendelser.length == 0) {
                hendelser = [];
            } else {
                hendelser = innslag_item.attr('data-hendelser').split(',');
            }
            collection.set(
                innslag_item.attr('data-id'),
                new innslag(
                    innslag_item.attr('data-id'),
                    innslag_item.attr('data-type'),
                    hendelser
                )
            );
        },
        getAll: function()  {
            return collection;
        },
        on: function(event, callback) {
            return emitter.on(event, callback);
        },
        once: function(event, callback) {
            return emitter.once(event, callback);
        }
    }

    return self;
}(jQuery);