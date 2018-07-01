Ext.define('Indi.controller.fsections', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$Type: {nojs: true},
            formItem$Filter: {
                considerOn: [{
                    name: 'type'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'r');
                    }
                }
            },
            formItem$DefaultLimit: {
                considerOn: [{
                    name: 'type'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'r');
                    }
                }
            },
            formItem$OrderBy: {
                nojs: true,
                considerOn: [{
                    name: 'type'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'r');
                    }
                }
            },
            formItem$OrderColumn: {
                considerOn: [{
                    name: 'type'
                }, {
                    name: 'orderBy'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'r' && d.orderBy == 'c');
                    }
                }
            },
            formItem$OrderDirection: {
                considerOn: [{
                    name: 'type'
                }, {
                    name: 'orderBy'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'r' && d.orderBy == 'c');
                    }
                }
            },
            formItem$OrderExpression: {
                considerOn: [{
                    name: 'type'
                }, {
                    name: 'orderBy'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'r' && d.orderBy == 'e');
                    }
                }
            },
            formItem$Where: {
                considerOn: [{
                    name: 'type'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 's');
                    }
                }
            },
            formItem$Index: {
                considerOn: [{
                    name: 'type'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 's');
                    }
                }
            }
        }
    }
});