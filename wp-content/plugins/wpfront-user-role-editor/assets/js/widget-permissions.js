function wpfront_ure_widget_permissions_update_widget_title(id) {
    var $ = jQuery;
    
    var $span = $('#' + id);
    $span.removeAttr('id').removeClass('hidden');
    
    var $h3 = $span.closest('div.widget-inside').prev().find('h3');
    $h3.find('span.in-title-access-type').remove();
    
    $h3.append($span);
}

(function ($) {
    var ROLES_USERS = 4;

    $(function () {
        var $widgets_holder = $('.widget-liquid-right');
        
        $widgets_holder.on('change', 'input.user-restriction-type', function () {
            var $this = $(this);
            if ($this.val() == ROLES_USERS) {
                $this.closest('span.user-restriction-container').find('span.roles-container').removeClass('hidden');
            } else {
                $this.closest('span.user-restriction-container').find('span.roles-container').addClass('hidden');
            }
        });
    });
})(jQuery);

