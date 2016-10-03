<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 11:35
 */

namespace OCFram;

class Page extends ApplicationComponent
{
    protected $contentFile;
    protected $vars;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this -> contentFile = '';
        $this -> vars = array();
    }

    public function addVar($var, $value)
    {
        if (!is_string($var) OR is_numeric($var) OR empty($var))
        {
            throw new \InvalidArgumentException('Variable name must be a non NULL string');
        }
         $this -> vars[$var] = $value;
    }

    public function getGeneratedPage()
    {
        if (!file_exists($this->contentFile))
        {
            throw new \RuntimeException('Specified view "'.$this -> contentFile.'" doesn\'t exists');
        }
        extract($this -> vars);

        // Créer la page en bufferisation
        ob_start();
        require $this -> contentFile;
        $content = ob_get_clean(); // Vider le buffer dans la sortie

        ob_start();
        require __DIR__.'/../../App/'.$this -> app -> name().'/Templates/layout.php';
        return ob_get_clean();
    }

    public function setContentFile($contentFile)
    {
        if (!is_string($contentFile) OR empty($contentFile))
        {
            throw new \InvalidArgumentException('View file name must be a non NULL string');
        }
        $this -> contentFile = $contentFile;
    }
}