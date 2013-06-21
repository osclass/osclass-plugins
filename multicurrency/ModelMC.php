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

    class ModelMC extends DAO {
        /**
         * It references to self object: ModelLOPD
         * It is used as a singleton
         * 
         * @access private
         * @since unknown
         * @var Currency
         */
        private static $instance;

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
                self::$instance = new self;
            }
            return self::$instance;
        }

        /**
         * Construct
         */
        function __construct() {
            parent::__construct();
            $this->setTableName('t_multicurrency');
            $this->setFields( array('s_from', 's_to', 'f_rate', 'dt_date') );
        }
        
        /**
         * Return table name MC
         * @return string
         */
        public function getTableName() {
            return DB_TABLE_PREFIX.'t_multicurrency';
        }
        
        /**
         * Import sql file
         * @param type $file 
         */
        public function import($file) {
            $path = osc_plugin_resource($file);
            $sql = file_get_contents($path);

            if(! $this->dao->importSQL($sql) ){
                throw new Exception( $this->dao->getErrorLevel().' - '.$this->dao->getErrorDesc() );
            }
        }
        
        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall() {
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTableName()) );
        }

        public function getCurrencies() {
            $this->dao->select('pk_c_code');
            $this->dao->from(sprintf('%st_currency', DB_TABLE_PREFIX));
            $result = $this->dao->get();

            if( $result->numRows == 0 ) {
                return array();
            }

            return $result->result();
        }

        public function getRates($currency) {
            $this->dao->select('*');
            $this->dao->from($this->getTableName());
            $this->dao->where('s_from', $currency);
            $result = $this->dao->get();

            if( $result->numRows == 0 ) {
                return array();
            }

            return $result->result();
        }

        public function replaceCurrency($from, $to, $rate) {
            return $this->dao->replace(
                $this->getTableName(),
                array(
                    's_from' => $from,
                    's_to' => $to,
                    'f_rate' => $rate,
                    'dt_date' => date('Y-m-d H:i:s')
                )
            );
        }

    }

?>