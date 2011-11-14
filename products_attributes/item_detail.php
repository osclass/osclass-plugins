<h3 style="margin-left: 40px; margin-top: 20px;"><?php _e('Products attributes', 'products_attributes') ; ?></h3>
<table style="margin-left: 20px;">
    <tbody>
        <tr>
            <td width="150px"><label for="make"><?php _e('Make', 'products_attributes'); ?></label></td>
            <td width="150px"><?php echo @$detail['s_make']; ?></td>
        </tr>
        <tr>
            <td width="150px"><label for="model"><?php _e('Model', 'products_attributes'); ?></label></td>
            <td width="150px"><?php echo @$detail['s_model']; ?></td>
        </tr>
    </tbody>
</table>