<?php
/**
 * @var $user    \OCFram\User
 * @var $content string Contenu de la page à afficher
 */

/**
 * @var $User    \OCFram\User Session utilisateur
 * @var $content string Contenu de la page générée
 * @var $menu_a array[] Panneau menu personnalisé en fonction de l'authentification
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
					<?php foreach ($menu_a as $element_a): ?>
						<li><a href="<?= $element_a['link'] ?>"><?= $element_a['label']?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
			
			<div id="content-wrap">
				<section id="main">
					<?php if(isset($flash)): ?>
						<p style="text-align: center;"><?= $flash ?></p>
					<?php endif ?>
					<?= $content ?>
				</section>
			</div>
			
			<footer></footer>
		</div>
	</body>
</html>