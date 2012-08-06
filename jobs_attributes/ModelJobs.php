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
     * @since 3.0
     */
    class ModelJobs extends DAO
    {
        /**
         * It references to self object: ModelJobs.
         * It is used as a singleton
         * 
         * @access private
         * @since 3.0
         * @var ModelJobs
         */
        private static $instance ;

        /**
         * It creates a new ModelJobs object class ir if it has been created
         * before, it return the previous object
         * 
         * @access public
         * @since 3.0
         * @return ModelJobs
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
         * Return table name jobs attributes
         * @return string
         */
        public function getTable_JobsAttr()
        {
            return DB_TABLE_PREFIX.'t_item_job_attr' ;
        }
        
        /**
         * Return table name jobs attributes description
         * @return string
         */
        public function getTable_JobsAttrDescription()
        {
            return DB_TABLE_PREFIX.'t_item_job_description_attr' ;
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
                throw new Exception( "Error importSQL::ModelJobs<br>".$file ) ;
            }
        }
        
        /**
         *  Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query('DROP TABLE '. $this->getTable_JobsAttrDescription());
            $this->dao->query('DROP TABLE '. $this->getTable_JobsAttr());
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
            
            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }
            return $result->result();
        }
        
        /**
         * Get Jobs attributes given a item id
         *
         * @param int $item_id
         * @return array
         */
        public function getJobsAttrByItemId($item_id)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsAttr());
            $this->dao->where('fk_i_item_id', $item_id);
            
            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }
            return $result->row();
        }
        
        /**
         * Get Jobs attributes descriptions given a item id
         *
         * @param int $item_id
         * @return array
         */
        public function getJobsAttrDescriptionsByItemId($item_id)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsAttrDescription());
            $this->dao->where('fk_i_item_id', $item_id);
            
            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }
            
            return $result->result();
        }
        
        /**
         * Insert Jobs attributes
         *
         * @param int $item_id
         * @param string $relation
         * @param string $company_name
         * @param string $position_type
         * @param int $salaryMin
         * @param int $salaryMax
         * @param int $salaryPeriod
         * @param int $salaryMinHour
         * @param int $salaryMaxHour 
         */
        public function insertJobsAttr($item_id, $relation, $company_name, $position_type, $salaryText)
        {
            $aSet = array(
                'fk_i_item_id'      => $item_id,
                'e_relation'        => $relation,
                's_company_name'    => $company_name,
                'e_position_type'   => $position_type,
                's_salary_text'     => $salaryText
            );
            
            return $this->dao->insert($this->getTable_JobsAttr(), $aSet);
        }
        
        /**
         * Insert Jobs attributes descriptions
         *
         * @param int $item_id
         * @param string $locale
         * @param string $desiredExp
         * @param string $studies
         * @param string $minRequirements
         * @param string $desiredRequirements
         * @param string $contract
         * @param string $companyDescription 
         */
        public function insertJobsAttrDescription($item_id, $locale, $desiredExp, $studies, $minRequirements, $desiredRequirements, $contract, $companyDescription)
        {
            $aSet = array(
                'fk_i_item_id'              => $item_id,
                'fk_c_locale_code'          => $locale,
                's_desired_exp'             => $desiredExp,
                's_studies'                 => $studies,
                's_minimum_requirements'    => $minRequirements,
                's_desired_requirements'    => $desiredRequirements,
                's_contract'                => $contract,
                's_company_description'     => $companyDescription
            );
            
            return $this->dao->insert($this->getTable_JobsAttrDescription(), $aSet);
        }
        
        /**
         * Replace salary_min_hour, salary_max_hour given a item id
         *
         * @param type $item_id
         * @param type $salaryHourmin
         * @param type $salaryHourMax 
         */
        public function replaceJobsSalaryAttr($item_id, $salaryText)
        {
            $aSet = array(
                'fk_i_item_id'      => $item_id,
                's_salary_text'     => $salaryText
            );
            return $this->dao->replace($this->getTable_JobsAttr(), $aSet);
        }
        
        /**
         * Replace Jobs attributes 
         */
        public function replaceJobsAttr($item_id, $relation, $company_name, $position_type, $salaryText)
        {
            $aSet = array(
                'fk_i_item_id'      => $item_id,
                'e_relation'        => $relation,
                's_company_name'    => $company_name,
                'e_position_type'   => $position_type,
                's_salary_text'     => $salaryText
            );
            return $this->dao->replace( $this->getTable_JobsAttr(), $aSet);
        }
        
        /**
         * Replace Jobs attributes descriptions
         */
        public function replaceJobsAttrDescriptions($item_id, $locale, $desiredExp, $studies, $minRequirements, $desiredRequirements, $contract, $companyDescription)
        {
            $aSet = array(
                'fk_i_item_id'              => $item_id,
                'fk_c_locale_code'          => $locale,
                's_desired_exp'             => $desiredExp,
                's_studies'                 => $studies,
                's_minimum_requirements'    => $minRequirements,
                's_desired_requirements'    => $desiredRequirements,
                's_contract'                => $contract,
                's_company_description'     => $companyDescription
            );
            return $this->dao->replace($this->getTable_JobsAttrDescription(), $aSet);
        }
        
        /**
         * Delete entries at jobs attr description table given a locale code
         *
         * @param type $locale 
         */
        public function deleteLocale($locale)
        {
            return $this->dao->delete($this->getTable_JobsAttrDescription(), array('fk_c_locale_code' => $locale) );
        }
        
        /**
         * Delete entries at jobs tables given a item id
         *
         * @param type $locale 
         */
        public function deleteItem($item_id)
        {
            $this->dao->delete($this->getTable_JobsAttr(), array('fk_i_item_id' => $item_id) );
            return $this->dao->delete($this->getTable_JobsAttrDescription(), array('fk_i_item_id' => $item_id) );
        }
        
        
        /**
         * Upgrades the database to be compatible with version 310 
         */
        public function upgradeTo310()
        {
            
            $this->dao->query("ALTER TABLE ".$this->getTable_JobsAttr()." ADD COLUMN s_salary_text TEXT NULL DEFAULT '' ");
            
            $jobs = $this->getAllAttributes();
            foreach($jobs as $job) {
                $tmp = array();
                if(isset($job['i_salary_min']) && $job['i_salary_min']!='' && $job['i_salary_min']!=0) {
                    $tmp[] = $job['i_salary_min'];
                }
                if(isset($job['i_salary_max']) && $job['i_salary_max']!='' && $job['i_salary_max']!=0) {
                    $tmp[] = $job['i_salary_max'];
                }
                
                $salary = implode(" - ", $tmp);
                if($job['e_salary_period']=='HOUR') {
                    $salary .= " ".__("Hour", "jobs_attributes");
                } else if($job['e_salary_period']=='DAY') {
                    $salary .= " ".__("Day", "jobs_attributes");
                } else if($job['e_salary_period']=='WEEK') {
                    $salary .= " ".__("Week", "jobs_attributes");
                } else if($job['e_salary_period']=='MONTH') {
                    $salary .= " ".__("Month", "jobs_attributes");
                } else {
                    $salary .= " ".__("Year", "jobs_attributes");
                } 
                    
                    
                $this->dao->update($this->getTable_JobsAttr(), array('s_salary_text' => $salary), array('fk_i_item_id' => $job['fk_i_item_id']));
            }

            $this->import('jobs_attributes/alterTable.sql');
            
            
        }
    }
?>