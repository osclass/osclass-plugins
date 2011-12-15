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
    $wSizeImage = 255;
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

<title><?php echo meta_title() ; ?></title>
<meta name="title" content="<?php echo meta_title() ; ?>" />
<meta name="description" content="<?php echo meta_description() ; ?>" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="Fri, Jan 01 1970 00:00:00 GMT" />

<script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.min.js') ; ?>"></script>

<script type="text/javascript">
    $(document).bind("mobileinit", function(){
        $.mobile.ajaxEnabled = false;
    });
</script>

<link href="<?php echo osc_current_web_theme_styles_url('jquery.mobile-1.0.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo osc_current_web_theme_styles_url('jquery.mobile.structure-1.0.css') ?>" rel="stylesheet" type="text/css" />

<link href="<?php echo osc_current_web_theme_styles_url('style.css') ?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.mobile-1.0.js') ; ?>"></script>
<script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('global.js') ; ?>"></script>

<?php osc_run_hook('header') ; ?>