<?php
    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */


    $ids = Params::getParam('id');
    if($ids!='') {
        if(Params::getParam('paction')=='activate') {
            foreach($ids as $id) {
                ModelLOPD::newInstance()->update(array('b_could_delete' => 1), array('fk_i_user_id' => $id));
            }
        } else if(Params::getParam('paction')=='deactivate') {
            foreach($ids as $id) {
                ModelLOPD::newInstance()->update(array('b_could_delete' => 0), array('fk_i_user_id' => $id));
            }
        }
    }
    $users = User::newInstance()->listAll();
    $last = end($users); $last_id = $last['pk_i_id'];
?>
<script type="text/javascript">
    $(function() {
        sSearchName = "<?php _e('Search'); ?>...";
        oTable = new osc_datatable();
        oTable.fnInit({
            'idTable'       : 'datatables_list'
            ,"sAjaxSource": "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=custom&ajaxfile=../oc-content/plugins/<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>"
            ,'iDisplayLength': '10'
            ,'iColumns'      : '5'
            ,'oLanguage'     : {
                    "sInfo":         "<?php _e('Showing _START_ to _END_ of _TOTAL_ entries') ; ?>"
                    ,"sZeroRecords":  "<?php _e('No matching records found') ; ?>"
                    ,"sInfoFiltered": "(<?php _e('filtered from _MAX_ total entries') ; ?>)"
                    ,"oPaginate": {
                                "sFirst":    "<?php _e('First') ; ?>",
                                "sPrevious": "<?php _e('Previous') ; ?>",
                                "sNext":     "<?php _e('Next') ; ?>",
                                "sLast":     "<?php _e('Last') ; ?>"
                            }
            }
            ,"aoColumns": [
                {"sTitle": "<div style='margin-left: 8px;'><input id='check_all' type='checkbox' /></div>"
                 ,"bSortable": false
                 ,"sClass": "center"
                 ,"sWidth": "10px"
                 ,"bSearchable": false
                 }
                ,{"sTitle": "<?php _e('E-mail'); ?>",
                 "sWidth": "30%"
                 ,"bSortable": true
                }
                ,{"sTitle": "<?php _e('Real name') ?>"
                    ,"bSortable": true
                }
                ,{"sTitle": "<?php _e('Date'); ?>"
                    ,"bSortable": true
                }
                ,{"sTitle": "<?php _e('Campo LOPD', 'lopd'); ?>"
                    ,"bSortable": true
                }
            ]                });
    });

    $('#datatables_list tr').live('mouseover', function(event) {
        $('#datatable_wrapper', this).show();
        $('#datatables_quick_edit', this).show();
    });

    $('#datatables_list tr').live('mouseleave', function(event) {
        $('#datatable_wrapper', this).hide();
        $('#datatables_quick_edit', this).hide();
    });

</script>
<script type="text/javascript" src="<?php echo osc_current_admin_theme_js_url('datatables.post_init.js') ; ?>"></script>
<form id="datatablesForm" action="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin_user.php'); ?>" method="post">
    <div id="TableToolsToolbar">
        <select name="paction" id="action" class="display">
            <option value=""><?php _e('Acciones en masa', 'lopd'); ?></option>
            <option value="activate"><?php _e('Permitir borrar', 'lopd'); ?></option>
            <option value="deactivate"><?php _e('Denegar borrar', 'lopd'); ?></option>
        </select>
        &nbsp;<button id="bulk_apply" class="display"><?php _e('Apply'); ?></button>
    </div>
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="datatables_list"></table>
    <br />
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $('#datatables_list tr').live('mouseover', function(event) {
            $('#datatables_quick_edit', this).show();
        });

        $('#datatables_list tr').live('mouseleave', function(event) {
            $('#datatables_quick_edit', this).hide();
        });
        
        $('#check_all').live('change',
            function(){
                if( $(this).attr('checked') ){
                    $('#'+oTable._idTable+" input").each(function(){
                        $(this).attr('checked','checked');
                    });
                } else {
                    $('#'+oTable._idTable+" input").each(function(){
                        $(this).attr('checked','');
                    });
                }
            }
        );        
    });
</script>