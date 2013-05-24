<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 * Modify Manage Applicants at oc-admin
 */
class JobboardManageApplicants
{
    /*
     * Add filtrers for Unread/Active/Interview/Rejected/Hired applicant status
     */
    static function applicants_shortcuts() {
        $shortcuts = array();
        $shortcuts['unread'] = array();
        $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsUnread();
        $shortcuts['unread']['total'] = $totalApplicantsShortcut;
        $shortcuts['unread']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&viewUnread=1';
        $shortcuts['unread']['active'] = false;
        if( Params::getParam('viewUnread') ) {
            $shortcuts['unread']['active'] = true;
        }
        $shortcuts['unread']['text'] = sprintf(__('Unread (%1$s)'), $totalApplicantsShortcut);
        $shortcuts['active'] = array();
        $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('0');
        $shortcuts['active']['total'] = $totalApplicantsShortcut;
        $shortcuts['active']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=0';
        $shortcuts['active']['active'] = false;
        if( Params::getParam('statusId') == '0' && !Params::getParam('viewUnread') ) {
            $shortcuts['active']['active'] = true;
        }
        $shortcuts['active']['text'] = sprintf(__('Active (%1$s)'), $totalApplicantsShortcut);
        $shortcuts['interview'] = array();
        $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('1');
        $shortcuts['interview']['total'] = $totalApplicantsShortcut;
        $shortcuts['interview']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=1';
        $shortcuts['interview']['active'] = false;
        if( Params::getParam('statusId') == '1' ) {
            $shortcuts['interview']['active'] = true;
        }
        $shortcuts['interview']['text'] = sprintf(__('Interview (%1$s)'), $totalApplicantsShortcut);
        $shortcuts['rejected'] = array();
        $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('2');
        $shortcuts['rejected']['total'] = $totalApplicantsShortcut;
        $shortcuts['rejected']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=2';
        $shortcuts['rejected']['active'] = false;
        if( Params::getParam('statusId') == '2' ) {
            $shortcuts['rejected']['active'] = true;
        }
        $shortcuts['rejected']['text'] = sprintf(__('Rejected (%1$s)'), $totalApplicantsShortcut);
        $shortcuts['hired'] = array();
        $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('3');
        $shortcuts['hired']['total'] = $totalApplicantsShortcut;
        $shortcuts['hired']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=3';
        $shortcuts['hired']['active'] = false;
        if( Params::getParam('statusId') == '3' ) {
            $shortcuts['hired']['active'] = true;
        }
        $shortcuts['hired']['text'] = sprintf(__('Hired (%1$s)'), $totalApplicantsShortcut);
        return $shortcuts;
    }

}