<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 11:19
 */

/**
 * @var $form string Code HTML du formulaire de création de news
 * @var $news \Entity\News News à modifier
 */
?>
	
<h2>Modifier une news</h2>

<form action="" method="post">
	<p>
		<?= $form ?>
		<input type="hidden" name="id" value="<?= $news[ 'id' ] ?>" />
		<input type="submit" value="Modifier" name="modifier" />
	</p>
</form>