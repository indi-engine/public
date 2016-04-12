Ext.define('Indi.controller.menu', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$MenuId: {allowBlank: true},
            formItem$Linked: {nojs: true},
            formItem$Url: {
                allowBlank: true,
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