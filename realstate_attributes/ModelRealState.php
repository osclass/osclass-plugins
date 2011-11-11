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
     * Model database for RealState tables
     * 
     * @package OSClass
     * @subpackage Model
     * @since unknown
     */
    class ModelRealState extends DAO
    {
        /**
         * It references to self object: ModelRealState.
         * It is used as a singleton
         * 
         * @access private
         * @since unknown
         * @var Currency
         */
        private static $instance ;

        /**
         * It creates a new ModelRealState object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since unknown
         * @return Currency
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
         * Return table name house attributes
         * @return string
         */
        public function getTable_HouseAttr()
        {
            return DB_TABLE_PREFIX.'t_item_house_attr';
        }
        
        /**
         * Return table name house description attributes
         * @return string
         */
        public function getTable_HouseAttrDesc()
        {
            return DB_TABLE_PREFIX.'t_item_house_description_attr';
        }
        
        /**
         * Return table name house property type attributes
         * @return string
         */
        public function getTable_HousePropertyType()
        {
            return DB_TABLE_PREFIX.'t_item_house_property_type_attr';
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
                throw new Exception( $this->dao->getErrorLevel().' - '.$this->dao->getErrorDesc() ) ;
            }
        }
        
        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_HouseAttr()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_HouseAttrDesc()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_HousePropertyType()) ) ;
            
            if( $error_num > 0 ) {
                throw new Exception($this->dao->getErrorLevel().' - '.$this->dao->getErrorDesc());
            }
        }
        
        /**
         * Insert house attributes
         * @param array $array 
         */
        public function insertAttr($array)
        {
            $aSet = array(
                'fk_i_item_id'      => $array['itemId'],
                's_square_meters'   => $array['squareMeters'],
                'i_num_rooms'       => $array['numRooms'],
                'i_num_bathrooms'   => $array['numBathrooms'],
                'e_type'            => $array['property_type'],
                'fk_i_property_type_id' => $array['p_type'],
                'e_status'          => $array['status'],
                'i_num_floors'      => $array['numFloors'],
                'i_num_garages'     => $array['numGarages'],
                'b_heating'         => $array['heating'],
                'b_air_condition'   => $array['airCondition'],
                'b_elevator'        => $array['elevator'],
                'b_terrace'         => $array['terrace'],
                'b_parking'         => $array['parking'],
                'b_furnished'       => $array['furnished'],
                'b_new'             => $array['new'],
                'b_by_owner'        => $array['by_owner'],
                's_condition'       => $array['condition'],
                'i_year'            => $array['year'],
                's_agency'          => $array['agency'],
                'i_floor_number'    => $array['floorNumber'],
                'i_plot_area'       => $array['squareMetersTotal']
                );
            $this->dao->insert( $this->getTable_HouseAttr(), $aSet) ;
        }
        
        public function replaceAttr($array)
        {
            $aSet = array(
                'fk_i_item_id'      => $array['itemId'],
                's_square_meters'   => $array['squareMeters'],
                'i_num_rooms'       => $array['numRooms'],
                'i_num_bathrooms'   => $array['numBathrooms'],
                'e_type'            => $array['property_type'],
                'fk_i_property_type_id' => $array['p_type'],
                'e_status'          => $array['status'],
                'i_num_floors'      => $array['numFloors'],
                'i_num_garages'     => $array['numGarages'],
                'b_heating'         => $array['heating'],
                'b_air_condition'   => $array['airCondition'],
                'b_elevator'        => $array['elevator'],
                'b_terrace'         => $array['terrace'],
                'b_parking'         => $array['parking'],
                'b_furnished'       => $array['furnished'],
                'b_new'             => $array['new'],
                'b_by_owner'        => $array['by_owner'],
                's_condition'       => $array['condition'],
                'i_year'            => $array['year'],
                's_agency'          => $array['agency'],
                'i_floor_number'    => $array['floorNumber'],
                'i_plot_area'       => $array['squareMetersTotal']
                );
            $this->dao->replace( $this->getTable_HouseAttr(), $aSet) ;
        } 
       
        /**
         * Get property types and return formated array
         * 
         * <code>
         *  $p_type[$d['fk_c_locale_code']][$d['pk_i_id']] = $d['s_name'];
         * </code>
         * 
         * @param bool $format if true, convert to array[locale][propertyId] = name
         * @return array formated array
         */
        public function getPropertyTypes($format = true)
        {
            $p_type = array();
            $results = $this->dao->query(sprintf('SELECT * FROM %s', $this->getTable_HousePropertyType()));
            $data = $results->result() ;
            
            if(!$format) {
                return $data;
            }  
            
            foreach ($data as $d) {
                $p_type[$d['fk_c_locale_code']][$d['pk_i_id']] = $d['s_name'];
            }
            return $p_type;
        }
        
        /**
         * Get house attributes given a item id
         * @param int $item_id
         * @return array 
         */
        public function getAttributes($item_id)
        {
            $types  = array();
            $detail = array();
            
            $result = $this->dao->query( sprintf("SELECT * FROM %s WHERE fk_i_item_id = %d", $this->getTable_HouseAttr(), $item_id) ) ;
            $detail = $result->row() ;
            
            $detail['locale'] = array();
            
            $result = $this->dao->query( sprintf('SELECT * FROM %s WHERE fk_i_item_id = %d', $this->getTable_HouseAttrDesc(), $item_id) ) ;
            $descriptions = $result->result() ;
            
            foreach ($descriptions as $desc) {
                $detail['locale'][$desc['fk_c_locale_code']] = $desc;
            }
            if(isset($detail['fk_i_property_type_id'])) {
                 $result = $this->dao->query( sprintf('SELECT * FROM %s WHERE pk_i_id = %d', $this->getTable_HousePropertyType(), $detail['fk_i_property_type_id']) );
                 $types = $result->result();
            }
            
            foreach ($types as $type) {
                $detail['locale'][$type['fk_c_locale_code']]['s_name'] = $type['s_name'];
            }
            return $detail;
        }
        
        /**
         * Return last id inserted into house property type table
         * 
         * @return int 
         */
        public function getLastPropertyTypeId()
        {
            $this->dao->select('pk_i_id');
            $this->dao->from($this->getTable_HousePropertyType()) ;
            $this->dao->orderBy('pk_i_id', 'DESC') ;
            $this->dao->limit(1) ;
            
            $result = $this->dao->get() ;
            $aux = $result->row();
            return $aux['pk_i_id']; 
        }
        
        /**
         * Insert house description attributes given a item id
         *
         * @param int $item_id
         * @param string $locale
         * @param string $transport
         * @param string $zone 
         */
        public function insertDescriptions($item_id, $locale, $transport, $zone)
        {
            $aSet = array();
            $aSet['fk_i_item_id']       = $item_id;
            $aSet['fk_c_locale_code']   = $locale;
            $aSet['s_transport']        = $transport;
            $aSet['s_zone']             = $zone ;
          
            $this->dao->insert( $this->getTable_HouseAttrDesc(), $aSet);
        }
        
        /**
         * Replace house description attributes given a item id
         *
         * @param int $item_id
         * @param string $locale
         * @param string $transport
         * @param string $zone 
         */
        public function replaceDescriptions($item_id, $locale, $transport, $zone)
        {
            $aSet = array();
            $aSet['fk_i_item_id']       = $item_id;
            $aSet['fk_c_locale_code']   = $locale;
            $aSet['s_transport']        = $transport;
            $aSet['s_zone']             = $zone ;
            
            $this->dao->replace( $this->getTable_HouseAttrDesc(), $aSet) ;
        }
        
        /**
         * Insert a property type 
         *
         * @param int $id
         * @param string $locale
         * @param string $name 
         */
        public function insertPropertyType($id, $locale, $name)
        {
            $aSet = array(
                'pk_i_id'           => $id,
                'fk_c_locale_code'  => $locale,
                's_name'            => $name
            );
            $this->dao->insert( $this->getTable_HousePropertyType(), $aSet) ; 
        }
        
        /**
         * Replace a property type attributes
         *
         * @param int $id
         * @param string $locale
         * @param string $name 
         */
        public function replacePropertyType($id, $locale, $name)
        {
            $aSet = array(
                'pk_i_id'           => $id,
                'fk_c_locale_code'  => $locale,
                's_name'            => $name
            );
            $this->dao->replace( $this->getTable_HousePropertyType(), $aSet) ;
        }
        
        /**
         * Delete house property attributes given a property id
         * @param int $id 
         */
        public function deletePropertyType($id)
        {
            $this->dao->query( sprintf('DELETE FROM %s WHERE pk_i_id = %d', $this->getTable_HousePropertyType(), $id) ) ;
        }
        
        /**
         * Delete house description attributes, and house property type attributes given a locale.
         * 
         * @param type $locale 
         */
        public function deleteLocale( $locale )
        {
            $this->dao->query( "DELETE FROM ".$this->getTable_HouseAttrDesc()." WHERE fk_c_locale_code = '" . $locale . "'") ;
            $this->dao->query( "DELETE FROM ".$this->getTable_HousePropertyType()." WHERE fk_c_locale_code = '" . $locale . "'") ;
        }
        
        /**
         * Delete house attributes given a item id
         * @param type $item_id 
         */
        public function deleteItem($item_id)
        {
            $this->dao->query("DELETE FROM ".$this->getTable_HouseAttr()." WHERE fk_i_item_id = '" . $item_id . "'") ;
            $this->dao->query("DELETE FROM ".$this->getTable_HouseAttrDesc()." WHERE fk_i_item_id = '" . $item_id . "'") ;
        }
    }

?>