<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
    /*
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

    Rewrite::newInstance()->init();
    $request_uri = Rewrite::newInstance()->get_raw_request_uri();
    $tmp = explode("&redirectto=", $request_uri);
    if(count($tmp)>1) {
        @ob_get_clean();
        osc_redirect_to($tmp[1]);
    }

    _e('There were some error', 'lopd');

?>