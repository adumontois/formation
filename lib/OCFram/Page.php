<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 11:35
 */

namespace OCFram;

/**
 * Class Page
 * Classe représentant la page générée et renvoyée à l'utilisateur.
 *
 * @package OCFram
 */
class Page extends ApplicationComponent {
	/**
	 * @var $contentFile string Chemin relatif vers le fichier de vue
	 */
	protected $contentFile;
	/**
	 * @var $vars array Tableau associatif donnant à chaque variable de la vue sa valeur
	 */
	protected $vars;
	/**
	 * @var $format string Format de la page (html, json, etc.)
	 */
	protected $format;
	/**
	 * @var $generateLayout bool Indique si la layout doit être généré
	 */
	protected $generateLayout;
	
	/**
	 * Construit une page vierge à partir de l'application choisie
	 *
	 * @param Application $app
	 * @param string      $format         Format de la page à afficher (html, json, etc.)
	 * @param bool        $generateLayout Indique si le layout doit être généré
	 */
	public function __construct( Application $app, $format = 'html', $generateLayout = true ) {
		parent::__construct( $app );
		$this->contentFile    = '';
		$this->vars           = array();
		$this->format         = $format;
		$this->generateLayout = $generateLayout;
	}
	
	/**
	 * Ajoute une variable à la page.
	 *
	 * @param $var   string Nom de la variable
	 * @param $value mixed Valeur de la variable
	 */
	public function addVar( $var, $value ) {
		if ( !is_string( $var ) OR is_numeric( $var ) OR empty( $var ) ) {
			throw new \InvalidArgumentException( 'Variable name must be a non NULL string' );
		}
		$this->vars[ $var ] = $value;
	}
	
	/**
	 * Génère le code html associé à la page courante.
	 *
	 * @return string
	 */
	public function getGeneratedPage() {
		if ( !file_exists( $this->contentFile ) ) {
			throw new \RuntimeException( 'Specified view "' . $this->contentFile . '" doesn\'t exists' );
		}
		/*
		 * @var User $User utilisée dans les vues
		 */
		$User = $this->app->user();
		
		if ( $this->format == 'json' ) {
			// On serialize toutes les Entity passées en paramètre
			foreach ( $this->vars as &$element ) {
				if ( $element instanceof Entity ) {
					$element = json_encode( $element );
				}
			}
		}
		extract( $this->vars );
		
		
		// Créer la page en bufferisation
		ob_start();
		require $this->contentFile; // Existence du fichier vérifiée
		/**
		 * @var $content string utilisée dans les vues
		 */
		
		// Générer le layout si besoin
		if ( $this->generateLayout ) {
			$content = ob_get_clean(); // Injecter le contenu de la page interne dans le layout
			
			ob_start();
			
			require __DIR__ . '/../../App/' . $this->app->name() . '/templates/layout.' . $this->format . '.php'; // Construction dynamique du chemin de layout OK
		}
		
		return ob_get_clean();
	}
	
	/**
	 * Setter pour l'attribut contentFile.
	 *
	 * @param $contentFile string Chemin relatif vers le fichier de vue
	 */
	public function setContentFile( $contentFile ) {
		if ( !is_string( $contentFile ) OR empty( $contentFile ) ) {
			throw new \InvalidArgumentException( 'View file name must be a non NULL string' );
		}
		$this->contentFile = $contentFile;
	}
	
	/**
	 * @return string
	 */
	public function format() {
		return $this->format;
	}
}