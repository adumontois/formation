<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 13/10/2016
 * Time: 10:21
 */

require 'layout.html.php';
?>


<?php
/*
 * 	// Test
	var comment = {
		comment: {
			fk_SNC: '18'
			author: 'Mon_auteur'
			content: 'Ceci est un contenu.'
			date: '13/10/2016 à 14h24'
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
 */