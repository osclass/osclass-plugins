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

     class MblItemForm extends ItemForm {
        
        static public function title_input($name, $locale = 'en_US', $value = '')
        {
            $locale = osc_current_user_locale();
            parent::generic_input_text($name . '[' . $locale . ']'.$locale, $value) ;
            return true ;
        }
        
        static public function multilanguage_title_description($locales = null, $item = null) 
        {
            if($item==null) { $item = osc_item(); }
            
            $locale = osc_current_user_locale();
            echo '<fieldset data-role="fieldcontain"><label for="title['.$locale.']">' . __('Title') . ' *</label>';
            $title = (isset($item) && isset($item['locale'][$locale]) && isset($item['locale'][$locale]['s_title'])) ? $item['locale'][$locale]['s_title'] : '' ;
            if( Session::newInstance()->_getForm('title') != "" ) {
                $title_ = Session::newInstance()->_getForm('title');
                if( $title_[$locale] != "" ){
                    $title = $title_[$locale];
                }
            }
            parent::title_input('title', $locale, $title);
            echo "</fieldset>";
            echo '<fieldset data-role="fieldcontain"><label for="description['.$locale.']">' . __('Description') . ' *</label>';
            $description = (isset($item) && isset($item['locale'][$locale]) && isset($item['locale'][$locale]['s_description'])) ? $item['locale'][$locale]['s_description'] : '';
            if( Session::newInstance()->_getForm('description') != "" ) {
                $description_ = Session::newInstance()->_getForm('description');
                if( $description_[$locale] != "" ){
                    $description = $description_[$locale];
                }
            }
            parent::description_textarea('description', $locale, $description);
            echo "</fieldset>";
        }
        
        static public function region_text_hidden()
        {
            $region   = '';
            $regionId = '';
            if( Session::newInstance()->_getForm('regionId') != "" ) {
                $regionId = Session::newInstance()->_getForm('regionId');
            }
            if( Session::newInstance()->_getForm('region') != "" ) {
                $region = Session::newInstance()->_getForm('region');
            }
            parent::generic_input_hidden('region', $region) ;
            parent::generic_input_hidden('regionId', $regionId) ;
        }
        
        static public function city_text_hidden()
        {
            $city   = '';
            $cityId = '';
            if( Session::newInstance()->_getForm('cityId') != "" ) {
                $cityId = Session::newInstance()->_getForm('cityId');
            }
            if( Session::newInstance()->_getForm('city') != "" ) {
                $city = Session::newInstance()->_getForm('city');
            }
            parent::generic_input_hidden('city', $city) ;
            parent::generic_input_hidden('cityId', $cityId) ;
        }
         
        
        static public function location_javascript($path = "front") {
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#countryId").live("change",function(){
            var pk_c_code = $(this).val();
            
            var url = '<?php echo osc_base_url(true)."?page=ajax&action=regions&countryId="; ?>' + pk_c_code;
            
            var result = '';

            if(pk_c_code != '') {

                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    success: function(data){
                        var length = data.length;
                        if(length > 0) {
                            for(key in data) {
                                result += '<li><a data-rel="back" onclick="select_region(\''+ data[key].s_name +'\',\''+ data[key].pk_i_id +'\');" value="' + data[key].pk_i_id + '">' + data[key].s_name + '</a></li>';
                            }
                        } else {
                        }
                        $("#list_regions").html(result);
                        $('#field_select_region').show();
                    }
                 });

             } else {
             }
        });

        $("#regionId").live("change",function(){
            var pk_c_code = $(this).val();
            
            var url = '<?php echo osc_base_url(true)."?page=ajax&action=cities&regionId="; ?>' + pk_c_code;

            var result = '';

            if(pk_c_code != '') {
                
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    success: function(data){
                        var length = data.length;
                        if(length > 0) {
                            for(key in data) {
                                result += '<li><a data-rel="back" onclick="select_city(\''+ data[key].s_name +'\',\''+ data[key].pk_i_id +'\');" value="' + data[key].pk_i_id + '">' + data[key].s_name + '</a></li>';
                            }
                        } else {
                        }
                        $("#list_cities").html(result);
                        
                        $('#field_select_city').show();
                        $('#field_select_city_area').show();
                        $('#field_select_address').show();
                    }
                 });
             } else {
             }
        });

    });
    
    function select_region(region_name, region_id){
        $('#a_select_region').find('span.ui-btn-text').text( region_name );
        $('#region').val(region_name);
        $('#regionId').val(region_id).trigger('change');
        $('.ui-dialog').dialog('close');
    }
    
    function select_city(city_name, city_id){
        $('#a_select_city').find('span.ui-btn-text').text( city_name );
        $('#city').val(city_name);
        $('#cityId').val(city_id);
//        $('.ui-dialog').dialog('close');
    }
    
    
    /**
     * Strip HTML tags to count number of visible characters.
     */
    function strip_tags(html) {
        if (arguments.length < 3) {
            html=html.replace(/<\/?(?!\!)[^>]*>/gi, '');
        } else {
            var allowed = arguments[1];
            var specified = eval("["+arguments[2]+"]");
            if (allowed){
                var regex='</?(?!(' + specified.join('|') + '))\b[^>]*>';
                html=html.replace(new RegExp(regex, 'gi'), '');
            } else{
                var regex='</?(' + specified.join('|') + ')\b[^>]*>';
                html=html.replace(new RegExp(regex, 'gi'), '');
            }
        }
        return html;
    }
    
    function delete_image(id, item_id,name, secret) {
        //alert(id + " - "+ item_id + " - "+name+" - "+secret);
        var result = confirm('<?php _e('This action can\\\'t be undone. Are you sure you want to continue?'); ?>');
        if(result) {
            $.ajax({
                type: "POST",
                url: '<?php echo osc_base_url(true); ?>?page=ajax&action=delete_image&id='+id+'&item='+item_id+'&code='+name+'&secret='+secret,
                dataType: 'json',
                success: function(data){
                    var class_type = "error";
                    if(data.success) {
                        $("div[name="+name+"]").remove();
                        class_type = "ok";
                    }
                    var flash = $("#flash_js");
                    var message = $('<div>').addClass('pubMessages').addClass(class_type).attr('id', 'FlashMessage').html(data.msg);
                    flash.html(message);
                    $("#FlashMessage").slideDown('slow').delay(3000).slideUp('slow');
                }
            });
        }
    }
    
    
</script>
<?php
        }
        
     }

     // ------------------------------------------------------------------------

    if(!function_exists('mbl_breadcrumbs') ){
        
        function mbl_breadcrumbs() {

            // You could modify the separator
            $separator = " / ";

            $location = Rewrite::newInstance()->get_location();
            $section = Rewrite::newInstance()->get_section();
            // You DO NOT have to modify anything else
            if($location=='search') {
                $category = osc_search_category_id();
                if(count($category)==1) {
                    $category = $category[0];
                }
            } else if($location=='item' && osc_item()!=null) {
                $category = osc_item_category_id();
            }

            $bc_text = "";
            $deep_c = -1;
            if(isset($category)) {
                $cats = Category::newInstance()->toRootTree($category);
                
                if(count($cats)>0) {
                    foreach($cats as $cat) {
                        $deep_c++;
                        $bc_text .= $separator."<a href='".mbl_breadcrumbs_category_url($cat['pk_i_id'])."' ><span class='bc_level_".$deep_c."'>".$cat['s_name']."</span></a>";
                    }
                }
            } else if($location!='index' && $location!='') {
                $bc_text .= $separator."<span class='bc_location'>".$location."</span>";
            }

            if($location=='item' && osc_item()!=null) {
                $bc_text .= $separator."<a href='".osc_item_url()."' ><span class='bc_last'>".osc_item_title()."</span></a>";
            } else if($section!='') {
                $bc_text .= $separator."<span class='bc_last'>".$section."</span>";
            } else {
                $bc_text = str_replace('bc_level_'.$deep_c, 'bc_last', str_replace('bc_location', 'bc_last', $bc_text));
            }

            echo $bc_text;

        }

        function mbl_breadcrumbs_category_url($category_id) {
            $path = '' ;
            if ( osc_rewrite_enabled() ) {
                if ($category_id != '') {
                    $category = Category::newInstance()->hierarchy($category_id) ;
                    $sanitized_category = "" ;
                    for ($i = count($category); $i > 0; $i--) {
                        $sanitized_category .= $category[$i - 1]['s_slug'] . '/' ;
                    }
                    $path = osc_base_url() . $sanitized_category ;
                }
            } else {
                $path = sprintf( osc_base_url(true) . '?page=search&sCategory=%d', $category_id ) ;
            }
            return $path ;
        }
    }

    $mbl_item_formated_price = function_exists('mbl_item_formated_price');
    if(!$mbl_item_formated_price){

        function mbl_item_formated_price()
        {
            $price = osc_item_field("i_price");
            if ($price == null) return osc_apply_filter ('item_price_null', __('Check') ) ;
            if ($price == 0) return osc_apply_filter ('item_price_zero', __('Free') ) ;

            $price = $price/1000000;

            $currencyFormat = '{NUMBER}{CURRENCY}';
            $currencyFormat = str_replace('{NUMBER}', number_format($price, 0, osc_locale_dec_point(), osc_locale_thousands_sep()), $currencyFormat);
            $currencyFormat = str_replace('{CURRENCY}', osc_item_currency(), $currencyFormat);
            return osc_apply_filter('item_price', $currencyFormat ) ;
        }

    }
    if(!function_exists('mbl_search_pagination')){
        function mbl_search_pagination() {
            $params = array('text_prev' => sprintf(__('%s Previous', 'osc_mobile'), '&laquo;'),
                            'text_next' => sprintf(__('Next %s', 'osc_mobile'), '&raquo;')
                            ) ;
            $pagination = new mlb_pagination($params);
            return $pagination->doPagination();
        }
    }

    class mlb_pagination extends Pagination{

        public function get_links()
        {
            $pages = $this->get_pages();
            $links = array();

            $links[] = '<fieldset class="ui-grid-a">';
            if( !isset($pages['prev']) && isset($pages['next']) ) {
                $links[] = '<div class="ui-block-a"></div>';
                $links[] = '<div class="ui-block-b"><a href="' . str_replace('{PAGE}', $pages['next'], str_replace(urlencode('{PAGE}'), $pages['next'], $this->url)) . '" data-role="button">'.$this->text_next.'</a></div>';
            }
            if( isset($pages['prev']) && isset($pages['next']) ) {
                $links[] = '<div class="ui-block-a"><a href="' . str_replace('{PAGE}', $pages['prev'], str_replace(urlencode('{PAGE}'), $pages['prev'], $this->url)) . '" data-role="button">'.$this->text_prev.'</a></div>';
                $links[] = '<div class="ui-block-b"><a href="' . str_replace('{PAGE}', $pages['next'], str_replace(urlencode('{PAGE}'), $pages['next'], $this->url)) . '" data-role="button">'.$this->text_next.'</a></div>';
            }
            if( isset($pages['prev']) && !isset($pages['next']) ) {
                $links[] = '<div class="ui-block-a"><a href="' . str_replace('{PAGE}', $pages['prev'], str_replace(urlencode('{PAGE}'), $pages['prev'], $this->url)) . '" data-role="button">'.$this->text_prev.'</a></div>';
            }
            $links[] = '</fieldset>';
            return $links;
        }
    }
    
     if(!function_exists('add_logo_header')){
        function add_logo_header() {
            $html = '<img border="0" alt="' . osc_page_title() . '" src="' . osc_current_web_theme_url('images/logo.jpg') . '">';
            $js = " <script>
                    $(document).ready(function () {
                        $('#logo').html('".$html."');
                    });
                 </script>";

            if( file_exists( WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg" ) ) {
                echo $js;
            }
        }

        osc_add_hook("header", "add_logo_header");
     }
     
     if( !function_exists('meta_title') ) {
         function meta_title( ) {
            $location = Rewrite::newInstance()->get_location();
            $section  = Rewrite::newInstance()->get_section();

            switch ($location) {
                case ('item'):
                    switch ($section) {
                        case 'item_add':    $text = __('Publish an item','osc_mobile') . ' - ' . osc_page_title(); break;
                        case 'item_edit':   $text = __('Edit your item','osc_mobile') . ' - ' . osc_page_title(); break;
                        case 'send_friend': $text = __('Send to a friend','osc_mobile') . ' - ' . osc_item_title() . ' - ' . osc_page_title(); break;
                        case 'contact':     $text = __('Contact seller','osc_mobile') . ' - ' . osc_item_title() . ' - ' . osc_page_title(); break;
                        default:            $text = osc_item_title() . ' - ' . osc_page_title(); break;
                    }
                break;
                case('page'):
                    $text = osc_static_page_title() . ' - ' . osc_page_title();
                break;
                case('search'):
                    $region   = Params::getParam('sRegion');
                    $city     = Params::getParam('sCity');
                    $pattern  = Params::getParam('sPattern');
                    $category = osc_search_category_id();
                    $category = ((count($category) == 1) ? $category[0] : '');
                    $s_page = '';
                    $i_page = Params::getParam('iPage');

                    if($i_page != '' && $i_page > 0) {
                        $s_page = __('page', 'osc_mobile') . ' ' . ($i_page + 1) . ' - ';
                    }

                    $b_show_all = ($region == '' && $city == '' & $pattern == '' && $category == '');
                    $b_category = ($category != '');
                    $b_pattern  = ($pattern != '');
                    $b_city     = ($city != '');
                    $b_region   = ($region != '');

                    if($b_show_all) {
                        $text = __('Show all items', 'osc_mobile') . ' - ' . $s_page . osc_page_title();
                    }

                    $result = '';
                    if($b_pattern) {
                        $result .= $pattern . ' &raquo; ';
                    }

                    if($b_category) {
                        $list        = array();
                        $aCategories = Category::newInstance()->toRootTree($category);
                        if(count($aCategories) > 0) {
                            foreach ($aCategories as $single) {
                                $list[] = $single['s_name'];
                            }
                            $result .= implode(' &raquo; ', $list) . ' &raquo; ';
                        }
                    }

                    if($b_city) {
                        $result .= $city . ' &raquo; ';
                    }

                    if($b_region) {
                        $result .= $region . ' &raquo; ';
                    }

                    $result = preg_replace('|\s?&raquo;\s$|', '', $result);

                    if($result == '') {
                        $result = __('Search', 'osc_mobile');
                    }

                    $text = $result . ' - ' . $s_page . osc_page_title();
                break;
                case('login'):
                    switch ($section) {
                        case('recover'): $text = __('Recover your password','osc_mobile') . ' - ' . osc_page_title();
                        default:         $text = __('Login','osc_mobile') . ' - ' . osc_page_title();
                    }
                break;
                case('register'):
                    $text = __('Create a new account','osc_mobile') . ' - ' . osc_page_title();
                break;
                case('user'):
                    switch ($section) {
                        case('dashboard'):       $text = __('Dashboard','osc_mobile') . ' - ' . osc_page_title(); break;
                        case('items'):           $text = __('Manage my items','osc_mobile') . ' - ' . osc_page_title(); break;
                        case('alerts'):          $text = __('Manage my alerts','osc_mobile') . ' - ' . osc_page_title(); break;
                        case('profile'):         $text = __('Update my profile','osc_mobile') . ' - ' . osc_page_title(); break;
                        case('change_email'):    $text = __('Change my email','osc_mobile') . ' - ' . osc_page_title(); break;
                        case('change_password'): $text = __('Change my password','osc_mobile') . ' - ' . osc_page_title(); break;
                        case('forgot'):          $text = __('Recover my password','osc_mobile') . ' - ' . osc_page_title(); break;
                        default:                 $text = osc_page_title(); break;
                    }
                break;
                case('contact'):
                    $text = __('Contact','osc_mobile') . ' - ' . osc_page_title();
                break;
                default:
                    $text = osc_page_title();
                break;
            }
            
            $text = str_replace('"', "'", $text);
            return ($text);
         }
     }

     if( !function_exists('meta_description') ) {
         function meta_description( ) {
            $location = Rewrite::newInstance()->get_location();
            $section  = Rewrite::newInstance()->get_section();
            $text = '';

            switch ($location) {
                case ('item'):
                    switch ($section) {
                        case 'item_add':    $text = ''; break;
                        case 'item_edit':   $text = ''; break;
                        case 'send_friend': $text = ''; break;
                        case 'contact':     $text = ''; break;
                        default:
                            $text = osc_item_category() . ', ' . osc_highlight(osc_item_description(), 140) . '..., ' . osc_item_category();
                            break;
                    }
                break;
                case('page'):
                    $text = osc_highlight(strip_tags(osc_static_page_text()), 140);
                break;
                case('search'):
                    $result = '';

                    if(osc_count_items() == 0) {
                        $text = '';
                    }

                    if(osc_has_items ()) {
                        $result = osc_item_category() . ', ' . osc_highlight(strip_tags(osc_item_description()), 140) . '..., ' . osc_item_category();
                    }

                    osc_reset_items();
                    $text = $result;
                case(''): // home
                    $result = '';

                    if(osc_count_latest_items() == 0) {
                        $text = '';
                    }

                    if(osc_has_latest_items()) {
                        $result = osc_item_category() . ', ' . osc_highlight(strip_tags(osc_item_description()), 140) . '..., ' . osc_item_category();
                    }

                    osc_reset_items();
                    $text = $result;
                break;
            }
            
            $text = str_replace('"', "'", $text);
            return ($text);
         }
     }
     
    function time_diff($s)
    {
        $s = substr($s, 0, -3);
        $_s = strtotime($s);
        
        $s = time()-$_s;
        
        $m=0;$hr=0;$d=0;
        $td="now";
        if($s>59) {
            $m = (int)($s/60);
            $s = $s-($m*60); // sec left over
            $td = "$m min";
        }
        if($m>59){
            $hr = (int)($m/60);
            $m = $m-($hr*60); // min left over
            $td = "$hr hr"; if($hr>1) $td .= "s";
            if($m>0) $td .= ", $m min";
        }
        if($hr>23){
            $d = (int)($hr/24);
            $hr = $hr-($d*24); // hr left over
            $td = "$d day"; if($d>1) $td .= "s";
            if($d<3){
                if($hr>0) $td .= ", $hr hr"; if($hr>1) $td .= "s";
            } 
        }
        return $td;
    }
  
?>