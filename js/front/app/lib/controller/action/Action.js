/**
 * Base class for all controller actions instances
 */
Ext.override(Indi.lib.controller.action.Action, {

    // @inheritdoc
    initComponent: function() {
        var me = this, wrp;

        // Set up docked items
        me.panel.dockedItems = me.panelDockedA();

        // Remove panel header
        me.panel.header = false;

        // Set up context to be available as panel's `$ctx` prop
        me.panel.$ctx = me;

        // If all contents should be added to existing panel
        if (me.cfg.into) me.panel.header = false; else {

            // Append tools and toolbars to the main panel
            Ext.merge(me.panel, {
                renderTo: 'i-center-center-body',
                tools: me.panelToolA()
            });

            // Update id of the main panel (temporary)
            Indi.centerId = me.panel.id;
        }

        // If we're going create a wrapper within a window
        // but wrapper with same id is already exist within a south-panel tab
        /*if ((wrp = Ext.getCmp(me.panel.id)) && !me.cfg.into) {

            // Backup some info (tab id and wrapper initial config),
            // that will help us to re-instantiate wrapper within tab
            // in case if user will close the window
            me.panel.tabDraft = {
                containerId: wrp.ownerCt.id,
                itemConfig: wrp.initialConfig
            }

            // Add placeholder into the tab
            wrp.up('[isSouth]').addTabPlaceholder(wrp.ownerCt.id, wrp.initialConfig.id, 'action');

            // Destroy wrapper, that currently exists within a south-panel tab
            // as we're going to create same wrapper within a separate window
            wrp.destroy();
        }*/

        // Create panel instance
        var panel = Ext.widget(me.panel);

        // If created instance should be inserted as a tab - do it
        if (me.cfg.into) Ext.getCmp(me.cfg.into).add(panel);

        // If panel has `onLoad` property, and it's a function - call it
        if (Ext.isFunction(panel.onLoad)) panel.onLoad(me);

        // If special `onLoad` callback is provided within me.cfg - call it
        if (Ext.isFunction(me.cfg.onLoad)) me.cfg.onLoad.call(panel, me);
    }
});
