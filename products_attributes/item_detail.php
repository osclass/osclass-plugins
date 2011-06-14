<h3><?php _e('Products attributes', 'products_attributes') ; ?></h3>
<table>
    <tr>
        <td><label for="make"><?php _e('Make', 'products_attributes'); ?></label></td>
        <td><?php echo @$detail['s_make']; ?></td>
    </tr>
    <tr>
        <td><label for="model"><?php _e('Model', 'products_attributes'); ?></label></td>
        <td><?php echo @$detail['s_model']; ?></td>
    </tr>
</table>