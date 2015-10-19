/**
 * Setup a viewport for Indi Engine interface, used to handle extjs usage within public area
 */
Ext.define('Indi.view.viewport.Base', {

    width: '100%',
    height: '100%',

    // @inheritdoc
    layout: {
        type: 'fit'
    },

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
    }
});
