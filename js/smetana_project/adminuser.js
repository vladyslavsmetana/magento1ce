var $j = jQuery.noConflict();

$j(document).ready(function() {
    function checkRole() {
        var $checked = $j('.radio').filter(':checked');
        var userRole = $checked.parent().next().text().trim();
        if (userRole != 'Специалист колл-центра') {
            $j('.entry-edit-head').last().hide();
            $j('#user_callcentre_fieldset').hide();
        } else {
            $j('.entry-edit-head').last().show();
            $j('#user_callcentre_fieldset').show();
        }
    }

    checkRole();

    $j('.radio').change(function() {
        checkRole();
    });
});
