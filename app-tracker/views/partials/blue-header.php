<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="header-menu" class="flt-left">
    <ul>
        <li><?php echo anchor('home','Home','class="header-list"'); ?></li>
        
        <?php if ( $this->session->userdata('is_admin') ) : ?>
        <li><?php echo anchor('account','Manage Users','class="header-list"'); ?></li>
        <?php endif; ?>
        
        <?php if ( $this->session->userdata('id') != false ) : ?>
        <li><?php echo anchor('home/logout','Logout','class="header-list"'); ?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="clr"></div>