<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 13/10/2016
 * Time: 10:21
 */

?>


<?php
$layout['master_code'] = isset($master_code)?$master_code:0;
$layout['master_error'] = isset($master_error)?$master_error:'';
$layout['content'] = $content;
return $layout;
?>