/*
 * Tooltip - jQuery Plugin
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
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Tooltip - jQuery Plugin
 * Requires: jQuery v1.4.2+
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

(function( $ ){
    var methods = {
	init : function( options ) {

	    paramsTooltip = $.extend( {
		position: '',
		triangleWidth: 7
	    }, options);
	    
	    var tooltip = $("#parsimonyTooltip");
	    if(tooltip.length ==0){
		$('<div id="parsimonyTooltip"><div class="tri"></div><div class="parsimonyTooltipContent"></div></div>').appendTo("body");
		tooltip = $("#parsimonyTooltip");
	    }
	    
	    var tooltipTriangle = $(".tri", tooltip);
	    var tooltipContent = $(".parsimonyTooltipContent", tooltip);

	    /* On mouse enter on the object */
	    $(this.context).on("mouseenter.parsimonyTooltip", this.selector, function(e){
		var $this = $(this);
		/* Get position of tooltip */
		var position = $(this).data("pos");
		/* Get offset position of tooltip */
		var off = $this.offset();
                /* get content to display */
                if($(this).data("tooltip").substring(0, 1) == "#") var content = $($(this).data("tooltip")).html();
                else var content = $(this).data("tooltip");
		/* Set data to display in tooltip */
		$(".parsimonyTooltipContent",tooltip).html(content);
		var left, top = '';
		off = $this.offset();
		/* If no position set */
		if(typeof position == 'undefined'){
		    if(paramsTooltip.position == ''){
			/* If no position set on data attributes */
                        if(!$(this).data("pos")){
			    /* If left space lower than right */
			    var first = 'w';
			    if( off.left <= $(window).width() - off.left + $this.width()){
				first = 'e';
			    }
			    /* If top space lower than bottom */
			    var second = 's';
			    if( off.top <= $(window).height() - off.top + $this.height()){
				second = 'n';
			    }
			    /* Set position of tooltip */
			    $(this).data("pos",first + second);
			}
		    }else{
			$(this).data("pos",paramsTooltip.position);
		    }
		    position = $(this).data("pos");
		}
		/* Calculate pos of tooltip */
		switch(position.substring(0,1)){
		    case 'n':
			tooltipContent.css("margin","0 0 " + paramsTooltip.triangleWidth + "px 0");
			if(position.length==1) {
			    left = off.left + ($this.outerWidth()/2) - (tooltip.outerWidth()/2);
			    tooltipTriangle.attr('style','').css("left",(tooltip.outerWidth()/2 - paramsTooltip.triangleWidth) + "px");
			}
			top = off.top - tooltip.outerHeight();
			break;
		    case 's':
			tooltipContent.css("margin","" + paramsTooltip.triangleWidth + "px 0 0 0");
			if(position.length==1) {
			    left = off.left + ($this.outerWidth()/2) - (tooltip.outerWidth()/2);
			    tooltipTriangle.attr('style','').css("left",(tooltip.outerWidth()/2 - paramsTooltip.triangleWidth) + "px");
			}
			top = off.top + $this.outerHeight();
			break;
		    case 'e':
			tooltipContent.css("margin","0 0 0 " + paramsTooltip.triangleWidth + "px");
			left = off.left + $this.outerWidth();
			if(position.length==1) {
			    top = (off.top + $this.outerHeight()/2)  - tooltip.outerHeight()/2;
			    tooltipTriangle.attr('style','').css("top",(tooltip.outerHeight()/2 - paramsTooltip.triangleWidth) + "px");
			}
			break;
		    case 'w':
			tooltipContent.css("margin","0 " + paramsTooltip.triangleWidth + "px 0 0");
			left = off.left - tooltip.outerWidth();
			if(position.length==1) {
			    top = (off.top + $this.outerHeight()/2)  - tooltip.outerHeight()/2;
			    tooltipTriangle.attr('style','').css("top",(tooltip.outerHeight()/2 - paramsTooltip.triangleWidth) + "px");
			}
			break;
		}
		if(position.length==2){
		    switch(position.substring(1)){
			case 'e':
			    left = off.left + $this.outerWidth() - tooltip.outerWidth();
			    tooltipTriangle.attr('style','').css("right",paramsTooltip.triangleWidth + "px");
			    break;
			case 'w':
			    left = off.left;
			    tooltipTriangle.attr('style','').css("left",paramsTooltip.triangleWidth + "px");
			    break;
			case 'n':
			    top = off.top;
			    tooltipTriangle.attr('style','').css("top",paramsTooltip.triangleWidth + "px");
			    break;
			case 's':
			    top = off.top + $this.outerHeight() - tooltip.outerHeight();
			    tooltipTriangle.attr('style','').css("bottom",paramsTooltip.triangleWidth + "px");
			    break;
		    }
		}
		/* Set class to the triangle of the tooltip */
		var classes = 'tri-' + position;
		if(position.length==2)classes += ' tri-' + position.substring(0,1);
		tooltipTriangle.attr('class','tri ' + classes);
		/* Set triangle style */
		tooltipTriangle.css("border-width", paramsTooltip.triangleWidth + "px");
		/* Display tooltip */
		tooltip.stop().css({
		    left:left,
		    top:top,
		    opacity:1,
		    display:"block"
		});
	    });
	    /* On mouse leave the object */
	    $(this.context).on("mouseleave.parsimonyTooltip", this.selector,function(e){
		/* We hide the tooltip */
		tooltip.fadeOut("speed");
	    });
	},
	destroy : function( ) {
	    return this.each(function(){
		$(this).off('.parsimonyTooltip');
	    })
	}
    };
    $.fn.parsimonyTooltip = function( method ) {
	if ( methods[method] ) {
	    return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
	} else if ( typeof method === 'object' || ! method ) {
	    return methods.init.apply( this, arguments );
	} else {
	    $.error( 'Method ' +  method + ' does not exist on jQuery.parsimonyTooltip' );
	}    
  
    };

})( jQuery );