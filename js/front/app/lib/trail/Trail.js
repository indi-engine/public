/**
 * Trail object. Is used to handle all levels of Indi Engine interface places hierarchy
 */
Ext.define('Indi.lib.trail.Trail', {

    // @inheritdoc
    alternateClassName: 'Indi.Trail',

    // @inheritdoc
    singleton: true,

    /**
     * Apply the store
     *
     * @param route
     */
    apply: function(scope){
        var section = scope.route.last().section.alias, action = scope.route.last().action.alias, controller;

        // Fulfil global fields storage
        scope.route.forEach(function(r, i, a) {
            if (r.fields) r.fields.forEach(function(fr, fi, fa){
                Indi.fields[fr.id] = new Indi.lib.dbtable.Row.prototype(fr);
            });
        });

        // Try to pick up loaded controller and dispatch it's certain action
        try {

            // Get controller
            controller = Indi.app.getController(section);

            // Try dispatch needed action
            try {controller.dispatch(scope);}

                // If dispatch failed - write the stack to the console
            catch (e) {console.log(e.stack);}

            // If failed
        } catch (e) {

            // Define needed controller on-the-fly
            Ext.define('Indi.controller.' + section, {extend: 'Indi.Controller'});

            // Instantiate it, and dispatch needed action
            Indi.app.getController(section).dispatch(scope);
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
    },

    /**
     * Empty function
     */
    breadCrumbs: function(){

    }
});