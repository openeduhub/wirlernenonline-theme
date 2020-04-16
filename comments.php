<?php if(!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) : ?>
	<?php die('You can not access this page directly!'); ?>
<?php endif; ?>
<?php if(!empty($post->post_password)) : ?>
	<?php if($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) : ?>
		<p><?php pll_e('This post is password protected. Enter the password to view comments.'); ?></p>
	<?php endif; ?>
<?php endif; ?>
<?php if($comments) : ?>
	<ol class="no-bullet">
		<?php foreach($comments as $comment) : ?>
			<li id="comment-<?php comment_ID(); ?>">
				<?php if ($comment->comment_approved == '0') : ?>
					<div class="callout warning">
						<p><?php pll_e('Your comment is awaiting approval') ?></p>
					</div>

				<?php endif; ?>
				<div class="callout secondary">
					<?php comment_text(); ?>
					<p class="meta small"><?php comment_type(); ?> <?php pll_e('by') ?> <?php comment_author_link(); ?> <?php pll_e('on') ?> <?php comment_date(); ?> <?php pll_e('at') ?> <?php comment_time(); ?></p>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
<?php else : ?>
	<h3><?php pll_e('Leave a comment'); ?></h3>
	<p><?php pll_e('No comments yet') ?></p>
<?php endif; ?>
<?php if(comments_open()) : ?>
	<?php if(get_option('comment_registration') && !$user_ID) : ?>
		<p><?php pll_e('You must be'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php pll_e('logged in') ?></a> <?php pll_e('to post a comment.'); ?></p><?php else : ?>
			<h3><?php pll_e('Leave a comment'); ?></h3>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
				<?php if($user_ID) : ?>
					<p>
						<?php pll_e('Logged in as'); ?>
						<a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a><br>
						<a class="button margin-top-1" href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account"><?php pll_e('Log out'); ?></a></p>
					<?php else : ?>
						<label for="author">Name <?php if($req) echo "*"; ?>
							<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
						</label>
						<label for="email"><?php pll_e('Email (will not be published)'); ?> <?php if($req) echo "*"; ?>
							<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
						</label>
						<label for="url">Website
							<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
						</label>
					<?php endif; ?>
					<textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea>
					<input class="button" name="submit" type="submit" id="submit" tabindex="5" value="<?php pll_e('Submit Comment') ?>" />
					<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
					<?php do_action('comment_form', $post->ID); ?>
				</form>
			<?php endif; ?>
		<?php else : ?>
			<p><?php pll_e('The comments are closed.'); ?></p>
		<?php endif; ?>
