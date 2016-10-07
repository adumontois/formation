<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:05
 */

/** @var $T_NEWS_PUTUPDATECOMMENT_FORM string
 * Formulaire d'ajout de commentaire
 *
 * @var $T_NEWS_PUTUPDATECOMMENT_COMMENT ['news'] integer
 * foreign key to news table
 */
?>
<form action="" method="post">
	<p>
		<?= $T_NEWS_PUTUPDATECOMMENT_FORM ?>
		<input type="hidden" name="news" value="<?= $T_NEWS_PUTUPDATECOMMENT_COMMENT[ 'news' ] ?>" />
		<input type="submit" value="Modifier" />
	</p>
</form>