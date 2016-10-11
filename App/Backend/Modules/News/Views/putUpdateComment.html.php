<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:05
 */

/** @var $form string
 * Formulaire d'ajout de commentaire
 * @var $Comment \Entity\Comment
 * @var $Comment ['news'] integer
 * foreign key to news table
 */
?>
<form action="" method="post">
	<p>
		<?= $form ?>
		<input type="hidden" name="news" value="<?= $Comment[ 'fk_SNC' ] ?>" />
		<input type="submit" value="Modifier" />
	</p>
</form>