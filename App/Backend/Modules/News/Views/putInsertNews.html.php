<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 11:19
 */

/**
 * @var $header string en-tête (ajout ou modif) du formulaire
 * @var $form string contenu du formulaire à afficher
 */
?>
<h2><?= $header ?></h2>
<form action="" method="post">
	<p>
		<?= $form ?>
		<input type="submit" value="Ajouter" />
	</p>
</form>