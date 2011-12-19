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
     * Model database for Jobs tables
     * 
     * @package OSClass
     * @subpackage Model
     * @since 1.0
     */
    class Jobboard extends DAO
    {
        /**
         * It references to self object: Jobboard.
         * It is used as a singleton
         * 
         * @access private
         * @since 1.0
         * @var Jobboard
         */
        private static $instance ;

        /**
         * It creates a new Jobboard object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since 1.0
         * @return Jobboard
         */
        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Construct
         */
        function __construct()
        {
            parent::__construct() ;
        }
        
        /**
         * Return table name jobs attributes
         * @return string
         */
        public function getTableJobboard()
        {
            return $this->getTablePrefix() . 't_item_jobboard' ;
        }
        
        /**
         * Return table name jobs attributes description
         * @return string
         */
        public function getTableJobboardDescription()
        {
            return $this->getTablePrefix() . 't_item_jobboard_description' ;
        }
        
        /**
         * Import sql file
         * 
         * @param type $file 
         */
        public function import($file)
        {
            $path = osc_plugin_resource( $file ) ;
            $sql  = file_get_contents( $path ) ;

            if( !$this->dao->importSQL( $sql ) ) {
                throw new Exception( __('Error importing the database structure of the jobboard plugin', 'jobboard') ) ;
            }
        }
        
        /**
         *  Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query( 'DROP TABLE ' . $this->getTableJobboardDescription() ) ;
            $this->dao->query( 'DROP TABLE ' . $this->getTableJobboard() ) ;
        }
        
        /**
         * Get all entries from jobs attributes table
         *
         * @return array
         */
        public function getAllAttributes()
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsAttr());
            $results = $this->dao->get();
            
            return $results->result();
        }
        
        /**
         * Get Jobs attributes given a item id
         *
         * @param int $item_id
         * @return array
         */
        public function getJobsAttrByItemId($itemID)
        {
            $this->dao->select() ;
            $this->dao->from( $this->getTableJobboard() ) ;
            $this->dao->where( 'fk_i_item_id', $itemID ) ;
            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            if( $result->numRows() == 0 ) {
                return array() ;
            }

            return $result->row() ;
        }
        
        /**
         * Get Jobs attributes descriptions given a item id
         *
         * @param int $item_id
         * @return array
         */
        public function getJobsAttrDescriptionsByItemId($item_id)
        {
            $this->dao->select() ;
            $this->dao->from( $this->getTableJobboardDescription() ) ;
            $this->dao->where( 'fk_i_item_id', $item_id ) ;
            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            if( $result->numRows() == 0 ) {
                return array() ;
            }

            return $result->result() ;
        }

        /**
         * Insert Jobs attributes
         *
         * @param int $itemID
         * @param string $positionType
         * @param string $salary
         */
        public function insertJobsAttr($itemID, $positionType, $salary)
        {
            $aSet = array(
                'fk_i_item_id'    => $itemID,
                'e_position_type' => $positionType,
                's_salary'        => $salary,
            ) ;

            $this->dao->insert( $this->getTableJobboard(), $aSet ) ;
        }

        /**
         * Insert Jobs attributes descriptions
         *
         * @param int $itemID
         * @param string $locale
         * @param string $contract
         * @param string $studies
         * @param string $experience
         * @param string $requirements
         */
        public function insertJobsAttrDescription($itemID, $locale, $contract, $studies, $experience, $requirements)
        {
            $aSet = array(
                'fk_i_item_id'     => $itemID,
                'fk_c_locale_code' => $locale,
                's_contract'       => $contract,
                's_studies'        => $studies,
                's_experience'     => $experience,
                's_requirements'   => $requirements,
            ) ;

            $this->dao->insert( $this->getTableJobboardDescription(), $aSet ) ;
        }

        /**
         * Replace Jobs attributes 
         */
        public function replaceJobsAttr($itemID, $positionType, $salary)
        {
            $aSet = array(
                'fk_i_item_id'    => $itemID,
                'e_position_type' => $positionType,
                's_salary'        => $salary,
            ) ;

            $this->dao->replace( $this->getTableJobboard(), $aSet);
        }

        /**
         * Replace Jobs attributes descriptions
         */
        public function replaceJobsAttrDescriptions($itemID, $locale, $contract, $studies, $experience, $requirements)
        {
            $aSet = array(
                'fk_i_item_id'     => $itemID,
                'fk_c_locale_code' => $locale,
                's_contract'       => $contract,
                's_studies'        => $studies,
                's_experience'     => $experience,
                's_requirements'   => $requirements,
            ) ;

            $this->dao->replace( $this->getTableJobboardDescription(), $aSet ) ;
        }
        
        /**
         * Delete entries at jobs attr description table given a locale code
         *
         * @param type $locale 
         */
        public function deleteLocale($locale)
        {
            $this->dao->delete( $this->getTableJobboardDescription(), array('fk_c_locale_code' => $locale) ) ;
        }
        
        /**
         * Delete entries at jobs tables given a item id
         *
         * @param type $locale 
         */
        public function deleteItem($itemID)
        {
            $this->dao->delete( $this->getTableJobboard(), array('fk_i_item_id' => $itemID) ) ;
            $this->dao->delete( $this->getTableJobboardDescription(), array('fk_i_item_id' => $itemID) ) ;
        }
    }

?>