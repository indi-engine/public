/**
 * Setup a viewport for Indi Engine interface, used to handle extjs usage within public area
 */
Ext.define('Indi.view.viewport.Iframe', {

    // @inheritdoc
    extend: 'Ext.container.Viewport',

    // @inheritdoc
    alternateClassName: 'Indi.Viewport.Iframe',

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
