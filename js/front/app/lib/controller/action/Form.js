/**
 * Base class for all controller actions instances, that operate with some certain rows,
 * and use forms controls to display/modify those rows properties, within website public area
 */
Ext.override(Indi.lib.controller.action.Form, {

    // @inheritdoc
    panel: {
        docked: {
            items: []
        },
        listeners: {

            // Override `afterrender` event handler - setup it as an empty function
            afterrender: Ext.emptyFn
        }
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Init `scope` trail item property as an empty object
        me.ti().scope = {};

        // Call parent
        me.callParent();
    }
});