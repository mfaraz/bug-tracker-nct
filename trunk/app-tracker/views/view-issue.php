<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="">
    <div class='column-header'>
        <?php echo $issue['id'] . '  : ' . $issue['summary'] ; ?>
        <a href="#" id="close-issue-page" rel="<?php echo $p_url; ?>" class="flt-right">close</a>
    </div>
    <div class="column-container">
        <div class="left-column flt-left">
            <div class='issue-header-info'>
                <ul>
                    <li><label><?php echo lang('issue_owner'); ?></label> : <?php echo $issue['owner']; ?></li>
                    <li><label><?php echo lang('issue_date_added'); ?></label> : <?php echo date("F d, Y",strtotime($issue['date_added'])); ?></li>
                    <li><label><?php echo lang('issue_severity'); ?></label> :<span class='severity-color' style="background-color:#<?php echo $issue['color']; ?>"><?php echo $issue['severity']; ?></span></li>
                </ul>
                <div class='clr'></div>
            </div>
            
            <div class='issue-body-content'>
                <h1><?php echo lang('issue_description'); ?></h1>
                <p>
                    <?php echo $issue['description']; ?>
                </p>
                <?php if( count($attachment) != 0 ) : ?>
                <div class='issue-attachment-container'>
                    <h1><?php echo lang('issue_attach'); ?></h1>
                    <ul>
                        <?php foreach ( $attachment as $at ) : ?>
                        <li><span class='attachment-icon'></span><span class='attachment-name'><a href="<?php echo site_url('attachment/download/'.$at['id']); ?>"><?php echo $at['name']; ?></a></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="issue-comment">
                    <div class='issue-comment-box'>
                        <textarea name="issue_comment" id="issue_comment" placeholder="Enter your comment..."></textarea><br/>
                        <input type='button' value='Comment' id='comment-button' class="button-link"/>
                    </div>
                    <div class='issue-comment-list'>
                        <ul>
                            <?php foreach( $comment as $com ) : ?>
                                <li>
                                    <div class='comment-header'>
                                        <span class='user-container'>
                                            <label>User : </label><?php echo $com['user']; ?>
                                        </span>
                                        <span class='date-container'>
                                            <label>Date : </label><?php echo $com['date_added']; ?>
                                        </span>
                                    </div>
                                    <div class='comment-content'>
                                        <p>
                                            <?php echo wordwrap(nl2br($com['comment']),60,'&#8203;',true); ?>
                                        </p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
           
        </div>
        <div class="right-column flt-left">
            
            <div class='info-box-container'>
                <h1><?php echo lang('issue_status'); ?></h1>
                <div class='status-container'>
                <ul>
                    <?php foreach( $status as $stat ): ?>
                        <li><span class="status-class" style="background:#<?php echo $stat['color']; ?>;"><?php echo $stat['name'] . ' - '.$stat['date_added'].'</span> ' ; ?><div class='clr'></div></li>
                    <?php endforeach; ?>
                </ul>
                </div>
            </div>
            
        </div>
        <div class='clr'></div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
        $('#issue_comment').focus(function(){
            
            if( $(this).val() == '' ) {
                $(this).css({height:'50px'});
            }
            
        }).blur(function(){
            
            if( $(this).val() == '' ) {
                $(this).css({height:'18px'});
            }
            
        }).keypress(function(e){
            
            if ( e.which == 13 )
            {
                if ( $(this).val() == '' ) {
                    $(this).html('');
                    e.preventDefault();
                }
            }
            
        });
        
        $('#comment-button').click(function(){
            
            if ( $('#issue_comment').val() != '' )
            {
                $(this).attr('disabled',true);
                $('#issue_comment').attr('disabled',true);
                
                $.ajax({
                    url         : '<?php echo site_url('ajax/comment'); ?>',
                    type        : 'POST',
                    data        : 'issue_id=<?php echo $issue['id']; ?>&user_id=1&issue_comment='+escape($('#issue_comment').val()),
                    dataType    : 'JSON',
                    success     : function(res) {
                        
                        if ( res.r == true ) {
                            
                            $('.issue-comment-list ul').prepend(
                                "<li><div class=\"comment-header\"><span class=\"user-container\"><label>User :</label> "+res.user+" </span> "+
                                "<span class=\"date-container\"><label>Date :</label> " + res.date +"</span></div>" +
                                "<div class=\"comment-content\"><p>"+res.comment+"</p></div>" +
                                "</li>"
                            );
                            
                            $('#comment-button').attr('disabled',false);
                            $('#issue_comment').attr('disabled',false);
                            $('#issue_comment').val('').blur();
                            
                        } else {
                            
                        }
                        
                    }
                });
            }
        });
    });
</script>