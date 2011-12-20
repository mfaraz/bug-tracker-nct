<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="issue-page-container" <?php echo (isset($issue_view) ) ? 'style="top:0;"' : ''; ?>>
    <div id="page-container-wrapper">
        <?php echo (isset($issue_view)) ? $issue_view : ''; ?>
    </div>
</div>



<div id='list-content-wrapper'>
    
    <div id='issue-search'>
        <a href="<?php echo site_url('bugs/create'); ?>" rel="bugs/create" class="button-link" id="new-issue">New Issue</a>
    </div>
    
    <div id='grid-container'>
        <table id="issue-grid">
            <thead>
                <tr>
                    <th><?php echo lang('issue_id'); ?></th>
                    <th><?php echo lang('issue_type'); ?></th>
                    <th><?php echo lang('issue_summary'); ?></th>
                    <th><?php echo lang('issue_owner'); ?></th>
                    <th><?php echo lang('issue_severity'); ?></th>
                    <th><?php echo lang('issue_status'); ?></th>
                    <th><?php echo lang('issue_date_added'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $row = 0 ; ?>
                <?php foreach( $issues as $issue ) : ?>
                <tr class="<?php echo ( $row % 2 ) ? 'even' : 'odd'; ?>">
                    <td><a href="<?php echo site_url('bugs/issue/'.$issue['id'].'/'.$currentpage); ?>" rel="<?php echo 'bugs/issue/'.$issue['id'].'/'.$currentpage; ?>" class="button-link-issue"><?php echo $issue['id']; ?></a></td>
                    <td><?php echo $issue['type']; ?></td>
                    <td><?php echo $issue['summary']; ?></td>
                    <td><?php echo $issue['owner']; ?></td>
                    <td><span class='severity-color' style="background-color:#<?php echo $issue['color']; ?>"><?php echo $issue['severity']; ?></span></td>
                    <td><?php echo $issue['status']; ?></td>
                    <td><?php echo $issue['date_added']; ?></td>
                </tr>
                <?php $row++; ?>
                <?php endforeach; ?>
                
            </tbody>
        </table>
        <div class='grid-pagination'>
            <?php echo $this->pagination->create_links(); ?>
        </div>
    </div>
</div>
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
        
        $('#new-issue, .button-link-issue').click(function(e){
        
            e.preventDefault();
            
            changeUrl($(this).attr('rel'));
            
            load_issue($(this).attr('href'));
        });
        
        var load_issue = function(url){
            $('#issue-page-container').animate({'top':0});
                
            $('#issue-page-container #page-container-wrapper').append("<div class='loader-wrapper'><img src='<?php echo base_url(); ?>resources/images/ajax-loader.gif' /></div>");
            
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