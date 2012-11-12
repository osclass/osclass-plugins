<?php

    class JobboardNotices
    {
        public function __construct()
        {
            $this->init();
        }

        public function init()
        {
            // hooks
            osc_add_hook('jobboard_header_dashboard', array(&$this, 'print_notices'), 10);
            osc_add_hook('jobboard_header_dashboard', array(&$this, 'print_notices_tips'), 10);

            // ajax dismiss
            osc_add_hook('ajax_admin_dismiss_tip', array(&$this, 'ajax_dismiss_tip'));

            // tip filters
            osc_add_filter('showNotice', array(&$this, 'unread_applicants'));
            osc_add_filter('showNoticeTips', array(&$this, 'empty_jobs'));
            osc_add_filter('showNoticeTips', array(&$this, 'publish_job_offers'));
            osc_add_filter('showNoticeTips', array(&$this, 'usage_applicants_filters'));
            osc_add_filter('showNoticeTips', array(&$this, 'zero_applicant_notes'));
            osc_add_filter('showNoticeTips', array(&$this, 'add_static_pages'));
            osc_add_filter('showNoticeTips', array(&$this, 'add_static_pages_company_value'));
            osc_add_filter('showNoticeTips', array(&$this, 'edit_corporate_page'));
            osc_add_filter('showNoticeTips', array(&$this, 'edit_legal_page'));
        }

        public function uninstall()
        {
            osc_delete_preference('notice_tips_dismissed', 'jobboard');
            osc_delete_preference('notice_empty_jobs', 'jobboard');
            osc_delete_preference('notice_edit_corporate', 'jobboard');
            osc_delete_preference('notice_edit_legal', 'jobboard');
        }

        /*
         * AJAX - add notice_tip_id to array of dismissed notice
         */
        function ajax_dismiss_tip()
        {
            $notice_id = Params::getParam('noticeID');
            $array     = json_decode( osc_get_preference('notice_tips_dismissed', 'jobboard'), true);

            if(!is_array($array)) {
                $array = array();
            }

            $array_[$notice_id] = 1;
            $merge = array_merge($array, $array_);

            if( osc_set_preference('notice_tips_dismissed', json_encode($merge), 'jobboard') ) {
                echo json_encode(array('error' => false));
                return true;
            }

            echo json_encode(array('error' => true));
            return false;
        }

        /*
         * Show notice at oc-admin
         */
        function print_notices()
        {
            $arrayNotice = osc_apply_filter('showNotice', array());
            if( count($arrayNotice) === 0 ) {
                return false;
            }

            $randIndex = array_rand($arrayNotice);
            echo '<div class="grid-first-row grid-100"><div class="row-wrapper flashmessage-dashboard-jobboard"><div class="flashmessage flashmessage-inline">'.
                    $arrayNotice[$randIndex].'<a class="btn ico btn-mini ico-close">x</a></div></div></div>';
        }

        /*
         * Show notice tips at oc-admin, this notice can be dismissed!
         */
        function print_notices_tips()
        {
            $arrayNotice = osc_apply_filter('showNoticeTips', array());
            // remove dismiss tips
            $aDismiss = json_decode(osc_get_preference('notice_tips_dismissed', 'jobboard'), true);
            // remove dismissed tips
            if( is_array($aDismiss) ) {
                foreach($aDismiss as $k => $v) {
                    unset($arrayNotice[$k]);
                }
            }

            if( count($arrayNotice) === 0) {
                return false;
            }

            $randIndex = array_rand($arrayNotice);
            echo '<div class="grid-first-row grid-100"><div class="row-wrapper flashmessage-dashboard-jobboard flashmessage-dismiss">'. sprintf(__('<div class="dismiss-tip"><a id="dismiss-tip" data-notice-id="%1$s" href="#">Dismiss</a> to not show again.</div>', 'jobboard'), $randIndex ) .'<div class="flashmessage flashmessage-inline">' .
                    $arrayNotice[$randIndex].'<a class="btn ico btn-mini ico-close">x</a></div></div></div>';
        }

        function empty_jobs($notice)
        {
            // empty jobs! add new job please
            $notice_empty_jobs = osc_get_preference('notice_empty_jobs', 'jobboard');
            if( $notice_empty_jobs === '' ) {
                osc_set_preference('notice_empty_jobs', '1','jobboard');
                osc_reset_preferences();
                $notice_empty_jobs = osc_get_preference('notice_empty_jobs', 'jobboard');
            }

            if($notice_empty_jobs == '1') {
                $jobs = ModelJB::newInstance()->search(0, 1);
                if(count($jobs)>0) {
                    osc_set_preference('notice_empty_jobs', '0', 'jobboard');
                }
                // ADD MESSAGE @TODO
                $notice['notice_empty_jobs'] = sprintf(__('You haven’t published any job offer yet. <a href="%1$s">Start now here</a>.', 'jobboard'), osc_admin_base_url(true) . '?page=items&action=post');
            }
            return $notice;
        }

        function publish_job_offers($notice)
        {
            if( osc_get_preference('notice_empty_jobs', 'jobboard') == '1' ) {
                return $notice;
            }

            $mSearch   = new Search();
            $jobOffers = $mSearch->count();
            $numJobOffers = count($jobOffers);
            if( $numJobOffers === 0 ) {
                return $notice;
            }

            $notice['publish_job_offers'] = sprintf(__('You have published %1$s job offers. Add new one <a href="%2$s">here</a>.', 'jobboard'), $numJobOffers, osc_admin_base_url(true) . '?page=items&action=post');
            return $notice;
        }

        function usage_applicants_filters($notice)
        {
            $notice['usage_applicants_filters'] = __('Use filters to manage better all the candidates.', 'jobboard');
            return $notice;
        }

        function zero_applicant_notes($notice)
        {
            if( ModelJB::newInstance()->countTotalNotes() !== 0 ) {
                return $notice;
            }

            $notice['zero_applicant_notes'] = __('You haven’t taken any note yet - remember you can take notes and rate candidates.', 'jobboard');
            return $notice;
        }

        function add_static_pages($notice)
        {
            $notice['add_static_pages'] = sprintf(__('You can add new pages to your job board providing more information about your company. <a href="%1$s">Try it here</a>.', 'jobboard'), osc_admin_base_url(true) . '?page=page&action=add');
            return $notice;
        }

        function add_static_pages_company_value($notice)
        {
            $notice['add_static_pages_company_value'] = sprintf(__('Transmit the values of your company by <a href="%1$s">creating additional pages</a> in few seconds.', 'jobboard'), osc_admin_base_url(true) . '?page=page&action=add');
            return $notice;
        }

        function edit_corporate_page($notice)
        {
            // update your corporativa page -> if t_page.dt_mod_date is null
            $notice_corporate_page = osc_get_preference('notice_edit_corporate', 'jobboard');
            if( $notice_corporate_page == '' ) {
                osc_set_preference('notice_edit_corporate', '1', 'jobboard');
                osc_reset_preferences();
                $notice_corporate_page = osc_get_preference('notice_edit_corporate', 'jobboard');
            }
            if( $notice_corporate_page == '1' ) {
                $page = Page::newInstance()->findByInternalName('corporate');
                if( is_null($page['dt_mod_date']) ) {
                    $notice['notice_edit_corporate'] = sprintf(__('You haven’t edited the corporate website yet. <a href="%1$s">Do it now</a>!', 'jobboard'), osc_admin_base_url(true) . '?page=page&action=edit&id=' . $page['pk_i_id']);
                }
            }
            return $notice;
        }

        function edit_legal_page($notice)
        {
            $notice_legal_page = osc_get_preference('notice_edit_legal', 'jobboard');
            if( $notice_legal_page == '' ) {
                osc_set_preference('notice_edit_legal', '1', 'jobboard');
                osc_reset_preferences();
                $notice_legal_page = osc_get_preference('notice_edit_legal', 'jobboard');
            }
            if( $notice_legal_page == '1' ) {
                $page = Page::newInstance()->findByInternalName('legal');
                if( is_null($page['dt_mod_date']) ) {
                    $notice['notice_edit_legal'] = sprintf(__('<a href="%1$s">Edit</a> your legal site - it is still empty!', 'jobboard'), osc_admin_base_url(true) . '?page=page&action=edit&id=' . $page['pk_i_id']);
                }
            }
            return $notice;
        }

        /**
         * Show if there are unread applicants
         */
        function unread_applicants($notice)
        {
            // unread messages notice
            $numUnread = count( ModelJB::newInstance()->search(0,1000, array('unread' => true) ) );
            if($numUnread>0) {
                $notice['notice_unread_applicants'] = sprintf(__('There are <strong>%1$s</strong> new applicants. <a href="%2$s">See them all</a>', 'jobboard'), $numUnread, osc_admin_render_plugin_url('jobboard/people.php') . '&viewUnread=1');
            }
            return $notice;
        }
    }

    // init
    if( OC_ADMIN ) {
        $jn = new JobboardNotices();
    }

    // End of file: ./jobboard/class/JobboardNotices.class.php