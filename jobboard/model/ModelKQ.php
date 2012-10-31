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
     * Model database for Killer Questions Forms
     *
     * You can add killer questions to a job offer, allowing add punctuation to the answers.
     * Two kind of questions, open questions and closed questions.
     * Closed questions can reject applicants who answer incorrect questions
     *
     * @package OSClass
     * @subpackage Model
     * @since 3.0
     */
    class ModelKQ extends DAO
    {
        /**
         * It references to self object: ModelKQ.
         * It is used as a singleton
         *
         * @access private
         * @since 3.0
         * @var ModelKQ
         */
        private static $instance ;

        /**
         * It creates a new ModelKQ object class ir if it has been created
         * before, it return the previous object
         *
         * @access public
         * @since 3.0
         * @return ModelKQ
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
         * Return table name question
         * @return string
         */
        public function getTable_Question()
        {
            return DB_TABLE_PREFIX.'t_question' ;
        }

        /**
         * Return table name answer
         * @return string
         */
        public function getTable_Answer()
        {
            return DB_TABLE_PREFIX.'t_answer' ;
        }

        /**
         * Return table name killer_form
         * @return string
         */
        public function getTable_KillerForm()
        {
            return DB_TABLE_PREFIX.'t_killer_form' ;
        }

        /**
         * Return table name killer_form_questions
         * @return string
         */
        public function getTable_KillerFormQuestions()
        {
            return DB_TABLE_PREFIX.'t_killer_form_questions' ;
        }

        /**
         * Return table name killer_form_results
         * @return string
         */
        public function getTable_KillerFormResults()
        {
            return DB_TABLE_PREFIX.'t_killer_form_results' ;
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
                throw new Exception( "Error importSQL::ModelKQ<br>".$file ) ;
            }
        }

        /**
         *  Remove data and tables related to the plugin. (Only killer questions)
         */
        public function uninstall()
        {
            $this->dao->query('DROP TABLE '. $this->getTable_KillerFormResults());
            $this->dao->query('DROP TABLE '. $this->getTable_KillerFormQuestions());
//            $this->dao->query('DROP TABLE '. $this->getTable_KillerForm()); // moved to ModelJB
            $this->dao->query('DROP TABLE '. $this->getTable_Answer());
            $this->dao->query('DROP TABLE '. $this->getTable_Question());
        }

        /**
         * Insert question to the system, by default question type is opened question
         *
         * @param type $text
         * @param type $type
         * @return type false if error happend, return id if insert correctly
         */
        public function insertQuestion($text, $type = 'OPENED')
        {
            $result = $this->dao->insert($this->getTable_Question(),
                        array(  'e_type'    => $type,
                                's_text'    => $text));
            if($result!==false) {
                return $this->dao->insertedId();
            }
            return false;
        }

        /**
         * Insert answer and relate with question
         *
         * @param type $questionId
         * @param type $text
         * @param type $punctuation
         */
        public function insertAnswer($questionId, $text, $punctuation)
        {
            $reject = 0;
            if($punctuation=='reject') {
                $reject = 1;
            }
            return $this->dao->insert($this->getTable_Answer(),
                    array(  'fk_i_question_id' => $questionId,
                            's_text'           => $text,
                            's_punctuation'    => $punctuation,
                            'b_reject'         => $reject
                            ));
        }

        /**
         * Add new kill form given a title
         *
         * @param type $title
         * @return bool
         */
        public function insertKillerForm($title)
        {
            $result = $this->dao->insert( $this->getTable_KillerForm(),
                    array('s_title'     => $title,
                          'dt_pub_date' => date('Y-m-d H:i:s') ));
            if($result!==false) {
                return $this->dao->insertedId();
            }
            return false;
        }

        /**
         * Insert killer form results, applicant test result.
         *
         * @param type $applicantId
         * @param type $aQuestions ??
         */

        public function insertAnswerClosed($applicantId, $killerFormId, $questionId, $answer ) {
            return $this->insertAnswerResult(false, $applicantId, $killerFormId, $questionId, $answer);
        }

        public function insertAnswerOpened($applicantId, $killerFormId, $questionId, $answer ) {
            return $this->insertAnswerResult(true , $applicantId, $killerFormId, $questionId, $answer);
        }

        private function insertAnswerResult($opened, $applicantId, $killerFormId, $questionId, $answer )
        {
            // get answer puntuation and add to array
            $array = array( 'fk_i_applicant_id'     => $applicantId,
                            'fk_i_killer_form_id'   => $killerFormId,
                            'fk_i_question_id'      => $questionId );
            if($opened) {
                $array['s_answer_opened'] = $answer;
            } else {
                $array['fk_i_answer_id']  = $answer;
                $this->dao->select('s_punctuation');
                $this->dao->from($this->getTable_Answer());
                $this->dao->where('pk_i_id', $answer);
                $result = $this->dao->get();
                if($result===false){
                    return false;
                }
                $aAnswer = $result->row();
                error_log(print_r($aAnswer, true));
                $array['s_punctuation']  = $aAnswer['s_punctuation'];
            }

            return $this->dao->insert($this->getTable_KillerFormResults(), $array );
        }

        /**
         * Add questions to killer form
         *
         * @param type $id
         * @param type $questionId
         * @param type $order
         * @return type
         */
        public function addQuestionsToKillerForm($id, $questionId, $order)
        {
            return $this->dao->insert($this->getTable_KillerFormQuestions(),
                        array('fk_i_killer_form_id' => $id,
                              'fk_i_question_id'    => $questionId,
                              'i_order'             => $order));
        }

        // updates
        /**
         * Update killer form title
         *
         * @param type $id
         * @param type $title
         */
        public function updateKillerForm($id, $title)
        {
            return $this->dao->update( $this->getTable_KillerForm(),
                    array('s_title'     => $title,
                          'dt_mod_date' => date('Y-m-d H:i:s')),
                    array('pk_i_id'     => $id)) ;
        }


        // getters
        /**
         * Update answer information given an answerId
         *
         * @param type $answerId
         * @param type $text
         * @param type $punctuation
         * @return type
         */
        public function updateAnswer($answerId, $text, $punctuation)
        {
            $reject = 0;
            if($punctuation=='reject') {
                $reject = 1;
            }

            return $this->dao->update($this->getTable_Answer(),
                    array(  's_text'           => $text,
                            's_punctuation'    => $punctuation,
                            'b_reject'         => $reject),
                    array(  'pk_i_id'          => $answerId));
        }

        /**
         * Update answer information given an answerId, if punctuation is 'reject' change applicant status
         *
         * @param type $answerId
         * @param type $text
         * @param type $punctuation
         * @return type
         */

        public function updatePunctuationQuestionResult($killerformId, $applicantId, $questionId, $punctuation)
        {
            if($punctuation=='reject') {
                ModelJB::newInstance()->changeStatus($applicantId, 2);
            }

            return $this->dao->update($this->getTable_KillerFormResults(),
                    array(  's_punctuation'    => $punctuation),
                    array(  'fk_i_killer_form_id'   => $killerformId,
                            'fk_i_applicant_id'     => $applicantId,
                            'fk_i_question_id'      => $questionId));
        }

        /**
         * Update question information given a question id
         *
         * @param type $questionId
         * @param type $text
         * @param type $type
         * @return type
         */
        public function updateQuestion($questionId, $text, $type)
        {
            // extra check type ?
            // only OPENED/CLOSED
            return $this->dao->update($this->getTable_Question(),
                        array(  'e_type'    => $type,
                                's_text'    => $text),
                        array('pk_i_id'     => $questionId ));
        }

        /**
         * Remove answer given an answer id
         *
         * @param type $answerId
         * @return type
         */
        public function removeAnswer($answerId)
        {
            return $this->dao->delete($this->getTable_Answer(),
                        array('pk_i_id'     => $answerId ));
        }

        /**
         * Remove question given a question id, if questions have answers,
         * will be remove too.
         *
         * @param type $questionId
         * @return type
         */
        public function removeQuestionsToKillerForm($killerFormId, $questionId)
        {
            $this->removeAnswersByQuestionId($questionId);
            $this->removeQuestionInForms($killerFormId, $questionId);

            return $this->dao->delete($this->getTable_Question(),
                        array('pk_i_id' => $questionId));
        }

        /**
         * Remove all answers given a question id
         *
         * @param type $questionId
         * @return type
         */
        public function removeAnswersByQuestionId($questionId)
        {
            return $this->dao->delete($this->getTable_Answer(),
                    array('fk_i_question_id'    => $questionId));
        }

        /**
         * Remove question from table fk_i_killer_form_id
         *
         * @param type $killerFormId
         * @param type $questionId
         * @return type
         */
        public function removeQuestionInForms($killerFormId, $questionId)
        {
            return $this->dao->delete($this->getTable_KillerFormQuestions(),
                        array(  'fk_i_killer_form_id' => $killerFormId,
                                'fk_i_question_id'    => $questionId));
        }

        /**
         * Return an array with all the answers to the given question id
         * if question is opened return null
         * else return array with answers
         *
         * @param type $questionId
         */
        public function getAnswers($questionId)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_Answer());
            $this->dao->where('fk_i_question_id',  $questionId);
            $result = $this->dao->get();
            if($result!==false) {
                return $result->result();
            }
            return array();
        }

        public function getAnswer($answerId)
        {
            if(!is_numeric($answerId)) {
                return array();
            }
            $this->dao->select();
            $this->dao->from($this->getTable_Answer());
            $this->dao->where('pk_i_id',  $answerId);
            $result = $this->dao->get();
            if($result!==false) {
                return $result->row();
            }
            return array();
        }

        /**
         * Get question attr and all the answers for this question id
         *
         * array('question'  => 'question text'
         *       'answers'   => array(
         *              0   => 'answer one'
         *              1   => 'answer two'
         *              2   => 'answer three'
         *              3   => 'answer four'
         *              )
         *       )
         *
         * @param type $questionId
         */
        public function getQuestion($questionId)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_Question());
            $this->dao->where('pk_i_id',  $questionId);
            $result = $this->dao->get();

            if($result!==false) {
                $result = $result->row();
                if($result['e_type']=='CLOSED') {
                    // find answers
                    $aAnswers = $this->getAnswers($questionId);
                    $result['a_answers'] = $aAnswers;
                } else {
                    $result['a_answers'] = false;
                }
                return $result;
            }
            return array();
        }

        /**
         * Get all questions belonging to killer form id
         *
         * @todo -> ORDER
         * @param type $killerFormId
         */
        public function getKillerQuestion($killerFormId)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_KillerFormQuestions());
            $this->dao->where('fk_i_killer_form_id', $killerFormId);
            $result = $this->dao->get();

            $aux_count = 1;
            if($result!==false) {
                $result = $result->result();
                foreach($result as $_aux) {
                    $q = $this->getQuestion($_aux['fk_i_question_id']);
                    $result['questions'][$aux_count] = $q;
                    $aux_count++;
                }
                return $result;
            }
            return  array();
        }

        /**
         * Return all killer forms saved into system
         */
        public function getAllKillerForm()
        {
            $this->dao->select();
            $this->dao->from($this->getTable_KillerForm());
            $result = $this->dao->get();
            if($result!==false) {
                $result = $result->result();
                return $result;
            }
            return array();
        }

        public function search($start = 0, $length = 10, $conditions = null, $order_col = 'kf.dt_pub_date', $order_dir = 'DESC')
        {
            $cond = array();
            if($conditions!=null) {
                foreach($conditions as $k => $v) {
                    if($k=='title') {
                        $cond[] = "s_title LIKE '%%".$this->dao->connId->real_escape_string($v)."%%'";
                    }
                }
            }
            $cond_str = '';
            if(!empty($cond)) {
                $cond_str = implode(" AND ", $cond)." ";
            }
            // subselect n_questions
            $sub_select_nquestions = "(select count(*) from ".DB_TABLE_PREFIX."t_killer_form_questions as kfq where kf.pk_i_id = kfq.fk_i_killer_form_id) as n_questions";
            $sub_select_is_used = "(select count(*) from ".DB_TABLE_PREFIX."t_item_job_attr as jia where kf.pk_i_id = jia.fk_i_killer_form_id) as n_used";

            $this->dao->select("kf.*, $sub_select_nquestions, $sub_select_is_used");
            $this->dao->from($this->getTable_KillerForm().' as kf');
            if($cond_str!='') {
                $this->dao->where($cond_str);
            }
            $this->dao->orderBy($order_col, $order_dir) ;
            $this->dao->limit($start, $length);
            $result = $this->dao->get();

            if( $result===false ) {
                return array() ;
            }

            return $result->result();
        }

        public function searchCount($conditions = null, $order_col = 'kf.dt_pub_date', $order_dir = 'DESC')
        {
            $cond = array();
            if($conditions!=null) {
                foreach($conditions as $k => $v) {
                    if($k=='title') {
                        $cond[] = "s_title = '".$this->dao->connId->real_escape_string($v)."'";
                    }
                }
            }
            $cond_str = '';
            if(!empty($cond)) {
                $cond_str = implode(" AND ", $cond)." ";
            }
            // subselect n_questions
            $sub_select_nquestions = "(select count(*) from ".DB_TABLE_PREFIX."t_killer_form_questions as kfq where kf.pk_i_id = kfq.fk_i_killer_form_id) as n_questions";
            $sub_select_is_used = "(select count(*) from ".DB_TABLE_PREFIX."t_item_job_attr as jia where kf.pk_i_id = jia.fk_i_killer_form_id) as n_used";

            $this->dao->select("kf.*, $sub_select_nquestions, $sub_select_is_used");
            $this->dao->from($this->getTable_KillerForm().' as kf');
            if($cond_str!='') {
                $this->dao->where($cond_str);
            }
            $result = $this->dao->get();
            if( !$result ) {
                $searchTotal = 0;
            } else {
                $searchTotal = count($result->result());
            }

            $this->dao->select( "COUNT(*) as total" ) ;
            $this->dao->from($this->getTable_KillerForm());
            $result = $this->dao->get();
            if( !$result ) {
                $total = 0;
            } else {
                $total = $result->row();
            }

            return array($searchTotal, $total['total']);
        }

        public function getKillerForm($formId)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_KillerForm());
            $this->dao->where('pk_i_id', $formId);
            $result = $this->dao->get();
            if($result===false) {
                return array();
            }

            $result = $result->row();
            return $result;
        }

        /**
         * Return all answered questions by an applicant
         *
         * @param type $applicantId
         * @return type
         */
        public function getResultsByApplicant($applicantId)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_KillerFormResults());
            $this->dao->where('fk_i_applicant_id', $applicantId);
            $result = $this->dao->get();
            if($result===false) {
                return array();
            }

            $result = $result->result();
            $array = array();
            foreach($result as $aux) {
                $array[$aux['fk_i_question_id']] = $aux;
            }
            return $array;
        }

        /**
         * Return score or rejected if any answer discards the applicant
         */
        public function calculatePunctuationOfApplicant($applicantId)
        {
            $this->dao->select();
            $this->dao->from($this->getTable_KillerFormResults());
            $this->dao->join($this->getTable_Answer(), $this->getTable_Answer().'.pk_i_id = '.$this->getTable_KillerFormResults().'.fk_i_answer_id', 'LEFT');
            $this->dao->where('fk_i_applicant_id', $applicantId);
            $result = $this->dao->get();

            if($result===false) {
                return array();
            }
            $result = $result->result();

            // calculate score.
            $maxPunctuation = 10;
            $scoreAcumulate = 0;
            $numQuestions   = count($result);
            foreach($result as $aux) {
                // s_punctuation
                $aux_punctuation = $aux['s_punctuation'];
                if($aux_punctuation=='reject') {
                    ModelJB::newInstance()->changeStatus($applicantId, 2);
                } else if( is_numeric($aux_punctuation) ){
                    $scoreAcumulate += $aux_punctuation;
                }
            }

            $score = ($maxPunctuation * $scoreAcumulate) / ($numQuestions*$maxPunctuation);

            // save punctuation on t_item_job_applicant + update status if $rejected == true
            ModelJB::newInstance()->changeScore($applicantId, $score);

            return $score;
        }
    }
    // end file
?>