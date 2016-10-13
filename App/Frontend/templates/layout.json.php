<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 13/10/2016
 * Time: 10:21
 */

/**
 * @var $layout_link_a string[] Liste des liens à afficher
 * @var $title string Titre de la page
 */
?>


{
	html: {
		head: {
			title: "<?= isset( $title ) ? $title : 'Mon super site' ?>",
			meta: {
				charset: "utf-8"
			}
			link: {
				rel: "stylesheet",
				href: "/css/Envision.css",
				type: "text/css"
			}
		}
	
		body: {
			div: {
				id: "wrap",
				header: {
					h1: {
						a: "Mon super site" {
							href: "<?= $layout_link_a['Frontend-buildIndex']?>"
						}
					},
					p: "Comment ça, il n'y a presque rien ?"
				}
			
			nav:
				ul:
					<?php foreach ($menu_a as $element_a): ?>
						li: {
							a: "<?= $element_a['label']?>" {
								href: "<?= $element_a['link'] ?>"
							}
						}
					<?php endforeach; ?>
				}
			}
			
			div: {
				id: "content-wrap",
				section: {
					id: "main",
					<?php if(isset($flash)): ?>
						p: "<?= $flash ?>" {
							style: "text-align: center;"
						}
					<?php endif ?>
					<?= $content ?>
				}
			}
			
			footer: {
			}
		}
	}
}