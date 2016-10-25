<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:37
 */

namespace Entity;


use App\Backend\Modules\News\NewsController;
use Helpers\LinkHelper;
use OCFram\Entity;

/**
 * Class Comment
 *
 * Modélise un commentaire.
 *
 * @package Entity
 */
class Comment extends Entity {
	/**
	 * @var $fk_SNC int
	 */
	protected $fk_SNC;
	/**
	 * @var $author string
	 */
	protected $author;
	/**
	 * @var $content string
	 */
	protected $content;
	/**
	 * @var $datecreation \DateTime
	 */
	protected $datecreation;
	/**
	 * @var $dateupdate \DateTime
	 */
	protected $dateupdate;
	
	public function __construct( array $values = array() ) {
		parent::__construct( $values );
	}
	
	/**
	 * Vérifie si le commentaire est valide.
	 *
	 * @return bool
	 */
	public function isValid() {
		return !empty( $this->fk_SNC ) AND !empty( $this->content ) AND is_string( $this->content );
	}
	
	/**
	 * @return int
	 */
	public function fk_SNC() {
		return $this->fk_SNC;
	}
	
	/**
	 * @return string
	 */
	public function author() {
		return $this->author;
	}
	
	/**
	 * @return string
	 */
	public function content() {
		return $this->content;
	}
	
	/**
	 * @return \DateTime
	 */
	public function datecreation() {
		return $this->datecreation;
	}
	
	/**
	 * @return \DateTime
	 */
	public function dateupdate() {
		return $this->dateupdate;
	}
	
	/**
	 * Setter pour l'attribut fk_SNC.
	 *
	 * @param $fk_SNC int
	 */
	public function setFk_SNC( $fk_SNC ) {
		if ( (int)$fk_SNC > 0 ) {
			$this->fk_SNC = $fk_SNC;
		}
	}
	
	/**
	 * Setter pour l'attribut author.
	 *
	 * @param $author string
	 */
	public function setAuthor( $author ) {
		if ( is_string( $author ) AND !empty( $author ) ) {
			$this->author = $author;
		}
	}
	
	/**
	 * Setter pour l'attribut content.
	 *
	 * @param $content string
	 */
	public function setContent( $content ) {
		$this->content = $content;
	}
	
	/**
	 * Setter pour l'attribut datecreation
	 *
	 * @param \DateTime $datecreation
	 */
	public function setDatecreation(\DateTime $datecreation) {
	    $this->datecreation = $datecreation;
	}   
	
	/**
	 * Setter pour l'attribut dateupdate
	 *
	 * @param \DateTime $dateupdate 
	 */
	public function setDateupdate(\DateTime $dateupdate) {
	    $this->dateupdate = $dateupdate;
	}
	
	/**
	 * Formate les dates pour affichage dans une vue. Cette méthode modifie la valeur des attributs datecreation et dateupdate.
	 */
	public function formatDate() {
		$this->datecreation = $this->datecreation->format('d/m/Y à H\hi');
		$this->dateupdate = $this->dateupdate->format('d/m/Y à H\hi');
	}
	
	/**
	 * Adds admin modification links into link_a dynamic attribute
	 * The controller must check if admin rights are OK
	 *
	 * @param string $format Format of admin actions : html or json (html by default)
	 */
	public function setAdminLinks($format = 'html') {
		if (!isset($this->link_a)) {
			$this->link_a = [];
		}
		switch($format) {
			case 'json':
				LinkHelper::addLink(
					LinkHelper::addLink(
						$this->link_a,
						\App\Frontend\Modules\News\NewsController::getLinkToPutUpdateCommentFromAjax( $this ),
						'Modifier',
						null,
						'',
						'update_comment_on_click'
					),
					\App\Frontend\Modules\News\NewsController::getLinkToClearCommentFromAjax( $this ),
					'Supprimer',
					null,
					'',
					'delete_comment_on_click'
				);
				break;
			case 'html':
				LinkHelper::addLink(
					LinkHelper::addLink(
						$this->link_a,
						NewsController::getLinkToPutUpdateComment( $this ),
						'',
						'/images/update.png',
						'Modifier'
					),
					NewsController::getLinkToClearComment( $this ),
					'',
					'/images/delete.png',
					'Supprimer'
				);
				break;
			default:
				throw new \InvalidArgumentException($format.' format doesn\'t exists !');
		}
	}
}