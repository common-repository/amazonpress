<div class='wrap amazonpressadmin'>

	<h2>AmazonPress: Options</h2>
	
	<ul id="tabnav">
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=dashboard">Dashboard</a></li>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=options" class='highlighted'>Options</a></li>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=message_log">Message Log</a></li>
		<li><a href="<?php echo $homePath; ?>&amazonPressAdminPage=view_cache">View/Empty Cache</a></li>
		<li><a href="http://cj7.co.uk/amazonpress-news/" target='_blank' class='highlighted-external'>AmazonPress Updates & News</a></li>
		<li><a href="http://www.monastars.com/amazonpress-special/" target='_blank' class='highlighted-external'>Get Free AmazonPress Pro</a></li>
	</ul>
	
	<ul class='secondTabs'>
		<li><a href="javascript:;" onClick="jQuery('#amazonPressConnection').show(); jQuery('#amazonPressSearch').hide(); jQuery('#amazonPressDisplay').hide(); jQuery('.updated').hide();">Connection</a></li>
		<li><a href="javascript:;" onClick="jQuery('#amazonPressConnection').hide(); jQuery('#amazonPressSearch').show(); jQuery('#amazonPressDisplay').hide(); jQuery('.updated').hide();">Search</a></li>
		<li><a href="javascript:;" onClick="jQuery('#amazonPressConnection').hide(); jQuery('#amazonPressSearch').hide(); jQuery('#amazonPressDisplay').show(); jQuery('.updated').hide();">Display</a></li>
	</ul>
	
	<form action='<?php echo $homePath; ?>&amazonPressAdminPage=options' method='POST'>
		<div id="amazonPressConnection">
			<table class='widefat' style='width: 700px;'>
				<tr>
					<td><strong>Select Locale:</strong><br/><small>If you reside in or wish to display products from a country other than the US (amazon.com) site, you can select your locale here.  Please be aware that in order to collect referral rewards, your associate account must be registered in the same locale as you are using to pull products from.</small></td>
					<td><select name='Locale'>
						<option value='United States' <?php if($this->options['Locale'] == 'United States') echo "selected"; ?>>United States</option>
						<option value='United Kingdom' <?php if($this->options['Locale'] == 'United Kingdom') echo "selected"; ?>>United Kingdom</option>
						<option value='France' <?php if($this->options['Locale'] == 'France') echo "selected"; ?>>France</option>
						<option value='Germany' <?php if($this->options['Locale'] == 'Germany') echo "selected"; ?>>Germany</option>
						<option value='Japan' <?php if($this->options['Locale'] == 'Japan') echo "selected"; ?>>Japan</option>						
						<option value='Canada' <?php if($this->options['Locale'] == 'Canada') echo "selected"; ?>>Canada</option>
						<option value='China' <?php if($this->options['Locale'] == 'China') echo "selected"; ?>>China</option>
						<option value='Italy' <?php if($this->options['Locale'] == 'Italy') echo "selected"; ?>>Italy</option>						
						<option value='Spain' <?php if($this->options['Locale'] == 'Spain') echo "selected"; ?>>Spain</option>						
					</select></td>
				</tr>
				<tr>
					<td><strong>AWS Access Key:</strong><br/><small>The access key you applied for and received from the <a href='http://aws.amazon.com/?_encoding=UTF8&tag=xxxxxxxxxxx-20'>Amazon Web Services site</a>.</small></td>
					<td><input type='text' name='AWSAccessKeyId' value='<?php echo $this->options['AWSAccessKeyId']; ?>' /></td>
				</tr>
				<tr>
					<td><strong>AWS Secret Access Key:</strong><br/><small>The secret access key you received from the <a href='http://aws.amazon.com/?_encoding=UTF8&tag=xxxxxxxxxxx-20'>Amazon Web Services site</a>.  Do NOT share. This key must be kept secure at all times.</small></td>
					<td><input type='password' name='AWSSecretAccessKeyId' value='<?php if($this->options['AWSSecretAccessKeyId']) echo "************************"; ?>' /></td>
				</tr>
				<tr>
<td><strong>Associate Tag:</strong><br/><small>Your Amazon associate tag which you received/created when you signed up as an <a href='https://affiliate-program.amazon.com/?_encoding=UTF8&tag=xxxxxxxxxxx-20'>Amazon associate</a>.</small></td>
<td><input type='text' name='AssociateTag' value='<?php echo $this->options['AssociateTag']; ?>' /></td>
				</tr>
			</table>
		</div>
	
		<div id="amazonPressSearch" style='display:none;'>
			<table class='widefat' style='width: 700px;'>
				<tr>
<td><strong>Search Based On:</strong><br/><small>Choose whether to search for related content based on categories or tags.</small></td>
					<td><select name='SearchFrom'>
<option value='categories' <?php if($this->options['SearchFrom'] == 'categories') echo "selected"; ?>>Categories</option>
<option value='tags' <?php if($this->options['SearchFrom'] == 'tags') echo "selected"; ?>>Tags</option>
					</select></td>
				</tr>
				<tr>
<td><strong>Search Index:</strong><br/><small>Select what types of related items you want featured on your blog. You may wish to clear the local cache after changing this in order for the changes to become visible more quickly.</small></td>
					<td>
<?php if(!isset($this->options['SearchIndex'])) $this->options['SearchIndex'] = 'Books'; ?>
<input type='radio' name='SearchIndex' value='Books' <?php if($this->options['SearchIndex'] == 'Books') echo 'checked'; ?> /> Books			<br/>
						
<input type='radio' name='SearchIndex' value='Music' <?php if($this->options['SearchIndex'] == 'Music') echo 'checked'; ?> /> Music				<br/>
						
<input type='radio' name='SearchIndex' value='Video' <?php if($this->options['SearchIndex'] == 'Video') echo 'checked'; ?> /> Video				<br/>
						
<input type='radio' name='SearchIndex' value='' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' disabled='disabled' /> Electronics		<br/>
						
<input type='radio' name='SearchIndex' value='Software' <?php if($this->options['SearchIndex'] == 'Software') echo 'checked'; ?> /> Software			<br/>
						
<input type='radio' name='SearchIndex' value='' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro'  /> Blended		<br/>
						
<input type='radio' name='SearchIndex' value='' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' /> All				<br/>
					</td>
				</tr>
				<tr>
<td><strong>Default Tags:</strong><br/><small>Enter a couple default tags seperated by commas to be used on articles not having any specific tags.  (used to find products related to these tags)</small></td>
<td><input type='text' name='DefaultTags' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' value='<?php echo $this->options['DefaultTags']; ?>' /></td>
				</tr>
				<tr>
<td><strong>Cache Expiry Minutes:</strong><br/><small>How many minutes would you like between when related items are refreshed from Amazon sites?</small></td>
<td><input type='text' name='CacheExpiry' value='<?php echo $this->options['CacheExpiry']; ?>' /></td>
				</tr>
				<tr>
<td><strong>Tip the Developer: or buy Premium/Pro to remove this.</strong><br/>
<small>If you find this plugin to be useful in helping you to make money with your blog, why not give a "tip" to the developer by allowing him to share a bit of the profit.<br/>
<br/>
Ok, so there are functions dissabled, what a jerk the developer is to try to make some money out of us who uses his free plugin.<br/>
You can either get the old version 9.0.1 and add your own patches and fixes and new Amazon API + upgrades. Or you can buy the full version and get fixes done by me.
<br/>
Allowing this box to be checked, will share up to 20% of ad-links with the developer instead of using your own affiliate ID 100%.<br/>
Thanks for supporting the development and improving this plugin.</small></td>

<td>

<input type='checkbox' name='AllowTip' checked='true' value='yes' alt='If You want this function active, please upgrade to Pro or edit the file' title='If You want this function active, please upgrade to Pro or edit the file' <?php if($this->options['AllowTip'] == true) echo "checked"; ?> />
 Yes, Give Tip</td>

				</tr>
			</table>
		</div>
	
		<div id="amazonPressDisplay" style='display:none;'>
			<table class='widefat' style='width: 700px;'>
				<tr>
<td><strong>Show On:</strong><br/><small>Select which areas of your blog you wish these items to be displayed on.</small></td>
<td>
<input type='checkbox' name='ShowOnPosts' value='yes' <?php if($this->options['ShowOnPosts'] == true) echo "checked"; ?> /> Posts<br/>
<input type='checkbox' name='ShowOnPages' checked='false' value='no' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' disabled='disabled'  /> Pages<br/>
<input type='checkbox' name='ShowOnHome' value='yes' <?php if($this->options['ShowOnHome'] == true) echo "checked"; ?> /> Home<br/>
<input type='checkbox' name='ShowOnCategories' value='yes' <?php if($this->options['ShowOnCategories'] == true) echo "checked"; ?> /> Categories<br/>
<input type='checkbox' name='ShowOnTags' value='yes' <?php if($this->options['ShowOnTags'] == true) echo "checked"; ?> /> Tags<br/>
<input type='checkbox' name='ShowOnSearch' checked='false' value='no' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' disabled='disabled' /> Search<br/>
					</td>
				</tr>
				<tr>
					<td><strong>Title Text:</strong><br/><small>Enter the text you wish to use in the related reading title or leave it as is.</small></td>
					<td><input type='text' name='TitleText' value='<?php echo htmlentities(stripslashes($this->options['TitleText']), ENT_QUOTES); ?>' /></td>
				</tr>
				<tr>
<td><strong>Max Results:</strong><br/><small>How many products would you like to promote on each post? (maximum)</small></td>
<td><input type='text' name='MaxResults' alt='If You want all function active, please upgrade to Pro' title='If You want all function active, please upgrade to Pro' value='<?php echo $this->options['MaxResults']; ?>' /></td>
				</tr>
				<tr>
<td><strong>Image Size to Display:</strong><br/><small>Choose what size images you want to display, if any.</small></td>
					<td>
<select name='ImageSize'>
<option value='0' <?php if($this->options['ImageSize'] == '0') echo "selected"; ?> />No Images</option>
<option value='1' <?php if($this->options['ImageSize'] == '1') echo "selected"; ?> />Small</option>
<option value='2' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' />Medium</option>
<option value='3' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' />Large</option>
</select>
				</tr>
				<tr>
<td><strong>Show Text:</strong><br/><small>Do you wish to show text associated with the related products?</small></td>
<td><input type='checkbox' name='ShowText' value='yes' <?php if($this->options['ShowText'] == true) echo "checked"; ?> /> Yes</td>
				</tr>
				<tr>
<td><strong>Show Descriptions:</strong><br/><small>Do you wish to show descriptions/reviews associated with the related products?</small></td>
<td>
<select name='ShowDesc'>
<option value='0' <?php if($this->options['ShowDesc'] == '0') echo "selected"; ?> />No</option>
<option value='1' <?php if($this->options['ShowDesc'] == '1') echo "selected"; ?> />Excerpts Only</option>
<option value='2' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' />Full Descriptions</option>
</select>
				</tr>
				<tr>
<td><strong>Link Targets:</strong><br/><small>What sort of window would you like the links to open in?  (ie new window? same window?)</small></td>
					<td><select name='LinkTarget'>
<option value='' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' <?php if($this->options['LinkTarget'] == '') echo "selected"; ?>>Default (same window)</option>
<option value='_blank' <?php if($this->options['LinkTarget'] == '_blank') echo "selected"; ?>>New Window</option>
					</select></td>
				</tr>
				<tr>
<td><strong>Display Order:</strong><br/><small>In what order would you like to display Amazon products?</small></td>
					<td>
<select name='SortBy'>
<option value='random' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' />Random</option>
<option value='salesrank' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' />Popularity (high to low)</option>
<option value='-salesrank' <?php if($this->options['SortBy'] == '-salesrank') echo "selected"; ?> />Reverse Popularity</option>
<option value='listprice' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' />Price (high to low)</option>
<option value='-listprice' <?php if($this->options['SortBy'] == '-listprice') echo "selected"; ?> />Reverse Price</option>
</select>
				</tr>
				<tr>
<td><strong>Display Position:</strong><br/><small>Where would you like to display the Amazon products?</small></td>
					<td>
						<select name='DisplayPosition'>
<option value='0' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' <?php if($this->options['DisplayPosition'] == '0') echo "selected"; ?> />Below the content</option>
<option value='1' disabled='disabled' alt='If You want this function active, please upgrade to Pro' title='If You want this function active, please upgrade to Pro' />Above the content</option>
				</select>
				</tr>
				<tr>
					<td><strong>Display StyleSheet:</strong><br/><small>Choose a stylesheet for use in displaying the products on your blog.  You can also create your own and put them in the amazonpress/css/ folder.</small></td>
					<td><select name='StyleSheet'>
						<?php
							foreach($stylesheets as $filename)
							{
								?>
								<option value='<?php echo $filename; ?>' <?php if($this->options['StyleSheet'] == $filename) echo "selected"; ?> ><?php echo $filename; ?></option>
								<?php
							}
						?>
					</select></td>
				</tr>
			</table>
		</div>

		<table class='widefat' style='width: 700px;'>
			<tr>
				<td>Once you have input and saved appropriate values for these options, your site will be able to begin displaying related items on all your blog pages and posts.</td>
				<td><input type='submit' action='submit' value='Save' /></td>
			</tr>
		</table>
	</form>
</div>