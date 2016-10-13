<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 13/10/2016
 * Time: 10:21
 */

require 'layout.html.php';
?>
	
<?php /*	<div id="test">
		'blabla'
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
	<script type="text/javascript">
		// Test avec le header
		var content = {
			div: {
				id: "Mon super site",
				name: "blabla",
			}
		};
		
		// Transformer la variable en string JSON
		var string = JSON.stringify( content );
		html       = jQuery.parseJSON( string );
		
		function printRecursively( object ) {
			// Si on n'a plus un objet, on arrête la récursion
			if ( typeof object === 'object' ) {
				var data = [];
				for ( var element in object ) {
					if ( typeof object[ element ] !== 'object' ) {
						// Si le fils n'est pas un objet, on retourne sa valeur
						data[ element ] = object[ element ];
					}
					else {
						var node = document.createElement( element );
						// Sinon on ajoute le noeud fils
						child    = printRecursively( object[ element ] );
						
						for ( var content in child ) {
							if (content == 'value') {
								// Ajouter le contenu entre les balises
								node.innerHTML = child[content];
							}
							else if ( typeof child === 'Node' ) {
								node.appendChild( child );
							}
							else {
								node.setAttribute( content, child[ content ] );
							}
						}
						return node;
					}
				}
				return data;
			}
		}
		
		node = printRecursively( html );
		document.getElementById('test').appendChild(node);
		console.log(document.getElementById('test'));
	
	</script>
<?php /*
{
	html: {
		head: {
			title: "<?= isset( $title ) ? $title : 'Mon super site' ?>",
			meta: {
				charset: "utf-8"
			},
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
}*/