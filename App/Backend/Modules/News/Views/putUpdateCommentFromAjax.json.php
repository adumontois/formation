<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 18/10/2016
 * Time: 12:44
 */

/**
 * @var \Entity\Comment $Comment Commentaire édité
 * @var string $form Formulaire à afficher
 */
$data_a = ['Comment' => $Comment];
if (isset($form)) {
	$data_a['form'] = $form;
}
return $data_a;