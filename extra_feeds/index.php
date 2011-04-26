<?php
/*
Plugin Name: Extra feeds
Plugin URI: http://www.osclass.org/
Description: Extra feeds.
Version: 2.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: extra_feeds
*/

function feed_indeed() {

    echo '<?xml version="1.0" encoding="utf-8"?>
    <source>
    <publisher>'.osc_page_title().'</publisher>
    <publisherurl>'.osc_base_url().'</publisherurl>
    <lastBuildDate>'.date("D, j M Y G:i:s T").'</lastBuildDate>';

    if(osc_count_items()) {
        while(osc_has_items()) {
            $item = feed_get_job_data(osc_item());

            $salary = "";
            if(isset($item['i_salary_min']) && $item['i_salary_min']!='') {
                $salary = $item['i_salary_min'];
            }
            if(isset($item['i_salary_max']) && $item['i_salary_max']!='') {
                if($salary!="") { $salary .= ' - '; };
                $salary .= $item['i_salary_max'];
            }
            if(isset($item['e_salary_period']) && $item['e_slary_period']!='') {
                if($salary!="") {
                    $salary .= ' ';
                    $salary .= $item['e_salary_period'];
                }
            }

            $locale = current($item['locale']);
            if(isset($locale['s_desired_exp']) && $locale['s_desired_exp']!='') {
                $experience = $locale['s_desired_exp'];
            } else {
                $experience = '';
            }
            if(isset($locale['s_studies']) && $locale['s_studies']!='') {
                $education = $locale['s_studies'];
            } else {
                $education = '';
            }

            echo '<job>
            <title><![CDATA['.osc_item_title().']]></title>
            <date><![CDATA['.osc_item_pub_date().']]></date>
            <referencenumber><![CDATA['.osc_item_id().']]></referencenumber>
            <url><![CDATA['.osc_item_url().']]></url>
            <company><![CDATA['.((isset($item['s_company_name']) && $item['s_company_name']!=NULL)?$item['s_company_name']:'').']]></company>
            <city><![CDATA['.((osc_item_city()!=NULL)?osc_item_city():'').']]></city>
            <state><![CDATA['.((osc_item_region()!=NULL)?osc_item_region():'').']]></state>
            <country><![CDATA['.((osc_item_country()!=NULL)?osc_item_country():'').']]></country>
            <postalcode><![CDATA['.((osc_item_zip()!=NULL)?osc_item_zip():'').']]></postalcode>
            <description><![CDATA['.((osc_item_description()!=NULL)?osc_item_description():'').']]></description>
            <salary><![CDATA['.$salary.']]></salary>
            <education><![CDATA['.$education.']]></education>
            <jobtype><![CDATA['.((isset($item['e_position_type']) && $item['e_position_type']!=NULL)?$item['e_position_type']:'').']]></jobtype>
            <category><![CDATA[]]></category>
            <experience><![CDATA['.$experience.']]></experience>
            </job>';
        }
    }
    echo '</source>';

}

function feed_trovit_houses() {

    echo '<?xml version="1.0" encoding="utf-8"?>
            <trovit>';

    if(osc_count_items()) {
        while(osc_has_items()) {
            $item = feed_get_house_data(osc_item());

            $date = date('d/m/Y');
            $time = date('H:i');

            if(preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})|', osc_item_pub_date(), $tmp)) {
                $date = $tmp[3]."/".$tmp[2]."/".$tmp[1];
                $time = $tmp[4].":".$tmp[5];
            }

            echo '<ad>
                <id><![CDATA['.osc_item_id().']]></id>
                <url><![CDATA['.osc_item_url().']]></url>
                <title><![CDATA['.osc_item_title().']]></title>
                <type><![CDATA['.((isset($item['e_type']))?$item['e_type']:'').']]></type>

                <agency><![CDATA['.((isset($item['s_agency']))?$item['s_agency']:'').']]></agency>
                <content><![CDATA['.osc_item_description().']]></content>

                <price><![CDATA['.osc_item_price().']]></price>
                <property_type><![CDATA['.((isset($item['property_type']))?$item['property_type']:'').']]></property_type>
                <floor_area unit="meters"><![CDATA['.((isset($item['s_square_meters']))?$item['s_square_meters']:'').']]></floor_area>
                <rooms><![CDATA['.((isset($item['i_num_rooms']))?$item['i_num_rooms']:'').']]></rooms>

                <bathrooms><![CDATA['.((isset($item['i_num_bathrooms']))?$item['i_num_bathrooms']:'').']]></bathrooms>
                <parking><![CDATA['.((isset($item['b_parking']))?$item['b_parking']:'0').']]></parking>

                <address><![CDATA['.((osc_item_address()!='')?osc_item_address():'').']]></address>
                <city><![CDATA['.((osc_item_city()!='')?osc_item_city():'').']]></city>

                <city_area><![CDATA['.((osc_item_city_area()!='')?osc_item_city_area():'').']]></city_area>
                <postcode><![CDATA['.((osc_item_zip()!='')?osc_item_zip():'').']]></postcode>
                <region><![CDATA['.((osc_item_region()!='')?osc_item_region():'').']]></region>

                <latitude><![CDATA['.((osc_item_latitude()!='')?osc_item_latitude():'').']]></latitude>
                <longitude><![CDATA['.((osc_item_longitude()!='')?osc_item_longitude():'').']]></longitude>

                <floor_number><![CDATA['.((isset($item['i_floor_number']))?$item['i_floor_number']:'').']]></floor_number>
                <plot_area><![CDATA['.((isset($item['i_plot_area']))?$item['i_plot_area']:'').']]></plot_area>
                <is_furnished><![CDATA['.((isset($item['b_furnished']))?$item['b_furnished']:'0').']]></is_furnished>
                <is_new><![CDATA['.((isset($item['b_new']))?$item['b_new']:'0').']]></is_new>
                <condition><![CDATA['.((isset($item['s_condition']))?$item['s_condition']:'').']]></condition>
                <year><![CDATA['.((isset($item['i_year']))?$item['i_year']:'').']]></year>
                <by_owner><![CDATA['.((isset($item['b_by_owner']))?$item['b_by_owner']:'0').']]></by_owner>';

            $res_string = '';
            if(osc_count_item_resources()>0) {
                while(osc_has_item_resources()) {
                    if(strpos(osc_resource_type(), 'image')!==FALSE) {
                        $res_string .= '<picture>
                                            <picture_url><![CDATA['.osc_resource_path().']]></picture_url>
                                            <picture_title><![CDATA['.osc_resource_name().']]></picture_title>
                                        </picture>';
                    }
                }
            }

            if($res_string!='') {
                echo '<pictures>'.$res_string.'</pictures>';
            }
                
            echo '
                <date><![CDATA['.$date.']]></date>
                <time><![CDATA['.$time.']]></time>
            </ad>';
        }
    }
    echo '</trovit>';
}

function feed_trovit_products() {

    echo '<?xml version="1.0" encoding="utf-8"?>
            <trovit>';

    if(osc_count_items()) {
        while(osc_has_items()) {

            $item = feed_get_product_data(osc_item());

            $date = date('d/m/Y');
            $time = date('H:i');

            if(preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})|', osc_item_pub_date(), $tmp)) {
                $date = $tmp[3]."/".$tmp[2]."/".$tmp[1];
                $time = $tmp[4].":".$tmp[5];
            }

            echo '<ad>
                    <id><![CDATA['.osc_item_id().']]></id>
                    <url><![CDATA['.osc_item_url().']]></url>
                    <title><![CDATA['.osc_item_title().']]></title>

                    <content><![CDATA['.osc_item_description().']]></content>

                    <price><![CDATA['.osc_item_price().']]></price>

                    <make><![CDATA['.((isset($item['s_make']))?$item['s_make']:'').']]></make>
                    <model><![CDATA['.((isset($item['s_model']))?$item['s_model']:'').']]></model>
                    <category><![CDATA['.((osc_item_category()!='')?osc_item_category():'').']]></category>

                    <address><![CDATA['.((osc_item_address()!='')?osc_item_address():'').']]></address>
                    <city><![CDATA['.((osc_item_city()!='')?osc_item_city():'').']]></city>

                    <city_area><![CDATA['.((osc_item_city_area()!='')?osc_item_city_area():'').']]></city_area>
                    <postcode><![CDATA['.((osc_item_zip()!='')?osc_item_zip():'').']]></postcode>
                    <region><![CDATA['.((osc_item_region()!='')?osc_item_region():'').']]></region>

                    <latitude><![CDATA['.((osc_item_latitude()!='')?osc_item_latitude():'').']]></latitude>
                    <longitude><![CDATA['.((osc_item_longitude()!='')?osc_item_longitude():'').']]></longitude>';


            $res_string = '';
            if(osc_count_item_resources()>0) {
                while(osc_has_item_resources()) {
                    if(strpos(osc_resource_type(), 'image')!==FALSE) {
                        $res_string .= '<picture>
                                            <picture_url><![CDATA['.osc_resource_path().']]></picture_url>
                                            <picture_title><![CDATA['.osc_resource_name().']]></picture_title>
                                        </picture>';
                    }
                }
            }

            if($res_string!='') {
                echo '<pictures>'.$res_string.'</pictures>';
            }
                
            echo '
                <date><![CDATA['.$date.']]></date>
                <time><![CDATA['.$time.']]></time>
            </ad>';
        }
    }
    echo '</trovit>';
}

function feed_trovit_jobs() {

    echo '<?xml version="1.0" encoding="utf-8"?>
            <trovit>';

    if(osc_count_items()) {
        while(osc_has_items()) {
            $item = feed_get_job_data(osc_item());

            $date = date('d/m/Y');
            $time = date('H:i');

            if(preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})|', osc_item_pub_date(), $tmp)) {
                $date = $tmp[3]."/".$tmp[2]."/".$tmp[1];
                $time = $tmp[4].":".$tmp[5];
            }

            echo '<ad>
                    <id><![CDATA['.osc_item_id().']]></id>
                    <url><![CDATA['.osc_item_url().']]></url>
                    <title><![CDATA['.osc_item_title().']]></title>

                    <content><![CDATA['.osc_item_description().']]></content>

                    <address><![CDATA['.((osc_item_address()!='')?osc_item_address():'').']]></address>
                    <city><![CDATA['.((osc_item_city()!='')?osc_item_city():'').']]></city>

                    <city_area><![CDATA['.((osc_item_city_area()!='')?osc_item_city_area():'').']]></city_area>
                    <postcode><![CDATA['.((osc_item_zip()!='')?osc_item_zip():'').']]></postcode>
                    <region><![CDATA['.((osc_item_region()!='')?osc_item_region():'').']]></region>

                    <latitude><![CDATA['.((osc_item_latitude()!='')?osc_item_latitude():'').']]></latitude>
                    <longitude><![CDATA['.((osc_item_longitude()!='')?osc_item_longitude():'').']]></longitude>

                    <salary><![CDATA['.((isset($item['i_salary_min']) && isset($item['i_salary_max']))?$item['i_salary_min'].' - '.$item['i_salary_max']:'').']]></salary>
                    <company><![CDATA['.((isset($item['s_company_name']))?$item['s_company_name']:'').']]></company>
                    <experience><![CDATA['.((isset($item['s_experience']))?$item['s_experience']:'').']]></experience>
                    <requirements><![CDATA['.((isset($item['s_requirements']))?$item['s_requirements']:'').']]></requirements>
                    <contract><![CDATA['.((isset($item['s_contract']))?$item['s_contract']:'').']]></contract>
                    <category><![CDATA['.((osc_item_category()!='')?osc_item_category():'').']]></category>';
            echo '
                <date><![CDATA['.$date.']]></date>
                <time><![CDATA['.$time.']]></time>
            </ad>';
        }
    }
    echo '</trovit>';
}


function feed_trovit_cars() {

    echo '<?xml version="1.0" encoding="utf-8"?>
            <trovit>';

    if(osc_count_items()) {
        while(osc_has_items()) {
            $item = feed_get_car_data(osc_item());

            $date = date('d/m/Y');
            $time = date('H:i');

            if(preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})|', osc_item_pub_date(), $tmp)) {
                $date = $tmp[3]."/".$tmp[2]."/".$tmp[1];
                $time = $tmp[4].":".$tmp[5];
            }

            echo '<ad>
                    <id><![CDATA['.osc_item_id().']]></id>
                    <url><![CDATA['.osc_item_url().']]></url>
                    <title><![CDATA['.osc_item_title().']]></title>

                    <content><![CDATA['.osc_item_description().']]></content>

                    <price><![CDATA['.osc_item_price().']]></price>

                    <make><![CDATA['.((isset($item['s_make']))?$item['s_make']:'').']]></make>
                    <model><![CDATA['.((isset($item['s_model']))?$item['s_model']:'').']]></model>
                    <color><![CDATA['.((isset($item['s_color']))?$item['s_color']:'').']]></color>

                    <mileage><![CDATA['.((isset($item['i_mileage']))?$item['i_mileage']:'').']]></mileage>
                    <doors><![CDATA['.((isset($item['i_doors']))?$item['i_doors']:'').']]></doors>
                    <fuel><![CDATA['.((isset($item['e_fuel']))?$item['e_fuel']:'').']]></fuel>
                    <transmission><![CDATA['.((isset($item['e_transmission']))?$item['e_transmission']:'').']]></transmission>
                    <engine_size><![CDATA['.((isset($item['i_engine_size']))?$item['i_engine_size']:'').']]></engine_size>
                    <cylinders><![CDATA['.((isset($item['i_cylinders']))?$item['i_cylinders']:'').']]></cylinders>
                    <power unit="'.((isset($item['e_power_unit']))?$item['e_power_unit']:'').'"><![CDATA['.((isset($item['i_power']))?$item['i_power']:'').']]></power>
                    <seats><![CDATA['.((isset($item['i_seats']))?$item['i_seats']:'').']]></seats>
                    <gears><![CDATA['.((isset($item['i_gears']))?$item['i_gears']:'').']]></gears>

                    <address><![CDATA['.((osc_item_address()!='')?osc_item_address():'').']]></address>
                    <city><![CDATA['.((osc_item_city()!='')?osc_item_city():'').']]></city>

                    <city_area><![CDATA['.((osc_item_city_area()!='')?osc_item_city_area():'').']]></city_area>
                    <postcode><![CDATA['.((osc_item_zip()!='')?osc_item_zip():'').']]></postcode>
                    <region><![CDATA['.((osc_item_region()!='')?osc_item_region():'').']]></region>

                    <latitude><![CDATA['.((osc_item_latitude()!='')?osc_item_latitude():'').']]></latitude>
                    <longitude><![CDATA['.((osc_item_longitude()!='')?osc_item_longitude():'').']]></longitude>';


            $res_string = '';
            if(osc_count_item_resources()>0) {
                while(osc_has_item_resources()) {
                    if(strpos(osc_resource_type(), 'image')!==FALSE) {
                        $res_string .= '<picture>
                                            <picture_url><![CDATA['.osc_resource_path().']]></picture_url>
                                            <picture_title><![CDATA['.osc_resource_name().']]></picture_title>
                                        </picture>';
                    }
                }
            }

            if($res_string!='') {
                echo '<pictures>'.$res_string.'</pictures>';
            }
                
            echo '
                <date><![CDATA['.$date.']]></date>
                <time><![CDATA['.$time.']]></time>
            </ad>';
        }
    }    
    echo '</trovit>';
}


function feed_google_jobs() {
 
    echo '<rss version ="2.0" xmlns:g="http://base.google.com/ns/1.0"> 
     
    <channel> 
	    <title>'.osc_page_title().'</title> 
	    <description>'.osc_page_description().'</description> 
	    <link>'.osc_base_url().'</link>';

    if(osc_count_items()) {
        while(osc_has_items()) {
            $item = feed_get_job_data(osc_item());

            $date = date('d/m/Y');
            $time = date('H:i');

            if(preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2})|', osc_item_pub_date(), $tmp)) {
                $date = $tmp[3]."/".$tmp[2]."/".$tmp[1];
            }

           


            echo '<item> 
            <title>'.osc_item_title().'</title> 
            <description>'.osc_item_description().'</description> 
            <g:education>'.((isset($item['s_studies']))?$item['s_studies']:'').'</g:education> 
            <g:employer>'.((isset($item['s_company_name']))?$item['s_company_name']:'').'</g:employer> 
            <g:id>'.osc_item_id().'</g:id> 
            <g:job_industry>'.((osc_item_category()!='')?osc_item_category():'').'</g:job_industry> 
            <g:job_type>'.((isset($item['s_contract']))?$item['s_contract']:'').'</g:job_type> 
            <link>'.osc_item_url().'</link> 
            <g:location>'.((osc_item_address()!='')?osc_item_address():'').', '.((osc_item_city()!='')?osc_item_city():'').', '.((osc_item_region()!='')?osc_item_region():'').', '.((osc_item_zip()!='')?osc_item_zip():'').' '.((osc_item_country()!='')?osc_item_country():'').'</g:location> 
            <g:publish_date>'.$date.'</g:publish_date> 
            <g:salary>'.((isset($item['i_salary_min']) && isset($item['i_salary_max']))?$item['i_salary_min'].' - '.$item['i_salary_max']:'').'</g:salary> 
            </item>';
        }
    }
    echo '</channel> 
    </rss>';

}


function feed_google_cars() {

    echo '<rss version ="2.0" xmlns:g="http://base.google.com/ns/1.0"> 
     
    <channel> 
	    <title>'.osc_page_title().'</title> 
	    <description>'.osc_page_description().'</description> 
	    <link>'.osc_base_url().'</link>';

    if(osc_count_items()) {
        while(osc_has_items()) {
            $item = feed_get_car_data(osc_item());

            $date = date('d/m/Y');
            $time = date('H:i');

            if(preg_match('|([0-9]{4})-([0-9]{2})-([0-9]{2})|', osc_item_pub_date(), $tmp)) {
                $date = $tmp[3]."/".$tmp[2]."/".$tmp[1];
            }

           


            echo '<item> 
            <title>'.osc_item_title().'</title> 
            <description>'.osc_item_description().'</description> 
            <g:id>'.osc_item_id().'</g:id> 
            <link>'.osc_item_url().'</link> 
            <g:location>'.((osc_item_address()!='')?osc_item_address():'').', '.((osc_item_city()!='')?osc_item_city():'').', '.((osc_item_region()!='')?osc_item_region():'').', '.((osc_item_zip()!='')?osc_item_zip():'').' '.((osc_item_country()!='')?osc_item_country():'').'</g:location> 
            <g:publish_date>'.$date.'</g:publish_date> 
            <g:color>'.((isset($item['s_color']))?$item['s_color']:'').'</g:color> 
            <g:condition>'.((isset($item['b_new']) && $item['b_new']==1)?'new':'used').'</g:condition>';
            if(osc_count_item_resources()>0) {
                while(osc_has_item_resources()) {
                    if(strpos(osc_resource_type(), 'image')!==FALSE) {
                        echo '<g:image_link>'.osc_resource_path().'</g:image_link>';
                    };
                };
            };
            echo '<g:make>'.((isset($item['s_make']))?$item['s_make']:'').'</g:make> 
            <g:mileage>'.((isset($item['i_mileage']))?$item['i_mileage']:'').'</g:mileage> 
            <g:model>'.((isset($item['s_model']))?$item['s_model']:'').'</g:model> 
            <g:price>'.((osc_item_price()!='')?osc_item_price():'').'</g:price> 
            <g:vehicle_type>'.((isset($item['s_name']))?$item['s_name']:'').'</g:vehicle_type> 
            <g:year>'.((isset($item['i_year']))?$item['i_year']:'').'</g:year>
            </item>';

        }
    }
    echo '</channel> 
    </rss>';

}



function feed_get_house_data($item) {
    $conn = getConnection() ;
    $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_house_attr WHERE fk_i_item_id = %d ", DB_TABLE_PREFIX, $item['pk_i_id']);
    if(count($detail)>0) {
        foreach($detail as $k => $v) {
            $item[$k] = $v;
        }
        $detail = $conn->osc_dbFetchResult("SELECT s_name as property_type FROM %st_item_house_property_type_attr WHERE pk_i_id = %d AND fk_c_locale_code = '%s'", DB_TABLE_PREFIX, $item['fk_i_property_type_id'], osc_language());
        if(count($detail)==0) {
            $detail = $conn->osc_dbFetchResult("SELECT s_name as property_type FROM %st_item_house_property_type_attr WHERE pk_i_id = %d ", DB_TABLE_PREFIX, $item['fk_i_property_type_id']);
        }
        $item['property_type'] = $detail['property_type'];
    }
    return $item;
}

function feed_get_car_data($item) {
    $conn = getConnection() ;
    $detail = $conn->osc_dbFetchResult("SELECT make.s_name as s_make, model.s_name as s_model, car.* FROM %st_item_car_attr as car, %st_item_car_make_attr as make, %st_item_car_model_attr as model WHERE car.fk_i_item_id = %d, make.pk_i_id = car.fk_i_make_id AND model.pk_i_id = car.fk_i_model_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX, DB_TABLE_PREFIX, $item['pk_i_id']);
    if(count($detail)>0) {
        foreach($detail as $k => $v) {
            $item[$k] = $v;
        }
    }    
    return $item;        
}


function feed_get_job_data($item) {
    $conn = getConnection() ;
    $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_job_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item['pk_i_id']);
    if(count($detail)>0) {
        foreach($detail as $k => $v) {
            $item[$k] = $v;
        }
    }

    $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_job_description_attr WHERE fk_i_item_id = %d AND fk_c_locale_code = '%s'", DB_TABLE_PREFIX, osc_item_id(), osc_language());
    if(count($detail)==0) {
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_job_description_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
    }

    if(count($detail)>0) {
        foreach($detail as $k => $v) {
            $item[$k] = $v;
        }
    }
    return $item;        
}

function feed_get_product_data($item) {
    $conn = getConnection() ;
    $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_products_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item['pk_i_id']);
    $item['s_make'] = $detail['s_make'];
    $item['s_model'] = $detail['s_model'];

    //$detail = $conn->osc_dbFetchResult("SELECT * FROM %st_category_description WHERE fk_i_category_id = %d AND fk_c_locale_code = '%s'", DB_TABLE_PREFIX, $item['fk_i_category_id'], osc_language());
    /*if(count($detail)>0) {
        osc_item_category() = $detail['s_category'];
    } else {
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_category_description WHERE fk_i_category_id = %d", DB_TABLE_PREFIX, $item['fk_i_category_id']);
        osc_item_category() = $detail['s_category'];
    }*/
    return $item;        
}


// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), '');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');

osc_add_filter('feed_indeed', 'feed_indeed');
osc_add_filter('feed_trovit_houses', 'feed_trovit_houses');
osc_add_filter('feed_trovit_jobs', 'feed_trovit_jobs');
osc_add_filter('feed_trovit_products', 'feed_trovit_products');
osc_add_filter('feed_trovit_cars', 'feed_trovit_cars');
osc_add_filter('feed_google_jobs', 'feed_google_jobs');
osc_add_filter('feed_google_cars', 'feed_google_cars');

?>
