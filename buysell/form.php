<label><?php _e("Offer type", 'buysell');?></label>
<?php $buysell_types = __get('buysell_types');?>
<select name="buysell_type" id="buysell_type">
    <?php foreach($buysell_types as $k => $v) {?>
    <option value="<?php echo $k; ?>" <?php if(@$detail['s_type']==$k) {echo "selected";};?>><?php echo $v; ?></option>
    <?php }; ?>
</select>