<h3><?php _e('Products attributes', 'products_attributes') ; ?></h3>
<table>
    <tr>
        <td><label for="make"><?php _e('Make', 'products_attributes'); ?></label></td>
    	<td><input type="text" name="make" id="make" value="<?php echo @$detail['s_make']; ?>" size="20" /></td>
    </tr>
    <tr>
        <td><label for="model"><?php _e('Model', 'products_attributes'); ?></label></td>
        <td><input type="text" name="model" id="model" value="<?php echo @$detail['s_model']; ?>" size="20" /></td>
    </tr>
</table>