<?php
    $make = Params::getParam('make') ;
    $model = Params::getParam('model') ;
?>
    
<h3><?php _e('Products attributes', 'products_attributes') ; ?></h3>
<div class="row one_input">
    <table>
        <tr>
            <td>
                <label for="make"><?php _e('Make', 'products_attributes'); ?></label>
                <br/>
                <input type="text" id="make" name="make" value="<?php echo $make;?>"/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="model"><?php _e('Model', 'products_attributes'); ?></label>
                <br/>
                <input type="text" id="model" name="model" value="<?php echo $model;?>"/>
            </td>
        </tr>
    </table>
</div>