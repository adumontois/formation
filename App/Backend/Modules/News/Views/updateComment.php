<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:05
 */

/** @var $form string
 * Formulaire d'ajout de commentaire
 *
 * @var $comment ['news'] integer
 * foreign key to news table
 */
?>
<form action="" method="post">
	<p>
		<?= $form ?>
		<input type="hidden" name="news" value="<?= $comment[ 'news' ] ?>" />
		<input type="submit" value="Modifier" />
	</p>
</form>