<?php

    /**
     * Model database for Jobs tables
     *
     * @package OSClass
     * @subpackage Model
     * @since 3.0
     */
    class ModelJB extends DAO
    {
        /**
         * It references to self object: ModelJB.
         * It is used as a singleton
         *
         * @access private
         * @since 3.0
         * @var ModelJB
         */
        private static $instance ;

        /**
         * It creates a new ModelJB object class ir if it has been created
         * before, it return the previous object
         *
         * @access public
         * @since 3.0
         * @return ModelJB
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
         * Return table name jobs applicants
         * @return string
         */
        public function getTable_JobsApplicants()
        {
            return DB_TABLE_PREFIX.'t_item_job_applicant' ;
        }

        /**
         * Return table name jobs files
         * @return string
         */
        public function getTable_JobsFiles()
        {
            return DB_TABLE_PREFIX.'t_item_job_file' ;
        }

        /**
         * Return table name jobs notes
         * @return string
         */
        public function getTable_JobsNotes()
        {
            return DB_TABLE_PREFIX.'t_item_job_note' ;
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
                throw new Exception( "Error importSQL::ModelJB<br>".$file ) ;
            }
        }

        /**
         *  Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query('DROP TABLE '. $this->getTable_JobsNotes());
            $this->dao->query('DROP TABLE '. $this->getTable_JobsFiles());
            $this->dao->query('DROP TABLE '. $this->getTable_JobsApplicants());
            $this->dao->query('DROP TABLE '. $this->getTable_JobsAttrDescription());
            $this->dao->query('DROP TABLE '. $this->getTable_JobsAttr());
            //$this->dao->query('DROP TABLE '. ModelKQ::newInstance()->getTable_KillerForm());

            $page = Page::newInstance()->findByInternalName('email_resumes_jobboard');
            if(isset($page['pk_i_id'])) {
                $res = Page::newInstance()->deleteByPrimaryKey($page['pk_i_id']);
            }

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
         * @param string $position_type
         * @param int $salaryText
         * @param int $numPositions
         */
        public function insertJobsAttr($item_id, $relation, $position_type, $salaryText, $numPositions, $killer_questions_form)
        {
            $aSet = array(
                'fk_i_item_id'          => $item_id,
                'e_position_type'       => $position_type,
                's_salary_text'         => $salaryText,
                'i_num_positions'       => $numPositions
            );
            if($killer_questions_form!='') {
                $aSet['fk_i_killer_form_id'] = $killer_questions_form;
            }

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
         */
        public function insertJobsAttrDescription($item_id, $locale, $desiredExp, $studies, $minRequirements, $desiredRequirements, $contract)
        {
            $aSet = array(
                'fk_i_item_id'              => $item_id,
                'fk_c_locale_code'          => $locale,
                's_desired_exp'             => $desiredExp,
                's_studies'                 => $studies,
                's_minimum_requirements'    => $minRequirements,
                's_desired_requirements'    => $desiredRequirements,
                's_contract'                => $contract
            );

            return $this->dao->insert($this->getTable_JobsAttrDescription(), $aSet);
        }

        /**
         * Replace s_salary_text given a item id
         *
         * @param type $item_id
         * @param type $salaryText
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
        public function replaceJobsAttr($item_id, $relation, $position_type, $salaryText, $numPositions, $killer_questions_form)
        {
            $aSet = array(
                'fk_i_item_id'          => $item_id,
                'e_position_type'       => $position_type,
                's_salary_text'         => $salaryText,
                    'i_num_positions'   => $numPositions
            );
            if($killer_questions_form!='') {
                $aSet['fk_i_killer_form_id'] = $killer_questions_form;
            }
            return $this->dao->replace( $this->getTable_JobsAttr(), $aSet);
        }

        /**
         * Replace Jobs attributes descriptions
         */
        public function replaceJobsAttrDescriptions($item_id, $locale, $desiredExp, $studies, $minRequirements, $desiredRequirements, $contract)
        {
            $aSet = array(
                'fk_i_item_id'              => $item_id,
                'fk_c_locale_code'          => $locale,
                's_desired_exp'             => $desiredExp,
                's_studies'                 => $studies,
                's_minimum_requirements'    => $minRequirements,
                's_desired_requirements'    => $desiredRequirements,
                's_contract'                => $contract
            );
            return $this->dao->replace($this->getTable_JobsAttrDescription(), $aSet);
        }

        /**
         * Insert files attached to an applicant
         *
         * @param $applicantId
         * @param $fileName
         * @return boolean
         */
        public function insertFile($applicantId, $fileName)
        {
            $secret = osc_genRandomPassword(12);
            return $this->dao->insert(
                    $this->getTable_JobsFiles()
                    ,array(
                        'fk_i_applicant_id' => $applicantId
                        ,'dt_date' => date("Y-m-d H:i:s")
                        ,'dt_secret_date' => date("Y-m-d H:i:s")
                        ,'s_name' => $fileName
                        ,'s_secret' => $secret
                    ));
        }

        /**
         * Insert an applicant
         *
         * @param $itemId
         * @param $name
         * @param $email
         * @param $coverLetter
         * @return applicant's ID
         */
        public function insertApplicant($itemId, $name, $email, $coverLetter, $phone, $birth, $sex, $source = '')
        {
            $date = date("Y-m-d H:i:s");
            $app = $this->dao->insert(
                    $this->getTable_JobsApplicants()
                    ,array(
                        'fk_i_item_id'      => $itemId
                        ,'s_name'           => $name
                        ,'s_email'          => $email
                        ,'s_phone'          => $phone
                        ,'s_cover_letter'   => $coverLetter
                        ,'dt_date'          => $date
                        ,'i_status'         => 0
                        ,'i_rating'         => 0
                        ,'s_ip'             => get_ip()
                        ,'dt_birthday'      => $birth
                        ,'s_sex'            => $sex
                        ,'s_source'         => $source
                    ));
            if($app) {
                return $this->dao->insertedId();
            } else {
                false;
            }
        }

        /**
         * Get applicants
         *
         * @param $start
         * @param $length
         * @param $conditions
         *
         * @return array
         */
        public function search($start = 0, $length = 10, $conditions = null, $order_col = 'a.dt_date', $order_dir = 'DESC')
        {
            // hack order by age.
            if($order_col=='a.dt_birthday') {
                if($order_dir=='DESC') { $order_dir='ASC'; }
                else { $order_dir='DESC'; }
            }
            $cond = array();
            if($conditions!=null) {
                foreach($conditions as $k => $v) {
                    if($k=='item') {
                        $cond[] = 'a.fk_i_item_id = '.$this->dao->connId->real_escape_string($v);
                    }
                    if($k=='item_text') {
                        $cond[] = "d.s_title LIKE '%%".$this->dao->connId->real_escape_string($v)."%%'";
                    }
                    if($k=='email') {
                        $cond[] = "a.s_email LIKE '%%".$this->dao->connId->real_escape_string($v)."%%'";
                    }
                    if($k=='name') {
                        $cond[] = "a.s_name LIKE '%%".$this->dao->connId->real_escape_string($v)."%%'";
                    }
                    if($k=='status') {
                        $cond[] = "a.i_status = ".$this->dao->connId->real_escape_string($v);
                    }
                    if($k=='unread') {
                        $cond[] = "a.b_read = 0";
                    }
                    if($k=='spontaneous') {
                        $cond[] = "d.s_title IS NULL";
                    }
                    if($k=='category') {
                        $sql_category = "select pk_i_id from ".DB_TABLE_PREFIX."t_category WHERE pk_i_id = ".$this->dao->connId->real_escape_string($v)." OR fk_i_parent_id = ".$this->dao->connId->real_escape_string($v);
                        $result = $this->dao->query($sql_category);
                        if( !$result ) {
                            return array();
                        }
                        $aux    = $result->result();
                        $array  = array();
                        foreach($aux as $catId) { $array[] = $catId['pk_i_id']; }
                        $cond[] = "i.fk_i_category_id IN (".  implode(',', $array).")";
                    }
                    if($k=='sex') {
                        $cond[] = "a.s_sex = '".$this->dao->connId->real_escape_string($v)."'";
                    }
                    // age
                    if($k=='minAge') {
                        $minDate = date("Y-m-d", strtotime("-$v year"));
                        $cond[] = "a.dt_birthday >= '".$this->dao->connId->real_escape_string($minDate)."'";
                    }
                    if($k=='maxAge') {
                        $maxDate = date("Y-m-d", strtotime("-$v year"));
                        $cond[] = "a.dt_birthday <= '".$this->dao->connId->real_escape_string($maxDate)."'";
                    }
                    if($k=='rating') {
                        $cond[] = "a.i_rating >= ". $this->dao->connId->real_escape_string($v) ;
                    }

                }
            }
            $cond_str = '';
            if(!empty($cond)) {
                $cond_str = " AND ".implode(" AND ", $cond)." ";
            }

            $tmp = explode(".", $order_col);
            $order_col2 = $order_col;
            if(count($tmp)>1) {
                $order_col2 = "dummy." . $tmp[1];
            }

            $sql = sprintf("SELECT a.fk_i_item_id as itemid, a.pk_i_id, a.s_name, a.s_email, a.s_source, a.s_sex, a.dt_birthday, a.s_phone, a.s_cover_letter, a.dt_date, a.i_status, a.b_read, a.b_has_notes, a.i_rating, a.d_score, a.b_corrected, d.*, FIELD(d.fk_c_locale_code, '%s') as locale_order
                FROM (%st_item_job_applicant a)
                LEFT JOIN %st_item_description d ON d.fk_i_item_id = a.fk_i_item_id
                LEFT JOIN %st_item i ON i.pk_i_id = a.fk_i_item_id
                WHERE (d.s_title != '' OR d.s_title IS NULL) %s ORDER BY locale_order DESC, %s %s", $this->dao->connId->real_escape_string(osc_current_admin_locale()), DB_TABLE_PREFIX, DB_TABLE_PREFIX, DB_TABLE_PREFIX, $cond_str, $order_col, $order_dir);
            $result = $this->dao->query(sprintf("SELECT * FROM (%s) as dummy GROUP BY dummy.pk_i_id ORDER BY %s %s LIMIT %d, %d", $sql, $order_col2, $order_dir, $start, $length));

            if( !$result ) {
                return array() ;
            }

            return $result->result();
        }

        public function searchCount($conditions = null, $order_col = 'a.dt_date', $order_dir = 'DESC')
        {
            $cond = array();
            if($conditions!=null) {
                foreach($conditions as $k => $v) {
                    if($k=='item') {
                        $cond[] = 'a.fk_i_item_id = '.$this->dao->connId->real_escape_string($v);
                    }
                    if($k=='item_text') {
                        $cond[] = "d.s_title LIKE '%%".$this->dao->connId->real_escape_string($v)."%%'";
                    }
                    if($k=='email') {
                        $cond[] = "a.s_email LIKE '%%".$this->dao->connId->real_escape_string($v)."%%'";
                    }
                    if($k=='name') {
                        $cond[] = "a.s_name LIKE '%%".$this->dao->connId->real_escape_string($v)."%%'";
                    }
                    if($k=='status') {
                        $cond[] = "a.i_status = ".$this->dao->connId->real_escape_string($v);
                    }
                    if($k=='unread') {
                        $cond[] = "a.b_read = 0";
                    }
                    if($k=='spontaneous') {
                        $cond[] = "d.s_title IS NULL";
                    }
                    if($k=='category') {
                        $sql_category = "select pk_i_id from ".DB_TABLE_PREFIX."t_category WHERE pk_i_id = ".$this->dao->connId->real_escape_string($v)." OR fk_i_parent_id = ".$this->dao->connId->real_escape_string($v);
                        $result = $this->dao->query($sql_category);
                        if( !$result ) {
                            return array();
                        }
                        $aux    = $result->result();
                        $array  = array();
                        foreach($aux as $catId) { $array[] = $catId['pk_i_id']; }
                        $cond[] = "i.fk_i_category_id IN (".  implode(',', $array).")";
                    }
                    if($k=='sex') {
                        $cond[] = "a.s_sex = '".$this->dao->connId->real_escape_string($v)."'";
                    }
                    // age
                    if($k=='minAge') {
                        $minDate = date("Y-m-d", strtotime("-$v year"));
                        $cond[] = "a.dt_birthday >= '".$this->dao->connId->real_escape_string($minDate)."'";
                    }
                    if($k=='maxAge') {
                        $maxDate = date("Y-m-d", strtotime("-$v year"));
                        $cond[] = "a.dt_birthday <= '".$this->dao->connId->real_escape_string($maxDate)."'";
                    }
                    if($k=='rating') {
                        $cond[] = "a.i_rating >= ". $this->dao->connId->real_escape_string($v);
                    }

                }
            }
            $cond_str = '';
            if(!empty($cond)) {
                $cond_str = " AND ".implode(" AND ", $cond)." ";
            }

            $sql = sprintf("SELECT a.fk_i_item_id as itemid, a.pk_i_id, a.s_name, a.s_email, a.s_source, a.s_sex, a.dt_birthday, a.s_phone, a.s_cover_letter, a.dt_date, a.i_status, a.b_read, a.b_has_notes, a.i_rating, a.d_score, a.b_corrected, d.*, FIELD(d.fk_c_locale_code, '%s') as locale_order
                FROM (%st_item_job_applicant a)
                LEFT JOIN %st_item_description d ON d.fk_i_item_id = a.fk_i_item_id
                LEFT JOIN %st_item i ON i.pk_i_id = a.fk_i_item_id
                WHERE (d.s_title != '' OR d.s_title IS NULL) %s ORDER BY locale_order DESC, a.dt_date DESC", $this->dao->connId->real_escape_string(osc_current_admin_locale()), DB_TABLE_PREFIX, DB_TABLE_PREFIX, DB_TABLE_PREFIX, $cond_str);
            $result = $this->dao->query(sprintf("SELECT * FROM (%s) as dummy GROUP BY dummy.pk_i_id ", $sql));

            if( !$result ) {
                $searchTotal = 0;
            } else {
                $searchTotal = count($result->result());
            }

            $this->dao->select( "COUNT(*) as total" ) ;
            $this->dao->from($this->getTable_JobsApplicants()." a");
            $result = $this->dao->get();
            if( !$result ) {
                $total = 0;
            } else {
                $total = $result->row();
            }

            return array($searchTotal, $total['total']);
        }

        public function countApplicantsUnread()
        {
            $this->dao->select('COUNT(*) AS total');
            $this->dao->from($this->getTable_JobsApplicants());
            $this->dao->where('b_read', '0');
            $result = $this->dao->get();
            if( !$result ) {
                return  0;
            }

            $total = $result->row();
            return $total['total'];
        }

        public function countApplicantsByStatus($status)
        {
            if( !in_array($status, array('0', '1', '2', '3')) ) {
                return 0;
            }
            $this->dao->select('COUNT(*) AS total');
            $this->dao->from($this->getTable_JobsApplicants());
            $this->dao->where('i_status', $status);
            $result = $this->dao->get();
            if( !$result ) {
                return  0;
            }

            $total = $result->row();
            return $total['total'];
        }

        /**
         * Set for a given job id the killer form with id killerFormId
         *
         * @param type $applicantId
         * @param type $killerFormId
         */
        public function setKillerForm($jobId, $killerFormId)
        {
            return $this->dao->update(
                    $this->getTable_JobsAttr()
                    ,array('fk_i_killer_form_id' => $killerFormId)
                    ,array('fk_i_item_id'        => $jobId));
        }

        /**
         * Set applicants rating
         *
         * @param $applicantId
         * @param $rating
         */
        public function setRating($applicantId, $rating)
        {
           return  $this->dao->update(
                    $this->getTable_JobsApplicants()
                    ,array('i_rating' => $rating)
                    ,array('pk_i_id' => $applicantId));
        }

        /**
         * return a number of times an email has applied for a job offer
         *
         * @param type $item
         * @param type $email
         */
        public function countApply($item, $email)
        {
            $this->dao->select('count(*) as total');
            $this->dao->from($this->getTable_JobsApplicants());
            if($item=='') {
                $this->dao->where("fk_i_item_id IS NULL");
            } else {
                $this->dao->where("fk_i_item_id", $item);
            }
            $this->dao->where("s_email", $email);

            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }

            $data = $result->row();
            return $data['total'];
        }

        /**
         * Get applicant
         *
         * @param $id
         *
         * @return array
         */
        public function getApplicant($id)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsApplicants());
            $this->dao->where("pk_i_id", $id);

            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }

            return $result->row();
        }

        /**
         * Get all the applicants
         *
         * @return array
         */
        public function getAllApplicants() {

            $this->dao->select();
            $this->dao->from($this->getTable_JobsApplicants());

            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }

            return $result->result();
        }

        /**
         * Get applicant's CV
         *
         * @param $id
         *
         * @return array
         */
        public function getCVFromApplicant($id)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsFiles());
            $this->dao->where("fk_i_applicant_id", $id);

            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }

            return $result->row();
        }

        /**
         * Get notes from applicant
         *
         * @param $id
         *
         * @return array
         */
        public function getNotesFromApplicant($id)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsNotes());
            $this->dao->where("fk_i_applicant_id", $id);
            $this->dao->orderBy("pk_i_id", 'desc');
            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }

            return $result->result();
        }

        /**
         * Get note by ID
         *
         * @param $id
         *
         * @return array
         */
        public function getNoteByID($noteID)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsNotes());
            $this->dao->where('pk_i_id', $noteID);
            $this->dao->limit(1);
            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }

            return $result->row();
        }

        public function getKillerFormScore($applicantId)
        {
            $this->dao->select('d_score');
            $this->dao->from($this->getTable_JobsApplicants());
            $this->dao->where($applicantId);
            $result = $this->dao->get();
            if( !$result ) {
                return  0;
            }

            $total = $result->row();
            return $total['d_score'];
        }

        public function countTotalNotes()
        {
            $this->dao->select('COUNT(*) AS total');
            $this->dao->from($this->getTable_JobsNotes());
            $result = $this->dao->get();
            if( !$result ) {
                return  0;
            }

            $total = $result->row();
            return $total['total'];
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
            $this->dao->delete($this->getTable_JobsAttrDescription(), array('fk_i_item_id' => $item_id) );

            $this->dao->select('pk_i_id');
            $this->dao->from($this->getTable_JobsApplicants());

            $result = $this->dao->get();
            if( !$result ) {
                return array() ;
            }
            $ids = $result->result();

            foreach($ids as $id) {
                $this->deleteApplicant($id['pk_i_id']);
            }

        }

        public function deleteApplicant($id) {
            $this->dao->delete($this->getTable_JobsNotes(), array('fk_i_applicant_id' => $id));
            $cv = $this->getCVFromApplicant($id);
            @unlink(osc_get_preference('upload_path', 'jobboard_plugin') . $cv['s_name']);
            $this->dao->delete($this->getTable_JobsFiles(), array('fk_i_applicant_id' => $id));
            return $this->dao->delete($this->getTable_JobsApplicants(), array('pk_i_id' => $id));
        }

        public function deleteNote($id)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_JobsNotes());
            $this->dao->where("pk_i_id", $id);
            $result = $this->dao->get();
            $success = $this->dao->delete($this->getTable_JobsNotes(), array('pk_i_id' => $id));
            if( $result ) {
                $row = $result->row();
                $notes = $this->getNotesFromApplicant($row['fk_i_applicant_id']);
                if(count($notes)==0) {
                    $this->dao->update($this->getTable_JobsApplicants(), array('b_has_notes' => 0), array('pk_i_id' => $row['fk_i_applicant_id']));
                }
            }
            return $success;
        }

        public function insertNote($id, $text)
        {
            $success = $this->dao->insert($this->getTable_JobsNotes(), array('dt_date' => date("Y-m-d H:i:s"), 's_text' => $text, 'fk_i_applicant_id' => $id));

            if( !$success ) {
                return false;
            }

            $noteID = $this->dao->insertedId();
            $this->dao->update($this->getTable_JobsApplicants(), array('b_has_notes' => 1), array('pk_i_id' => $id));
            return $noteID;
        }

        public function updateNote($id, $text)
        {
            return $this->dao->update($this->getTable_JobsNotes(), array('dt_date' => date("Y-m-d H:i:s"), 's_text' => $text), array('pk_i_id' => $id));
        }

        public function changeStatus($applicantId, $status)
        {
            return $this->dao->update($this->getTable_JobsApplicants(), array('i_status' => $status), array('pk_i_id' => $applicantId));
        }

        public function changeRead($applicantId)
        {
            return $this->dao->update($this->getTable_JobsApplicants(), array('b_read' => 1), array('pk_i_id' => $applicantId));
        }

        public function changeUnread($applicantID)
        {
            return $this->dao->update($this->getTable_JobsApplicants(), array('b_read' => 0), array('pk_i_id' => $applicantID));
        }

        public function changeSecret($fileId)
        {
            return $this->dao->update($this->getTable_JobsFiles(), array('s_secret' => osc_genRandomPassword(12), 'dt_secret_date' => date("Y-m-d H:i:s")), array('pk_i_id' => $fileId));
        }

        public function updateScore($applicantId, $score, $bCorrected = false)
        {
            if($bCorrected){
                $bCorrected = 1;
            } else {
                $bCorrected = 0;
            }
            $_score = number_format($score, 2);
            return $this->dao->update($this->getTable_JobsApplicants(),
                    array(  'd_score'       => $score,
                            'b_corrected'   => $bCorrected),
                    array('pk_i_id' => $applicantId));
        }
    }

    // End of file: ./jobboard/model/ModelJB.php