<?php
/**
 * @var $user    \OCFram\User
 * @var $content string Contenu de la page à afficher
 */

/**
 * @var $User    \OCFram\User Session utilisateur
 * @var $content string Contenu de la page générée
 * @var $menu string Panneau menu personnalisé en fonction de l'authentification
 * @var $flash string Affichage du flash (message à l'utilisateur)
 */

?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			<?= isset( $title ) ? $title : 'Mon super site' ?>
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
					<?= $menu ?>
				</ul>
			</nav>
			
			<div id="content-wrap">
				<section id="main">
					<?= $flash ?>
					<?= $content ?>
				</section>
			</div>
			
			<footer></footer>
		</div>
	</body>
</html>