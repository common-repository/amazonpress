<label for="amazonpress_keywords">Page specific keywords for finding Amazon products: </label>
<input type="input" name="amazonpress_keywords" value="<?php echo $amazonpress_keywords; ?>" size="25" />
<br/>
<br/>
<label for="amazonpress_disabled">Disable Amazon related products on this page: </label>
<input type="checkbox" name="amazonpress_disabled" value="true" <?php echo $amazonpress_disabled; ?> /> Disabled
<br/>
<br/>
<label for="amazonpress_sortby">Sorting order for Amazon products on this page: </label>
<select name='amazonpress_sortby'>
	<option value='default' <?php if($amazonpress_sortby == 'default') echo "selected"; ?>>Default Setting</option>
	<option value='random' <?php if($amazonpress_sortby == 'random') echo "selected"; ?>>Random</option>
	<option value='salesrank' <?php if($amazonpress_sortby == 'salesrank') echo "selected"; ?>>Popularity (high to low)</option>
	<option value='-salesrank' <?php if($amazonpress_sortby == '-salesrank') echo "selected"; ?>>Reverse Popularity</option>
	<option value='listprice' <?php if($amazonpress_sortby == 'listprice') echo "selected"; ?>>Price (high to low)</option>
	<option value='-listprice' <?php if($amazonpress_sortby == '-listprice') echo "selected"; ?>>Reverse Price</option>
</select>
