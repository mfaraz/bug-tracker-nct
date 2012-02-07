<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>resources/css/style.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>resources/js/jquery.js"></script>
<?php if ( $this->session->userdata('id') ) : ?>
<script>
    
    $(document).ready(function(){
        
        window.onpopstate = function(event) {
            if ( window.location != '<?php echo site_url($originalUrl); ?>' && window.location != '<?php echo site_url($originalUrl); ?>/'){
                load_issue(window.location);
            } else {
                if ( '' == '<?php echo (isset($issue_view)) ? 'true' : ''; ?>') {
                    $('#issue-page-container').animate({'top':'-1000px'});
                }
            }
            
        };
         
        $(document).delegate('#close-issue-page','click',function(e){
            e.preventDefault();
            
            if (history.pushState) {
                changeUrl( $(this).attr('rel') )
            } else {
                window.location.hash="#";
            }
            $('#issue-page-container').animate({'top':'-1000px'});
        });
        
        $(document).delegate('#new-issue,#new-user, .button-link-issue, .edit-button, #edit-button, #cancel-button','click',function(e){
        
            e.preventDefault();
            
            
            
            changeUrl($(this).attr('rel'));
            
            load_issue($(this).attr('href'));
        });
        
        var load_issue = function(url){
            $('#issue-page-container').animate({'top':0});
                
            $('#issue-page-container #page-container-wrapper').html("<div class='loader-wrapper'><img src='<?php echo base_url(); ?>resources/images/ajax-loader.gif' /></div>");
            
            $.ajax({
                url : url,
                data : 'p_url=<?php echo $originalUrl; ?>',
                type: 'POST',
                success:function(obj) {
                    $('#page-container-wrapper').html(obj);
                }
            });
        };
        

        var changeUrl = function( url ) {
            
            if (!history.pushState) { //Compatability check
                
                window.location.hash = url
                
            } else {
                var stateObj = { type: url }; 
                history.pushState(stateObj, "Title", "<?php echo base_url(); ?>"+url);
            }

        }
       
    });
    
</script>

<!-- [if IE]>  Firefox and others will use outer object -->
<script>

    $(document).ready(function(){
        
        var hash = window.location.hash.substring(1);
        if ( hash != '' )
        {
            load_issue('<?php echo base_url(); ?>'+hash);
        }
            
    });
    
</script>
<!--<![endif]-->
<?php endif; ?>