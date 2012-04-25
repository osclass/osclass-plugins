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
     * Model database for Products tables
     * 
     * @package OSClass
     * @subpackage Model
     * @since 3.0
     */
    class ModelProducts extends DAO
    {
        /**
         * It references to self object: ModelProducts.
         * It is used as a singleton
         * 
         * @access private
         * @since 3.0
         * @var ModelProducts
         */
        private static $instance ;

        /**
         * It creates a new ModelProducts object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since 3.0
         * @return ModelProducts
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
            parent::__construct();
        }
        
        /**
         * Return table name products attributes
         * @return string
         */
        public function getTable_ProductsAttr()
        {
            return DB_TABLE_PREFIX.'t_item_products_attr' ;
        }
        
        /**
         * Import sql file
         * @param type $file 
         */
        public function import($file)
        {
            $path = osc_plugin_resource($file) ;
            $sql = file_get_contents($path);

            if(! $this->dao->importSQL($sql) ){
                throw new Exception( "Error importSQL::ModelProducts<br>".$file ) ;
            }
        }
        
        /**
         *  Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query('DROP TABLE '. $this->getTable_ProductsAttr());
        }
        
        /**
         * Get products attributes given a item id 
         *
         * @param int $item_id
         * @return array
         */
        public function getAttrByItemId( $item_id )
        {
            $this->dao->select();
            $this->dao->from( $this->getTable_ProductsAttr() );
            $this->dao->where('fk_i_item_id', $item_id );
            
            $result = $this->dao->get();
            
            if( !$result ) {
                return array();
            }
            
            return $result->row();
        }
        
        /**
         * Insert products attributes
         *
         * @param int $item_id
         * @param string $make
         * @param string $model 
         */
        public function insertAttr( $item_id, $make, $model)
        {
            $aSet = array(
                's_make'  => $make,
                's_model' => $model,
                'fk_i_item_id' => $item_id
                );
            
            return $this->dao->insert( $this->getTable_ProductsAttr(), $aSet);
        }
        
        /**
         * Update products attributes
         *
         * @param string $item_id
         * @param string $make
         * @param string $model 
         */
        public function updateAttr($item_id, $make, $model)
        {
            $aSet = array(
                's_make'  => $make,
                's_model' => $model
            );
            
            $aWhere = array( 'fk_i_item_id' => $item_id);
            
            return $this->_update($this->getTable_ProductsAttr(), $aSet, $aWhere);
        }
        
         /**
         * Delete house attributes given a item id
         * @param type $item_id 
         */
        public function deleteItem($item_id)
        {
            return $this->dao->delete($this->getTable_ProductsAttr(), array('fk_i_item_id' => $item_id) ) ;

        }
        
        // update
        function _update($table, $values, $where)
        {
            $this->dao->from($table) ;
            $this->dao->set($values) ;
            $this->dao->where($where) ;
            return $this->dao->update() ;
        }
    }
?>