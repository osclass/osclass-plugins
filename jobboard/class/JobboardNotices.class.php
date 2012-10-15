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
            osc_add_filter('showNoticeTips', array(&$this, 'edit_corporate_page'));
            osc_add_filter('showNoticeTips', array(&$this, 'edit_colors_theme'));
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
            echo '<div class="grid-first-row grid-100"><div class="row-wrapper flashmessage-dashboard-jobboard"><div class="flashmessage flashmessage-inline">' .
                    $arrayNotice[$randIndex]. sprintf(__('<a id="dismiss-tip" data-notice-id="%1$s" href="#">Dismiss</a> to not show again.', 'jobboard'), $randIndex ) .
                    '<a class="btn ico btn-mini ico-close">x</a></div></div></div>';
        }

        // Jobboard tips
        /*
         * Filter - Empty jobs ok
         */
        function empty_jobs($notice)
        {
            // empty jobs! add new job please
            $notice_empty_jobs = osc_get_preference('notice_empty_jobs', 'jobboard');
            if( $notice_empty_jobs === '' ) {
                osc_set_preference('notice_empty_jobs', '1','jobboard');
                osc_reset_preferences();
                $notice_empty_jobs = osc_get_preference('notice_empty_jobs', 'jobboard');
            }

            if($notice_empty_jobs == '1'){
                $jobs = ModelJB::newInstance()->search(0,1);
                if(count($jobs)>0) {
                    osc_set_preference('notice_empty_jobs', '0', 'jobboard');
                }
                // ADD MESSAGE @TODO
                $notice['notice_empty_jobs'] = __('1 empty jobboard', 'jobboard');
            }
            return $notice;
        }

        /*
         * Filter - Edit corporate page ok
         */
        function edit_corporate_page($notice)
        {
            // update your corporativa page -> if t_page.dt_mod_date is null
            $notice_update_corporatepage = osc_get_preference('notice_edit_corporatepage', 'jobboard');
            if($notice_update_corporatepage=='') {
                osc_set_preference('notice_edit_corporatepage', '1', 'jobboard');
                osc_reset_preferences();
                $notice_update_corporatepage = osc_get_preference('notice_empty_jobs', 'jobboard');
            }
            if($notice_update_corporatepage=='1') {
                $corporatePage = Page::newInstance()->findByInternalName('corporate');
                if(is_null($corporatePage['dt_mod_date']) ) {
                    // ADD MESSAGE @TODO
                    $notice['notice_edit_corporate_page'] = __('2 edit your corporate page', 'jobboard');
                }
            }
            return $notice;
        }

        /*
         * Filter - Theme colors edited ok
         */
        function edit_colors_theme($notice)
        {
            // can change theme colors! +link
            if(function_exists('corporateboard_array_theme_options')) {
                $array = corporateboard_array_theme_options();
                $pageColorEdited = false;
                $aDefaults  = $array['defaults'];
                $aKeys      = $array['keys'];
                foreach($aKeys as $key => $value) {
                    if($aDefaults[$key] != osc_get_preference($value, 'jobboard') ) {
                        $pageColorEdited = true;
                        break;
                    }
                }
                if(!$pageColorEdited) {
                    $notice['notice_edit_colors_theme'] = __('4 Edit your theme colors url', 'jobboard');
                }
            }
            return $notice;
        }

        /*
         * Filter - Unread applicants ok
         * show allways
         */
        function unread_applicants($notice)
        {
            // unread messages notice
            $numUnread = count( ModelJB::newInstance()->search(0,1000, array('unread' => true) ) );
            if($numUnread>0) {
                $notice['notice_unread_applicants'] = __(sprintf('There are <b>%s</b> <a href="%s">unread applicants</a>', $numUnread, osc_admin_render_plugin_url('jobboard/people.php') . '&viewUnread=1'), 'jobboard');
            }
            return $notice;
        }
    }

    // init
    if( OC_ADMIN ) {
        $jn = new JobboardNotices();
    }

    // End of file: ./jobboard/class/JobboardNotices.class.php