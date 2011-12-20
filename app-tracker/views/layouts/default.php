<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	
	<head>
        <title><?php echo $template['title']; ?></title>
		<?php echo $template['partials']['header']; ?>
		<?php echo $template['metadata']; ?>
	</head>
	
	<body>
        <div id="wrapper">
			
            <div id='blue-header'>
				<div class='content-wrapper'>
					<?php echo $template['partials']['blue_header']; ?>
				</div>
			</div>
			<?php if ( isset($breadcrumbs) ) : ?>
			<div id="breadcrumbs">
				<div class='content-wrapper'>
					<ul>
						<?php foreach ( $breadcrumbs as $k => $v ) : ?>
							<li><?php echo anchor($v,ucfirst($k),'class="breadcrumb-list"'); ?></li>	
						<?php endforeach; ?>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<?php endif; ?>
            <div id="content-body">
				<div class='content-wrapper'>
					<?php echo $template['body']; ?>
				</div>
            </div>
			
			
			
            <div id='footer'>
                <div class='content-wrapper'>
					<?php echo $template['partials']['footer']; ?>
                </div>
            </div>
			
        </div>
	</body>
</html>