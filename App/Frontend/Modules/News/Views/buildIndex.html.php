<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 15:34
 */
/**
 * @var $News_list_a \Entity\News[]
 */
?>

<?php foreach ( $News_list_a as $News ): ?>
	<h2><a href="news-<?= $News[ 'id' ] ?>.html"><?= htmlspecialchars( $News[ 'title' ] ) ?></a> par <em><?= htmlspecialchars($News[ 'User' ]) ?></em></h2>
	<p><?= nl2br( htmlspecialchars( $News[ 'content' ] ) ) ?></p>
<?php endforeach;