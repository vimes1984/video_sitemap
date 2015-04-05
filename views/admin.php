<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   video-sitemap
 * @author    bawd <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 4-4-2015 BAWD
 */
	$getclass = new VideoSitemap();
	$post_types = $getclass->get_post_types()
?>
<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<h3><?php _e('Xml sitemap for videos', 'video sitemap'); ?></h3>
	<!-- TODO: Provide markup for your options page here. -->
	<?php
	 	if(isset( $_POST['create_sitemap'] )){ ?>
		<?php

		$getclass->video_sitemap_output();


		} else{ ?>
			<form class="" action="" method="POST">
				<input type="hidden" name="create_sitemap" value="Y" />
				<table class="widefat">
				<thead>
				    <tr>
				        <th>RegId</th>
				        <th>Name</th>
				    </tr>
				</thead>
				<tfoot>
				    <tr>
				    <th>RegId</th>
				    <th>Name</th>
				    </tr>
				</tfoot>
				<tbody>
				<?php
					foreach($post_types as $key => $value){
						//stop media since we are only looking through posts
							$name 	= $value->name;
							$regid 	= $value->label;
						if($name != "attachment"){

						?>
				   <tr>
				     <td><input type="checkbox" name="post_slug[]" value="<?php echo $name; ?>" /><?php echo $regid; ?></td>
				     <td><?php echo $name; ?></td>
				   </tr>

					<?php

						}
					}

					?>
				</tbody>
				</table>


				<input type="submit" value="Create Sitemap" id="submitbtn" class="button-primary"/>
			</form>
	<?php } ?>
</div>
