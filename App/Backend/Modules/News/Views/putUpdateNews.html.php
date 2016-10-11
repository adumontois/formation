<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 11:19
 */

/**
 * @var $header string En-tête du formulaire
 * @var $form string Code HTML du formulaire de création de news
 * @var $News \Entity\News News à modifier
 */
?>

<h2><?= $header ?></h2>

<form action="" method="post">
	<p>
		<?= $form ?>
		<input type="submit" value="Modifier" name="modifier" />
	</p>
</form>