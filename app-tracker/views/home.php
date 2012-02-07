<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="body-header">
    <h6>Welcome to SassyDumpling Bug Tracking System</h6>
    <p>
           The SassyDumpling bug tracking system is for use by developers to report bugs found in SassyDumpling.
    If you are not a developer, please visit the SassyDumpling FAQ page instead. 
    
    </p>
    
</div>
<div id='body-white'>
    
    <div id='login-wrapper' class="flt-left">
        <div class='form-login'>
            
            <?php if ( $this->session->flashdata('msg') ) : ?>
            <div class='err-message'>
                <?php echo $this->session->flashdata('msg'); ?>
            </div>
            <?php endif; ?>
            
            <?php echo form_open('home/login/authenticate'); ?>
                <table>
                    <tr>
                        <td><?php echo lang('user_username'); ?></td>
                        <td><input type='email' name='username' value='' /></td>
                    </tr>
                   <tr>
                        <td><?php echo lang('user_password'); ?></td>
                        <td><input type='password' name='password' value='' /></td>
                    </tr>
                   <tr>
                        <td></td>
                        <td><input type='submit' value='Login' class="button-link" /></td>
                   </tr>
                </table>
            <?php echo form_close(); ?>
            
        </div>
    </div>
    <div id="create-account-wrapper" class="flt-left align-center">
        <div class='flt-left'>
        <img src='<?php echo base_url(); ?>resources/images/html5.png' class="html5logo"/>
        </div>
        <div class='flt-left'>
        <img src='<?php echo base_url(); ?>resources/images/sassy.jpg' class="html5logo"/>
        </div>
    </div>
    <div class='clr'></div>
    
</div>