<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 12:26
 */

namespace OCFram;

class Config extends ApplicationComponent
{
    protected $vars;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this -> vars = array();
    }

    public function get($var)
    {
        if (empty($vars))
        // Fulfill $vars by parsing XML
        {
            $xml = new \DOMDocument();
            $xml -> load(__DIR__.'/../../App/'.$this -> app() -> name().'/Config/app.xml');
            $data = $xml -> getElementsByTagName('define');
            foreach ($data as $value)
            {
                if ($value -> hasAttribute('var') AND $value -> hasAttribute('value'))
                {
                    $vars[$value -> getAttribute('var')] = $value -> getAttribute('value');
                }
            }
        }

        if (!isset($vars[$var]))
        {
            return NULL;
        }
        return $vars[$var];
    }
}