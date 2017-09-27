Ext.define('Indi.lib.controller.Meta', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            store: {
                groupDir: 'DESC'
            },
            gridColumn$Up_Renderer: function(v, m, r) {
                if (r && r.key('type') == 'static') return '';
                return v;
            }
        },
        form: {
            formItem$FieldId: {
                considerOn: [{
                    name: 'type'
                }, {
                    name: 'source'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'dynamic' && d.source == 'row');
                    }
                }
            },
            formItem$EntityId: function() {
                var me = this;
                return {
                    hidden: true,
                    considerOn: [{
                        name: 'up'
                    }, {
                        name: 'fsectionId'
                    }],
                    listeners: {
                        enablebysatellite: function(c, d) {
                            if (d.up <= c.sbl('up').maxValue) Ext.Ajax.request({
                                url: Indi.pre + me.uri + 'consider/' + c.name + '/',
                                params: d,
                                success: function(response) {
                                    var json = Ext.JSON.decode(response.responseText, true);
                                    c.val(json.entityId);
                                }
                            });
                        }
                    }
                }
            },
            formItem$Type: {nojs: true},
            formItem$Source: {
                nojs: true,
                considerOn: [{
                    name: 'type'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'dynamic');
                    }
                }
            },
            formItem$Content: {
                considerOn: [{
                    name: 'type'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.type == 'static');
                    }
                }
            },
            formItem$Up: function(){
                var me = this;
                return {
                    maxValue: me.ti().row.view('up').maxValue,
                    considerOn: [{
                        name: 'type'
                    }],
                    listeners: {
                        enablebysatellite: function(c, d) {
                            c.setVisible(d.type == 'dynamic');
                            c.setDisabled(d.type == 'static' || !c.maxValue);
                        }
                    }
                }
            }
        }
    }
});