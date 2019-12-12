var supply = function($) {
    return function(id) {
        var emitter = new UKMresources.emitter(id);
        //emitter.enableDebug();
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
                emitter.emit('init', ['supply', id, self]);
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
                emitter.emit('lost', [id, self.getIdFromItem(item)]);
            },
            getIdFromItem: function(item) {
                return $(item).attr('data-id');
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
            },
            on: function(event, callback) {
                return emitter.on(event, callback);
            },
            once: function(event, callback) {
                return emitter.once(event, callback);
            }
        };

        self.init();

        return self;
    }
}(jQuery);