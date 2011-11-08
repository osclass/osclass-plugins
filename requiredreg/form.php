<!-- uncomment those fields you want to use, uncomment next line if you want
to use drop-down menus for location -->

<!--<?php UserForm::location_javascript(); ?>-->

<label for="user_type"><?php _e('User type', 'modern') ; ?></label>
<?php UserForm::is_company_select() ; ?>
<br />

<label for="phoneMobile"><?php _e('Cell phone', 'modern') ; ?></label>
<?php UserForm::mobile_text() ; ?>
<br />

<!--<label for="phoneLand"><?php _e('Phone', 'modern') ; ?></label>
<?php UserForm::phone_land_text() ; ?>
<br />-->

<!--<label for="country"><?php _e('Country', 'modern') ; ?> *</label>
<?php UserForm::country_select(osc_get_countries()) ; ?>
<br />-->

<!--<label for="region"><?php _e('Region', 'modern') ; ?> *</label>
<?php UserForm::region_select(array()) ; ?>
<br />-->

<!--<label for="city"><?php _e('City', 'modern') ; ?> *</label>
<?php UserForm::city_select(array()) ; ?>
<br />-->

<!--<label for="city_area"><?php _e('City area', 'modern') ; ?></label>
<?php UserForm::city_area_text() ; ?>
<br />-->

<!--<label for="address"><?php _e('Address', 'modern') ; ?></label>
<?php UserForm::address_text() ; ?>
<br />-->

<label for="webSite"><?php _e('Website', 'modern') ; ?></label>
<?php UserForm::website_text() ; ?>
<br />

<script>
    $(document).ready(function(){
        $("#s_phone_mobile").rules("add", {required: true, messages: { required: "Mobile phone is required" }});
        /*$("#s_phone_land").rules("add", {required: true, messages: { required: "Land phone is required" }});*/
        /*$("#countryId").rules("add", {required: true, messages: { required: "Country is required" }});*/
        /*$("#region").rules("add", {required: true, messages: { required: "Region is required" }});*/
        /*$("#city").rules("add", {required: true, messages: { required: "City is required" }});*/
        /*$("#cityArea").rules("add", {required: true, messages: { required: "City area is required" }});*/
        $("#s_website").rules("add", {required: true, messages: { required: "Website is required" }});
    }); 
</Script>