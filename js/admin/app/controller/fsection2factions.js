Ext.define('Indi.controller.fsection2factions', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$Rename: {
                considerOn: [{
                    name: 'blink'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(!d.blink);
                    }
                }
            },
            formItem$Alias: {
                considerOn: [{
                    name: 'blink'
                }, {
                    name: 'rename'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(!d.blink && d.rename);
                    }
                }
            }
        }
    }
});