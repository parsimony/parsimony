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
                stopResizable: function() {},
                initPos:{}
            }, options);
            var initContext = $("#overlays");
            params.initContext = initContext;

            var context = this.closest("html");
            initContext.off("mousemove").off("mouseup");
            $(".parsimonyDND",initContext).off("click").off("mousedown").off("click",".parsimonyResize").find(".parsimonyResize").off("mousedown");
            $(".parsimonyResize",initContext).remove();
            $(".parsimonyDND",context).removeClass("parsimonyDND");
            $(".parsimonyResizeInfo #resizeinfo",initContext).text("");
            
            return this.each(function() {
                var $this = $(this);
                $this.addClass("parsimonyDND");
                if($this.css('position')=="static"){
                    $this.css('position','relative');
                    $('#panelcss select[name="position"]').val('relative');
                }
                params.initPos = {
                    left : isNaN(parseFloat($this.css("left"))) ? 'initial' : $this.css("left"),
                    top : isNaN(parseFloat($this.css("top"))) ? 'initial' : $this.css("top"),
                    width : isNaN(parseFloat($this.css("width"))) ? 'initial' : $this.css("width"),
                    height : isNaN(parseFloat($this.css("height"))) ? 'initial' : $this.css("height")
                };
                initContext.append('<div class="parsimonyDND" style="relative"><div class="parsimonyResizeInfo"> <span class="parsimonyResizeReInit spanDND ui-icon-arrowrefresh-1-w" title="Reinit"></span> </a><span class="parsimonyResizeClose spanDND closedesign ui-icon-closethick"></span><span id="resizeinfo" style="display:inline-block"></span> </div><div class="parsimonyResize se"></div><div class="parsimonyResize nw"></div><div class="parsimonyResize ne"></div><div class="parsimonyResize sw"></div></div>');
                var offset = $(this).offset();
                $(".parsimonyDND",initContext).css({
                    position: "absolute",
                    left : offset.left,
                    top : offset.top,
                    width : params.initPos.width,
                    height : params.initPos.height
                });
                var dnd = $(".parsimonyDND", initContext);
                dnd.on("mousedown.parsimonyDND",function(e){
                    var $this = $(".parsimonyDND",context);
                    var dndstart = {
                        $this : $this,
                        left : isNaN(parseFloat($this.css("left"))) ? 0 : $this.css("left"),
                        top : isNaN(parseFloat($this.css("top"))) ? 0 : $this.css("top"),
                        pageX : e.pageX,
                        pageY : e.pageY
                    };
                    $("#overlays").css("pointer-events","all");
                    initContext.on("mousemove.parsimonyDND",dndstart,function(e){
                        $this.css({
                            left: parseFloat(dndstart.left) + e.pageX - dndstart.pageX + "px",
                            top: parseFloat(dndstart.top) + e.pageY - dndstart.pageY + "px"
                        });
                        $(".parsimonyResizeInfo #resizeinfo",initContext).text("W : " + $this.width() + "px | H : " + $this.height() + "px | T : " + $this.css("top") + " | L : " + $this.css("left") );
                        var bounds1 = $this.get(0).getBoundingClientRect();
                        var bounds = $this.offset();
                        $(".parsimonyDND",initContext).css({
                            top: bounds.top + "px",
                            left: bounds.left + "px",
                            width: bounds1.width + "px",
                            height: bounds1.height + "px"
                        });
                    }).on("mouseup.parsimonyDND",dndstart,function(e){
                        $("#overlays").css("pointer-events","none");
                        params.stopDraggable(e,$this);
                        initContext.off("mousemove").off("mouseup");
                    });
                });
                initContext.on("click.parsimonyDND",function(e){
                    e.stopImmediatePropagation();
                });
                initContext.on("click.parsimonyDND",".parsimonyResize",function(e){
                    e.stopImmediatePropagation();
                });
                initContext.find(".parsimonyResizeReInit").on("click.parsimonyDND",function(e){
                    e.preventDefault();
                    $this.closest(".parsimonyDND").parsimonyDND("reInit");
                });
                initContext.find(".parsimonyResizeClose").on("click.parsimonyDND",function(e){
                    e.preventDefault();
                    $this.closest(".parsimonyDND").parsimonyDND("destroy");
                });
                initContext.find(".parsimonyResize").on("mousedown.parsimonyDND",function(e){
                    e.stopImmediatePropagation();
                    $("#overlays").css("pointer-events","all");
                    var parent = $(".parsimonyDND",context);
                    var bounds = parent.get(0).getBoundingClientRect();
                    var dndstart = {
                        $this : parent,
                        width : bounds.width,
                        height : bounds.height,
                        top : isNaN(parseFloat(parent.css("top"))) ? 0 : parent.css("top"),//$(this).css('top'),
                        left : isNaN(parseFloat(parent.css("left"))) ? 0 : parent.css("left"),//$(this).css('left'),
                        pageX : e.pageX,
                        pageY : e.pageY,
                        dir : $(this).attr('class').replace("parsimonyResize ", "")
                    };
		    
                    initContext.on("mousemove.parsimonyDND",dndstart,function(e){
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
                        var bounds1 = $this.get(0).getBoundingClientRect();
                        var bounds = $this.offset();
                        $(".parsimonyDND",initContext).css({
                            top: bounds.top + "px",
                            left: bounds.left + "px",
                            width: bounds1.width + "px",
                            height: bounds1.height + "px"
                        });
                        $(".parsimonyResizeInfo #resizeinfo",initContext).text("W : " + $this.width() + "px | H : " + $this.height() + "px | T : " + $this.css("top") + " | L : " + $this.css("left"));
                    }).on("mouseup",dndstart,function(e){
                        $("#overlays").css("pointer-events","none");
                        params.stopResizable(e,$this);
                        initContext.off("mousemove").off("mouseup");
                    });
                });
            });
        },
        reInit : function( ) {
            return this.each(function(){
                $(this).css({
                    top: params.initPos.top,
                    left: params.initPos.left,
                    width: params.initPos.width,
                    height: params.initPos.height
                });
                $(".parsimonyResizeInfo #resizeinfo",$(this)).text("W : " + $(this).width() + "px | H : " + $(this).height() + "px | T : " + $(this).css("top") + "| L : " + $(this).css("left") );
                var bounds1 = this.getBoundingClientRect();
                var bounds = $(this).offset();
                $(".parsimonyDND",params.initContext).css({
                    top: bounds.top + "px",
                    left: bounds.left + "px",
                    width: bounds1.width + "px",
                    height: bounds1.height + "px"
                });
                params.stopDraggable("",$(this));
            })
        },
        destroy : function( ) {
            
            return this.each(function(){
                $(".parsimonyResize,.parsimonyResizeInfo",params.initContext).remove();
                $(this).removeClass('parsimonyDND');
                $(this).off('.parsimonyDND');
            })
        }
    };

    $.fn.parsimonyDND = function( method ) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }  
  
    };

})( jQuery );