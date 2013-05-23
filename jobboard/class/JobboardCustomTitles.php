<?php

class JobboardCustomTitles
{
    public function __construct()
    {
        osc_add_filter('custom_plugin_title',   array(&$this, 'jobboard_title'));
    }

    function jobboard_title($string)
    {
        if(Params::getParam('page') == 'plugins') {
            if(Params::getParam('file') == 'jobboard/people_detail.php') {
                $string = __('Applicant', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
            }
            if(Params::getParam('file') == 'jobboard/dashboard.php') {
                $string = __('Dashboard', 'jobboard');
            }
            if(Params::getParam('file') == 'jobboard/resume_download.php') {
                $string = __('Download resumes', 'jobboard');
            }
            if(Params::getParam('file') == 'jobboard/people.php') {
                $string = __('Applicants', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
            }
        }
        return $string;
    }
}