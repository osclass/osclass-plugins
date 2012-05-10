<?php
function get_realestate_attributes(){
    $locale = osc_current_user_locale();
    $return = array('attributes','other_attributes');
    $detail = ModelRealEstate::newInstance()->getAttributes( osc_item_id() );
    $keys = array_keys($detail) ;
    if(count($keys) == 1 && $keys[0] == 'locale' && is_null($detail[0]['locale']) ){
        // nothing to do
        return false;
    } 
    if(@$detail['e_type'] != "") {
        $return['attributes']['type'] = array(
                 'label' =>__('Type', 'realestate_attributes')
                ,'value' => @$detail['e_type']
            );
    }
    if(@$detail['locale'][$locale]['s_name'] != "") {
        $return['attributes']['property_type'] = array(
                 'label' =>__('Property type', 'realestate_attributes')
                ,'value' => @$detail['locale'][$locale]['s_name']
            );
    }
    if(@$detail['i_num_rooms'] != "") {
        $return['attributes']['rooms'] = array(
                 'label' =>__('Num. Rooms', 'realestate_attributes')
                ,'value' => @$detail['i_num_rooms']
            );
    }
    if(@$detail['i_num_bathrooms'] != "") {
        $return['attributes']['bathrooms'] = array(
                 'label' =>__('Num. Bathrooms', 'realestate_attributes')
                ,'value' => @$detail['i_num_bathrooms']
            );
    }
    if(@$detail['e_status'] != "") {
        $return['attributes']['status'] = array(
                 'label' =>__('Status', 'realestate_attributes')
                ,'value' => @$detail['e_status']
            );
    }
    if(@$detail['s_square_meters'] != "") {
        $return['attributes']['square_meters'] = array(
                 'label' =>__('Square Meters', 'realestate_attributes')
                ,'value' => @$detail['s_square_meters']
            );
    }
    if(@$detail['i_plot_area'] != "") {
        $return['attributes']['plot_area'] = array(
                 'label' =>__('Square Meters (total)', 'realestate_attributes')
                ,'value' => @$detail['i_plot_area']
            );
    }
    if(@$detail['i_num_floors'] != "") {
        $return['attributes']['floors'] = array(
                 'label' =>__('Num. Floors', 'realestate_attributes')
                ,'value' => @$detail['i_num_floors']
            );
    }
    if(@$detail['i_year'] != "") {
        $return['attributes']['year'] = array(
                 'label' =>__('Construction Year', 'realestate_attributes')
                ,'value' => @$detail['i_year']
            );
    }
    if(@$detail['s_condition'] != "") {
        $return['attributes']['year'] = array(
                 'label' =>__('Condition', 'realestate_attributes')
                ,'value' => @$detail['s_condition']
            );
    }
    if(@$detail['s_agency'] != "") {
        $return['attributes']['year'] = array(
                 'label' =>__('Agency', 'realestate_attributes')
                ,'value' => @$detail['s_agency']
            );
    }
    if(@$detail['s_agency'] != "") {
        $return['attributes']['year'] = array(
                 'label' =>__('Agency', 'realestate_attributes')
                ,'value' => @$detail['s_agency']
            );
    }
    if(@$detail['i_floor_number'] != "") {
        $return['attributes']['year'] = array(
                 'label' =>__('Floor Number', 'realestate_attributes')
                ,'value' => @$detail['i_floor_number']
            );
    }
    if(@@$detail['locale'][$locale]['s_transport'] != "") {
        $return['attributes']['year'] = array(
                 'label' =>__('Transport', 'realestate_attributes')
                ,'value' => @@$detail['locale'][$locale]['s_transport']
            );
    }
    if(@@$detail['locale'][$locale]['s_zone'] != "") {
        $return['attributes']['year'] = array(
                 'label' =>__('Zone', 'realestate_attributes')
                ,'value' => @@$detail['locale'][$locale]['s_zone']
            );
    }
    //other attributes
    if(@$detail['b_heating']) {
        $return['other_attributes']['b_heating'] = array(
                     'label' =>__('Heating', 'realestate_attributes')
                    ,'value' => true
                );
    }
    if(@$detail['b_air_condition']) {
        $return['other_attributes']['b_air_condition'] = array(
                         'label' =>__('Air Condition', 'realestate_attributes')
                        ,'value' => true
                    );
    }
    if(@$detail['b_elevator']) {
        $return['other_attributes']['b_elevator'] = array(
                         'label' =>__('Elevator', 'realestate_attributes')
                        ,'value' => true
                    );
    }
    if(@$detail['b_terrace']) {
        $return['other_attributes']['b_terrace'] = array(
                         'label' =>__('Terrace', 'realestate_attributes')
                        ,'value' => true
                    );
    }
    if(@$detail['b_parking']) {
        $return['other_attributes']['b_parking'] = array(
                         'label' =>__('Parking', 'realestate_attributes')
                        ,'value' => true
                    );
    }
    if(@$detail['b_furnished']) {
        $return['other_attributes']['b_furnished'] = array(
                         'label' =>__('Furnished', 'realestate_attributes')
                        ,'value' => true
                    );
    }
    if(@$detail['b_new']) {
        $return['other_attributes']['b_new'] = array(
                         'label' =>__('New', 'realestate_attributes')
                        ,'value' => true
                    );
    }
    if(@$detail['b_by_owner']) {
        $return['other_attributes']['b_by_owner'] = array(
                         'label' =>__('By Owner', 'realestate_attributes')
                        ,'value' => true
                    );
    }
    return $return;
}
function table_realestate_attributes(){ 
    $detail = get_realestate_attributes();
    if($detail['attributes']){
    ?>
    <h3><?php _e('Realestate attributes', 'realestate_attributes') ; ?></h3>
    <div class="table-attributes">
        <table>
        <?php
            foreach($detail['attributes'] as $item){
                echo '<tr><td class="row-title">'.$item['label'].'</td><td>'.$item['value'].'</td></tr>';
            } 
        ?>
        </table>
    </div>
    <?php
    }
}
function table_realestate_other_attributes(){ 
    $detail = get_realestate_attributes();
    if($detail['other_attributes']){
    ?>
    <h4><?php _e('Other characteristics', 'realestate_attributes'); ?></h4>
    <ul class="list-other-attributes">
    <?php
        foreach($detail['other_attributes'] as $item){
            echo '<li><img src="'.osc_plugin_url(__FILE__).'img/tick.png"/>'.$item['label'].'</li>';
        } 
    ?>
    </ul>
    <?php
    }
}
function realestate_attributes(){
    echo '<div class="realestate-details">';
    table_realestate_attributes();
    table_realestate_other_attributes();    
    echo '<div class="clear"></div></div>';
}
?>