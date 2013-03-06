
    tinyMCE.init({
        mode : "none",
        theme : richedit.theme,
        skin: richedit.skin,
        width: richedit.width,
        height: richedit.height,
        skin_variant : richedit.skin_variant,
        theme_advanced_buttons1 : richedit.theme_advanced_buttons1,
        theme_advanced_buttons2 : richedit.theme_advanced_buttons2,
        theme_advanced_buttons3 : richedit.theme_advanced_buttons3,
        theme_advanced_toolbar_align : "left",
        theme_advanced_toolbar_location : "top",
        plugins : richedit.plugins
    });
    
    $(document).ready(function () {
        $("textarea[id^=description]").each(function(){
            tinyMCE.execCommand("mceAddControl", true, this.id);
        });
    });
