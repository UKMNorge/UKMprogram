var innslagFilter = function($) {
    return function(id, alleInnslag) {
        var jQuerySelector = '#' + id + ' .filterSelector';

        var self = {
            type: false,
            hendelse: false,
            count: false,

            filter: function()  {
                self.init();
                var visible = self.getVisible();
                self.getInnslag().each(function() {
                    //console.log($(this).attr('data-id');
                    if (visible.includes($(this).attr('data-id'))) {
                        $(this).slideDown();
                    } else {
                        $(this).slideUp();
                    }
                });

            },
            change: function(innslag) {
                // ui.sortable-item klones på slutten av funksjonen,
                // og denne trigges før det skjer. Vent derfor 10ms,
                // så er elementet klonet, lagt tilbake i supplylisten,
                // og klart til å skjules
                setTimeout(
                    self.filter,
                    10
                );
            },
            object: function() {
                return $(jQuerySelector);
            },
            getInnslag: function()  {
                return self.object().parents('.supplyContainer').find('li.innslag');
            },
            init: function() {
                var type = self.object().find('[name="filter_type"]').val();
                if (type == 'false') {
                    self.type = false;
                } else {
                    self.type = type;
                }

                var hendelse = self.object().find('[name="filter_hendelser"]').val();
                if (hendelse.indexOf('antall_') == 0) {
                    self.count = hendelse.replace('antall_', '');
                    self.hendelse = false;
                } else {
                    self.count = false;
                    self.hendelse = hendelse.replace('hendelse_', '');
                }
            },
            getVisible: function() {
                var visible = [];
                alleInnslag.getAll().forEach(function(innslag) {
                    if (self.show(innslag)) {
                        visible.push(innslag.getId());
                    }
                });
                return visible;
            },
            show: function(innslag) {
                // Vi har et type-filter
                //console.group('show( ' + innslag.getId() + ')');
                if (self.type !== false && innslag.getType() != self.type) {
                    //console.log('Filter:type');
                    //console.log(self.type);
                    //console.log(innslag.getType());
                    //console.groupEnd();
                    return false;
                }

                // Vi har et antall hendelser-filter
                if (self.count !== false && self.count !== 'anything' && self.count != innslag.getAntallHendelser()) {
                    //console.log('Filter:count');
                    //console.log(self.count);
                    //console.log(innslag.getAntallHendelser());
                    //console.log(innslag.getHendelser());
                    //console.groupEnd();
                    return false;
                }

                // Vi har et i denne hendelsen-filter
                if (self.hendelse !== false && !innslag.getHendelser().includes(self.hendelse)) {
                    //console.log('Filter:hendelse');
                    //console.log(self.hendelse);
                    //console.log(innslag.getHendelser());
                    //console.groupEnd();
                    return false;
                }
                //console.log('visible');
                //console.groupEnd();
                return true;
            }
        }

        // Bind til innslag-endringer        
        alleInnslag.on('change', self.change);

        // Bind til gui
        $(document).on('click', '#' + id + ' .doFilter', function() {
            self.filter();
        });

        return self;
    }
}(jQuery);