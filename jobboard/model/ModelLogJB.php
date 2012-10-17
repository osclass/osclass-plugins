<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.') ;

    class ModelLogJB extends Log
    {
        private static $instance;
        protected $section;
        protected $who;
        protected $whoID;

        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct()
        {
            parent::__construct();
            $this->section = 'jobboard';
            $this->who     = osc_logged_admin_username();
            $this->whoID   = osc_logged_admin_id();
        }

        public function logJobboard($action, $id, $content)
        {
            return $this->insertLog($this->section, $action, $id, $content, $this->who, $this->whoID);
        }

        public function confirmDelete($oSection, $oAction, $oId, $dSection)
        {
            return $this->dao->update($this->getTableName(), array('s_section' => $dSection), array('s_section' => $oSection, 's_action' => $oAction, 'fk_i_id' => $oId) );
        }

        public function getActivity($limit = 10)
        {
            $this->dao->select();
            $this->dao->from($this->getTableName());
            $this->dao->where('s_section', 'jobboard');
            $this->dao->orderBy('dt_date', 'DESC');
            $this->dao->limit(0, $limit);
            $result = $this->dao->get();

            if(  $result === false ) {
                return array();
            }

            return $result->result();
        }
    }

    // End of file: ./jobboard/model/ModelLogJB.php