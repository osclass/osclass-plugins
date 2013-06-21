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

    class ModelLOPD extends DAO {
        /**
         * It references to self object: ModelLOPD
         * It is used as a singleton
         * 
         * @access private
         * @since unknown
         * @var Currency
         */
        private static $instance ;

        /**
         * It creates a new ModelLOPD object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since unknown
         * @return Currency
         */
        public static function newInstance() {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Construct
         */
        function __construct() {
            parent::__construct();
            $this->setTableName('t_lopd') ;
            $this->setPrimaryKey('fk_i_user_id') ;
            $this->setFields( array('fk_i_user_id', 'dt_date', 's_ip', 'b_could_delete') ) ;
        }
        
        /**
         * Return table name LOPD 
         * @return string
         */
        public function getTable() {
            return DB_TABLE_PREFIX.'t_lopd';
        }
        
        /**
         * Import sql file
         * @param type $file 
         */
        public function import($file) {
            $path = osc_plugin_resource($file) ;
            $sql = file_get_contents($path);

            if(! $this->dao->importSQL($sql) ){
                throw new Exception( $this->dao->getErrorLevel().' - '.$this->dao->getErrorDesc() ) ;
            }
        }
        
        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall() {
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable()) ) ;
        }
        
        public function hasAccepted($userId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable()) ;
            $this->dao->where('fk_i_user_id', $userId);
            $result = $this->dao->get() ;

            if( $result->numRows == 0 ) {
                return false ;
            }

            return $result->row()>0?true:false;
        }
        
        public function acceptLOPD($userId) {
            $this->dao->insert(
                $this->getTable(),
                array(
                    'fk_i_user_id' => $userId,
                    'dt_date' => date('Y-m-d H:i:s'),
                    's_ip' => @$_SERVER['REMOTE_ADDR']
                )
            );
        }
        
        public function couldDelete($userId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable()) ;
            $this->dao->where('fk_i_user_id', $userId);
            $result = $this->dao->get() ;

            if( $result->numRows == 0 ) {
                return false ;
            }

            $row = $result->row();
            return $row['b_could_delete']==1?true:false;
        }

/**
         * Return list of users
         * 
         * @access public
         * @since unknown
         * @param int $start
         * @param int $end
         * @param string $order_column
         * @param string $order_direction
         * @return array
         */
        public function search($start = 0, $end = 10, $order_column = 'pk_i_id', $order_direction = 'DESC')
        {
            
            // SET data, so we always return a valid object
            $users = array();
            $users['rows'] = 0;
            $users['total_results'] = 0;
            $users['users'] = array();
            
            $sql = sprintf("SELECT SQL_CALC_FOUND_ROWS u.* FROM %st_user u, %st_lopd l WHERE u.pk_i_id = l.fk_i_user_id ORDER BY %s %s LIMIT %s, %s", DB_TABLE_PREFIX, DB_TABLE_PREFIX, $order_column, $order_direction, $start, $end);
            $result = $this->dao->query($sql) ;
            
            if(!$result) {
                return $users;
            }
            
            $datatmp  = $this->dao->query('SELECT FOUND_ROWS() as total');
            $data = $datatmp->row();
            if(isset($data['total'])) {
                $users['total_results'] = $data['total'];
            }
            
            $users['users'] = $result->result();
            $users['rows'] = $result->numRows();
            
            
            return $users;
        }        
    }

?>