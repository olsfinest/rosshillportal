<?php /* Template Name: ExportImportPage */ 
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

<div id="primary" class="content-area">

	<main id="main" class="site-main" role="main">
	


		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="page-content">
			
			<?

			$host = 'n22l41937881096.db.41937881.1f3.hostedresource.net:3306';	//your Hostname
			$user = 'n22l41937881096'; //Database Username
			$pass = 'T9akl#KJ(/a-(';	//Database Pasword
			$db = 'n22l41937881096';	//Database name
			$table = 'wp_0k8kmyk1bc_participants_database';	//Table name
			$file = 'export';	//CSV File name

			$link = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error());
			
			mysql_select_db($db) or die("Can not connect.");

			
			$result = mysql_query("SHOW COLUMNS FROM ".$table."");
			$i = 0;
			
		?><table>
		<tbody><?php
		?><tr><?php	
			if (mysql_num_rows($result) > 0) {
				
				while ($row = mysql_fetch_assoc($result)) {
				 $csv_output .=	$row['Field'] . ',';
				 
				 ?><th style="text-transform:uppercase; text-align:left;"><strong><?php echo str_replace('_', ' ' ,  $row['Field']);  ?></strong></th><?php
				 
				 $i++;
					
				}
				
			}
			
		
			
				$csv_output .= "\r\n";
			
			$values = mysql_query("SELECT * FROM ".$table."");
			?><tr><?php	
			while ($rowr = mysql_fetch_row($values)) {
				
				?></tr><?php	

				for ($j=0; $j<$i-1; $j++) {

				
					 $csv_output .= $rowr[$j] . ',';
					 
					  ?><td style="text-align:left;"><?php
					  
					  echo $rowr[$j];
					  
					  ?></td><?php
				
				}
			
				?></tr><?php	
			
				$csv_output .= "\r\n";
			
			}
			
		
			
				$filename = date("Y-m-d_H-i",time());
			
				$file = 'csvexportfiles/'.$filename.'rosehillportaldata.csv';
				
				chmod($file, 0777);
				
				file_put_contents($file, $csv_output);

			?>
			</tbody>
			</table>

			</div><!-- .page-content -->
			
		<style>
			table td { padding: 0 10px; font-size: 12px; }
			tbody  {
				max-height:500px;
				 overflow-y: scroll;
			  display: block;
			}
			table { 
				border-collapse: collapse;
				margin: 0 0 1.5em;
				width: 100%;
				display: block;
			}
			th { white-space: nowrap;  padding: 0 10px; font-size: 12px; }
			</style>
			<br/>
			<p>This will download the whole list of participants that match your search terms, and in the order specified by the sort. The export will include records on all list pages. The fields included in the export are defined in the "CSV" column on the Manage Database Fields page.</p>
			<a target="blank" href="https://rosehillportal.com/csvexportfiles/<?php echo ''.$filename.'rosehillportaldata.csv'; ?>" class="button button-primary">Download CSV for this list</a>
			

		</article><!-- #post-## -->

	</main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_sidebar( 'tertiary' ); ?>

<?php get_footer(); ?>
