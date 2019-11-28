<?php
/* Template Name: SearchPage */
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Primer
 * @since   1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area" style="background-color:white; padding: 20px;">

	<main id="main" class="site-main" role="main">

		<form method="post" class="sort_filter_form" action="https://rosehillportal.com/reports/" data-ref="update" _lpchecked="1">
		<input name="action" type="hidden" value="pdb_list_filter">

		<input name="target_instance" type="hidden" value="1">

		<input name="instance_index" type="hidden" value="1">

		<input name="pagelink" type="hidden" value="/reports/?listpage=%1$s">

		<input name="sortstring" type="hidden" value="">

		<input name="orderstring" type="hidden" value="">

		<fieldset class="widefat inline-controls">
			<legend>Search:</legend>
			
			<select name="search_field" id="pdb-search_field" class="search-item">
				<option value="none">(select)</option>
				<option value="id">Record ID</option>
				<option value="section">Section</option>
				<option value="lot">Lot</option>
				<option value="compass">Compass</option>
				<option value="gps_latitude">gps latitude</option>
				<option value="gps_longitude">gps longitude</option>
				<option value="owner_1_last_name">Owner Last Name</option>
				<option value="owner_1">Primary Owner</option>
				<option value="address">Address</option>
				<option value="city">City</option>
				<option value="state">State</option>
				<option value="zip">Zip Code</option>
				<option value="phone">Primary Phone</option>
				<option value="email">Primary Email</option>
				<option value="owner_2">Co-Owner</option>
				<option value="asofdate">As Of Date</option>
				<option value="lot_purchase_date">Lot Purchase Date</option>
				<option value="lot_purchase_price">Lot Purchase Price</option>
				<option value="deed_transfer">Deed Transfer</option>
				<option value="transfer_from_name">Deed Transfer From Name</option>
				<option value="deed_transfer_date">Deed Transfer Date</option>
				<option value="burial_last">Burial Last Name</option>
				<option value="burial_first">Burial First Name</option>
				<option value="burial_middle">Burial Middle Name</option>
				<option value="burial_suffix">Burial Suffix</option>
				<option value="burial_display_name">Burial Display Name</option>
				<option value="burial_number">Burial Number</option>
				<option value="burial_type">Burial Type</option>
				<option value="date_of_birth">date of birth</option>
				<option value="date_of_death">Date of Death</option>
				<option value="date_of_burial">Date of Burial</option>
				<option value="funeral_home">Funeral Home</option>
				<option value="bronze_type">Bronze Type</option>
				<option value="bronze_set">Bronze Set</option>
				<option value="bronze_storage">Bronze Storage</option>
				<option value="veteran">Veteran</option>
				<option value="service_branch">Service Branch</option>
				<option value="notes">Notes</option>
				<option value="map_section">Maps Section</option>
				<option value="section_lots">Section Lots</option>
			</select>

			<input name="operator" type="hidden" class="search-item" value="=">
		
			<input id="participant_search_term" type="text" name="value" class="search-item" value="">
		
			<br/><br/>
			
			<select name="search_field1" id="pdb-search_field2" class="search-item2">
				<option value="none">(select)</option>
				<option value="id">Record ID</option>
				<option value="section">Section</option>
				<option value="lot">Lot</option>
				<option value="compass">Compass</option>
				<option value="gps_latitude">gps latitude</option>
				<option value="gps_longitude">gps longitude</option>
				<option value="owner_1_last_name">Owner Last Name</option>
				<option value="owner_1">Primary Owner</option>
				<option value="address">Address</option>
				<option value="city">City</option>
				<option value="state">State</option>
				<option value="zip">Zip Code</option>
				<option value="phone">Primary Phone</option>
				<option value="email">Primary Email</option>
				<option value="owner_2">Co-Owner</option>
				<option value="asofdate">As Of Date</option>
				<option value="lot_purchase_date">Lot Purchase Date</option>
				<option value="lot_purchase_price">Lot Purchase Price</option>
				<option value="deed_transfer">Deed Transfer</option>
				<option value="transfer_from_name">Deed Transfer From Name</option>
				<option value="deed_transfer_date">Deed Transfer Date</option>
				<option value="burial_last">Burial Last Name</option>
				<option value="burial_first">Burial First Name</option>
				<option value="burial_middle">Burial Middle Name</option>
				<option value="burial_suffix">Burial Suffix</option>
				<option value="burial_display_name">Burial Display Name</option>
				<option value="burial_number">Burial Number</option>
				<option value="burial_type">Burial Type</option>
				<option value="date_of_birth">date of birth</option>
				<option value="date_of_death">Date of Death</option>
				<option value="date_of_burial">Date of Burial</option>
				<option value="funeral_home">Funeral Home</option>
				<option value="bronze_type">Bronze Type</option>
				<option value="bronze_set">Bronze Set</option>
				<option value="bronze_storage">Bronze Storage</option>
				<option value="veteran">Veteran</option>
				<option value="service_branch">Service Branch</option>
				<option value="notes">Notes</option>
				<option value="map_section">Maps Section</option>
				<option value="section_lots">Section Lots</option>
			</select>
			
			<input id="participant_search_term" type="text" name="value1" class="search-item" value="">
		
			<br/><br/>
			
			<input name="submit_button" class="search-form-submit" data-submit="search" type="submit" value="Search">
		
			<input name="submit_button" class="search-form-clear" data-submit="clear" type="submit" value="Clear">
			
		</fieldset>
		
		<fieldset class="widefat inline-controls">
		
		<legend>Sort by:</legend>
		
		<select name="sortBy" id="pdb-sortBy" class="search-item">
			<option value="date_updated" selected="selected">Date Updated</option>
			<option value="owner_1">Primary Owner</option>
			<option value="city">City</option>
			<option value="state">State</option>
			<option value="zip">Zip Code</option>
			<option value="asofdate">As Of Date</option>
		</select>

		<div class="radio-group">
			<label class="checkbox inline search-item" for="pdb-ascdesc-asc">
			<input name="ascdesc" id="pdb-ascdesc-asc" type="radio" class="checkbox inline search-item" value="ASC">
			Ascending</label>
			<label class="checkbox inline search-item" for="pdb-ascdesc-desc">
			<input name="ascdesc" checked="checked" id="pdb-ascdesc-desc" type="radio" class="checkbox inline search-item" value="DESC">
			Descending</label>
		</div>


		</fieldset>
		
</form>



<br/><br/>

<?php if(isset($_POST['submit_button'])) {	 ?>

<table class="wp-list-table widefat fixed pages list-container">


<?php 
	$search_field = $_POST['search_field'];
	
	$search_field1 = $_POST['search_field1'];
	
	$value = $_POST['value'];
	
	$value1 = $_POST['value1'];
	
 	$orderby = $_POST['sortBy'];
	
	$ascdec = $_POST['ascdesc'];
	 
	 if($value1 == NULL && $value != NULL) {
		
		$results = $wpdb->get_results('SELECT * FROM wp_0k8kmyk1bc_participants_database WHERE '.$search_field.' = "'.$value.'" ORDER BY '.$orderby.' '.$ascdec.''); // Query to fetch data from database table and storing in $results
		
	 } else if ($value1 != NULL && $value == NULL) {
		 
		$results = $wpdb->get_results('SELECT * FROM wp_0k8kmyk1bc_participants_database WHERE '.$search_field1.' = "'.$value1.'" ORDER BY '.$orderby.' '.$ascdec.''); 
		
		
	 } else if  ($value == NULL && $value1 == NULL) {
		 
		$results = $wpdb->get_results('SELECT * FROM wp_0k8kmyk1bc_participants_database LIMIT 500 ORDER BY '.$orderby.' '.$ascdec.''); // Query to fetch data from database table and storing in $results
	
		
	 } else if ($value != NULL && $value1 != NULL) {
		 
		
		$results = $wpdb->get_results('SELECT * FROM wp_0k8kmyk1bc_participants_database WHERE '.$search_field1.' = "'.$value1.'" AND '.$search_field.' = "'.$value.'" ORDER BY '.$orderby.' '.$ascdec.'');  
		 
	 }
	 
	

?>

    <caption class="pdb-list-count">Total Records Found: <?php echo $count =  count($results); ?></caption>    	
	<thead>
		<tr><td>There are <?php echo $count; ?> records</td></tr>
	</thead>
      <thead>

        <tr style="text-align:left;">
          <th class="id" scope="col">Record ID</th>
		  <th class="section" scope="col">Section</th>
		  <th class="lot" scope="col">Lot</th>
		  <th class="compass" scope="col">Compass</th>
		  <th class="burial_display_name" scope="col">Burial Display Name</th>
		  <th class="date_of_death" scope="col">Date of Death</th>	  
        </tr>
		
      </thead>

     <tbody> 
	 
	 <?php 

		if(!empty($results))                        // Checking if $results have some values or not
		{  
		
		foreach($results as $row){
		
	?>
		 
		 <tr> 
			 <td class="id-field"> <a href="https://rosehillportal.com/edit-record/?edit=<?php echo $row->id; ?>">Edit Record</a> </td> 
			 <td class="section-field"><?php echo $row->section; ?></td> 
			 <td class="lot-field"><?php echo $row->lot; ?></td> 
			 <td class="lot-compass"><?php echo $row->compass; ?></td> 
			 <td class="owner_2-field"><?php echo $row->burial_display_name; ?></td>
			 <td class="date_of_death-field"><?php echo $row->date_of_death; ?></td> 
		 </tr>
		 
	 <?php
						
		}
		
		}

	?>

	</table>
	
<?php }	?>

	</main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_sidebar( 'tertiary' ); ?>

<?php get_footer(); ?>
