<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    $applicantId = Params::getParam("people");

    $mjb = ModelJB::newInstance();
    $people = $mjb->getApplicant($applicantId);
    $file = $mjb->getCVFromApplicant($applicantId);
    
    $job = Item::newInstance()->findByPrimaryKey($people['fk_i_item_id']);
    
    


?>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.MetaData.js" type="text/javascript" language="javascript"></script>
<link href="<?php echo osc_plugin_url(__FILE__); ?>js/rating/jquery.rating.css" type="text/css" rel="stylesheet">
<script type="text/javascript">
    $(document).ready(function() {
        
        $("#dashboard").click(function(){
            $("#dashboard_div").show();
            $("#log_div").hide();
            return false;
        });
        
        $("#log").click(function(){
            $("#dashboard_div").hide();
            $("#log_div").show();
            return false;
        });
        
        
        $('.auto-star').rating({
            callback: function(value, link, input){
                var data = value.split("_");
                $.getJSON(
                    "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__);?>ajax.php&paction=rating",
                    {"applicantId" : data[0], "rating" : data[1]},
                    function(data){
                    }
                );
            }
        });
    });
</script>
<div>
    <h1><?php echo @$people['s_name']; ?></h1>
    
</div>

<ul class="jobs_tabs">
    <li><a id="dashboard" href="#" ><?php _e("Dashboard", "jobboard"); ?></a></li>
    <li><a id="profile" href="#" ><?php _e("Profile", "jobboard"); ?></a></li>
    <li><a id="log" href="#" ><?php _e("Activity", "jobboard"); ?></a></li>
</ul>

<div id="dashboard_div">
    <h2><?php _e("Dashboard", "jobboard"); ?></h2>
    <h3><?php _e("Job applied for", "jobboard"); ?></h3>
    <div id="dashboard_job">
        <table class="table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e("Job title", "jobboard"); ?></th>
                    <th><?php _e("Rating for job", "jobboard"); ?></th>
                    <th><?php _e("Workflow status", "jobboard"); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $job['s_title']; ?></td>
                    <td>
                        <?php for($k=1;$k<=5;$k++) {
                            echo '<input name="star'.$people['pk_i_id'].'" type="radio" class="auto-star required" value="'.$people['pk_i_id'].'_'.$k.'" title="'.$k.'" '.($k==$people['i_rating']?'checked="checked"':'').'/>';
                        } ?>
                    </td>
                    <td><?php echo $people['i_status']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="clear:both;"></div>

    <h3><?php _e("Resume", "jobboard"); ?></h3>
    
    <div id="dashboard_notes">
        <?php if(empty($file)) {
            _e("This applicant has not sumitted any resume", "jobboard");
        } else { ?>
        
        <div>
            <button id="prev" onclick="goPrevious()"><?php _e("Previous", "jobboard"); ?></button>
            <button id="next" onclick="goNext()"><?php _e("Next", "jobboard"); ?></button>
            &nbsp; &nbsp;
            <span><?php _e("Page", "jobboard"); ?>: <span id="page_num"></span> / <span id="page_count"></span></span>
            <a href="<?php echo osc_plugin_url(__FILE__);?>download.php?id=<?php echo $applicantId; ?>" id="download_pdf" ><?php _e("Download", "jobboard"); ?></a>
        </div>

        <canvas id="the-canvas" style="border:1px solid black"></canvas>        

        <script type="text/javascript" src="<?php echo osc_plugin_url(__FILE__);?>js/pdf/pdf.js"></script>
        <script type="text/javascript">
            //
            // NOTE:
            // Modifying the URL below to another server will likely *NOT* work. Because of browser
            // security restrictions, we have to use a file server with special headers
            // (CORS) - most servers don't support cross-origin browser requests.
            //
            var url = '<?php echo osc_plugin_url(__FILE__);?>download.php?id=<?php echo $applicantId; ?>';

            //
            // Disable workers to avoid yet another cross-origin issue (workers need the URL of
            // the script to be loaded, and dynamically loading a cross-origin script does
            // not work)
            //
            PDFJS.disableWorker = true;

            var pdfDoc = null,
                pageNum = 1,
                scale = 1.0,
                canvas = document.getElementById('the-canvas'),
                ctx = canvas.getContext('2d');

            //
            // Get page info from document, resize canvas accordingly, and render page
            //
            function renderPage(num) {
            // Using promise to fetch the page
            pdfDoc.getPage(num).then(function(page) {
                var viewport = page.getViewport(scale);
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render PDF page into canvas context
                var renderContext = {
                canvasContext: ctx,
                viewport: viewport
                };
                page.render(renderContext);
            });


            // Update page counters
            document.getElementById('page_num').textContent = pageNum;
            document.getElementById('page_count').textContent = pdfDoc.numPages;
            }

            //
            // Go to previous page
            //
            function goPrevious() {
            if (pageNum <= 1)
                return;
            pageNum--;
            renderPage(pageNum);
            }

            //
            // Go to next page
            //
            function goNext() {
            if (pageNum >= pdfDoc.numPages)
                return;
            pageNum++;
            renderPage(pageNum);
            }

            //
            // Asynchronously download PDF as an ArrayBuffer
            //
            PDFJS.getDocument(url).then(function getPdfHelloWorld(_pdfDoc) {
            pdfDoc = _pdfDoc;
            renderPage(pageNum);
            });
        </script>

        <?php }; ?>
    </div>
    
    <div style="clear:both;"></div>

    <h3><?php _e("Notes", "jobboard"); ?></h3>

    <div id="dashboard_notes">
        <?php _e("No notes have been added to this applicant", "jobboard"); ?>
    </div>
    
    <div style="clear:both;"></div>

</div>


<div id="log_div" style="display:none;">
    <h2><?php _e("Activity", "jobboard"); ?></h2>
    <div style="clear:both;"></div>
</div>