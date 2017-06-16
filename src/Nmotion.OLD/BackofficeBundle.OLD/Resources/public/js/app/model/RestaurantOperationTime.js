define([

    // Libs
    'lib/Model', 'lib/view/Form'

], function (Model, Form) {

    Backbone.Form.setTemplates({
        operationTimeField      : '\
            <div class="pull-left control-group field-{{key}}" style="margin: 0 20px 0 0; width: 100px">\
                <label for="{{id}}" style="text-align: left; display: inline; margin-right: 5px">{{title}}</label>\
                {{editor}}\
                <div class="help-inline">{{error}}</div>\
            </div>\
        '
    });

    /**
     * @class RestaurantOperationTimeModel
     * @extends Model
     */
    var RestaurantOperationTimeModel = Model.extend('RestaurantOperationTimeModel', {

        idAttribute: 'id',

        schema: {
            id          : {
                type: 'Hidden'
            },
            dayOfTheWeek: {
                type: 'Hidden'
            },
            timeFrom    : {
                type      : 'Time',
                title     : 'From:',
                template  : 'operationTimeField'
            },
            timeTo      : {
                type      : 'Time',
                title     : 'To:',
                template  : 'operationTimeField'
            }
        },

        initialize: function () {
        }

    });

    return RestaurantOperationTimeModel;

});
