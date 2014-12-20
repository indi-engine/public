Ext.override(Indi, {

    /**
     * Adjust appFolder, for it to use 'front' instead of 'admin'
     */
    appFolder: '/js/front/app',

    /**
     * Launch callback
     */
    launch: function() {
        var me = this;

        // Merge static properties, passed within construction, with prototype's static properties
        me.self = Ext.merge(me.self, me.statics);

        // Create a viewport
        Indi.viewport = Ext.create('Indi.view.Viewport');

        // Link an app
        Indi.app = me;

        // Run
        Indi.trail(true).run();
    }
});