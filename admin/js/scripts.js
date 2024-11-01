jQuery(function ($) {

    $(document).ready(function () {

        // Update key form send

        $('#webeyez_update_key').on('submit', function () {

            var self = $(this);
            var submitButton = self.find('button[type=submit]');
            var formData = self.serializeArray();
            formData.push({ name: 'action', value: 'webeyez_key' });
            var formMessageBox = self.find('.form-message');

            $.ajax({
                url: ajaxurl,
                data: formData,
                type: 'POST',
                beforeSend: function beforeSend() {
                    self.find('.form-row input').removeClass('invalid');
                    self.find('.field-error').remove();
                    formMessageBox.removeClass('form-error').text('');
                    submitButton.prop('disabled', true);
                    submitButton.find('span').hide();
                    submitButton.find('.webeyez-loader').fadeIn();
                },
                success: function success(response) {
                    if (response.success) {
                        formMessageBox.removeClass('form-error').text(response.data);
                    } else {
                        if (response.data.fields) {
                            $.each(response.data.fields, function (index, value) {
                                self.find('#' + value).addClass('invalid').after('<span class="field-error">' + response.data.messages[index] + '</span>');;
                            });
                        }
                        if (response.data.message) {
                            formMessageBox.addClass('form-error').text(response.data.message);
                        }
                    }
                    submitButton.prop('disabled', false);
                    submitButton.find('span').fadeIn();
                    submitButton.find('.webeyez-loader').hide();
                }
            });

            return false;
        });
    });
});