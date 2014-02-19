/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony.js
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


var Parsimony = {
	blocks: {},
	registerBlock: function(name, block) { /* Important to be Web component ready */
		this.blocks[name] = block;
		if (typeof document.registerElement == "functionff") {
			 var proto = Object.create(HTMLElement.prototype);
			proto.createdCallback = function() {
			  this.innerHTML = new Date();
			};
			document.registerElement(name, {prototype: proto});
		}
	},
	blocksDispatch: function(methodName) { /* Call this method for all blocks */
		for (var i in this.blocks) {
			if (typeof this.blocks[i][methodName] == "function") {
				this.blocks[i][methodName]();
			}
		}
	},
	dispatchEvents: function(e) { 
		var elementName = this.getAttribute("is");
		var events = Parsimony.blocks[elementName].events[e.type];
		for (var selector in events) {
			var node = e.target;
			while (node != this) {
				if (matchesSelector.call(node, selector)) {
					events[selector](e, this, node);
					e.stopPropagation();
					e.preventDefault();
					break;
				}
				node = (node.parentNode || this);
			}
		}
	 }
}

var matchesSelector = (document.documentElement.webkitMatchesSelector || document.documentElement.mozMatchesSelector || document.documentElement.msMatchesSelector || document.documentElement.matchesSelector);

$(document).ready(function() {
	
	/* Web components polyfill */
	
	for(var elementName in Parsimony.blocks) {
		var elmts = document.querySelectorAll('[is="' + elementName + '"]');
		for(var i = 0, len = elmts.length; i < len; i++) {
			var elmt = elmts[i];
			var definition = Parsimony.blocks[elementName];
			
			//if (typeof document.registerElement != "functionttt") {
				
				/* set prototype */
				for(var key in definition.prototype){
					elmt[key] = definition.prototype[key];
				}

				/* call createdCallback id defined */
				if(typeof definition.prototype["createdCallback"] == "function"){
					definition.prototype.createdCallback.bind(elmt)();
				}
			//}
			
			/*  register events */
			if(typeof definition.events == "object"){
				for(var eventType in definition.events) {
					elmt.addEventListener(eventType, Parsimony.dispatchEvents, false);
				}
			}
		}
	}
	
});

function loadBlock(id, params, callback) {
	if (!params)
		params = {};
	if (!callback)
		window['callback'] = '';
	else
		window['callback'] = callback;
	$.get(window.location.href.toLocaleString(), params, function(data) {
		$('#' + id).html($("<div>").append(data.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")).find("#" + id).html());
		if (typeof window['callback'] == 'function')
			window['callback'].call();
	});
}

/* Client Side Traduction */
var $lang = new Array;
function t(val) {
	if ($lang[val]) {
		return $lang[val];
	} else {
		return val;
	}
}
