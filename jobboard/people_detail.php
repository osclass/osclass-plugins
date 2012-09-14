<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $applicantId = Params::getParam("people");

    $mjb = ModelJB::newInstance();
    $people = $mjb->getApplicant($applicantId);
    
    $file = $mjb->getCVFromApplicant($applicantId);
    ModelJB::newInstance()->changeSecret($file['pk_i_id']);
    $file = $mjb->getCVFromApplicant($applicantId);
    $notes = $mjb->getNotesFromApplicant($applicantId);
    
    $job = Item::newInstance()->findByPrimaryKey($people['fk_i_item_id']);

    if($people['b_read']==0) {
        ModelJB::newInstance()->changeRead($applicantId);
    }
?>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.MetaData.js" type="text/javascript" language="javascript"></script>
<link href="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.css" type="text/css" rel="stylesheet" />
<link href="<?php echo osc_plugin_url(__FILE__); ?>css/style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
    function setIcon(){
        $('.status-icon').css({
                backgroundPosition: $("#applicant_status").val() * 60
        });
    }

    $(document).ready(function() {
        $("#dialog-note-delete").dialog({
            autoOpen: false,
            modal: true
        });

        $("#dialog-note-edit").dialog({
            autoOpen: false,
            modal: true
        });

        $("#applicant_status").change(function(){
            $.getJSON(
                "<?php echo osc_admin_ajax_hook_url('applicant_status'); ?>",
                {"applicantId" : <?php echo $applicantId; ?>, "status" : $("#applicant_status option:selected").attr("value")},
                function(data){}
            );
            setIcon();
        });
        setIcon();
        $('.auto-star').rating({
            callback: function(value, link, input){
                var data = value.split("_");
                $.getJSON(
                    "<?php echo osc_admin_ajax_hook_url('jobboard_rating'); ?>",
                    {"applicantId" : data[0], "rating" : data[1]},
                    function(data){}
                );
            }
        });
    });

    function delete_note(note_id) {
        $("#note_id").attr("value", note_id);
        $("#dialog-note-delete").dialog('open');
    }

    function edit_note(note_id, note_text) {
        $("#note_edit_id").attr("value", note_id);
        $("#note_edit_text").attr("value", note_text);
        $("#note_action").attr("value", "edit_note");
        $("#dialog-note-edit").dialog('open');
    }

    function add_note() {
        $("#note_edit_id").attr("value", "");
        $("#note_edit_text").attr("value", "");
        $("#note_action").attr("value", "add_note");
        $("#dialog-note-edit").dialog('open');
    }
</script>
<div id="applicant-detail">
    <div class="applicant-header">
        <h2 class="render-title"><?php echo @$people['s_name']; ?></h2>
    </div>
    <div class="applicant-cover-letter">
        <p><?php echo nl2br($people['s_cover_letter']); ?></p>
    </div>
    <div id="left-side">
        <div id="dashboard_notes">
            <?php if(empty($file)) {
                _e("This applicant has not sumitted any resume", "jobboard");
            } else { ?>
            <div id="applicant-resume">
            <iframe src="http://docs.google.com/viewer?embedded=true&url=<?php echo str_replace("localhost", "95.62.72.123", osc_plugin_url(__FILE__));?>download.php?data=<?php echo $applicantId; ?>|<?php echo $file['s_secret']; ?>"></iframe>
            </div>
            <?php } ?>
        </div>
        
    </div>
    <div id="right-side">
        <div class="download-resume">
            <a href="<?php echo osc_plugin_url(__FILE__); ?>download.php?data=<?php echo $applicantId; ?>|<?php echo $file['s_secret']; ?>" class="btn"><?php _e('Download resume', 'jobboard'); ?></a>
            <div style="clear:both;"></div>
        </div>
        <div class="well ui-rounded-corners applicant-box">
            <div class="status-icon">
            </div>
            <div class="rater big-star">
                <?php for($k=1;$k<=5;$k++) {
                    echo '<input name="star'.$people['pk_i_id'].'" type="radio" class="auto-star required" value="'.$people['pk_i_id'].'_'.$k.'" title="'.$k.'" '.($k==$people['i_rating']?'checked="checked"':'').'/>';
                } ?>
            </div>
            <select id="applicant_status" name="applicant_status" class="select-box-medium">
                <?php
                $st_array = jobboard_status();
                foreach($st_array as $k => $v) {
                    echo '<option value="'.$k.'" '.($k==$people['i_status']?'selected="selected"':'').'>'.$v.'</option>';
                }
                ?>
            </select>
            <div class="applied-for">
                <?php _e("Applied for:", "jobboard"); ?><br />
                <a href="<?php echo osc_item_admin_edit_url($job['fk_i_item_id']); ?>"><?php echo $job['s_title']; ?></a>
            </div>
        </div>
        <h3 class="sidebar-title"><?php _e("Notes", "jobboard"); ?> <span class="note_plus"><a id="add_note" href="javascript:add_note();"><?php _e("Add note", "jobboard"); ?></a></span></h3>
        <div id="dashboard_notes">
            <div id="nots_table_div">
                <?php if(count($notes)>0) { ?>
                    <?php foreach($notes as $note) { ?>
                        <div class="note well ui-rounded-corners">
                            <div class="note-actions">
                                <a class="delete_note" href="javascript:delete_note(<?php echo $note['pk_i_id']; ?>);" ><?php _e("DELETE", "jobboard"); ?></a>
                                <a class="edit_note" href="javascript:edit_note(<?php echo $note['pk_i_id']; ?>, '<?php echo osc_esc_js($note['s_text']); ?>');" ><?php _e("EDIT", "jobboard"); ?></a>
                            </div>
                            <div class="note-date">
                                <b><?php echo date('d', strtotime($note['dt_date'])); ?></b>
                                <span><?php echo date('M', strtotime($note['dt_date'])); ?><br>
                                <?php echo date('Y', strtotime($note['dt_date'])); ?></span>
                            </div>
                            <div class="clear"></div>
                            <p><?php echo $note['s_text']; ?></p>
                        </div>
                    <?php }; ?>
                <?php } else { ?>
                    <div class="well ui-rounded-corners">
                    <?php _e("No notes have been added to this applicant", "jobboard"); ?>
                    </div>
                <?php }; ?>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div style="clear:both;"></div>
</div>
<form id="dialog-note-delete" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__('Delete note', 'jobboard')); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>actions.php" />
    <input type="hidden" name="applicantId" value="<?php echo $applicantId; ?>" />
    <input type="hidden" name="paction" value="delete_note" />
    <input type="hidden" id="note_id" name="id" value="" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Are you sure you want to delete this note?', 'jobboard'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-note-delete').dialog('close', 'jobboard');"><?php _e('Cancel', 'jobboard'); ?></a>
            <input id="note-delete-submit" type="submit" value="<?php echo osc_esc_html( __('Delete', 'jobboard') ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>
<form id="dialog-note-edit" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__('Note', 'jobboard')); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>actions.php" />
    <input type="hidden" name="applicantId" value="<?php echo $applicantId; ?>" />
    <input type="hidden" id="note_edit_id" name="id" value="" />
    <input type="hidden" id="note_action" name="paction" value="add_note" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Note', 'jobboard'); ?>
        </div>
        <div class="form-row">
            <textarea id="note_edit_text" name="note_text" style="margin: 2px 0px; width: 255px; height: 90px; "></textarea>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-note-edit').dialog('close', 'jobboard');"><?php _e('Cancel', 'jobboard'); ?></a>
            <input id="note-edit-submit" type="submit" value="<?php echo osc_esc_html( __('Ok', 'jobboard') ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>