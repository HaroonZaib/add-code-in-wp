jQuery(document).ready(function ($) {
    var dataSaved = '<div id="dataSaved">Data Saved!</div>';

        $('#add_code_in_wp_form').submit(function (e){
		e.preventDefault();
            var data = {
                    action : 'add_code_in_wp',
                    action_value: $(this).serialize(),
                };
                $.post(add_code_in_wp_obj.url, data, function(response) {
                    $("#response_data").html(response);
            });
        });
});
