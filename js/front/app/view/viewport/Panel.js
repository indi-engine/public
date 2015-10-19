/**
 * Setup a viewport for Indi Engine interface, used to handle extjs usage within public area
 */
Ext.define('Indi.view.viewport.Panel', {

    // @inheritdoc
    extend: 'Ext.panel.Panel',

    // @inheritdoc
    alternateClassName: 'Indi.Viewport.Panel',

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup items
        me.items = [me.center];

        // Call parent
        me.callParent();
    }
}, function() {

    // Borrow 'mergeParent' method from Ext.Component class
    this.borrow(Indi.view.viewport.Base, ['center', 'width', 'height', 'layout']);
});
