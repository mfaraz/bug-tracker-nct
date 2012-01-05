<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id='body-white'>
    <div class='column-header'>
        Edit Issue - <?php echo $issue['id']; ?>
        <a href="#" id="close-issue-page" rel="<?php echo $p_url; ?>" class="flt-right">close</a>
    </div>
    <div class="form-container">
        <?php echo form_open('bugs/update/'.$issue['id'],'enctype="multipart/form-data"'); ?>
        <table>
            
        <tr>
            <td><?php echo lang('issue_summary'); ?></td>
            <td><input type='text' autocomplete="off" required="required" name='issue_summary' value='<?php echo set_value('issue_summary',$issue['summary']); ?>' class="input-min-width" /></td>
            <td rowspan="9">
                <?php echo validation_errors('<div class="error-container">','</div>'); ?>
            </td>
        </tr>
        
        <tr>
            <td><?php echo lang('issue_description'); ?></td>
            <td><textarea name='issue_description' required="required" ><?php echo set_value('issue_description',$issue['description']); ?></textarea></td>
        </tr>
        
        <?php if(count($attachment) != 0 ) : ?>
            <tr>
                <td></td>
                <td>
                    <?php foreach( $attachment as $at ) : ?>
                    <div class='attachment-container-box'>
                    <span><?php echo $at['name']; ?></span>
                    <span class='remove' title='Remove' rel='<?php echo $at['id']; ?>'></span>
                    </div>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endif; ?>
        
        <tr id="attachment-row">
            <td></td>
            <td><div class="attachment-container"></div></td>
        </tr>
        
        <tr>
            <td></td>
            <td><span id="attach-file">Attach file</span></td>
        </tr>
        <tr>
            <td><?php echo lang('issue_type'); ?></td>
            <td>
                <select name="issue_type" class="input-min-width">
                    <option value=''>--Select Type--</option>
                    <?php foreach( $type as $k => $v ) : ?>
                        <optgroup label="<?php echo $k ?>">
                            <?php foreach( $v as $stat ) : ?>
                                <?php
                                $select = '';
                                if ( set_value('issue_type') == $stat['id'] || $issue['type_id'] == $stat['id']  ) {
                                    $select = 'selected="selected"';
                                }
                                
                                ?>
                                <option value='<?php echo $stat['id']; ?>' <?php echo $select; ?>><?php echo $stat['name'] .' - '.$stat['description']; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo lang('issue_status'); ?></td>
            <td>
                <select name="issue_status" required="required" class="input-min-width">
                    <?php foreach( $status as $k => $v ) : ?>
                        <optgroup label="<?php echo $k ?>">
                            <?php foreach( $v as $stat ) : ?>
                                <?php
                                $select = '';
                                if ( set_value('issue_status',$i_status[0]['id']) == $stat['id'] ) {
                                    $select = 'selected="selected"';
                                }
                                
                                ?>
                                <option value='<?php echo $stat['id']; ?>' <?php echo $select; ?>><?php echo $stat['name'] .' - '.$stat['description']; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        
        <tr>
            <td><?php echo lang('issue_severity'); ?></td>
            <td>
                <select name='issue_severity' required="required" class="input-min-width">
                    <option value=''>--Select Severity--</option>
                    <?php foreach ($severity as $sev) : ?>
                            <?php
                            $select = '';
                            if ( set_value('issue_severity',$issue['severity_id']) == $sev['id'] ) {
                                $select = 'selected="selected"';
                            }
                            
                            ?>
                        <option value='<?php echo $sev['id']; ?>' <?php echo $select; ?>><?php echo $sev['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        
        <tr>
            <td><?php echo lang('issue_owner'); ?></td>
            <td>
                <select name="issue_owner" required="required" class="input-min-width">
                    <option value=''>--Select Owner--</option>
                    <?php foreach( $users as $user) : ?>
                        <?php
                        $select = '';
                        if ( set_value('issue_owner',$issue['owner_id']) == $user['id'] ) {
                            $select = 'selected="selected"';
                        }
                        
                        ?>
                        <option value='<?php echo $user['id']; ?>' <?php echo $select; ?>><?php echo $user['first_name'].' '.$user['last_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        
        <tr>
            <td><?php echo lang('issue_cc'); ?></td>
            <td>
                <input type='text' name="issue_cc" id="issue_cc" class="input-min-width"/>
            </td>
        </tr>
        
        <tr>
            <td><?php echo lang('issue_label'); ?></td>
            <td> <input type='text' name='issue_label' id="issue_label" /></td>
        </tr>
        
        <tr>
            <td></td>
            <td><input type='submit' value='Update' class="button-link" /> <a href="<?php echo site_url('bugs/issue/'.$issue['id'].'/'.$currentpage); ?>" rel="bugs/issue/<?php echo $issue['id'].'/'.$currentpage; ?>" class="button-link" id="cancel-button">Cancel</a></td>
        </tr>
        
        </table>    
        <?php echo form_close(); ?>
    </div>
</div>
<script>

    $(document).ready(function(){
        
        var attachment = 0;
        
        $('#attach-file').click(function(){
            attachment++;
            $('#attachment-row').show();
            $('.attachment-container').append(
                
                $('<div>').append(
                    $('<input>').attr({'type':'file','name':'issue_attachment'+attachment}),
                    $('<span>').html('remove').bind('click',function(){
                        $(this).parent().remove();
                    })
                )
            );
        });
        
        $('.remove').click(function(){
            
            var a_id = $(this).attr('rel');
            var form = $(this);
            $.ajax({
                url : '<?php echo site_url('ajax/deleteattachment/'); ?>',
                data : 'issue_id=<?php echo $issue['id']; ?>&attachment_id='+a_id,
                type : 'POST',
                dataType : 'JSON',
                success :function(obj) {
                    if ( obj.r == true ) {
                        $(form).parent().remove();
                    }
                }
            })
        })
        
        $("#issue_label").tokenInput("<?php echo site_url('ajax/label'); ?>",{
            theme:'facebook',
            method:'post',
            preventDuplicates:true,
            onReady:function(){
                
                $("#token-input-issue_label").keypress(function(event){
                    
                    var val = $('#token-input-issue_label').val();
                    
                    if(event.which == 13) {
                        
                        event.preventDefault();
                        if ( val != '' ) {
                            $('#issue_label').tokenInput("add",{id:val,name:val});
                        }
                    }
                });
            },
            prePopulate: [
                <?php $total = count($label); ?>
                <?php $row = 0; ?>
                <?php foreach( $label as $l ) : ?>
                    <?php if ( $row == $total) : ?>
                    {id:<?php echo $l['id']; ?>,name:'<?php echo $l['name']; ?>'}
                    <?php else : ?>
                    {id:<?php echo $l['id']; ?>,name:'<?php echo $l['name']; ?>'},
                    <?php endif; ?>
                <?php $row++; ?>
                <?php endforeach; ?>
            ]
        });
        
        
        $("#issue_cc").tokenInput("<?php echo site_url('ajax/cc'); ?>",{
            theme:'facebook',
            method:'post',
            preventDuplicates:true,
            onReady:function(){
                $('#token-input-issue_cc').keypress(function(e){
                    
                    if(e.which == 13) {
                        e.preventDefault();
                    }
                });
            },
            prePopulate: [
                <?php $total = count($cc); ?>
                <?php $row = 0; ?>
                <?php foreach( $cc as $l ) : ?>
                    <?php if ( $row == $total) : ?>
                    {id:<?php echo $l['id']; ?>,name:'<?php echo $l['name']; ?>'}
                    <?php else : ?>
                    {id:<?php echo $l['id']; ?>,name:'<?php echo $l['name']; ?>'},
                    <?php endif; ?>
                <?php $row++; ?>
                <?php endforeach; ?>
            ]
        });
        
    });
    
</script>