Ext.override(Indi, {

    /**
     * Adjust appFolder, for it to use 'front' instead of 'admin'
     */
    appFolder: '/js/front/app',

    /**
     * Launch callback
     */
    launch: function() {
        var me = this, xbody;

        // Merge static properties, passed within construction, with prototype's static properties
        me.self = Ext.merge(me.self, me.statics);

        // Chose viewport
        Indi.viewport = (xbody = Ext.getBody().down('[i-load] .x-body'))
            ? Ext.create('Indi.view.viewport.Panel', {renderTo: xbody})
            : Ext.create('Indi.view.viewport.Iframe');

        // Link an app
        Indi.app = me;

        // Run
        Indi.trail(true).apply(Ext.merge(json, {uri: window.location.pathname, cfg: {}}));
    }
});