// create icheck
$(function() {
    $('body').find('input[type="checkbox"],input[type="radio"]')
        .each(function() {
            var $this = $(this);
            $this.iCheck({
                checkboxClass: 'icheckbox_md',
                radioClass: 'iradio_md',
                increaseArea: '20%'
            });
        });
    });