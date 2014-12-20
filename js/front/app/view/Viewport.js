/**
 * Setup a viewport for Indi Engine interface, used to handle extjs usage within public area
 */
Ext.define('Indi.view.Viewport', {

    // @inheritdoc
    extend: 'Ext.container.Viewport',

    // @inheritdoc
    layout: {
        type: 'fit'
    },

    // @inheritdoc
    alternateClassName: 'Indi.Viewport',

    /**
     * Center panel cfg
     */
    center: {
        region: 'center',
        defaults: {split: true},
        border: 0,
        layout: {type: 'border', padding: '0 0 0 0'},
        id: 'i-center',
        items: [{
            region: 'center',
            id: 'i-center-center',
            border: 0
        }]
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup items
        me.items = [me.center];

        // Call parent
        me.callParent();
    }
});
