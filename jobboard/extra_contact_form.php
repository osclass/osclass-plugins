<?php function extra_contac_form_validation() {?>
<script type="text/javascript">
    
    function isValidDate(s) {
        var bits = s.split('/');
        // new Date(year, month, day, hours, minutes, seconds, milliseconds);
        var d = new Date(bits[2] + '/' + bits[0] + '/' + bits[1]);
        return !!(d && (d.getMonth() + 1) == bits[0] && d.getDate() == Number(bits[1]));
    } 
    
    jQuery.validator.addMethod("birthdate", function(value, element) {
        return isValidDate( value );
    }, "<?php _e('Invalid birthday date', 'jobboard');?>");
    
    $(document).ready(function() {
        
        $('#sex').rules("add", {
        required: true,
        messages: {
            required: "<?php _e("Sex: this field is required", 'jobboard'); ?>"
        }});
        
        $('#birthday').rules("add", {
        required: true,
        birthdate: true,
        messages: {
            required: "<?php _e("Birthday: this field is required", 'jobboard'); ?>"
        }});
    });
    
</script>
<?php 
}
osc_add_hook('footer', 'extra_contac_form_validation');
?>

<?php 
$birthday = '';
if( Session::newInstance()->_getForm("birthday") != "" ) {
    $birthday = Session::newInstance()->_getForm("birthday") ;
}
?>
<label for="birthday"><?php _e('Birthday', 'jobboard'); ?></label>
<input value="<?php echo osc_esc_html($birthday); ?>" placeholder="mm/dd/yyyy" type="text" id="birthday" name="birthday"/>

<?php 
$sex = '';
if( Session::newInstance()->_getForm("sex") != "" ) {
    $sex = Session::newInstance()->_getForm("sex") ;
}
?>
<p>
    <label for="sex" style="display:inline;">Sex</label>
    <select id="sex" name="sex">
        <option <?php if($sex=='male') {echo 'selected="selected"'; } ?> value="male"><?php _e('Male', 'jobboard'); ?></option>
        <option <?php if($sex=='female') {echo 'selected="selected"'; } ?> value="female"><?php _e('Female', 'jobboard'); ?></option>
        <option <?php if($sex=='prefernotsay') {echo 'selected="selected"'; } ?> value="prefernotsay"><?php _e('Prefer not say', 'jobboard'); ?></option>
    </select>
</p>