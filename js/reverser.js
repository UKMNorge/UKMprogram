jQuery(document).ready(function() {
    jQuery(".reverser_rekkefolge").click(function(clickEvent) {
        clickEvent.preventDefault();
        if(confirm('Er du sikker på at du vil reversere rekkefølgen på alle innslag i denne hendelsen?')) {
            jQuery.post(
                ajaxurl, {
                    action: 'UKMprogramV2_ajax',
                    controller: 'save',
                    save: 'reverserRekkefolge',
                    hendelse: jQuery(clickEvent.target).data('hendelse')
                },
                function(response) {
                    if (response !== null && response.success) {
                        location.reload();
                    } else {
                        alert('Klarte ikke å reversere rekkefølgen på innslagene.');
                    }
                }
            );
        }
    });
});