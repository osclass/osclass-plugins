<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.') ;

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
     * ModelLogJB DAO
     */
    class ModelLogJB extends Log
    {
        /**
         *
         * @var type
         */
        private static $instance ;

        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        public function confirmDelete($oSection, $oAction, $oId, $dSection) {
            return $this->dao->update($this->getTableName(), array('s_section' => $dSection), array('s_section' => $oSection, 's_action' => $oAction, 'fk_i_id' => $oId) );
        }

        public function getActivity($limit = 10) {
            $this->dao->select();
            $this->dao->from($this->getTableName());
            $this->dao->where('s_section', 'jobboard');
            $this->dao->orderBy('dt_date', 'DESC');
            $this->dao->limit(0, $limit);
            $result = $this->dao->get();

            if($result===false) {
                return array();
            }

            return $result->result();
        }
    }
?>