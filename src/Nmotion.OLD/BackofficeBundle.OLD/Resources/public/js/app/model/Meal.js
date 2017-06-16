define([

    // Libs
    'lib/Console',
    'lib/Model',

    // App
    'model/Asset',
    'model/MealExtraIngredient',
    'model/MealOption',
    'collection/MealExtraIngredient',
    'collection/MealOption'

], function (

    console,
    Model,

    AssetModel,
    MealExtraIngredientModel,
    MealOptionModel,
    MealExtraIngredientCollection,
    MealOptionCollection

) {

    /**
     * @class MealModel
     * @extends Model
     */
    var MealModel = Model.extend('MealModel', {

        /**
         * @protected
         */
        idAttribute: 'id',

        /**
         * @protected
         */
        defaults: {
            id            : null,
            visible       : false,
            logoAsset     : null,
            thumbLogoAsset: null,
            timeFrom      : 0,
            timeTo        : 0
        },

        /**
         * @protected
         * @type {function(): string}
         */
        urlRoot: function () {
            if (this.isNew()) {
                if (! this.has('menuCategory')) {
                    throw new Error('menu category must be set for new meal');
                }
                return '/backoffice/menucategories/' + this.get('menuCategory').getId() + '/meals';
            } else {
                return '/backoffice/meals';
            }
        },

        /**
         * Definition of associations between Meal model and others
         *
         * @protected
         */
        relations: [
            {
                type             : Backbone.HasMany,
                key              : 'mealExtraIngredients',
                relatedModel     : MealExtraIngredientModel,
                includeInJSON    : true,
                collectionType   : MealExtraIngredientCollection,
                collectionOptions: function (meal) {
                    return {
                        mealId: meal.getId()
                    };
                },
                reverseRelation  : {
                    includeInJSON: false,
                    key          : 'meal'
                }
            },
            {
                type             : Backbone.HasMany,
                key              : 'mealOptions',
                relatedModel     : MealOptionModel,
                includeInJSON    : true,
                collectionType   : MealOptionCollection,
                collectionOptions: function (meal) {
                    return {
                        mealId: meal.getId()
                    };
                },
                reverseRelation  : {
                    includeInJSON: false,
                    key          : 'meal'
                }
            },
            {
                type         : Backbone.HasOne,
                key          : 'logoAsset',
                relatedModel : AssetModel,
                includeInJSON: 'id'
            },
            {
                type         : Backbone.HasOne,
                key          : 'thumbLogoAsset',
                relatedModel : AssetModel,
                includeInJSON: 'id'
            }
        ],

        initialize: function () {
        },

        ensureRelationsLoaded: function ensureRelationsLoaded() {
            var options = this.get('mealOptions'),
                extraIngredients = this.get('mealExtraIngredients');

            if (options.isEmpty() && ! this.isNew()) {
                options.fetch({async: false});
            }

            if (extraIngredients.isEmpty() && ! this.isNew()) {
                extraIngredients.fetch({async: false});
            }
        },

        getExtraIngredients: function () {
            return this.get('mealExtraIngredients');
        },

        getOptions: function () {
            return this.get('mealOptions');
        }

    });

    return MealModel;

});
