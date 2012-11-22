<?php

    @Session::newInstance()->_drop('userId');
    @Session::newInstance()->_drop('userName');
    @Session::newInstance()->_drop('userEmail');
    @Session::newInstance()->_drop('userPhone');

    @Cookie::newInstance()->pop('oc_userId');
    @Cookie::newInstance()->pop('oc_userSecret');
    @Cookie::newInstance()->set();

?>
<script type="text/javascript">
    window.location = "<?php echo osc_base_url(); ?>";
</script>