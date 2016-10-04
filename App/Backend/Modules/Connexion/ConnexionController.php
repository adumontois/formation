<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:44
 */

namespace App\Backend\Modules\Connexion;


use OCFram\BackController;
use OCFram\HTTPRequest;

class ConnexionController extends BackController
{
    public function executeIndex(HTTPRequest $request)
    {
        if ($request -> postExists('login'))
        {
            if ($request -> postData('login') === $this -> app -> config() -> get('login')
                AND $request -> postData('login') === $this -> app -> config() -> get('password'))
            {
                $this -> app -> user() -> setAuthenticated();
                $this -> app -> httpResponse() -> redirect('.');
            }
            else
            {
                $this -> app -> user() -> setFlash('Login-password combination is incorrect.');
            }
        }
    }
}