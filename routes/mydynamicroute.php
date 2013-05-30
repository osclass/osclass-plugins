<div style="padding: 20px;">
    <div style="float: left; width: 100%;">
        <?php
            echo '<p>'.__('This is a DINAMIC ROUTE, variables received', 'routes').'</p><br/>';
            $args = Params::getParamsAsArray();
            foreach($args as $k => $v) {
                echo "<p><b>".$k."</b> => ".$v."</p>";
            }
        ?>
    </div>
    <div style="clear: both;"></div>
</div>
