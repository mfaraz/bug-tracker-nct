<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="issue-page-container" <?php echo (isset($issue_view) ) ? 'style="top:0;"' : ''; ?>>
    <div id="page-container-wrapper">
        <?php echo (isset($issue_view)) ? $issue_view : ''; ?>
    </div>
</div>



<div id='list-content-wrapper'>
    
    <div id='issue-search'>
        <?php if ( $this->session->userdata('id') != false ) : ?>
        <a href="<?php echo site_url('bugs/create'); ?>" rel="bugs/create" class="button-link" id="new-issue">New Issue</a>
        <?php endif; ?>
        <div class='search-container flt-right'>
        <?php echo form_open('bugs/search/'); ?>
        <input type='text' name='q'  placeholder="Search..." />
        <input type='submit' class='button-link' value='Search' />
        <?php form_close(); ?>
        </div>
        <div class='clr'></div>
    </div>
    <?php if ( $this->session->flashdata('msg') )  :?>
    <div class='err-message'>
        <?php echo $this->session->flashdata('msg'); ?>
    </div>
    <?php endif; ?>
    
    <div id='grid-container'>
        <table id="issue-grid">
            <thead>
                <tr>
                    <?php if ( $this->session->userdata('is_admin') ) : ?>
                    <th style="width:70px;"></th>
                    <?php endif; ?>
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
                <tr id='list-<?php echo $issue['id']; ?>' class="<?php echo ( $row % 2 ) ? 'even' : 'odd'; ?>">
                    <?php if ( $this->session->userdata('is_admin') ) : ?>
                    <td>
                        <a href="<?php echo site_url('bugs/issue/'.$issue['id'].'/'.$currentpage.'/edit'); ?>" rel="<?php echo 'bugs/issue/'.$issue['id'].'/'.$currentpage.'/edit'; ?>" class="button-link button-link-issue" title="Edit"><span class="icon edit-icon"></span></a>
                        <a href="#" class="button-link delete-button" rel="<?php echo $issue['id']; ?>"><span class="icon delete-icon"></span></a>
                    </td>
                    <?php endif; ?>
                    <td><a href="<?php echo site_url('bugs/issue/'.$issue['id'].'/'.$currentpage); ?>" rel="<?php echo 'bugs/issue/'.$issue['id'].'/'.$currentpage; ?>" class="button-link button-link-issue" title="Delete"><?php echo $issue['id']; ?></a></td>
                    <td><?php echo $issue['type']; ?></td>
                    <td><?php echo $issue['summary']; ?></td>
                    <td><?php echo $issue['owner']; ?></td>
                    <td><span class='severity-color' style="background-color:#<?php echo $issue['color']; ?>"><?php echo $issue['severity']; ?></span></td>
                    <td id="issue_<?php echo $issue['id']; ?>"><span class='status-class' style='background-color:#<?php echo $issue['status_color']; ?>'><?php echo $issue['status']; ?></span></td>
                    <td><?php echo date("M-d-Y h:i a",strtotime($issue['date_added'])); ?></td>
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
 $('.delete-button').click(function(e){
        e.preventDefault();
        var del = confirm("Are you sure you want to delete the current issue?");
        if ( del )
        {
            var id = $(this).attr('rel');
            
            $.ajax({
                url : '<?php echo site_url('ajax/deleteissue/'); ?>',
                type: 'POST',
                data: 'issue_id='+id,
                dataType: 'JSON',
                success: function(obj){
                    
                    if ( obj.r == true )
                    {
                        $('#list-'+id).remove();
                    }
                    
                    $('#close-issue-page').trigger('click');
                }
            });
        }
        return false;
    });
</script>
