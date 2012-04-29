/**
 * Drag n'Drop - jQuery Plugin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
 * @category  Drag n'Drop - jQuery Plugin
 * Requires: jQuery v1.4.2+
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


(function( $ ){

    var methods = {
	init : function( options ) {

	    params = $.extend( {
		stopDraggable: function() {},
		stopResizable: function() {}
	    }, options);
	    
	    var context = this.closest("html");
	    context.get(0).onselectstart = new Function ("return false");
	    context.on('dragstart','img', function(event) {
		event.preventDefault()
	    });
	    context.off("mousemove").off("mouseup");
	    $(".parsimonyDND",context).off("click").off("mousedown").off("click",".parsimonyResize").find(".parsimonyResize").off("mousedown");
	    $(".parsimonyResize",context).remove();
	    $(".parsimonyDND",context).removeClass("parsimonyDND");
    
	    return this.each(function() {
		var $this = $(this);
		$this.addClass("parsimonyDND");
		if($this.css('position')=="static"){
		    $this.css('position','relative');
		    $('#panelcss select[name="position"]').val('relative');
		}
		$this.append('<div class="parsimonyResizeInfo parsimonyResize"><span></span> | <a href="#" class="parsimonyResizeReInit" style="color:#fff">Reinit</a></div><div class="parsimonyResize se"></div><div class="parsimonyResize nw"></div><div class="parsimonyResize ne"></div><div class="parsimonyResize sw"></div>');
		$this.on("mousedown.parsimonyDND",function(e){
		    e.stopImmediatePropagation();
		    var dndstart = {
			$this : $(this),
			left : isNaN(parseFloat($(this).css("left"))) ? 0 : $(this).css("left"),
			top : isNaN(parseFloat($(this).css("top"))) ? 0 : $(this).css("top"),
			pageX : e.pageX,
			pageY : e.pageY
		    };
		    context.on("mousemove.parsimonyDND",dndstart,function(e){
			$this.css({
			    left: parseFloat(dndstart.left) + e.pageX - dndstart.pageX + "px",
			    top: parseFloat(dndstart.top) + e.pageY - dndstart.pageY + "px"
			});
			$(".parsimonyResizeInfo span",$this).text("W : " + $this.width() + "px | H : " + $this.height() + "px | T : " + $this.css("top") + " | L : " + $this.css("left") );
		    }).on("mouseup.parsimonyDND",dndstart,function(e){
			params.stopDraggable(e,$this);
			context.off("mousemove").off("mouseup");
		    });
		});
		$this.on("click.parsimonyDND",function(e){
		    e.stopImmediatePropagation();
		});
		$this.on("click.parsimonyDND",".parsimonyResize",function(e){
		    e.stopImmediatePropagation();
		});
		$this.find(".parsimonyResizeReInit").on("click.parsimonyDND",function(e){
		    e.preventDefault();
		    $(this).closest(".parsimonyDND").parsimonyDND("reInit");
		});
		$this.find(".parsimonyResize").on("mousedown.parsimonyDND",function(e){
		    e.stopImmediatePropagation();
		    var marg = isNaN(parseFloat($(this).css("width"))) ? 0 : parseFloat($(this).outerWidth());
		    var parent = $(this).parent();
		    var dndstart = {
			$this : parent,
			width : isNaN(parseFloat(parent.css("width"))) ? 0 : parent.outerWidth(),
			height : isNaN(parseFloat(parent.css("height"))) ? 0 : parent.outerHeight(),
			top : isNaN(parseFloat(parent.css("top"))) ? 0 : parent.css("top"),//$(this).css('top'),
			left : isNaN(parseFloat(parent.css("left"))) ? 0 : parent.css("left"),//$(this).css('left'),
			pageX : e.pageX,
			pageY : e.pageY,
			dir : $(this).attr('class').replace("parsimonyResize ", "")
		    };
		    context.on("mousemove.parsimonyDND",dndstart,function(e){
			switch(dndstart.dir){
			    case "se":
				$this.css({
				    width: parseFloat(dndstart.width) + (e.pageX - dndstart.pageX) + "px",
				    height: parseFloat(dndstart.height) + (e.pageY - dndstart.pageY) + "px"
				});
				break;
			    case "nw":
				$this.css({
				    top: parseFloat(dndstart.top) + (e.pageY - dndstart.pageY) + "px",
				    left: parseFloat(dndstart.left) + (e.pageX - dndstart.pageX) + "px",
				    width: parseFloat(dndstart.width) - (e.pageX - dndstart.pageX) + "px",
				    height: parseFloat(dndstart.height) - (e.pageY - dndstart.pageY) + "px"
				});
				break;
			    case "ne":
				$this.css({
				    top: parseFloat(dndstart.top) + (e.pageY - dndstart.pageY) + "px",
				    width: parseFloat(dndstart.width) + (e.pageX - dndstart.pageX) + "px",
				    height: parseFloat(dndstart.height) - (e.pageY - dndstart.pageY) + "px"
				});
				break;
			    case "sw":
				$this.css({
				    left: parseFloat(dndstart.left) + (e.pageX - dndstart.pageX) + "px",
				    width: parseFloat(dndstart.width) - (e.pageX - dndstart.pageX) + "px",
				    height: parseFloat(dndstart.height) + (e.pageY - dndstart.pageY) + "px"
				});
				break;
			}
			$(".parsimonyResizeInfo span",$this).text("W : " + $this.width() + "px | H : " + $this.height() + "px | T : " + $this.css("top") + " | L : " + $this.css("left"));
		    }).on("mouseup",dndstart,function(e){
			params.stopResizable(e,$this);
			context.off("mousemove").off("mouseup");
		    });
		});
	    });
	},
	reInit : function( ) {
	    return this.each(function(){
		$(this).css({
		    top: "auto",
		    left: "auto",
		    width: "auto",
		    height: "auto"
		});
		$(".parsimonyResizeInfo span",$(this)).text("W : " + $(this).width() + "px | H : " + $(this).height() + "px | T : " + $(this).css("top") + "| L : " + $(this).css("left") );
	    })
	},
	destroy : function( ) {
	    return this.each(function(){
		$(".parsimonyResize,.parsimonyResizeInfo",this).remove();
		$(this).removeClass('parsimonyDND');
		$(this).off('.parsimonyDND');
		$(this).closest("html").get(0).onselectstart = null;
	    })
	}
    };

    $.fn.parsimonyDND = function( method ) {
	if ( methods[method] ) {
	    return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
	} else if ( typeof method === 'object' || ! method ) {
	    return methods.init.apply( this, arguments );
	} else {
	    $.error( 'Method ' +  method + ' does not exist on jQuery.parsimonyDND' );
	}    
  
    };

})( jQuery );