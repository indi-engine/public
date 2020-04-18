Ext.define('Indi.controller.menu', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        form: {
            formItem$Url: {
                considerOn: [{
                    name: 'linked'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.linked == 'n');
                    }
                }
            },
            formItem$StaticpageId: {
                jump: '/staticpages/form/id/{id}/',
                considerOn: [{
                    name: 'linked'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.linked == 'y');
                    }
                }
            }
        }
    }
});