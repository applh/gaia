<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">

	<title>SHOW MUST GO ON...</title>

	<meta name="description" content="A framework for easily creating beautiful presentations using HTML">
	<meta name="author" content="Hakim El Hattab">

	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="/media/revealjs/reset.css">
	<link rel="stylesheet" href="/media/revealjs/reveal.css">
	<link rel="stylesheet" href="/media/revealjs/theme/black.css" id="theme">

	<!-- Theme used for syntax highlighting of code -->
	<link rel="stylesheet" href="/media/revealjs/plugin/highlight/monokai.css">
	<style>
		.reveal {
			background: no-repeat center top fixed url('/media/images/earth-night.jpg');
			background-size: contain;
		}
		.reveal pre.code-wrapper > code {
			max-height: 1200px;
			padding: 2vmax;
		}
	
	</style>
</head>

<body>

	<div class="reveal">

		<!-- Any section element inside of this container is displayed as a slide -->
		<div class="slides">

			<?php web::slides() ?>

		</div>

	</div>

	<script src="/media/revealjs/reveal.js"></script>
	<script src="/media/revealjs/plugin/zoom/zoom.js"></script>
	<script src="/media/revealjs/plugin/notes/notes.js"></script>
	<script src="/media/revealjs/plugin/search/search.js"></script>
	<script src="/media/revealjs/plugin/markdown/markdown.js"></script>
	<script src="/media/revealjs/plugin/highlight/highlight.js"></script>
	<script>
		// Also available as an ES module, see:
		// https://revealjs.com/initialization/
		Reveal.initialize({
			width: 1600,
			height: 2000,
			controls: false,
			progress: false,
			center: true,
			hash: true,
			slideNumber: 'c/t',
			// center: false,
			margin: 0.05,
			autoAnimate: false,
			transition: 'none',
			// Learn about plugins: https://revealjs.com/plugins/
			plugins: [RevealZoom, RevealNotes, RevealSearch, RevealMarkdown, RevealHighlight]
		});

		Reveal.on('ready', event => {
			// too late :-/
			// document.querySelectorAll("pre code").forEach((c) => {
			// 	c.setAttribute("data-line-numbers", "");
			// 	console.log(c);
			// });
		});
	</script>

</body>

</html>