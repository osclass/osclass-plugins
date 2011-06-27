<div class="row one_input">
    
<h6><?php _e("Offer type", 'buysell');?></h6>
<?php $buysell_types = __get('buysell_types');?>
<select name="buysell_type" id="buysell_type">
    <option value="ALL" ><?php _e('All', 'buysell_type'); ?></option>
    <?php foreach($buysell_types as $k => $v) {?>
    <option value="<?php echo $k; ?>" <?php if(Params::getParam('buysell_type')==$k) {echo "selected";};?>><?php echo $v; ?></option>
    <?php }; ?>
</select>

</div>