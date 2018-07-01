/**
 * Current Indi Engine front controller concept is that it's the same as admin controller,
 * but with a different set of modes and views for possible actions, currently
 */
Ext.override(Indi.lib.controller.Controller, {

    /**
     * Dictionary of default modes and views for different actions
     */
    statics: {
        defaultMode: {index: 'rowset', create: 'row'},
        defaultView: {create: 'form'}
    }
});
