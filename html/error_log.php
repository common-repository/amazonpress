<div class='wrap amazonpressadmin'>
	<h2>AmazonPress: Message Log</h2>
	
	<ul id="tabnav">
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=dashboard">Dashboard</a></li>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=options">Options</a></li>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=message_log" class='highlighted'>Message Log</a></li>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=view_cache">View Cache</a></li>
		<li><a href="http://cj7.co.uk/amazonpress-news/" target='_blank' class='highlighted-external'>AmazonPress Updates & News</a></li>
		<li><a href="http://www.monastars.com/amazonpress-special/" target='_blank' class='highlighted-external'>Get Free AmazonPress Pro</a></li>
	</ul>
	
	<ul class='secondTabs'>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=message_log">Refresh Log</a></li>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=message_log&clear_log=true" onClick="return(confirm('Are you sure you want to clear the message log?'));">Clear Message Log</a></li>
	</ul>
	
	
	<table class='widefat' style='width: 600px;'>
		<tr>
			<td align="left">&nbsp;</td>
			<td align="right"><?php echo $page_navigation; ?></td>
		</tr>
		<?php
		if(!$errors OR count($errors) == 0)
		{
			echo "<td>Congratulations, there are no errors logged.</td><td>&nbsp;</td>";
		}
		else
		{
			foreach($errors as $error)
			{
				$timestamp = date('F j, Y, g:i a', $error->timestamp);
				$msg = $error->message;
			?>
		<tr>
			<td><?php echo $timestamp; ?></td>
			<td><?php echo $msg; ?></td>
		</tr>
			<?php
			}
		}
		?>
		<tr>
			<td align="left">&nbsp;</td>
			<td align="right"><?php echo $page_navigation; ?></td>
		</tr>
	</table>

	</form>
</div>