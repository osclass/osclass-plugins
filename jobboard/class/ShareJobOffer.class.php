<?php

    class ShareJobOffer
    {
        public function __construct()
        {
            osc_add_hook('init_admin', array(&$this, 'sharejob'));
        }

        function sharejob()
        {
            if( is_numeric(Session::newInstance()->_get('jobboard_share_job')) ) {
                osc_add_hook('admin_footer', array(&$this, 'print_sharejob'));
            }
        }

        function print_sharejob()
        {
            // get data
            $this->get_share_job_data();

            $shareURL   = urlencode( osc_item_url() );
            $shareTitle = urlencode(sprintf(__('We are hiring! %1$s %2$s #osclasscom', 'jobboard'), osc_item_title(), osc_item_url()));

            $facebookShareUrl  = 'http://www.facebook.com/share.php?';
            $facebookShareUrl .= 'u=' . $shareURL;
            $facebookShareUrl .= '&t=' . $shareTitle;
            $facebookShareUrl .= '&id=' . $shareTitle;

            $twitterShareLink  = 'https://twitter.com/intent/tweet?';
            $twitterShareLink .= 'original_referer=' . $shareURL;
            $twitterShareLink .= '&source=tweetbutton';
            $twitterShareLink .= '&text=' . urlencode(sprintf(__('We are hiring! %1$s %2$s #osclasscom', 'jobboard'), osc_item_title(), osc_item_url()));
            $twitterShareLink .= '&url=' . $shareURL;

            $linkedinShareLink  = 'http://www.linkedin.com/shareArticle?mini=true';
            $linkedinShareLink .= '&url=' . $shareURL;
            $linkedinShareLink .= '&title=' . urlencode( osc_item_title() );
            $linkedinShareLink .= '&summary=' . urlencode( osc_item_description() );
            $linkedinShareLink .= '&source=Osclass.com';
    ?>
    <div id="dialog-share-job" title="<?php echo osc_esc_html(__('Share your vacancy', 'jobboard')); ?>" class="hide">
        <div class="form-horizontal">
            <div class="form-row">
                <?php _e('Good stuff! Your vacancy has been published.', 'jobboard'); ?>
                <p><?php _e('Would you like to share your vacancy?', 'jobboard'); ?></p>
                <a href="<?php echo $twitterShareLink; ?>" class="share-social-popup share-twitter"><?php _e('Share to Twitter', 'jobboard'); ?></a>
                <a href="<?php echo $facebookShareUrl; ?>" class="share-social-popup share-facebook"><?php _e('Share to Facebook', 'jobboard'); ?></a>
                <a href="<?php echo $linkedinShareLink; ?>" class="share-social-popup share-linkedin"><?php _e('Share to Linkedin', 'jobboard'); ?></a>
            </div>
        </div>
    </div>
    <script type="text/javascript">
         $(document).ready(function() {
             $("#dialog-share-job").dialog({
                autoOpen: false,
                modal: true,
                dialogClass:'share-dialog',
                width:440
            });
            $("#dialog-share-job").dialog('open');

            $('.share-social-popup').click(function(){
                window.open($(this).attr('href'), '1352814942738', 'width=700,height=250,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                return false;
            });
        });
    </script>
    <?php
        }

        /**
         * Receive listing ID via Session: jobboard_share_job
         */
        private function get_share_job_data()
        {
            $listingID = Session::newInstance()->_get('jobboard_share_job');
            Session::newInstance()->_drop('jobboard_share_job');

            $listing = Item::newInstance()->findByPrimaryKey($listingID);
            View::newInstance()->_exportVariableToView('item', $listing);
        }
    }

    $sjo = new ShareJobOffer();
    // End of file: ./jobboard/class/JobboardNotices.class.php