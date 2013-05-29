<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

class JobboardListingActions
{
    public function __construct() {
        // add/add-post
        osc_add_hook('item_form',          array(&$this, 'jobboard_form') );
        osc_add_hook('item_form_post',     array(&$this, 'jobboard_form_post') );
        // edit/edit-post
        osc_add_hook('item_edit',          array(&$this, 'jobboard_item_edit') );
        osc_add_hook('item_edit_post',     array(&$this, 'jobboard_item_edit_post') );
        // duplicate vacancy
        osc_add_hook('before_admin_html',  array(&$this, 'jobboard_duplicate_job') );
        // delete item/delete locale
        osc_add_hook('delete_locale',      array(&$this, 'job_delete_locale') );
        osc_add_hook('delete_item',        array(&$this, 'job_delete_item') );
        // vacancy detail
        osc_add_hook('item_detail',        array(&$this, 'job_item_detail') );
        osc_add_hook('item_detail',        array(&$this, 'job_linkedin') );
        // pre item post - save params into session
        osc_add_hook('pre_item_post',      array(&$this, 'job_pre_item_post') );
        // keepForm params into session
        osc_add_hook('save_input_session', array(&$this, 'job_save_inputs_into_session') );

    }



    /*
     * Remove - vacancy
     */
    function job_delete_item($item_id) {
        ModelJB::newInstance()->deleteItem($item_id);
    }
    function job_delete_locale($locale) {
        ModelJB::newInstance()->deleteLocale($locale);
    }

    /*
     * Add - vacancy
     */
    function jobboard_form($catID = null) {
        $detail = array(
            'e_position_type' => '',
            's_salary_text'   => '',
            'i_num_positions' => 1,
            'locale'          => array()
        );
        foreach(osc_get_locales() as $locale) {
            $detail['locale'][$locale['pk_c_code']] = array(
                's_desired_exp'                 => '',
                's_studies'                     => '',
                's_minimum_requirements'        => '',
                's_desired_requirements'        => '',
                's_contract'                    => ''
            );
        }
        // session variables
        $detail = $this->get_jobboard_session_variables($detail);

        $item_form_path = osc_apply_filter('jobboard_item_edit_form_path', JOBBOARD_PATH . 'item_edit.php');
        if( file_exists($item_form_path) ) {
            include_once( $item_form_path );
        }
        if( OC_ADMIN ) {
            include_once(JOBBOARD_PATH . 'item_edit_killer_questions.php');
        }
        Session::newInstance()->_clearVariables();
    }

    /*
     * Add post - vacancy
     */
    function jobboard_form_post($catID = null, $itemID = null)  {
        // add killer questions form
        $killerFormId = '';
        $aDataKiller = $this->getParamsKillerForm_insert();
        // if have questions ... create killer form
        if(count($aDataKiller)>0) {
            $title = "killer_questions_job_".$itemID;
            $killerFormId = ModelKQ::newInstance()->insertKillerForm($title);
            $this->_insertKillerQuestions($killerFormId, $aDataKiller);
        }

        ModelJB::newInstance()->insertJobsAttr($itemID, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText'), Params::getParam('numPositions'), $killerFormId);

        // prepare locales
        // init variable
        $dataItem = array();
        $dataItem[osc_locale_code()]['min_reqs']     = '';
        $dataItem[osc_locale_code()]['desired_reqs'] = '';
        $dataItem[osc_locale_code()]['desired_exp']  = '';
        $dataItem[osc_locale_code()]['studies']      = '';
        $dataItem[osc_locale_code()]['contract']     = '';
        if( is_array(Params::getParam('min_reqs')) ) {
            foreach(Params::getParam('min_reqs') as $k => $v) {
                $dataItem[$k]['min_reqs'] = $v;
            }
        }
        if( is_array(Params::getParam('desired_reqs')) ) {
            foreach(Params::getParam('desired_reqs') as $k => $v) {
                $dataItem[$k]['desired_reqs'] = $v;
            }
        }
        if( is_array(Params::getParam('desired_exp')) ) {
            foreach(Params::getParam('desired_exp') as $k => $v) {
                $dataItem[$k]['desired_exp'] = $v;
            }
        }
        if( is_array(Params::getParam('studies')) ) {
            foreach(Params::getParam('studies') as $k => $v) {
                $dataItem[$k]['studies'] = $v;
            }
        }
        if( is_array(Params::getParam('contract')) ) {
            foreach(Params::getParam('contract') as $k => $v) {
                $dataItem[$k]['contract'] = $v;
            }
        }

        // insert locales
        foreach ($dataItem as $k => $data) {
            ModelJB::newInstance()->insertJobsAttrDescription($itemID, $k, $data['desired_exp'], $data['studies'], $data['min_reqs'], $data['desired_reqs'], $data['contract']);
        }

        $this->_clear_session_variables();

        // save itemId into session, this way can share on manage listings
        Session::newInstance()->_set('jobboard_share_job', $itemID);
    }

    /*
     * Edit - vacancy
     */
    function jobboard_item_edit($catID = null, $itemID = null) {
        $detail       = ModelJB::newInstance()->getJobsAttrByItemId($itemID);
        $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($itemID);
        $detail['locale'] = array();
        foreach ($descriptions as $desc) {
            $detail['locale'][$desc['fk_c_locale_code']] = $desc;
        }

        // session variables
        $detail = $this->get_jobboard_session_variables($detail);

        $item_form_path = osc_apply_filter('jobboard_item_edit_form_path', JOBBOARD_PATH . 'item_edit.php');
        if( file_exists($item_form_path) ) {
            include_once( $item_form_path );
        }
        if( OC_ADMIN ) {
            include_once(JOBBOARD_PATH . 'item_edit_killer_questions.php');
        }
        Session::newInstance()->_clearVariables();
    }

    /*
     * Edit post - vacancy
     */
    function jobboard_item_edit_post($catID = null, $itemID = null)
    {
        $aux_job    = ModelJB::newInstance()->getJobsAttrByItemId($itemID);
        $killerFormId   = @$aux_job['fk_i_killer_form_id'];
        $array_new      = $this->getParamsKillerForm_insert();
        $array_update   = $this->getParamsKillerForm_update();

        if(( !empty($array_new) || !empty($array_update) ) && !is_numeric($killerFormId) ) {
            $title = "killer_questions_job_".$itemID;
            $killerFormId = ModelKQ::newInstance()->insertKillerForm($title);
        }

        $resAdd     = $this->_insertKillerQuestions($killerFormId, $array_new);
        if($resAdd===false) {
            osc_add_flash_message(__('Some errors occurs adding new killer questions', 'jobboard'), 'admin');
            header('Location: ' . osc_admin_base_url(true).'page=items&action=item_edit&id='.$itemID); exit;
        }
        $resEdit    = $this->_updateKillerQuestions($killerFormId, $array_update);
        if($resEdit===false) {
            osc_add_flash_message(__('Some errors occurs updating existing killer questions', 'jobboard'), 'admin');
            header('Location: ' . osc_admin_base_url(true).'page=items&action=item_edit&id='.$itemID); exit;
        }

        ModelJB::newInstance()->replaceJobsAttr($itemID, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText'), Params::getParam('numPositions'), $killerFormId );

        // prepare locales
        $dataItem = array();
        if( is_array(Params::getParam('min_reqs')) ) {
            foreach(Params::getParam('min_reqs') as $k => $v) {
                $dataItem[$k]['min_reqs'] = $v;
            }
        }
        if( is_array(Params::getParam('desired_reqs')) ) {
            foreach(Params::getParam('desired_reqs') as $k => $v) {
                $dataItem[$k]['desired_reqs'] = $v;
            }
        }
        if( is_array(Params::getParam('desired_exp')) ) {
            foreach(Params::getParam('desired_exp') as $k => $v) {
                $dataItem[$k]['desired_exp'] = $v;
            }
        }
        if( is_array(Params::getParam('studies')) ) {
            foreach(Params::getParam('studies') as $k => $v) {
                $dataItem[$k]['studies'] = $v;
            }
        }
        if( is_array(Params::getParam('contract')) ) {
            foreach(Params::getParam('contract') as $k => $v) {
                $dataItem[$k]['contract'] = $v;
            }
        }

        // insert locales
        foreach ($dataItem as $k => $data) {
            ModelJB::newInstance()->replaceJobsAttrDescriptions($itemID, $k, $data['desired_exp'], $data['studies'], $data['min_reqs'], $data['desired_reqs'], $data['contract']);
        }

        $this->_clear_session_variables();
    }

    /*
     * Duplicate vacancy
     */
    function jobboard_duplicate_job() {
        if(Params::getParam('page')=='items' && Params::getParam('action')=='post') {
            $id = Params::getParam('duplicatefrom') ;
            if($id!='') {
                $item = Item::newInstance()->findByPrimaryKey($id);

                View::newInstance()->_exportVariableToView("item", $item);
                View::newInstance()->_exportVariableToView("new_item", TRUE);
                View::newInstance()->_exportVariableToView("actions", array());

                $detail       = ModelJB::newInstance()->getJobsAttrByItemId($id);
                $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($id);

                Session::newInstance()->_setForm('pj_positionType',  @$detail['e_position_type'] );
                Session::newInstance()->_setForm('pj_salaryText', @$detail['s_salary_text'] );
                Session::newInstance()->_setForm('pj_numPositions', @$detail['i_num_positions'] );

                $dataItem = array();
                foreach ($descriptions as $v) {
                    $dataItem[$v['fk_c_locale_code']] = array();
                    $dataItem[$v['fk_c_locale_code']]['contract'] = $v['s_contract'];
                    $dataItem[$v['fk_c_locale_code']]['studies'] = $v['s_studies'];
                    $dataItem[$v['fk_c_locale_code']]['desired_exp'] = $v['s_desired_exp'];
                    $dataItem[$v['fk_c_locale_code']]['min_reqs'] = $v['s_minimum_requirements'];
                    $dataItem[$v['fk_c_locale_code']]['desired_reqs'] = $v['s_desired_requirements'];
                }
                Session::newInstance()->_setForm('pj_data', $dataItem );

                Session::newInstance()->_keepForm('pj_positionType');
                Session::newInstance()->_keepForm('pj_salaryText');
                Session::newInstance()->_keepForm('pj_numPositions');
                Session::newInstance()->_keepForm('pj_data');

                osc_current_admin_theme_path('items/frm.php') ;
                Session::newInstance()->_clearVariables();
                osc_run_hook('after_admin_html');
                exit;
            }
        }
    }


    /*
     *  vacancy detail
     */

    /**
     * Add extra fields to vacancy details
     */
    function job_item_detail() {
        $detail = ModelJB::newInstance()->getJobsAttrByItemId(osc_item_id());
        $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId(osc_item_id());
        $detail['locale'] = array();
        foreach ($descriptions as $desc) {
            $detail['locale'][$desc['fk_c_locale_code']] = $desc;
        }
        require_once(JOBBOARD_PATH . 'item_detail.php');
    }

    /**
     * Add button for apply with linkedin
     */
    function job_linkedin() {
        require_once(JOBBOARD_PATH . 'linkedinApply.php');
    }

    /*
     * Pre item post -> Save params into session
     */
    function job_pre_item_post() {
        Session::newInstance()->_setForm('pj_positionType',  Params::getParam('positionType'));
        Session::newInstance()->_setForm('pj_salaryText', Params::getParam('salaryText'));
        Session::newInstance()->_setForm('pj_numPositions', Params::getParam('numPositions'));
        // prepare locales
        $dataItem = array();
        $request = Params::getParamsAsArray();
        foreach ($request as $k => $v) {
            if(is_array($v)) {
                foreach($v as $locale => $value) {
                    $dataItem[$locale][$k] = $value;
                }
            }
        }
        Session::newInstance()->_setForm('pj_data', $dataItem );

        // keep values on session
        Session::newInstance()->_keepForm('pj_positionType');
        Session::newInstance()->_keepForm('pj_salaryText');
        Session::newInstance()->_keepForm('pj_numPositions');
        Session::newInstance()->_keepForm('pj_data');
    }

    /*
     * KeepForm params into session
     */
    function job_save_inputs_into_session() {
        Session::newInstance()->_keepForm('pj_positionType');
        Session::newInstance()->_keepForm('pj_salaryText');
        Session::newInstance()->_keepForm('pj_numPositions');
        Session::newInstance()->_keepForm('pj_data');
    }



    // --- --- utils --- ---
    function get_jobboard_session_variables($detail) {
        if( Session::newInstance()->_getForm('pj_positionType') != '' ) {
            $detail['e_position_type'] = Session::newInstance()->_getForm('pj_positionType');
        }
        if( Session::newInstance()->_getForm('pj_salaryText') != '' ) {
            $detail['s_salary_text'] = Session::newInstance()->_getForm('pj_salaryText');
        }
        if( Session::newInstance()->_getForm('pj_numPositions') != '' ) {
            $detail['i_num_positions'] = Session::newInstance()->_getForm('pj_numPositions');
        }
        if( Session::newInstance()->_getForm('pj_data') != '' ) {
            foreach(osc_get_locales() as $locale) {
                $data = Session::newInstance()->_getForm('pj_data');
                $detail['locale'][$locale['pk_c_code']]['s_desired_exp']          = @$data[$locale['pk_c_code']]['desired_exp'];
                $detail['locale'][$locale['pk_c_code']]['s_studies']              = @$data[$locale['pk_c_code']]['studies'];
                $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements'] = @$data[$locale['pk_c_code']]['min_reqs'];
                $detail['locale'][$locale['pk_c_code']]['s_desired_requirements'] = @$data[$locale['pk_c_code']]['desired_reqs'];
                $detail['locale'][$locale['pk_c_code']]['s_contract']             = @$data[$locale['pk_c_code']]['contract'];
            }
        }
        return $detail;
    }

    function _clear_session_variables() {
        Session::newInstance()->_dropKeepForm('pj_positionType');
        Session::newInstance()->_dropKeepForm('pj_salaryText');
        Session::newInstance()->_dropKeepForm('pj_numPositions');
        Session::newInstance()->_dropKeepForm('pj_data');
    }

    /*
     *     kiler question related
     */
    function getParamsKillerForm_insert() {
        return $this->getParamsKillerForm(true);
    }
    function getParamsKillerForm_update() {
        return $this->getParamsKillerForm(false);
    }
    function getParamsKillerForm($new = false) {

        $aQuestions = array();
        $max_answer = osc_get_preference('max_answers', 'jobboard_plugin');
        $questions = array();
        if($new) {
            $questions  = Params::getParam('new_question');
        } else {
            $questions  = Params::getParam('question');
        }

        if(is_array($questions) && !empty($questions) ) {
            foreach($questions as $key => $q) {
                $s_question = $q['question'];
                if($s_question!='') {
                    // update / insert (when question is not created) ---------/
                    $old_answer         = @$q['answer'];
                    $old_answer_punct   = @$q['answer_punct'];
                    $aOldAnswer         = array();
                    $aRemoveAnswer      = array();
                    if(is_array($old_answer) && !empty($old_answer)) {
                        foreach($old_answer as $_key => $_a) {
                            if($_a!='') { // add answer only if is not empty
                                $aOldAnswer[$_key]['id']     = $_key;  // answer_id
                                $aOldAnswer[$_key]['text']   = $_a;
                                $aOldAnswer[$_key]['punct']  = $old_answer_punct[$_key];
                            } else {
                                $aRemoveAnswer[$_key]['id'] = $_key;
                            }
                        }
                    }
                    // insert --------------------------------------------------/
                    $new_answer         = @$q['new_answer'];
                    $new_answer_punct   = @$q['new_answer_punct'];
                    $aNewAnswer         = array();
                    if(is_array($new_answer) && !empty($new_answer)) {
                        foreach($new_answer as $_key => $_a) {
                            if($_a!='') { // add answer only if is not empty
                                $aNewAnswer[$_key]['text']  = $_a;
                                $aNewAnswer[$_key]['punct'] = $new_answer_punct[$_key];
                            }
                        }
                    }
                    // ---------------------------------------------------------/
                    $aQuestions[$key]['question']   = $s_question;
                    $aQuestions[$key]['answer']     = $aOldAnswer;
                    $aQuestions[$key]['new_answer'] = $aNewAnswer;
                    $aQuestions[$key]['remove']     = $aRemoveAnswer;
                } // question empty
            }
        }
        return $aQuestions;
    }

    /**
     * Insert killer questions and make associations with killer form id
     *
     * @param type $killerFormId
     * @param type $aQuestions
     * @return type
     */
    function _insertKillerQuestions($killerFormId, $aQuestions) {
        $error = false;
        foreach($aQuestions as $key => $q) {
            $id = -1;
            if($q['answer']===array()) { // opened question
                $id = ModelKQ::newInstance()->insertQuestion($q['question'], 'OPENED');
            } else {                    // closed question
                $id = ModelKQ::newInstance()->insertQuestion($q['question'], 'CLOSED');
                if( $id!==false && is_numeric($id) ) {
                    // insert answers
                    $aAnswers = $q['answer'];
                    foreach($aAnswers as $key_ => $a) {
                        $answer_id = ModelKQ::newInstance()->insertAnswer($id, $a['text'], $a['punct']);
                    }
                } else {
                    // error occurs
                    $error = true;
                }
            }
            ModelKQ::newInstance()->addQuestionsToKillerForm($killerFormId, $id, $key);
        }

        if(!$error) {
            osc_add_flash_ok_message(__('Killer question form added correctly', 'jobboard'), 'admin');
            return true;
        } else {
            osc_add_flash_message(__('Error adding Killer question form', 'jobboard'), 'admin');
            return false;
        }
    }

    /**
     * Update killer questions
     *
     * @param type $killerFormId
     * @param type $aQuestions
     * @return type
     */
    function _updateKillerQuestions($killerFormId, $aQuestions) {
        $error = false;
        foreach($aQuestions as $questionId => $q) {
            $rInsert = true;
            $rUpdate = true;
            $rRemove = true;
            // update question text
            if($q['answer']===array() && $q['new_answer']===array()) { // opened question
                // force question  e_type = OPENED
                $rUpdate = ModelKQ::newInstance()->updateQuestion($questionId, $q['question'], 'OPENED');
                // if there is answers remove them
                $rRemove = ModelKQ::newInstance()->removeAnswersByQuestionId($questionId);
                if($rRemove===false || $rUpdate===false) { $error = true; }
            } else {  // closed question
                // force question e_type = CLOSED
                $rUpdate = ModelKQ::newInstance()->updateQuestion($questionId, $q['question'], 'CLOSED');

                // --------- add new answers to existing questions -----------------
                $arrayNews = $q['new_answer'];
                // if there is new answers insert them and asociate to killerForm (t_killer_form_questions)
                if(is_array($arrayNews) && !empty($arrayNews)) {
                    foreach($arrayNews as $_auxNew) {
                        $rInsert = ModelKQ::newInstance()->insertAnswer($questionId, $_auxNew['text'], $_auxNew['punct']);
                        if($rInsert===false){ $error = true; }
                    }
                    ModelKQ::newInstance()->addQuestionsToKillerForm($killerFormId, $questionId, '');
                }
                if($rUpdate===false){ $error = true; }
                // ------------------ update existing answers ----------------------
                $arrayOld = $q['answer'];
                if(is_array($arrayOld) && !empty($arrayOld)) {
                    foreach($arrayOld as $_auxOld) {
                        $rUpdate = ModelKQ::newInstance()->updateAnswer($_auxOld['id'], $_auxOld['text'], $_auxOld['punct']);
                        if($rUpdate===false){ $error = true; }
                    }
                }
                // ----------------- remove old answers ----------------------------
                $arrayRemove = $q['remove'];
                if(is_array($arrayRemove) && !empty($arrayRemove)) {
                    foreach($arrayRemove as $_auxRm) {
                        $rRemove = ModelKQ::newInstance()->removeAnswer($_auxRm['id']);
                        if($rRemove===false){ $error = true; }
                    }
                }
            }
        }

        if(!$error) {
            osc_add_flash_ok_message(__('Killer question form updated correctly', 'jobboard'), 'admin');
            return true;
        } else {
            osc_add_flash_message(__('Error updating Killer question form', 'jobboard'), 'admin');
            return false;
        }
    }
}
