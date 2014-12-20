/**
 * Trail object. Is used to handle all levels of Indi Engine interface places hierarchy
 */
Ext.define('Indi.lib.trail.Trail', {

    // @inheritdoc
    alternateClassName: 'Indi.Trail',

    // @inheritdoc
    singleton: true,

    /**
     * The data array, that indi.trail will be operating with.
     * Data will be set by php's json_encode($this->trail->toArray()) call
     *
     * @type Array
     */
    store: [],

    /**
     * Apply the store
     *
     * @param store
     */
    apply: function(store){
        var me = this, i;

        // Reset store
        me.store = [];

        // Update store
        for (i = 0; i < store.length; i++) me.store.push(Ext.create('Indi.trail.Item', Ext.merge({level: i}, store[i])));
    },

    /**
     *
     */
    run: function() {

        Indi.story.push(window.location.pathname);

        // Try to load controller, or use existing (if it was initially or already loaded)
        try {
            var controller = Indi.app.getController(Indi.trail().section.alias);

            // Try to dispatch action
            try { controller.dispatch(Indi.trail().action.alias, Indi.story[Indi.story.length-1]); }

            // If try was unsuccessful - log the error stack to the console
            catch (e) { console.log(e.stack); }

        // If try was unsuccessful
        } catch (e){

            // Build controller name and define it in the 'on-the-fly' mode
            Ext.define('Indi.controller.' + Indi.trail().section.alias, {extend: 'Indi.Controller'});

            // Instantiate that controller and dispatch an action
            Indi.app.getController(Indi.trail().section.alias).dispatch(Indi.trail().action.alias, Indi.story[Indi.story.length-1]);
        }
    },

    /**
     * Get the trail item
     *
     * @param stepsUp
     * @return {*}
     */
    item: function(stepsUp) {
        var me = this;

        // Normalize `stepsUp` argument
        if (typeof stepsUp == 'undefined') stepsUp = 0;

        // Get the trail item, located at required level, and return it
        return me.store[me.store.length - 1 - stepsUp];
    }
});