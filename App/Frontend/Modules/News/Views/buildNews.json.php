<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 13/10/2016
 * Time: 10:49
 */

/**
 * @var $News              \Entity\News News à afficher
 * @var $Comment_list_a    \Entity\Comment[] Liste des commentaires à afficher
 * @var $link_a string[] Liste des liens à afficher
 */
?>

p: {
	"Par ",
	em: "<?= htmlspecialchars( $News[ 'User' ] ) ?>",
	", le <?= $News[ 'dateadd' ] ?>"
},
h2: "<?= htmlspecialchars( $News[ 'title' ] ) ?>",
p: "<?= nl2br( htmlspecialchars( $News[ 'content' ] ) ) ?>",

<?php if ( $News[ 'dateadd' ] != $News[ 'dateupdate' ] ): ?>
	p: {
		style: "text-align: right;"
		small: {
			em: "Modifiée le <?= $News[ 'dateupdate' ] ?>"
		}
	}
<?php endif; ?>

p: {
	a: "Ajouter un commentaire" {
		href: "<?= $link_a['putInsertComment'] ?>"
	}
}

<?php if ( empty( $Comment_list_a ) ): ?>
	p: "Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !"
<?php endif; ?>

<?php foreach ( $Comment_list_a as $Comment ): ?>
	fieldset: {
		legend: {
			"Posté par ",
			strong: "<?= htmlspecialchars( $Comment[ 'author' ] ) ?>",
			"le <?= $Comment[ 'date' ] ?>",
			<?php if (!empty($Comment['action_a'])): ?>
				" - ",
				<?php foreach ($Comment['action_a'] as $action_a): ?>
					a: "<?= $action_a['label'] ?>",
					href: "<?= $action_a['link'] ?>",
				<?php endforeach; ?>
			<?php endif; ?>
		}
		p: "<?= nl2br( htmlspecialchars( $Comment[ 'content' ] ) ) ?>"
	}
<?php endforeach; ?>

p: {
	a: "Ajouter un commentaire" {
		href: "<?= $link_a['putInsertComment'] ?>"
	}
}