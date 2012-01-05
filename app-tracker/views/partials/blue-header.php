<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="header-menu" class="flt-left">
    <ul>
        <li><?php echo anchor('home','Home','class="header-list"'); ?></li>
        <li><?php echo anchor('bugs','Bugs','class="header-list"'); ?></li>
        <li><?php echo anchor('search','Search','class="header-list"'); ?></li>
        <?php if ( $this->session->userdata('id') == false ) : ?>
        <li><?php echo anchor('home/login','Login','class="header-list"'); ?></li>
        <?php else : ?>
        <li><?php echo anchor('home/logout','Logout','class="header-list"'); ?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="clr"></div>