<!-- Display AmazonPress Widget Configuration Options -->
<label for="amazonpress-title" style="line-height:35px;display:block;"><?php _e('Display title:', 'widgets'); ?> <input type="text" id="amazonpress-title" name="amazonpress-title" value="<?php echo wp_specialchars($options['TitleText'], true); ?>" /></label>
<label for="amazonpress-tags" style="line-height:35px;display:block;"><?php _e('Default tags:', 'widgets'); ?> <input type="text" id="amazonpress-tags" name="amazonpress-tags" value="<?php echo wp_specialchars($options['DefaultTags'], true); ?>" /></label>
<label for="amazonpress-results" style="line-height:35px;display:block;"><?php _e('Max results:', 'widgets'); ?> <input type="text" id="amazonpress-results" name="amazonpress-results" value="<?php echo wp_specialchars($options['MaxResults'], true); ?>" /></label>
<label for="amazonpress-image" style="line-height:35px;display:block;"><?php _e('Show Images:', 'widgets'); ?> 
	<select id="amazonpress-image" name='amazonpress-image'>
		<option value='0' <?php if($options['ImageSize'] == '0') echo "selected"; ?>>No Images</option>
		<option value='1' <?php if($options['ImageSize'] == '1') echo "selected"; ?>>Small</option>
		<option value='2' <?php if($options['ImageSize'] == '2') echo "selected"; ?>>Medium</option>
	</select>
</label>
<label for="amazonpress-text" style="line-height:35px;display:block;"><?php _e('Show Titles:', 'widgets'); ?> <input type="checkbox" id="amazonpress-text" name="amazonpress-text" value="yes" <?php if($options['ShowText'] == '1') echo 'checked'; ?> /></label>
<label for="amazonpress-desc" style="line-height:35px;display:block;"><?php _e('Show Descriptions:', 'widgets'); ?> <input type="checkbox" id="amazonpress-desc" name="amazonpress-desc" value="yes" <?php if($options['ShowDesc'] == '1') echo 'checked'; ?> /></label>
<label for="amazonpress-sortby">Sorting order for Amazon products on this widget: </label>
<select name='amazonpress-sortby'>
	<option value='default' <?php if($options['SortBy'] == 'default') echo "selected"; ?> />Default Setting</option>
	<option value='random' <?php if($options['SortBy'] == 'random') echo "selected"; ?> />Random</option>
	<option value='salesrank' <?php if($options['SortBy'] == 'salesrank') echo "selected"; ?> />Popularity (high to low)</option>
	<option value='-salesrank' <?php if($options['SortBy'] == '-salesrank') echo "selected"; ?> />Reverse Popularity</option>
	<option value='listprice' <?php if($options['SortBy'] == 'listprice') echo "selected"; ?> />Price (high to low)</option>
	<option value='-listprice' <?php if($options['SortBy'] == '-listprice') echo "selected"; ?> />Reverse Price</option>
</select>
<input type="hidden" name="amazonpresswidget-submit" id="amazonpresswidget-submit" value="1" />
