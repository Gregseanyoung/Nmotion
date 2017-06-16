define([

    // Libs
    'backbone', 'lib/view/Form'

], function (Backbone, Form) {

    /**
     * @class SymfonyForm
     * @extends Form
     */
    var SymfonyForm = Form.extend('SymfonyForm', {

        handleServerValidationErrors: function (model, response) {
            var fields = $.parseJSON(response.responseText).errors[0], errors;

            var handleFormFields = function (fields, selector) {
                _.each(fields, function (val, fieldName) {
                    var selectorNextDepth = selector;

                    if (_.isEmpty(val)) {
                        return;
                    }

                    if (fieldName === 'errors') {
                        $(selector).addClass('error');
                        $(selector + ' .help-inline').html(val.join('<br>'));
                        return;
                    }

                    if (fieldName === 'children' && ! _.isArray(val)) {
                        handleFormFields(val, selector);
                        return;
                    }

                    if (_.isArray(val)) {
                        if ($('ul>li', selector).length) {
                            selectorNextDepth += ' ul>li';
                        }
                    } else if (_.isObject(val)) {
                        if (_.isNumber(fieldName)) {
                            selectorNextDepth += ':eq(' + fieldName + ')';
                        } else {
                            selectorNextDepth += ' .field-' + fieldName;
                        }
                    } else {
                        console.debug('single value: %s', val);
                        return;
                    }

                    handleFormFields(val, selectorNextDepth);
                });
            };

            handleFormFields(fields, '');
        }


    });

    return SymfonyForm;

});
