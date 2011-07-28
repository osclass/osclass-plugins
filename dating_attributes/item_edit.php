<h3><?php _e('Dating attributes', 'dating_attributes'); ?></h3>
<table>
    <tr>
        <td><?php _e('You are', 'dating_attributes'); ?>:</td>
        <?php
            if( Session::newInstance()->_getForm('pd_genderFrom') != '' ) {
                $detail['e_gender_from'] = Session::newInstance()->_getForm('pd_genderFrom');
            }
        ?>
        <td>
            <label for="gfm"><input type="radio" name="genderFrom" id="gfm" value="MAN" <?php if( @$detail['e_gender_from'] == "MAN" ) { echo "checked"; } ?>/><?php _e('Man', 'dating_attributes'); ?></label><br />
            <label for="gfw"><input type="radio" name="genderFrom" id="gfw" value="WOMAN" <?php if( @$detail['e_gender_from'] == "WOMAN" ) { echo "checked"; } ?>/><?php _e('Woman', 'dating_attributes'); ?></label><br />
            <label for="gfn"><input type="radio" name="genderFrom" id="gfn" value="NI" <?php if( @$detail['e_gender_from'] == "NI" ) { echo "checked"; } ?>/><?php _e('Not informed', 'dating_attributes'); ?></label><br />
	</td>
    </tr>
    <tr>
        <td><?php _e('Looking for', 'dating_attributes'); ?>:</td>
        <?php
            if( Session::newInstance()->_getForm('pd_genderTo') != '' ) {
                $detail['e_gender_to'] = Session::newInstance()->_getForm('pd_genderTo');
            }
        ?>
        <td>
            <label for="gtm"><input type="radio" name="genderTo" id="gtm" value="MAN" <?php if( @$detail['e_gender_to'] == "MAN" ) { echo "checked"; } ?>/><?php _e('Man', 'dating_attributes'); ?></label><br />
            <label for="gtw"><input type="radio" name="genderTo" id="gtw" value="WOMAN" <?php if( @$detail['e_gender_to'] == "WOMAN" ) { echo "checked"; } ?>/><?php _e('Woman', 'dating_attributes'); ?></label><br />
            <label for="gtn"><input type="radio" name="genderTo" id="gtn" value="NI" <?php if( @$detail['e_gender_to'] == "NI" ) { echo "checked"; } ?>/><?php _e('Not informed', 'dating_attributes'); ?></label><br />
	</td>
    </tr>
    <tr>
        <td><?php _e('Relation type', 'dating_attributes'); ?>:</td>
        <?php
            if( Session::newInstance()->_getForm('pd_relation') != '' ) {
                $detail['e_relation'] = Session::newInstance()->_getForm('pd_relation');
            }
        ?>
        <td>
            <label for="grm"><input type="radio" name="relation" id="grm" value="FRIENDSHIP" <?php if( @$detail['e_relation'] == "FRIENDSHIP" ) { echo "checked"; } ?>/><?php _e('Friendship', 'dating_attributes'); ?></label><br />
            <label for="grw"><input type="radio" name="relation" id="grw" value="FORMAL" <?php if( @$detail['e_relation'] == "FORMAL" ) { echo "checked"; } ?>/><?php _e('Formal relation', 'dating_attributes'); ?></label><br />
            <label for="grn"><input type="radio" name="relation" id="grn" value="INFORMAL" <?php if( @$detail['e_relation'] == "INFORMAL" ) { echo "checked"; } ?>/><?php _e('Informal relation', 'dating_attributes'); ?></label><br />
        </td>
    </tr>
</table>