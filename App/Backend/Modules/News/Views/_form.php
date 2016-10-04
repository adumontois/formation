<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 10:58
 */
?>

<form action = "" method = "post">
    <p>
        <?php
        if (isset($news) && !$news -> isNew())
        {
            ?>
            <input type = "hidden" name = "id" value = "<?= $news['id'] ?>" />
            <input type = "submit" value = "Modifier" name = "modifier" />
            <?php
        }
        else
        {
            ?>
            <input type = "submit" value = "Ajouter" />
            <?php
        }
        ?>
    </p>
</form>
