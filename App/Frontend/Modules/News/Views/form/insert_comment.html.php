<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 14/10/2016
 * Time: 11:08
 */
?>
<?php
/**
 * @var string $form_id
 * @var string $form_action
 * @var string $form
 */
?>
<h2>
	Ajouter un commentaire
</h2>
<form id="<?=$form_id?>" action="<?= $form_action ?>" method="post" class="js-form-insert-comment">
	<p>
		<?= $form ?>
		<input type="submit" value="Commenter" />
	</p>
</form>
