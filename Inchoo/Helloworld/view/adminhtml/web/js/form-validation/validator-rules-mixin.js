define([
    'jquery',
    'moment'
], function ($, moment) {
    'use strict';

    return function (validator) {

          validator.addRule(
                'select_example', // Rule name
                function (value) {
                  var selectElement = $('select[name="select_example"]');
                var dependentField = $('[data-index="dependent_field_1"]');
                           selectElement.on('change', function () {
                               if ($(this).val() === '2') {
                                   dependentField.hide();
                               } else {
                                  dependentField.show();
                               }
                           });
                    return value !== '0'; // Ensure the user doesn't select the default "empty" option
                },
                $.mage.__('Please select a valid option.') // Custom error message
            );
        return validator;
    };
});
