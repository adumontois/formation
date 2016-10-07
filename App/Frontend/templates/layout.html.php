<?php
/**
 * @var $user    \OCFram\User
 * @var $content string Contenu de la page à afficher
 */

/**
 * @var $T_USER    \OCFram\User Session utilisateur
 * @var $T_CONTENT string Contenu de la page générée
 */

?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			<?= isset( $T_TITLE ) ? $T_TITLE : 'Mon super site' ?>
		</title>
		
		<meta charset="utf-8" />
		
		<link rel="stylesheet" href="../css/Envision.css" type="text/css" />
	</head>
	
	<body>
		<div id="wrap">
			<header>
				<h1><a href="/">Mon super site</a></h1>
				<p>Comment ça, il n'y a presque rien ?</p>
			</header>
			
			<nav>
				<ul>
					<li><a href="/">Accueil</a></li>
					<li><a href="/admin/">Admin<?= $T_USER->isAuthenticated() ? ' (connecté)' : ' (non connecté)' ?></a></li>
					<?php
					if ( $T_USER->isAuthenticated() ): ?>
						<li><a href="/admin/logout.html">Déconnexion</a></li>
						<li><a href="/admin/news-insert.html">Ajouter une news</a></li>
						<?php
					endif; ?>
				
				</ul>
			</nav>
			
			<div id="content-wrap">
				<section id="main">
					<?php if ( $T_USER->hasFlash() ):
						echo '<p style="text-align: center;">', $T_USER->getFlash(), '</p>';
					endif; ?>
					<?= $T_CONTENT ?>
				</section>
			</div>
			
			<footer></footer>
		</div>
	</body>
</html>