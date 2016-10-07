<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 11:19
 */

/**
 * @var $T_NEWS_PUTUPDATENEWS_HEAD string En-tête du formulaire
 * @var $T_NEWS_PUTUPDATENEWS_FORM string Code HTML du formulaire de création de news
 * @var $T_NEWS_PUTUPDATENEWS_NEWS \Entity\News News à modifier
 */
?>

<h2><?= $T_NEWS_PUTUPDATENEWS_HEAD ?></h2>

<form action="" method="post">
	<p>
		<?= $T_NEWS_PUTUPDATENEWS_FORM ?>
		<input type="hidden" name="id" value="<?= $T_NEWS_PUTUPDATENEWS_NEWS[ 'id' ] ?>" />
		<input type="submit" value="Modifier" name="modifier" />
	</p>
</form>