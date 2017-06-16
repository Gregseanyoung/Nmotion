define([

    // Libs
    'lib/Model',

    // Application
    'model/Asset',
    'model/User',
    'model/Staff',
    'model/RestaurantAddress',
    'model/MenuCategory',
    'model/RestaurantOperationTime',
    'model/Income',
    'collection/MenuCategory',
    'collection/RestaurantOperationTime',
    'collection/Income',
    'collection/Staff'

], function (

    // Libs
    Model,

    // Application
    AssetModel,
    UserModel,
    StaffModel,
    RestaurantAddressModel,
    MenuCategoryModel,
    RestaurantOperationTimeModel,
    RestaurantIncomeModel,
    MenuCategoryCollection,
    RestaurantOperationTimeCollection,
    RestaurantIncomeCollection,
    StaffCollection

) {

    /**
     * @class RestaurantModel
     * @extends Model
     */
    var RestaurantModel = Model.extend('RestaurantModel', {

        idAttribute: 'id',

        defaults: {
            id             : null,
            logoAsset      : null,
            invoicingPeriod: 'monthly',
            checkOutTime   : 480,
            vatNo          : '',
            regNo          : '',
            kontoNo        : '',
            visible        : false,
            takeaway       : false,
            taMember       : false,
            address        : {
                id: null
            },
            adminUser      : {
                id: null
            },
            operationTimes : [
                {id: null, dayOfTheWeek: 1, timeFrom: 0, timeTo: 0},
                {id: null, dayOfTheWeek: 2, timeFrom: 0, timeTo: 0},
                {id: null, dayOfTheWeek: 3, timeFrom: 0, timeTo: 0},
                {id: null, dayOfTheWeek: 4, timeFrom: 0, timeTo: 0},
                {id: null, dayOfTheWeek: 5, timeFrom: 0, timeTo: 0},
                {id: null, dayOfTheWeek: 6, timeFrom: 0, timeTo: 0},
                {id: null, dayOfTheWeek: 7, timeFrom: 0, timeTo: 0}
            ]
        },

        urlRoot: '/backoffice/restaurants',

        /**
         * @private
         */
        relations: [
            {
                type             : Backbone.HasMany,
                key              : 'menuCategories',
                includeInJSON    : false,
                relatedModel     : MenuCategoryModel,
                collectionType   : MenuCategoryCollection,
                collectionOptions: function (restaurant) {
                    return {
                        restaurantId: restaurant.getId()
                    };
                },
                reverseRelation  : {
                    key          : 'restaurant',
                    includeInJSON: false,
                    parse        : true
                },
                parse            : true
            },
            {
                type             : Backbone.HasMany,
                key              : 'incomes',
                includeInJSON    : false,
                relatedModel     : RestaurantIncomeModel,
                collectionType   : RestaurantIncomeCollection,
                collectionOptions: function (restaurant) {
                    return {
                        restaurantId: restaurant.getId()
                    };
                },
                reverseRelation: {
                    key          : 'restaurant',
                    includeInJSON: false
                }
            },
            {
                type         : Backbone.HasOne,
                key          : 'address',
                relatedModel : RestaurantAddressModel
            },
            {
                type         : Backbone.HasOne,
                key          : 'adminUser',
                relatedModel : UserModel
            },
            {
                type             : Backbone.HasMany,
                key              : 'staff',
                includeInJSON    : false,
                relatedModel     : StaffModel,
                collectionType   : StaffCollection,
                collectionOptions: function (restaurant) {
                    return {
                        restaurantId: restaurant.getId()
                    };
                },
                reverseRelation: {
                    key          : 'restaurant',
                    includeInJSON: false
                },
                parse            : true
            },
            {
                type         : Backbone.HasOne,
                key          : 'logoAsset',
                relatedModel : AssetModel,
                includeInJSON: 'id'
            },
            {
                type             : Backbone.HasMany,
                key              : 'operationTimes',
                relatedModel     : RestaurantOperationTimeModel,
                collectionType   : RestaurantOperationTimeCollection,
                collectionOptions: function (restaurant) {
                    return {
                        restaurantId: restaurant.getId()
                    };
                },
                reverseRelation: {
                    key          : 'restaurant',
                    includeInJSON: false
                }
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
            if (resp && resp.menuCategories && _.isObject(resp.menuCategories)) {
                resp.menuCategories = _.toArray(resp.menuCategories);
            }

            return this.callParent(arguments);
        },

        getMenuCategories: function () {
            this.fetchRelated('menuCategories', {async: false});
            return this.get('menuCategories');
        },

        getIncomes: function () {
            if (this.get('incomes').isEmpty()) {
                this.get('incomes').fetch({async: false});
            }
            return this.get('incomes');
        }

    });

    return RestaurantModel;

});
