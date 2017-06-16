define([

    // Libs
    'lib/Console', 'lib/Model',

    // Application
    'model/Meal', 'collection/Meal'

], function (console, Model, MealModel, MealCollection) {

    /**
     * @class MenuCategoryModel
     * @extends Model
     */
    var MenuCategoryModel = Model.extend('MenuCategoryModel', {

        idAttribute: 'id',

        defaults: {
            id      : null,
            timeFrom: 0,
            timeTo  : 0,
            visible : false
        },

        /**
         * @protected
         * @type {function(): string}
         */
        urlRoot: function () {
            if (this.isNew()) {
                if (! this.has('restaurant')) {
                    throw new Error('restaurant must be set for new menu category');
                }
                return '/backoffice/restaurants/' + this.get('restaurant').getId() + '/menucategories';
            } else {
                return '/backoffice/menucategories';
            }
        },

        /**
         * @private
         */
        relations: [
            {
                type             : Backbone.HasMany,
                key              : 'meals',
                relatedModel     : MealModel,
                includeInJSON    : false,
                collectionType   : MealCollection,
                collectionOptions: function (menuCategory) {
                    return {
                        menuCategoryId: menuCategory.getId()
                    };
                },
                reverseRelation  : {
                    includeInJSON: false,
                    key          : 'menuCategory',
                    parse        : true
                },
                parse            : true
            }
        ],

        initialize: function () {
        },

        /**
         *
         * @override
         * @param {Object} resp
         * @param {Object} xhr
         * @return {Array.<Object>}
         */
        parse: function parse(resp, xhr) {
            if (resp && resp.meals && _.isObject(resp.meals)) {
                resp.meals = _.toArray(resp.meals);
            }

            return this.callParent(arguments);
        },

        getRestaurant: function getRestaurant() {
            return this.get('restaurant');
        },

        getMeals: function getMeals() {
            if (this.get('meals').isEmpty()) {
                this.get('meals').fetch({async: false});
            }
            return this.get('meals');
        }

    });

    return MenuCategoryModel;

});
