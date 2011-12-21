<?php
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

    /**
     * Description of UserAgentClass
     */
    class UserAgent {
        
        private $user_agent       = null;
        private $mobile_platforms = array();
        
        private $is_mobile        = false;
        
        public function __construct( )
        {
            $this->_load_data( );
            
            if ( !is_null($this->user_agent) ) {
                $this->_set_platform( ) ;
            }
        }
        
        /* Private */
        private function _load_data( )
        {
            include dirname( osc_plugin_path(__FILE__) ) . '/user_agents.php' ;
            
            if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
                $this->user_agent = trim($_SERVER['HTTP_USER_AGENT']) ;
            }
            
            if ( !is_null($this->user_agent) ) {
                $this->mobile_platforms = $mobiles ;
                unset($mobiles) ;
            }
        }
        
        private function _set_platform( )
        {
            if (is_array($this->mobile_platforms) AND count($this->mobile_platforms) > 0) {
                foreach ($this->mobile_platforms as $key => $val) {
                    if (false !== (strpos(strtolower($this->user_agent), $key))) {
                        $this->is_mobile = true ;
                    }
                }
            }
        }
        
        /* Public */
        public function is_mobile( )
        {
            return $this->is_mobile ;
        }
    }

?>