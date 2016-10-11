<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 06/10/2016
 * Time: 09:45
 */

namespace OCFram;

/**
 * Class FormHandler
 *
 * Classe modélisant un gestionnaire de formulaires.
 *
 * @package OCFram
 */
class FormHandler {
	/**
	 * @var $form Form formulaire à gérer
	 */
	protected $form;
	/**
	 * @var $manager Manager manager associé à l'entité à enregistrer
	 */
	protected $manager;
	/**
	 * @var $request HTTPRequest Requête du client (en GET ou POST)
	 */
	protected $request;
	
	/**
	 * FormHandler constructor.
	 *
	 * @param Form        $form
	 * @param Manager     $manager
	 * @param HTTPRequest $request
	 */
	public function __construct( Form $form, Manager $manager, HTTPRequest $request ) {
		$this->setForm( $form );
		$this->setManager( $manager );
		$this->setRequest( $request );
	}
	
	/**
	 * Exécute la sauvegarde d'un formulaire.
	 * Renvoie vrai si le résultat a été sauvegardé par envoi valide, faux sinon.
	 *
	 * @return bool
	 */
	public function process() {
		if ( $this->request->method() == HTTPRequest::POST_METHOD AND $this->form->isValid() ) {
			$this->manager->save( $this->form->entity() );
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Setter pour l'attribut form.
	 *
	 * @param Form $form
	 */
	public function setForm( Form $form ) {
		$this->form = $form;
	}
	
	/**
	 * Setter pour l'attribut manager.
	 *
	 * @param Manager $manager
	 */
	public function setManager( Manager $manager ) {
		$this->manager = $manager;
	}
	
	/**
	 * Setter pour l'attribut request.
	 *
	 * @param HTTPRequest $request
	 */
	public function setRequest( HTTPRequest $request ) {
		$this->request = $request;
	}
}