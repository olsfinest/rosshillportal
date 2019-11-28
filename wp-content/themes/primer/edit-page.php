<?php $get = $_GET['edit']; ?>
<?php /* Template Name: EditPage */ 
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
<?php if(isset($_POST['submit_button'])) {
	
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
	
	
	
	$files1 = $_POST["linkaudio1"];
	
	$countlink11 = count($files1);
	
	for($i = 0; $i < $countlink11; $i++) {
		
		$audiosql .= $files1[$i];
		$audiosql .= ',';
	
	}
	
	
	$files2 = $_POST["linkvideo1"];
	

	
	$countlink11 = count($files2);
	
	for($i = 0; $i < $countlink11; $i++) {
		
		$videosql .= $files2[$i];
		$videosql .= ',';
	
	}
	
	
	$files2 = $_POST["document1"];
	

	$countlink11 = count($files2);
	
	for($i = 0; $i < $countlink11; $i++) {
		
		$docssql .= $files2[$i];
		$docssql .= ',';
	
	}
	

	$files3 = $_POST["picturespic1"];
	
	$countlink11 = count($files3);
	
	for($i = 0; $i < $countlink11; $i++) {
		
		$picssql .= $files3[$i];
		$picssql .= ',';
	
	}
	
	
	$files = $_FILES["linkaudio"];
	
	
    foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
            $file = array(
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key]
            );
            $_FILES = array("upload_file" => $file);
            $attachment_id = media_handle_upload("upload_file", 0);

            if (is_wp_error($attachment_id)) {
                // There was an error uploading the image.
                "Error adding file";
            } else {
				
				$file['type'];
				
				 if($file['type'] == 'audio/mp3'){
						
					 "File added successfully with ID: " . $attachment_id . "<br>";
					 wp_get_attachment_image($attachment_id, array(800, 600)) . "<br>"; 
					
					$audiosql .= $attachment_id;
					$audiosql .= ',';
					
				}
				 
				if($file['type'] == 'video/mp4'){
					  
					 "File added successfully with ID: " . $attachment_id . "<br>";
					 wp_get_attachment_image($attachment_id, array(800, 600)) . "<br>"; 
					
					$videosql .= $attachment_id;
					$videosql .= ',';
					  	  
					  
				}	

				if($file['type'] == 'application/pdf' || $file['type'] == 'application/msword' || $file['type'] == 'text/plain' || $file['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
						
					 "File added successfully with ID: " . $attachment_id . "<br>";
					 wp_get_attachment_image($attachment_id, array(800, 600)) . "<br>"; 
					
					$docssql .= $attachment_id;
					$docssql .= ',';
					
				}	

				if($file['type'] == 'image/jpeg' || $file['type'] == 'image/png'){
							
					"File added successfully with ID: " . $attachment_id . "<br>";
					wp_get_attachment_image($attachment_id, array(800, 600)) . "<br>"; 
							
					$picssql .= $attachment_id;
					$picssql .= ',';
						
				}
				
                
            }
        }
    }
	
	


	$link = $_POST['link'];
	$text = $_POST['text'];


	$countlink = count($link);
	
	$countlink = $countlink - 1;
	
	for($i = 0; $i <= $countlink; $i++) {
		
		$sociallink .= 	'<a href="'.$link[$i].'">'.$text[$i].'</a>';
		$sociallink .= ',';
	
	}	
	
	$sociallink;
	
	$deletefile = $_POST['photo-deletefile'];
	
	$date  = $_POST['asofdate'];
	
	$dt = $date;
	
	if($_FILES['photo']['name'] != NULL) {
			
			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			$uploadedfile = $_FILES['photo'];

			$upload_overrides = array( 'test_form' => false);

			$movefile = wp_handle_upload($uploadedfile , $upload_overrides);

			if($movefile && ! isset($movefile['error'])) {

				$imageurl = $movefile['url'];
						
					if($deletefile == 'delete') {					

		
				} else {				
				
				
			    $wpdb->update('wp_0k8kmyk1bc_participants_database', array('id'=>$get, 'section'=>$_POST['section'] , 'lot'=>$_POST['lot'] , 'compass'=>$_POST['compass'] , 'owner_1_last_name'=>$_POST['owner_1_last_name'] , 'owner_1'=>$_POST['owner_1'] , 'address'=>$_POST['address']  , 'city'=>$_POST['city'] , 'state'=>$_POST['state'] , 'zip'=>$_POST['zip'] , 'phone'=>$_POST['phone'] , 'email_primary'=>$_POST['email_primary'] , 'owner_2'=>$_POST['owner_2'] , 'asofdate'=> $_POST['asofdate'] , 'lot_purchase_date'=>$_POST['lot_purchase_date'] , 'lot_purchase_price'=>$_POST['lot_purchase_price'] , 'burial_last'=>$_POST['burial_last'] , 'burial_first'=>$_POST['burial_first'] , 'burial_middle'=>$_POST['burial_middle'] , 'burial_suffix'=>$_POST['burial_suffix'] , 'burial_display_name'=>$_POST['burial_display_name'] , 'burial_number'=>$_POST['burial_number'] , 'burial_type'=>$_POST['burial_type'] , 'date_of_death'=>$_POST['date_of_death'] , 'date_of_burial'=>$_POST['date_of_burial'] , 'funeral_home'=>$_POST['funeral_home'] , 'bronze_type'=>$_POST['bronze_type'] , 'bronze_set'=>$_POST['bronze_set'] , 'bronze_storage'=>$_POST['bronze_storage'] , 'veteran'=>$_POST['veteran'] , 'service_branch'=>$_POST['service_branch'] , 'notes'=>$_POST['notes'] , 'map_section'=> 'a:2:{i:0;s:87:"'.$_POST['map_section1'].'";i:1;s:4:"'.$_POST['map_section2'].'";}' , 'section_lots'=> 'a:2:{i:0;s:92:"'.$_POST['section_lots1'].'";i:1;s:4:"'.$_POST['section_lots2'].'";}' , 'deed_transfer'=>$_POST['deed_transfer'] , 'transfer_from_name'=>$_POST['transfer_from_name'] , 'deed_transfer_date'=>$_POST['deed_transfer_date'] , 'photo'=> $imageurl , 'website'=> $sociallink , 'audio'=> $audiosql ,  'video'=> $videosql ,   'docs'=> $docssql , 'pictures'=> $picssql   , 'date_of_birth'=> $_POST['date_of_birth'] , 'gps_latitude'=> $_POST['gps_latitude'] ,  'gps_longitude'=> $_POST['gps_longitude']  ), array('id'=>$get));	
					
				}
				
			}

	} else {
			
			
			
				if($deletefile == 'delete') {
					
				$wpdb->update('wp_0k8kmyk1bc_participants_database', array('id'=>$get, 'section'=>$_POST['section'] , 'lot'=>$_POST['lot'] , 'compass'=>$_POST['compass'] , 'owner_1_last_name'=>$_POST['owner_1_last_name'] , 'owner_1'=>$_POST['owner_1'] , 'address'=>$_POST['address']  , 'city'=>$_POST['city'] , 'state'=>$_POST['state'] , 'zip'=>$_POST['zip'] , 'phone'=>$_POST['phone'] , 'email_primary'=>$_POST['email_primary'] , 'owner_2'=>$_POST['owner_2'] , 'asofdate'=> $_POST['asofdate'] , 'lot_purchase_date'=>$_POST['lot_purchase_date'] , 'lot_purchase_price'=>$_POST['lot_purchase_price'] , 'burial_last'=>$_POST['burial_last'] , 'burial_first'=>$_POST['burial_first'] , 'burial_middle'=>$_POST['burial_middle'] , 'burial_suffix'=>$_POST['burial_suffix'] , 'burial_display_name'=>$_POST['burial_display_name'] , 'burial_number'=>$_POST['burial_number'] , 'burial_type'=>$_POST['burial_type'] , 'date_of_death'=>$_POST['date_of_death'] , 'date_of_burial'=>$_POST['date_of_burial'] , 'funeral_home'=>$_POST['funeral_home'] , 'bronze_type'=>$_POST['bronze_type'] , 'bronze_set'=>$_POST['bronze_set'] , 'bronze_storage'=>$_POST['bronze_storage'] , 'veteran'=>$_POST['veteran'] , 'service_branch'=>$_POST['service_branch'] , 'notes'=>$_POST['notes'] , 'map_section'=> 'a:2:{i:0;s:87:"'.$_POST['map_section1'].'";i:1;s:4:"'.$_POST['map_section2'].'";}' , 'section_lots'=> 'a:2:{i:0;s:92:"'.$_POST['section_lots1'].'";i:1;s:4:"'.$_POST['section_lots2'].'";}' , 'deed_transfer'=>$_POST['deed_transfer'] , 'transfer_from_name'=>$_POST['transfer_from_name'] , 'deed_transfer_date'=>$_POST['deed_transfer_date'] , 'photo'=> '' , 'website'=> $sociallink , 'audio'=> $audiosql ,  'video'=> $videosql ,   'docs'=> $docssql , 'pictures'=> $picssql   , 'date_of_birth'=> $_POST['date_of_birth'] , 'gps_latitude'=> $_POST['gps_latitude'] ,  'gps_longitude'=> $_POST['gps_longitude']   ), array('id'=>$get));	
				

				} else {				
				
				$wpdb->update('wp_0k8kmyk1bc_participants_database', array('id'=>$get, 'section'=>$_POST['section'] , 'lot'=>$_POST['lot'] , 'compass'=>$_POST['compass'] , 'owner_1_last_name'=>$_POST['owner_1_last_name'] , 'owner_1'=>$_POST['owner_1'] , 'address'=>$_POST['address']  , 'city'=>$_POST['city'] , 'state'=>$_POST['state'] , 'zip'=>$_POST['zip'] , 'phone'=>$_POST['phone'] , 'email_primary'=>$_POST['email_primary'] , 'owner_2'=>$_POST['owner_2'] , 'asofdate'=> $_POST['asofdate'] , 'lot_purchase_date'=>$_POST['lot_purchase_date'] , 'lot_purchase_price'=>$_POST['lot_purchase_price'] , 'burial_last'=>$_POST['burial_last'] , 'burial_first'=>$_POST['burial_first'] , 'burial_middle'=>$_POST['burial_middle'] , 'burial_suffix'=>$_POST['burial_suffix'] , 'burial_display_name'=>$_POST['burial_display_name'] , 'burial_number'=>$_POST['burial_number'] , 'burial_type'=>$_POST['burial_type'] , 'date_of_death'=>$_POST['date_of_death'] , 'date_of_burial'=>$_POST['date_of_burial'] , 'funeral_home'=>$_POST['funeral_home'] , 'bronze_type'=>$_POST['bronze_type'] , 'bronze_set'=>$_POST['bronze_set'] , 'bronze_storage'=>$_POST['bronze_storage'] , 'veteran'=>$_POST['veteran'] , 'service_branch'=>$_POST['service_branch'] , 'notes'=>$_POST['notes'] , 'map_section'=> 'a:2:{i:0;s:87:"'.$_POST['map_section1'].'";i:1;s:4:"'.$_POST['map_section2'].'";}' , 'section_lots'=> 'a:2:{i:0;s:92:"'.$_POST['section_lots1'].'";i:1;s:4:"'.$_POST['section_lots2'].'";}' , 'deed_transfer'=>$_POST['deed_transfer'] , 'transfer_from_name'=>$_POST['transfer_from_name'] , 'deed_transfer_date'=>$_POST['deed_transfer_date']  , 'website'=> $sociallink , 'audio'=> $audiosql ,  'video'=> $videosql ,   'docs'=> $docssql , 'pictures'=> $picssql  , 'date_of_birth'=> $_POST['date_of_birth'] , 'gps_latitude'=> $_POST['gps_latitude'] ,  'gps_longitude'=> $_POST['gps_longitude']   ), array('id'=>$get));
				
				}
			

	}	
	 
	?>
		
	<div id="successfull" style="padding:20px; text-align:center; font-size:25px; font-weight:bold;">Update Successful</div>

	<?php

	
} ?>

<div id="primary" class="content-area">

	<main id="main" class="site-main" role="main">
	


		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="page-content">
			
			<a style=" -webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px; float:left; background-color: #3e4c75; border-color: #3e4c75; color:white; padding:10px 15px;" href="https://rosehillportal.com/delete-a-record/">Delete A Record</a>
<a style=" -webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px; float:right; background-color: #3e4c75; border-color: #3e4c75; color:white; padding:10px 15px;"  href="https://rosehillportal.com/update-a-record/">Update A Record</a>
<br style="clear:both;" />
			
			<?php
			$results = $wpdb->get_results('SELECT * FROM wp_0k8kmyk1bc_participants_database WHERE id = '.$get.''); // Query to fetch data from database table and storing in $results
			if(!empty($results))                        // Checking if $results have some values or not
			{    
				     
				foreach($results as $row){
					
				?>
				<div class="page-content">

	<p>Edit a record in the table below. To move to the next field in the same row, press TAB or click the cell in the next field.</p>
<div class="wrap pdb-signup pdb-instance-2 ">

    <form method="post" enctype="multipart/form-data" autocomplete="off" action="/edit-record/?edit=<?php echo $get; ?>#successfull"><input name="action" type="hidden" value="signup">

<table class="form-table pdb-signup">
	
	
	  <tbody class="field-group field-group-submit">
      
        <tr>
          <td class="submit-buttons">
            
           <input name="submit_button" type="submit" class="button-primary pdb-submit" value="Update Record">
            
          </td>
          <td class="submit-buttons">
            
                      
          </td>
        </tr>
        
      </tbody>	

      
      <tbody class="field-group field-group-main">
                
		
		  
        <tr class="text-line">

          <th for="pdb-section">Section</th>

          <td>

            <input name="section" id="pdb-section" type="text" class=" regular-text" value="<?php 	echo $row->section; ?>" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-lot">Lot</th>

          <td>

            <input name="lot" id="pdb-lot" type="text" class=" regular-text" value="<?php 	echo $row->lot; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-compass">Compass</th>

          <td>

            <input name="compass" id="pdb-compass" type="text" class=" regular-text" value="<?php 	echo $row->compass; ?>">

                        
          </td>

        </tr>
		
		
		<tr class="text-line">

          <th for="pdb-notes">GPS Latitude</th>

          <td>

            <input name="gps_latitude" id="gps_latitude" type="text" class=" regular-text" value="<?php echo $row->gps_latitude; ?>">

                        
          </td>

        </tr>
		
		<tr class="text-line">

          <th for="pdb-notes">GPS Longitude</th>

          <td>

            <input name="gps_longitude" id="gps_longitude" type="text" class=" regular-text" value="<?php echo $row->gps_longitude; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-owner_1_last_name">Owner Last Name</th>

          <td>

            <input name="owner_1_last_name" id="pdb-owner_1_last_name" type="text" class=" regular-text" value="<?php 	echo $row->owner_1_last_name; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-owner_1">Primary Owner</th>

          <td>

            <input name="owner_1" id="pdb-owner_1" type="text" class=" regular-text" value="<?php 	echo $row->owner_1; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-address">Address</th>

          <td>

            <input name="address" id="pdb-address" type="text" class=" regular-text" value="<?php 	echo $row->address; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-city">City</th>

          <td>

            <input name="city" id="pdb-city" type="text" class=" regular-text" value="<?php 	echo $row->city; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-state">State</th>

          <td>

            <input name="state" id="pdb-state" type="text" class=" regular-text" value="<?php 	echo $row->state; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-zip">Zip Code</th>

          <td>

            <input name="zip" id="pdb-zip" type="text" class=" regular-text" value="<?php 	echo $row->zip; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-phone">Primary Phone</th>

          <td>

            <input name="phone" id="pdb-phone" type="text" class=" regular-text" value="<?php 	echo $row->phone; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-email">Primary Email</th>

          <td>

            <input name="email_primary" id="pdb-email_primary" type="text" class="required-field regular-text" value="<?php 	echo $row->email_primary; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-owner_2">Co-Owner</th>

          <td>

            <input name="owner_2" id="pdb-owner_2" type="text" class=" regular-text" value="<?php 	echo $row->owner_2; ?>">

                        
          </td>

        </tr>
  
        
		  <?php $timestamp= $row->asofdate; ?>
		  
        <tr class="date">

          <th for="pdb-asofdate">As Of Date</th>

          <td>

            <input name="asofdate" id="pdb-asofdate" type="text" class=" regular-text date_field" value="<?php echo $row->asofdate;?>">
	  
			   
			  
		
			  <br/>
                        <span class="helptext">Enter date record has been updated. MM/DD/YYYY</span>
                        
          </td>

        </tr>
		  
		  
  
        
        <tr class="text-line">

          <th for="pdb-lot_purchase_date">Lot Purchase Date</th>

          <td>

            <input name="lot_purchase_date" id="pdb-lot_purchase_date" type="text" class=" regular-text" value="<?php echo $row->lot_purchase_date; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-lot_purchase_price">Lot Purchase Price</th>

          <td>

            <input name="lot_purchase_price" id="pdb-lot_purchase_price" type="text" class=" regular-text" value="<?php 	echo $row->lot_purchase_price; ?>">

                        
          </td>

        </tr>
  
		  <tr class="dropdown">

          <th for="pdb-deed_transfer">Deed Transfer</th>

          <td>

            <select name="deed_transfer" id="pdb-deed_transfer">
<option value="<?php 	echo $row->deed_transfer; ?>" selected="selected"><?php 	echo $row->deed_transfer; ?></option>
<option value="No">No</option>
<option value="Yes">Yes</option>
</select>

                        
          </td>

        </tr>
		  
		  <tr class="text-line">

          <th for="pdb-transfer_from_name">Deed Transfer From Name</th>

          <td>

            <input name="transfer_from_name" id="pdb-transfer_from_name" type="text" class=" regular-text" value="<?php 	echo $row->transfer_from_name; ?>">

                        
          </td>

        </tr>
		  
		  <tr class="text-line">

          <th for="pdb-deed_transfer_date">Deed Transfer Date</th>

          <td>

            <input name="deed_transfer_date" id="pdb-deed_transfer_date" type="text" class=" regular-text" value="<?php 	echo $row->deed_transfer_date; ?>">

                        
          </td>

        </tr>
        
        <tr class="text-line">

          <th for="pdb-burial_last">Burial Last Name</th>

          <td>

            <input name="burial_last" id="pdb-burial_last" type="text" class=" regular-text" value="<?php 	echo $row->burial_last; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-burial_first">Burial First Name</th>

          <td>

            <input name="burial_first" id="pdb-burial_first" type="text" class=" regular-text" value="<?php 	echo $row->burial_first; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-burial_middle">Burial Middle Name</th>

          <td>

            <input name="burial_middle" id="pdb-burial_middle" type="text" class=" regular-text" value="<?php 	echo $row->burial_middle; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-burial_suffix">Burial Suffix</th>

          <td>

            <input name="burial_suffix" id="pdb-burial_suffix" type="text" class=" regular-text" value="<?php 	echo $row->burial_suffix; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-burial_display_name">Burial Display Name</th>

          <td>

            <input name="burial_display_name" id="pdb-burial_display_name" type="text" class=" regular-text" value="<?php 	echo $row->burial_display_name; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-burial_number">Burial Number</th>

          <td>

            <input name="burial_number" id="pdb-burial_number" type="text" class=" regular-text" value="<?php 	echo $row->burial_number; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="dropdown">

          <th for="pdb-burial_type">Burial Type</th>

          <td>
<select name="burial_type" id="pdb-burial_type">
<option value="<?php echo $row->burial_type; ?>" selected="selected"><?php echo $row->burial_type; ?></option>
<option value="Full">Full</option>
<option value="Cremains">Cremains</option>
<option value="Baby">Baby</option>
<option value="Limbs">Limbs</option>
<option value="Disinterred">Disinterred</option>
<option value="Other">Other</option>
</select>
                        
          </td>

        </tr>
		
		<tr class="text-line">

          <th for="pdb-date_of_birth">Date of Birth</th>

          <td>

            <input name="date_of_birth" id="pdb-date_of_birth" type="text" class=" regular-text" value="<?php echo $row->date_of_birth; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-date_of_death">Date of Death</th>

          <td>

            <input name="date_of_death" id="pdb-date_of_death" type="text" class=" regular-text" value="<?php echo $row->date_of_death; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-date_of_burial">Date of Burial</th>

          <td>

            <input name="date_of_burial" id="pdb-date_of_burial" type="text" class=" regular-text" value="<?php echo $row->date_of_burial; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-funeral_home">Funeral Home</th>

          <td>

            <input name="funeral_home" id="pdb-funeral_home" type="text" class=" regular-text" value="<?php echo $row->funeral_home; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="dropdown">

          <th for="pdb-bronze_type">Bronze Type</th>

          <td>
		  
		  <select name="bronze_type" id="pdb-bronze_type">
<option value="<?php echo $row->bronze_type; ?>" selected="selected"><?php echo $row->bronze_type; ?></option>
<option value="None">None</option>
<option value="Individual">Individual</option>
<option value="Outside">Outside</option>
<option value="Comp">Comp</option>
<option value="VA Marker">VA Marker</option>
<option value="GI">GI</option>
<option value="Payment Plan">Payment Plan</option>
<option value="Ordered">Ordered</option>
</select>

                
          </td>

        </tr>
  
        
        <tr class="dropdown">

          <th for="pdb-bronze_set">Bronze Set</th>

          <td>

            <select name="bronze_set" id="pdb-bronze_set">
<option value="<?php echo $row->bronze_set; ?>" selected="selected"><?php echo $row->bronze_set; ?></option>
<option value="Yes">Yes</option>
<option value="On Order">On Order</option>
</select>

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-bronze_storage">Bronze Storage</th>

          <td>

            <input name="bronze_storage" id="pdb-bronze_storage" type="text" class=" regular-text" value="<?php echo $row->bronze_storage; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="dropdown">

          <th for="pdb-veteran">Veteran</th>

          <td>

            <select name="veteran" id="pdb-veteran">
<option value="<?php echo $row->veteran; ?>" selected="selected"><?php echo $row->veteran; ?></option>
<option value="Yes">Yes</option>
<option value="No">No</option>
</select>

                        
          </td>

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-service_branch">Service Branch</th>
		  
		  <td>

            <select name="service_branch" id="pdb-service_branch">
<option value="<?php echo $row->service_branch; ?>" selected="selected"><?php echo $row->service_branch; ?></option>
<option value="Army">Army</option>
<option value="Navy">Navy</option>
<option value="Air Force">Air Force</option>
<option value="Marines">Marines</option>
<option value="Coast Guard">Coast Guard</option>
<option value="Unknown">Unknown</option>
</select>

                        <span class="helptext">Branch of Service</span>
                        
          </td>

        

        </tr>
  
        
        <tr class="text-line">

          <th for="pdb-notes">Notes</th>

          <td>

            <input name="notes" id="pdb-notes" type="text" class=" regular-text" value="<?php echo $row->notes; ?>">

                        
          </td>

        </tr>
  
        
        <tr class="link">

          <th for="pdb-map_section">Maps Section</th>

          <td>

        <div class="link-element">
		
		
		<?php $url = explode('"', $row->map_section);	?>
			
	
		<?php $url1 = explode('"', $row->section_lots);  ?>
		
				
		<input name="map_section1" id="pdb-map_section-url" placeholder="(URL)" type="url" value="<?php  echo $url[1]; ?>">
		<input name="map_section2" id="pdb-map_section-text" placeholder="Link Text" type="text" value="<?php  echo $url[3]; ?>"></div>
			  
	
                
          </td>

        </tr>
  
        
        <tr class="link">

          <th for="pdb-section_lots">Section Lots</th>

          <td>

            <div class="link-element">
<input name="section_lots1" id="pdb-section_lots-url" placeholder="(URL)" type="url" value="<?php  echo $url1[1]; ?>">
<input name="section_lots2" id="pdb-section_lots-text" placeholder="Link Text" type="text" value="<?php  echo $url1[3]; ?>"></div>

                        
          </td>

        </tr>
		
  
                
        </tbody>
	
	<tbody class="field-group field-group-personal">
                
        
        <tr class="image-upload">
		
          <th for="pdb-photo">Profile Photo </th>

          <td>

            <input name="MAX_FILE_SIZE" type="hidden" value="52428800">

<input name="photo" type="hidden" value="">
<div class="pdb-upload" style="width: 400px; float: left;">
<?php 
	if ($row->photo != NULL) {	
		 echo $row->photo; 	
		 echo '<br/>';
	}		 
?>
<input name="photo" id="pdb-photo" type="file">
</div>

<span style="display:block;" class="file-delete"><label style="background-color:#FFBBBB; padding: 5px;"><input type="checkbox" value="delete" name="photo-deletefile">delete</label></span>

                        
          </td>

        </tr>
  
        
        <tr class="text-area">

          <th for="pdb-website">Website, Blog or Social Media Link
		  
		  <?php 
		  
		  $website = explode(",",$row->website);
		  

		  $countwebsite = count($website);
		  $countwebsite = $countwebsite - 1;
		  $countwebsite;
		  
		  ?>
		  
		  </th>

          <td>
		  
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
		<script src="https://use.fontawesome.com/e2d16502eb.js"></script>
		
		
		<?php for($i = 0; $i < $countwebsite; $i++) { 
		
		$website1 = explode('"', $website[$i]);
		
		
		?>
		
			<div class="row" id="social<?php echo $i + 100; ?>">
	  
						<div class="col-md-5">
						<div class="form-group">
					
							<input type="text" class="form-control" name="link[]" placeholder="link" id="link<?php echo $i + 100; ?>" value="<?php echo $website1[1]; ?>">
						
						</div>
						</div>

						<div class="col-md-5">
						<div class="form-group">
					
						<input type="text" class="form-control" name="text[]" placeholder="text" id="text<?php echo $i + 100; ?>" value="<?php   
$website2 = str_replace("</a>","", $website1[2]);
echo str_replace(">","", $website2);
?>">
							
						</div>
						</div>


						<div class="col-md-2"> 
						<a href="javascript:void(0);" onclick="removeRow(<?php echo $i + 100; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
						</div>

			</div>
				
		
		
		<?php } ?>
		
	
			<div class="row" id="social<?php echo $i + 1; ?>">
	  
						<div class="col-md-5">
						<div class="form-group">
					
							<input type="text" class="form-control" name="link[]" placeholder="link" id="link<?php echo $i + 1; ?>" value="">
						
						</div>
						</div>

						<div class="col-md-5">
						<div class="form-group">
					
						<input type="text" class="form-control" name="text[]" placeholder="text" id="text<?php echo $i + 1; ?>" value="">
							
						</div>
						</div>


						<div class="col-md-2"> 
						<a href="javascript:void(0);" onclick="removeRow(<?php echo $i + 1; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
						</div>

			</div>
				


		<div id="addedRows"></div>


		<div class="row">
		<button class="btn btn-warning" onclick="addMoreRows(this.form);" type="button" style="margin: 0 10px 10px 15px;">Add New Social</button>    
		</div>    


		<script>
		var rowCount = 1;
		function addMoreRows(frm) {
		rowCount ++;
		var recRow = '<div class="row" id="social'+rowCount+'"><div class="col-md-5"><div class="form-group"><input type="text" name="link[]" id="link'+rowCount+'" placeholder="link" class="form-control"></div></div><div class="col-md-5"><div class="form-group"><input type="text" class="form-control" name="text[]" placeholder="text" id="text'+rowCount+'"></div></div><div class="col-md-2"><a href="javascript:void(0);" onclick="removeRow('+rowCount+');" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a></div></div>';
		jQuery('#addedRows').append(recRow);
		}
		   
		function removeRow(removeNum) {
			
		if(removeNum == 1){
			alert("Cannot Delete this Row");
			return false;
		} else {    
		jQuery('#social'+removeNum).remove(); 
		}

		}   
		</script>


          
                        
          </td>

        </tr>

          <tr class="text-area">

          <th for="pdb-audio">Audio</th>

          <td>
		  
		  <?php 
		  
		  $audio = explode(',', $row->audio);
		  
		  $count1 = count($audio);
		  $count1 = $count1 - 1;
		  
		  for($i = 0; $i < $count1; $i++) { 
		  
		  ?>
		  
		  <div class="row" id="audio<?php echo $i + 100; ?>">

				  
				<div class="col-md-10">
				<div class="form-group">
				
					<input type="hidden" class="form-control" name="linkaudio1[]" id="linkvideo<?php echo $i + 100; ?>" value="<?php echo $audio[$i]; ?>">		
					<input type="text" class="form-control disabled" name="linkaudio12[]" id="linkaudio<?php echo $i + 100; ?>" value="<?php echo wp_get_attachment_url( $audio[$i] ); ?>">
					
				</div>
				</div>


				<div class="col-md-2"> 
					<a href="javascript:void(0);" onclick="removeRow1(<?php echo $i + 100; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
				</div>

			</div>
		  
		  <?php
	
	
	
		  } ?>

			<div class="row" id="audio<?php echo $i + 1; ?>">

				  
				<div class="col-md-10">
				<div class="form-group">
						
					<input type="file" class="form-control" name="linkaudio[]" id="linkaudio<?php echo $i + 1; ?>" value="">
							
				</div>
				</div>


				<div class="col-md-2"> 
					<a href="javascript:void(0);" onclick="removeRow1(<?php echo $i + 1; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
				</div>

			</div>
					



			<div id="addedRows1"></div>


			<div class="row">
			<button class="btn btn-warning" onclick="addMoreRows1(this.form);" type="button" style="margin: 0 10px 10px 15px;">Add New audio</button>    
			</div>    


			<script>
			var rowCount = 1;
			function addMoreRows1(frm) {
			rowCount ++;
			var recRow = '<div class="row" id="audio'+rowCount+'"><div class="col-md-10"><div class="form-group"><input type="file" name="linkaudio[]" id="linkaudio'+rowCount+'" class="form-control"></div></div><div class="col-md-2"><a href="javascript:void(0);" onclick="removeRow1('+rowCount+');" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a></div></div>';
			jQuery('#addedRows1').append(recRow);
			}
			   
			function removeRow1(removeNum1) {
				
			if(removeNum1 == 1){
				alert("Cannot Delete this Row");
				return false;
			} else {    
			jQuery('#audio'+removeNum1).remove(); 
			}

			}   
			</script>


         
                        
          </td>

        </tr>
  
        
         <tr class="text-area">

          <th for="pdb-video">Video</th>
		  
          <td>
		  
		  <?php 

		  $video = explode(',', $row->video);
		  
		  $count1 = count($video);
		  $count1 = $count1 - 1;
		  
		  for($i = 0; $i < $count1; $i++) { 
		  
		  ?>
		  
		  <div class="row" id="video<?php echo $i + 100; ?>">

				  
				<div class="col-md-10">
				<div class="form-group">
				
					<input type="hidden" class="form-control" name="linkvideo1[]" id="linkvideo<?php echo $i + 100; ?>" value="<?php echo $video[$i]; ?>">	
					<input type="text" class="form-control disabled" name="linkvideo12[]" id="linkvideo<?php echo $i + 100; ?>" value="<?php echo wp_get_attachment_url( $video[$i] ); ?>">
							
				</div>
				</div>


				<div class="col-md-2"> 
					<a href="javascript:void(0);" onclick="removeRow2(<?php echo $i + 100; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
				</div>

			</div>
		  
		  <?php 
		  
		  }
		  
		  ?>
		  


				<div class="row" id="video<?php echo $i + 1; ?>">

					  
					<div class="col-md-10">
					<div class="form-group">
							
						<input type="file" class="form-control" name="linkaudio[]" id="linkaudio<?php echo $i + 1; ?>" value="">
								
					</div>
					</div>


					<div class="col-md-2"> 
						<a href="javascript:void(0);" onclick="removeRow2(<?php echo $i + 1; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
					</div>

				</div>
						



				<div id="addedRows2"></div>


				<div class="row">
				<button class="btn btn-warning" onclick="addMoreRows2(this.form);" type="button" style="margin: 0 10px 10px 15px;">Add New video</button>    
				</div>    


				<script>
				var rowCount = 1;
				function addMoreRows2(frm) {
				rowCount ++;
				var recRow = '<div class="row" id="video'+rowCount+'"><div class="col-md-10"><div class="form-group"><input type="file" name="linkaudio[]" id="linkaudio'+rowCount+'" class="form-control"></div></div><div class="col-md-2"><a href="javascript:void(0);" onclick="removeRow2('+rowCount+');" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a></div></div>';
				jQuery('#addedRows2').append(recRow);
				}
				   
				function removeRow2(removeNum1) {
					
				if(removeNum1 == 1){
					alert("Cannot Delete this Row");
					return false;
				} else {    
				jQuery('#video'+removeNum1).remove(); 
				}

				}   
				</script>
                        
          </td>

        </tr>
  
  
        
        <tr class="text-area">

          <th for="pdb-docs">Docs</th>

          <td>
		  
		  
		    <?php 

		  $docs = explode(',', $row->docs);
		  
		  $count1 = count($docs);
		  $count1 = $count1 - 1;
		  
		  for($i = 0; $i < $count1; $i++) { 
		  
		  ?>
		  
		  <div class="row" id="docs<?php echo $i + 100; ?>">

				  
				<div class="col-md-10">
				<div class="form-group">
				
					<input type="hidden" class="form-control" name="document1[]" id="document<?php echo $i + 100; ?>" value="<?php echo $docs[$i]; ?>">	
					<input type="text" class="form-control disabled" name="document12[]" id="document<?php echo $i + 100; ?>" value="<?php echo wp_get_attachment_url( $docs[$i] ); ?>">
							
				</div>
				</div>


				<div class="col-md-2"> 
					<a href="javascript:void(0);" onclick="removeRow3(<?php echo $i + 100; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
				</div>

			</div>
		  
		  <?php 
		  
		  }
		  
		  ?>
		  
		
		  	<div class="row" id="docs<?php echo $i + 1; ?>">

					  
			<div class="col-md-10">
				<div class="form-group">
							
						<input type="file" class="form-control" name="linkaudio[]" id="linkaudio<?php echo $i + 1; ?>" value="">
								
					</div>
					</div>


					<div class="col-md-2"> 
						<a href="javascript:void(0);" onclick="removeRow3(<?php echo $i + 1; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
					</div>

			</div>
					

			<div id="addedRows3"></div>


			<div class="row">
			<button class="btn btn-warning" onclick="addMoreRows2(this.form);" type="button" style="margin: 0 10px 10px 15px;">Add New docs</button>    
			</div>    


			<script>
			var rowCount = 1;
			
			function addMoreRows2(frm) {
				rowCount ++;
				var recRow = '<div class="row" id="docs'+rowCount+'"><div class="col-md-10"><div class="form-group"><input type="file" name="linkaudio[]" id="linkaudio'+rowCount+'" class="form-control"></div></div><div class="col-md-2"><a href="javascript:void(0);" onclick="removeRow3('+rowCount+');" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a></div></div>';
				jQuery('#addedRows3').append(recRow);
			}
			   
			function removeRow3(removeNum3) {
				
			if(removeNum3 == 1){
				alert("Cannot Delete this Row");
				return false;
			} else {    
				jQuery('#docs'+removeNum3).remove(); 
			}

			}   
			</script>
		
               
          </td>

        </tr>
  
        
        <tr class="text-area">

          <th for="pdb-pictures">Pictures</th>

          <td>
		  
		      <?php 

		  $pictures = explode(',', $row->pictures);
		  
		  $count1 = count($pictures);
		  $count1 = $count1 - 1;
		  
		  for($i = 0; $i < $count1; $i++) { 
		  
		  ?>
		  
		  <div class="row" id="pictures<?php echo $i + 100; ?>">

				  
				<div class="col-md-10">
				<div class="form-group">
				
					<input type="hidden" class="form-control" name="picturespic1[]" id="picturespic<?php echo $i + 100; ?>" value="<?php echo $pictures[$i]; ?>">	
					<input type="text" class="form-control disabled" name="picturespic12[]" id="picturespic<?php echo $i + 100; ?>" value="<?php echo wp_get_attachment_url( $pictures[$i] ); ?>">
							
				</div>
				</div>


				<div class="col-md-2"> 
					<a href="javascript:void(0);" onclick="removeRow4(<?php echo $i + 100; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
				</div>

			</div>
		  
		  <?php 
		  
		  }
		  
		  ?>
		  

            <div class="row" id="pictures<?php echo $i + 1; ?>">

					  
			<div class="col-md-10">
				<div class="form-group">
							
						<input type="file" class="form-control" name="linkaudio[]" id="linkaudio<?php echo $i + 1; ?>" value="">
								
					</div>
					</div>


					<div class="col-md-2"> 
						<a href="javascript:void(0);" onclick="removeRow4(<?php echo $i + 1; ?>);" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a>    
					</div>

			</div>
					

			<div id="addedRows4"></div>


			<div class="row">
			<button class="btn btn-warning" onclick="addMoreRows3(this.form);" type="button" style="margin: 0 10px 10px 15px;">Add New pictures</button>    
			</div>    


			<script>
			var rowCount1 = 1;
			
			function addMoreRows3(frm) {
				rowCount1 ++;
				var recRow = '<div class="row" id="pictures'+rowCount1+'"><div class="col-md-10"><div class="form-group"><input type="file" name="linkaudio[]" id="linkaudio'+rowCount1+'" class="form-control"></div></div><div class="col-md-2"><a href="javascript:void(0);" onclick="removeRow4('+rowCount1+');" style="float:left;"> <i  class="fa fa-trash btn btn-danger"></i></a></div></div>';
				jQuery('#addedRows4').append(recRow);
			}
			   
			function removeRow4(removeNum4) {
				
			if(removeNum4 == 1){
				alert("Cannot Delete this Row");
				return false;
			} else {    
				jQuery('#pictures'+removeNum4).remove(); 
			}

			}   
			</script>

			
          </td>

        </tr>
  
                
        </tbody>

	
            
      
	  <tbody class="field-group field-group-submit">
      
        <tr>
          <td class="submit-buttons">
            
           <input name="submit_button" type="submit" class="button-primary pdb-submit" value="Update Record">
            
          </td>
          <td class="submit-buttons">
            
                      
          </td>
        </tr>
        
      </tbody>	
      
    </table>

    
    
  </form>  
</div>


</div>
				
<?php
				
					         
				
					
	}
				

	}
?>





			</div><!-- .page-content -->


		</article><!-- #post-## -->

	</main><!-- #main -->

</div><!-- #primary -->

<style>
.disabled { pointer-events: none;  }
</style>

<?php get_sidebar(); ?>

<?php get_sidebar( 'tertiary' ); ?>

<?php get_footer(); ?>