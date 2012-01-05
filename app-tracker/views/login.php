<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id='body-white'>
    <div id='login-wrapper'>
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
</div>

