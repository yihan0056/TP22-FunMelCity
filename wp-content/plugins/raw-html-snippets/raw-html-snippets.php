<?php
/*
Plugin Name: Raw HTML Snippets
Plugin URI: http://theandystratton.com
Version: 2.0.3
Author: theandystratton
Author URI: http://theandystratton.com
Description: Uses a shortcode to give users multiple methods of properly inserting RAW HTML content without disabling core WordPress content filters. Management functionality for your snippets had been moved to the Tools menu in WP 5.6 and above.
*/

\define( 'SZBL_RHS_VERSION', '2.0.2' );

\add_filter( 'plugin_action_links_' . \plugin_basename( __FILE__ ) , function( $actions ){
	$actions[] = '<a href="' . \admin_url( '/tools.php?page=raw-html-snippets' ) . '">Manage HTML Snippets</a>';
	return $actions;
} );

// version checker + uprade notice for legacy users
\add_action( 'admin_init', function(){

	if ( \version_compare( \get_option( 'szbl_rhs_version' ), SZBL_RHS_VERSION ) < 0 )
	{
		\update_option( 'szbl_rhs_version', SZBL_RHS_VERSION );

		\add_action( 'admin_notices', function(){

			echo '<div class="notice notice-info is-dismissible">';
			echo '<p style="font-size:1.25em;"><strong style="display:block;">Please Note!</strong> Raw HTML Snippets are now managed under the Tools > Raw HTML Snippets option. They have been removed from the Settings menu.</p>';
			echo '</div>';

		});
	}

});

\add_shortcode( 'raw_html_snippet', 'rhs_raw_html_snippet_shortcode' );
function rhs_raw_html_snippet_shortcode( $atts, $content = '' )
{
	\extract( \shortcode_atts([
		'id' => false
	], $atts) );
	
	if ( !isset( $id ) || !$id ) 
		return '';
	
	$snippet = \get_option( 'rhs_snippet-' . $id );
	
	return $snippet;
}

\add_action( 'admin_menu', 'rhs_raw_html_snippet_admin_menu' );
function rhs_raw_html_snippet_admin_menu()
{
	\add_submenu_page( 'tools.php', 'Raw HTML Snippets', 'Raw HTML Snippets', 'edit_posts', 'raw-html-snippets', 'rhs_raw_html_snippet_settings');
}

function rhs_raw_html_snippet_settings()
{
	
	if ( isset( $_GET['edit'] ) && $_GET['edit'] )
		return \rhs_raw_html_snippet_editor();
		
	if ( isset($_GET['add']) && $_GET['add'] )
		return \rhs_raw_html_snippet_add();
	
	$errors = [];
	$clean = [];
	
	if ( isset( $_GET['rhs_del'] ) && $_GET['rhs_del'] && \wp_verify_nonce( $_GET['rhs_nonce'], 'rhs_delete' ) ) 
	{
		\delete_option( 'rhs_snippet-' . $_GET['rhs_del'] );
		
		$snippet_list = get_option('rhs_snippet_list');
		if ( \is_array( $snippet_list ) && \in_array( $_GET['rhs_del'], $snippet_list ) ) 
		{
			$snippet_list = \array_diff( $snippet_list, [ $_GET['rhs_del'] ] );

			\update_option('rhs_snippet_list', $snippet_list);
			$success = 'Snippet with ID &quot;' . esc_html($_GET['rhs_del']) . '&quot; successfully deleted.';
		}
	}

	
	$snippet_list = \get_option('rhs_snippet_list');

	if ( !\is_array( $snippet_list ) )
		$snippet_list = [];
	
?>
<div class="wrap">
	<h2>Manage Raw HTML Snippets</h2>
	<p>
		Create and manage your HTML snippets here. Name them with an id (letters, numbers, and dashes only) and then
		call them within your content via shortcode: <br />
		<code>[raw_html_snippet id="my-unique-id"]</code>
	</p>
	<p>
		This will use maintain core WordPress content filtering and autoformatting for all other elements within your content while 
		allowing you to easily insert managed RAW HTML.
	</p>
	<p>
		<strong>WARNGING:</strong> This does not filter your HTML for errors or
		for malicious scripts. Use at your own risk.
	</p>
	
	<?php if ( \count( $snippet_list ) > 0 ) : ?>
	
	
	<form method="get" action="">
		<p class="alignright">
			<input type="hidden" name="page" value="raw-html-snippets" />
			<input type="hidden" name="add" value="1" />
			<input type="submit" class="button-primary" value="Add a New Raw HTML Snippet &raquo;" />
		</p>
	</form>
	
	<h2>Your Snippet Library</h2>
	
	<textarea id="rhs-copy-box" style="position:absolute;top:-9999em;left:-9999em;z-index:0;"></textarea>

	<table class="widefat fixed">
	<thead>
	<tr>
		<th>Snippet Name</th>
		<th colspan="2">Shortcode</th>
		<th>Actions</th>
	</tr>
	</thead>
	<tbody>
	
	<?php foreach ( $snippet_list as $index => $snippet_id ) : ?>
	<tr>
		<td>
			<?php echo \esc_html($snippet_id);?>
			<div class="row-actions">
				<a href="?page=raw-html-snippets&amp;edit=<?php echo \rawurlencode( $snippet_id ); ?>">Edit</a> | 
				<span class="trash"><a class="submitdelete" onclick="return confirm('Are you sure you want to delete this snippet?');" href="?page=raw-html-snippets&amp;rhs_nonce=<?php echo esc_attr(wp_create_nonce('rhs_delete')); ?>&amp;rhs_del=<?php echo esc_attr($snippet_id); ?>">Delete</a></span>
			</div>
		</td>
		<td>
			<input id="rhs-<?php echo (int) $index + 1; ?>" type="text" readonly value="<?php echo \esc_attr( '[raw_html_snippet id="' . \esc_attr( $snippet_id ) . '"]'	); ?>" class="widefat">
		</td>
		<td>
			<button class="rhs-copy button" data-target="rhs-<?php echo (int) $index + 1; ?>">Copy to Clipboard</button>
			<div class="rhs-message"></div>
		</td>
		<td>
			<a href="?page=raw-html-snippets&amp;edit=<?php echo \rawurlencode( $snippet_id ); ?>">Edit Snippet</a> | 
			<span class="trash"><a onclick="return confirm('Are you sure you want to delete this snippet?');" href="?page=raw-html-snippets&amp;rhs_nonce=<?php echo esc_attr(wp_create_nonce('rhs_delete')); ?>&amp;rhs_del=<?php echo rawurlencode($snippet_id); ?>">Delete Snippet</a></span>
		</td>
	</tr>
	<?php endforeach; ?>
	
	</tbody>
	</table>
	
	<?php else : ?>
		<h2>Your Snippets Library is Empty</h2>
		<p>You have no snippets, please <a href="?page=raw-html-snippets&amp;add=1">please add one</a>.</p>
	<?php endif; ?>
</div>

<script>
jQuery(document).ready(function($){

	$( '.rhs-copy' ).click(function(){
		var $target = $( '#' + $(this).data( 'target' ) );
		var elem = $target.get(0);

		$( '#rhs-copy-box' ).val( $target.val() )
		elem.focus();
		elem.setSelectionRange( 0, elem.value.length );
		var succeed;
		try 
		{
			succeed = document.execCommand("copy");
		}
		catch(e) 
		{
			succeed = false;
		}
		if ( succeed )
		{
			$( '.rhs-message' ).html( '' );
			$(this).next().html( '<small><em>This shortcode has been copied to your clipboard.</em></small>' );
		}

		return false;
	});

});
</script>
<?php 
}

function rhs_raw_html_snippet_editor() {

	$snippet_id = $_GET['edit'];
	$errors = [];

	if ( !empty($_POST) && \wp_verify_nonce( $_POST['rhs_nonce'], 'rhs_nonce' ) ) 
	{
		$snippet = stripslashes($_POST['snippet_code']);
	
		if ( empty( $snippet ) ) 
			$errors[] = 'Enter some HTML for this snippet.';

		if ( \count( $errors ) <= 0 ) 
		{
			\update_option( 'rhs_snippet-' . $snippet_id, $snippet );
			$success = 'Your changes have been saved.';
		}
	}
	$snippet = \get_option('rhs_snippet-' . $snippet_id);
	$clean = [
		'snippet_code' => $snippet
	];
?>
<div class="wrap">
	<h2>Edit Raw HTML Snippet: &quot;<?php echo \esc_html($snippet_id); ?>&quot;</h2>
	<p><a href="?page=raw-html-snippets">&laquo; Back to main page</a></p>
	<form method="post" action="">
		
		<?php if ( count($errors) > 0 ) : ?>
		<div class="message error"><?php echo \wpautop( \implode( "\n", $errors ) ); ?></div>
		<?php endif; ?>
		<?php if ( isset( $success ) && !empty( $success ) ) : ?>
		<div class="message updated"><?php echo wpautop($success); ?></div>
		<?php endif; ?>
		
		<?php \wp_nonce_field( 'rhs_nonce', 'rhs_delete' ); ?>
		
		<p><label for="snippet_code">Snippet Code:</label></p>
		<textarea dir="ltr" dirname="ltr" id="snippet_code" name="snippet_code" rows="10" style="font-family:Monaco,'Courier New',Courier,monospace;font-size:12px;width:80%;color:#555;"><?php
			if ( isset( $clean['snippet_code'] ) )
				echo \esc_attr( $clean['snippet_code'] );
		?></textarea>
		
		<p>
			<input type="submit" class="button-primary" value="Save Snippet &raquo;" /> 
			<?php \wp_nonce_field( 'rhs_nonce', 'rhs_nonce' ); ?>
			<input type="button" class="button" value="Delete This Snippet" onclick="if ( confirm('Are you sure you want to delete this snippet?') ) window.location = '?page=raw-html-snippets&amp;rhs_del=<?php echo esc_attr($snippet_id); ?>&amp;rhs_nonce=<?php echo esc_attr(wp_create_nonce('rhs_delete')); ?>';" />
		</p>
	</form>	
</div>
<?php
}

function rhs_raw_html_snippet_add() {
	
	$snippet_list = \get_option( 'rhs_snippet_list' );

	if ( !\is_array( $snippet_list ) )
		$snippet_list = [];
		
	$errors = [];
	$clean = [];
	
	if ( !empty($_POST) && \wp_verify_nonce( $_POST['rhs_nonce'], 'rhs_nonce' ) ) 
	{

		$clean = \stripslashes_deep( $_POST );
			
		if ( empty( $clean['snippet_id'] ) ) 
			$errors[] = 'Please enter a unique snippet ID.';

		elseif ( \in_array( \strtolower( $clean['snippet_id'] ), $snippet_list ) )
			$errors[] = 'You have entered a snippet ID that already exists. IDs are NOT case-sensitive.';
		
		if ( empty( $clean['snippet_code'] ) )
			$errors[] = 'Enter some HTML for this snippet.';
		
		if ( \count( $errors ) <= 0 ) 
		{
			// save snippet
			$snippet_id = \strtolower( $clean['snippet_id'] );
			$snippet_list[] = $snippet_id;
			\update_option( 'rhs_snippet_list', $snippet_list );
			\update_option( 'rhs_snippet-' . $snippet_id, $clean['snippet_code'] );
			$success = 'Your snippet has been saved.';
			$clean = [];
		}
	}
	
?>
<div class="wrap">
	<h2>Add Raw HTML Snippet:</h2>
	
	<p><a href="?page=raw-html-snippets">&laquo; Back to main page</a></p>
		
	<form method="post" action="" style="margin: 1em 0;padding: 1px 1em;background: #fff;border: 1px solid #ccc;">
		
		<?php if ( count($errors) > 0 ) : ?>
		<div class="message error"><?php echo \wpautop( \implode( "\n", $errors ) ); ?></div>
		<?php endif; ?>
		<?php if ( $success ) : ?>
		<div class="message updated"><?php echo \wpautop( $success ); ?></div>
		<?php endif; ?>
		
		<?php \wp_nonce_field( 'rhs_nonce', 'rhs_nonce' ); ?>
		
		<p>
			<label for="snippet_id">Snippet ID:</label>
			<br />
			<input type="text" name="snippet_id" id="snippet_id" size="40" value="<?php
			if ( isset( $clean['snippet_id'] ) ) 
				echo \esc_attr( $clean['snippet_id'] );
		?>" />
		</p>
		
		<p><label for="snippet_code">Snippet Code:</label></p>
		<textarea dir="ltr" dirname="ltr" id="snippet_code" name="snippet_code" rows="10" style="font-family:Monaco,'Courier New',Courier,monospace;font-size:12px;width:80%;color:#555;"><?php
			if ( isset( $clean['snippet_code'] ) )
				echo \esc_attr( $clean['snippet_code'] );
		?></textarea>
		
		<p><input type="submit" class="button-primary" value="Add Snippet &raquo;" />
	</form>	
</div>
<?php	
}
