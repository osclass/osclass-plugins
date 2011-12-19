<?php

    /**
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    if( Params::getParam('plugin_action') == 'done' ) {
        osc_set_preference( 'company', Params::getParam('company'), 'jobboard' ) ;
        osc_reset_preferences() ;
    }

    osc_show_flash_message('admin') ; 

?>
<style>
    #settings_form.jobboard-options h3 {
        margin: 5px 5px 10px 5px;
        padding: 5px 0;
        border-bottom: 1px dotted #404040;
    }

    #settings_form.jobboard-options label {
        padding-top: 6px;
        text-align: right;
        float: left;
        width: 140px
    }

    #settings_form.jobboard-options .clear:after {
        clear: both;
    }

    #settings_form.jobboard-options .input {
        margin-left: 150px ;
    }

    #settings_form.jobboard-options .input input[type="text"] {
        width: 300px;
    }
</style>
<div id="settings_form" class="jobboard-options">
    <h3><?php _e( 'Job board options', 'jobboard' ) ; ?></h3>
    <form name="jobboard_form" id="jobboard_form" action="<?php echo osc_admin_base_url( true ) ; ?>" method="GET">
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="jobboard/conf.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <div class="clear">
            <label>Company name</label>
            <div class="input">
                <input type="text" name="company" value="<?php echo get_company_name() ; ?>">
            </div>
        </div>
        <div class="clear">
            <button type="submit">update</button>
        </div>
    </form>
</div>