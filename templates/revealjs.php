<!doctype html>
<html lang="en">

	<head>
		<meta charset="utf-8">

		<title>reveal.js – The HTML Presentation Framework</title>

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
	</head>

	<body>

		<div class="reveal">

			<!-- Any section element inside of this container is displayed as a slide -->
			<div class="slides">


				<?php web::slides("pages/project-blog.md") ?>

				<section>
					<h2>G.A.I.A</h2>
					<p>
						GeoCMS Artificial Intelligence Applications
					</p>
				</section>

				<section>
					<h2>Application Streaming</h2>
					<p>
						Load only the application you need
					</p>
				</section>
				
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
				controls: true,
				progress: true,
				center: true,
				hash: true,

				// Learn about plugins: https://revealjs.com/plugins/
				plugins: [ RevealZoom, RevealNotes, RevealSearch, RevealMarkdown, RevealHighlight ]
			});

		</script>

	</body>
</html>
