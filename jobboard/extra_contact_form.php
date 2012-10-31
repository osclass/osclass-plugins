<?php
$birthday = '';
if( Session::newInstance()->_getForm("birthday") != "" ) {
    $birthday = Session::newInstance()->_getForm("birthday") ;
}
$sex = '';
if( Session::newInstance()->_getForm("sex") != "" ) {
    $sex = Session::newInstance()->_getForm("sex") ;
}
?>
<label for="birthday"><?php _e('Birthday', 'jobboard'); ?> (MM/DD/YYYYY)</label>
<input value="<?php echo osc_esc_html($birthday); ?>" placeholder="MM/DD/YYYY" type="text" id="birthday" name="birthday"/>
<label for="sex" style="display:inline;"><?php _e('Sex', 'jobboard'); ?></label>
<select id="sex" name="sex">
    <option <?php if($sex === 'male') {echo 'selected="selected"'; } ?> value="male"><?php _e('Male', 'jobboard'); ?></option>
    <option <?php if($sex === 'female') {echo 'selected="selected"'; } ?> value="female"><?php _e('Female', 'jobboard'); ?></option>
    <option <?php if($sex === 'prefernotsay') {echo 'selected="selected"'; } ?> value="prefernotsay"><?php _e('Prefer not say', 'jobboard'); ?></option>
</select>