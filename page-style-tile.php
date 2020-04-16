<?php get_header(); ?>
<script>
jQuery(document).ready(function(){
var size_h1 = jQuery("h1").css('font-size');
document.getElementById("h1-display").innerHTML = size_h1;
var size_h2 = jQuery("h2").css('font-size');
document.getElementById("h2-display").innerHTML = size_h2;
var size_h3 = jQuery("h3").css('font-size');
document.getElementById("h3-display").innerHTML = size_h3;
var size_h4 = jQuery("h4").css('font-size');
document.getElementById("h4-display").innerHTML = size_h4;
var size_h5 = jQuery("h5").css('font-size');
document.getElementById("h5-display").innerHTML = size_h5;
var size_h6 = jQuery("h6").css('font-size');
document.getElementById("h6-display").innerHTML = size_h6;
var size_h6 = jQuery("p").css('font-size');
document.getElementById("p-display").innerHTML = size_h6;
});
</script>
<div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="medium-12 cell">
			<hr>
			<label>h1 <span id="h1-display"></span></label>
			<h1>Level One Heading</h1>
			<hr>
			<label>h2 <span id="h2-display"></span></label>
			<h2>Level Two Heading</h2>
			<hr>
			<label>h3 <span id="h3-display"></span></label>
			<h3>Level Three Heading</h3>
			<hr>
			<label>h4 <span id="h4-display"></span></label>
			<h4>Level Four Heading</h4>
			<hr>
			<label>h5 <span id="h5-display"></span></label>
			<h5>Level Five Heading</h5>
			<hr>
			<label>h6 <span id="h6-display"></span></label>
			<h6>Level Six Heading</h6>
			<h1 class="space-top-big">Paragraphs</h1>
			<hr>
			<label>p <span id="p-display"></span></label>
			<p>This is a standard paragraph created using the WordPress TinyMCE text editor. It has a <strong>strong tag</strong>, an <em>em tag</em> and a <del>strikethrough</del> which is actually just the del element. There are a few more inline elements which are not in the WordPress admin but we should check for incase your users get busy with the copy and paste. These include <cite>citations</cite>, <abbr title="abbreviation">abbr</abbr>, bits of <code>code</code> and <var>variables</var>, <q>inline quotations</q>, <ins datetime="2011-12-08T20:19:53+00:00">inserted text</ins>, text that is <s>no longer accurate</s> or something <mark>so important</mark> you might want to mark it. We can also style subscript and superscript characters like C0<sub>2</sub>, here is our 2<sup>nd</sup> example. If they are feeling non-semantic they might even use <b>bold</b>, <i>italic</i>, <big>big</big> or <small>small</small> elements too.&nbsp;Incidentally, these HTML4.01 tags have been given new life and semantic meaning in HTML5, you may be interested in reading this <a title="HTML5 Semantics" href="http://csswizardry.com/2011/01/html5-and-text-level-semantics">article by Harry Roberts</a> which gives a nice excuse to test a link.&nbsp;&nbsp;It is also worth noting in the "kitchen sink" view you can also add <span style="text-decoration: underline;">underline</span>&nbsp;styling and set <span style="color: #ff0000;">text color</span> with pesky inline CSS.</p>
			<hr>
			<label>blockquote</label>
			<blockquote>
				Currently WordPress blockquotes are just wrapped in blockquote tags and have no clear way for the user to define a source. Maybe one day they'll be more semantic (and easier to style) like the version below.
			</blockquote>
			<blockquote cite="http://html5doctor.com/blockquote-q-cite/">
				<p>HTML5 comes to our rescue with the footer element, allowing us to add semantically separate information about the quote.</p>
				<footer>
					<cite>
					<a href="http://html5doctor.com/blockquote-q-cite/">Oli Studholme, HTML5doctor.com</a>
					</cite>
				</footer>
			</blockquote>
			<hr>
			<label>lists</label>
			<ul>
				<li>Unordered list item one.</li>
				<li>Unordered list item two.</li>
				<li>Unordered list item three.</li>
				<li>Unordered list item four.</li>
				<li>By the way, Wordpress does not let you create nested lists through the visual editor.</li>
			</ul>
			<ol>
				<li>Ordered list item one.</li>
				<li>Ordered list item two.</li>
				<li>Ordered list item three.</li>
				<li>Ordered list item four.</li>
				<li>By the way, Wordpress does not let you create nested lists through the visual editor.</li>
			</ol>
		</div>
		<div class="medium-12 cell">
			<hr>
			<label>Buttons</label>
			<label>Full</label>
			<a class="secondary button" href="#">Secondary Color</a>
			<a class="success button" href="#">Success Color</a>
			<a class="alert button" href="#">Alert Color</a>
			<a class="warning button" href="#">Warning Color</a>
			<a class="disabled button" href="#">Disabled Button</a>
			<label>hollow</label>
			<button class="hollow button" href="#">Primary Color</button>
			<button class="secondary hollow button" href="#">Secondary Color</button>
			<button class="success hollow button" href="#">Success Color</button>
			<button class="alert hollow button" href="#">Alert Color</button>
			<button class="warning hollow button" href="#">Warning Color</button>
		</div>
	</div>
	<div class="grid-x grid-margin-x">
		<div class="cell medium-12">
			<h2>Framework Colors</h2>
		</div>
	</div>
	<div class="bock-grid grid-x grid-margin-x small-up-1 medium-up-2 large-up-5">
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height primary-color-bg">
					<h6>Primary Color</h6>
				</div>
			</div>
		</div>
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height secondary-color-bg">
					<h6>Secondary Color</h6>
				</div>
			</div>
		</div>
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height success-color-bg">
					<h6>Success Color</h6>
				</div>
			</div>
		</div>
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height warning-color-bg">
					<h6>Warning Color</h6>
				</div>
			</div>
		</div>
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height alert-color-bg">
					<h6>Alert Color</h6>
				</div>
			</div>
		</div>
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height light-gray-bg">
					<h6>Light Gray Color</h6>
				</div>
			</div>
		</div>
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height medium-gray-bg">
					<h6>Medium Gray Color</h6>
				</div>
			</div>
		</div>
		<div class="cell">
			<div class="one-one-min-height-container">
				<div class="one-one-min-height dark-gray-bg">
					<h6>Dark Gray Color</h6>
				</div>
			</div>
		</div>
	</div>
	<div class="grid-x grid-margin-x padding-top-large">
		<div class="cell medium-12">
			<h1>Spaces</h1>
			<h2 class="margin-bottom-1">Space Bottom 1</h2>
			<hr>
			<h2 class="margin-bottom-2">Space Bottom 2</h2>
			<hr>
			<h2 class="margin-bottom-3">Space Bottom 3</h2>
			<hr>
		</div>
	</div>
</div>

<?php get_footer(); ?>
