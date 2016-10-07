<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 11:19
 */

/**
 * @var $T_NEWS_PUTINSERTNEWS_HEAD string en-tête (ajout ou modif) du formulaire
 * @var $T_NEWS_PUTINSERTNEWS_FORM string contenu du formulaire à afficher
 */
?>
<h2><?= $T_NEWS_PUTINSERTNEWS_HEAD ?></h2>
<form action="" method="post">
	<p>
		<?= $T_NEWS_PUTINSERTNEWS_FORM ?>
		<input type="submit" value="Ajouter" />
	</p>
</form>