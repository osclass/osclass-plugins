<h2><?php _e("Digital Goods", 'digitalgoods') ; ?></h2>
<div class="box">
    <div class="box dg_files">
        <div class="row">
            <p><?php printf(__('Allowed extensions are %s. Any other file will not be uploaded', 'digitalgoods'), osc_get_preference('allowed_ext', 'digitalgoods')) ; ?></p>
        </div>
            <?php
            if($dg_files != null && is_array($dg_files) && count($dg_files) > 0) {
                foreach($dg_files as $_r) { ?>
                    <div id="<?php echo $_r['pk_i_id'] ; ?>" fkid="<?php echo $_r['fk_i_item_id'];?>" name="<?php echo $_r['s_name'];?>">
                        <p><?php echo $_r['s_name'] ; ?> <a href="javascript:delete_dg_file(<?php echo $_r['pk_i_id'] . ", " . $_r['fk_i_item_id'] . ", '" . $_r['s_name'] . "', '" . $secret . "'" ;?>);"  class="delete"><?php _e('Delete', 'digitalgoods') ; ?></a></p>
                    </div>
                <?php }
            } ?>
        <div id="dg_files">
            <?php if(osc_get_preference('max_files', 'digital_goods') == 0 || (osc_get_preference('max_files', 'digital_goods') != 0 && count($dg_files) < osc_get_preference('max_files', 'digital_goods'))) { ?>
            <div class="row">
                <input type="file" name="dg_files[]" />
            </div>
            <?php } ?>
        </div>
        <div class="row">
            <a href="#" onclick="addNewDG(); return false;"><?php _e('Add new digital file', 'digitalgoods') ; ?></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dgIndex = 0;
    function gebi(id) { return document.getElementById(id); }
    function ce(name) { return document.createElement(name); }
    function re(id) {
        var e = gebi(id);
        e.parentNode.removeChild(e);
    }
    function addNewDG() {
        var max = <?php echo osc_get_preference('max_files', 'digitalgoods'); ?>;
        var num_img = $('input[name="dg_files[]"]').size() + $("a.delete").size();
        if((max!=0 && num_img<max) || max==0) {
            var id = 'p-' + dgIndex++;

            var i = ce('input');
            i.setAttribute('type', 'file');
            i.setAttribute('name', 'dg_files[]');

            var a = ce('a');
            a.style.fontSize = 'x-small';
            a.style.paddingLeft = '10px';
            a.setAttribute('href', '#');
            a.setAttribute('divid', id);
            a.onclick = function() { re(this.getAttribute('divid')); return false; }
            a.appendChild(document.createTextNode('<?php _e('Remove'); ?>'));

            var d = ce('div');
            d.setAttribute('id', id);
            d.setAttribute('style','padding: 4px 0;')

            d.appendChild(i);
            d.appendChild(a);

            gebi('dg_files').appendChild(d);

            $("#"+id+" input:file").uniform();
        } else {
            alert('<?php _e('Sorry, you have reached the maximum number of files per ad'); ?>');
        }
    }

    setInterval("add_file_field()", 250);

    function add_file_field() {
        var count = 0;
        $('input[name="dg_files[]"]').each(function(index) {
            if ( $(this).val() == '' ) {
                count++;
            }
        });
        var max = <?php echo osc_get_preference('max_files', 'digitalgoods'); ?>;
        var num_img = $('input[name="dg_files[]"]').size() + $("a.delete").size();
        if (count == 0 && (max==0 || (max!=0 && num_img<max))) {
            addNewDG();
        }
    }
    
    function delete_dg_file(id, item_id,name, secret) {
        var result = confirm('<?php echo __('This action can\\\'t be undone. Are you sure you want to continue?', 'digitalgoods'); ?>');
        if(result) {
            $.ajax({
                type: "POST",
                <?php if(osc_version()<320) { ?>
                url: '<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&id='+id+'&item='+item_id+'&code='+name+'&secret='+secret,
                <?php } else { ?>
                url: '<?php echo osc_route_ajax_url('digitalgoods-ajax'); ?>&id='+id+'&item='+item_id+'&code='+name+'&secret='+secret,
                <?php }; ?>
                dataType: 'json',
                success: function(data){
                    var class_type = "error";
                    if(data.success) {
                        $("div[name="+name+"]").remove();
                        class_type = "ok";
                    }
                    var flash = $("#flash_js");
                    var message = $('<div>').addClass('pubMessages').addClass(class_type).attr('id', 'FlashMessage').html(data.msg);
                    flash.html(message);
                    $("#FlashMessage").slideDown('slow').delay(3000).slideUp('slow');
                }
            });
        }
    }
</script>