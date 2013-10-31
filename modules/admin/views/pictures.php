<?php
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
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 3.0
 * @category  Parsimony
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<style>
#canvglob {position: absolute;top: 93px;bottom: 0;left: 1px;right: 110px;}
#contcanv {position: absolute;width: 100%;height: 100%;overflow: auto;border-right: 1px solid rgb(221, 221, 221);}
#panel {overflow: hidden}
#initsize{margin-bottom: 5px}
#results {position: absolute;right: 0;top: 92px;width: 110px;line-height: 24px;padding: 6px 6px 6px 12px;background: white;}
#crop{background-position: 5px 2px;}
#fliph{background-position: -63px 8px;}
#flipv{background-position: -113px 8px;}
#rotateright{background-position: -329px 2px;}
#rotateleft{background-position: -261px 2px;}
#undo{background-position: -392px 13px;}
#redo{background-position: -163px 13px;}
#resize{background-position: -202px 8px;}
#coord{width:110px}
#coord label{display: inline-block;width: 20px}
#coord input{width:45px;margin: 4px 0;}
#preserve{background: url('<?php echo BASE_PATH?>admin/img/spritelockunlock.png');width: 16px;height: 16px;margin-left: 75px;margin-top: -15px;}
#preserve.active{background: url('<?php echo BASE_PATH?>admin/img/spritelockunlock.png') 0 -33px no-repeat}
#savepict {position: relative;height: 61px;padding: 5px;z-index: 999;width: 100%;border-bottom: 1px dashed rgb(221, 221, 221);border-top: 1px solid rgb(221, 221, 221);}
#savepict div{cursor:pointer;background-image: url('<?php echo BASE_PATH?>admin/img/spriteimgfeatures.png');width: 48px;height: 48px;display: inline-block;background-repeat: no-repeat;}
.savePic{position: relative;top: -17px;}
.unsaved{font-weight:bold}
.unsaved .name:after{ content:"*";}
#initsize{display: none;}
#buttonRes{display: none;}
#buttonCrop{display: none;}
#editpictures{display: none;}
#initsize{display: block}
.crop #coord, .resize #coord{display: block;}
.crop #buttonCrop, .resize #buttonRes{display: block}
.crop #buttonRes,.resize #buttonCrop{display: none}
.resize #xx, .resize #yy{display: none}
#coord{display: none;}
#savepict .current {background-color: #E9E7E7;border-radius: 2px;}
</style>
<div class="container">
	<div id="savepict">
		<input type="button" class="savePic" value="Save">
		<div id="crop" title="Crop" data-tooltip="Crop" data-pos="s"></div>
		<div id="resize" title="Resize" data-tooltip="Resize" data-pos="s"></div>
		<div id="fliph" title="Flip horizontally" data-tooltip="Flip" data-pos="s"></div>
		<div id="flipv" title="Flip vertically" data-tooltip="Flip" data-pos="s"></div>
		<div id="rotateleft" title="Rotate left" data-tooltip="Flip" data-pos="s"></div>
		<div id="rotateright" title="Rotate right" data-tooltip="Flip" data-pos="s"></div>
		<div id="undo" title="Undo" data-tooltip="Undo" data-pos="s"></div>
		<div id="redo" title="Redo" data-tooltip="Redo" data-pos="s"></div> 
	</div>
	<div id="canvglob">
		<div id="contcanv">
		<canvas id="panel"> </canvas>
		</div>
	</div>

	<div id="results">
		<div id="initsize">
			Width:<span id="initWidth"></span>px <br> Height:<span id="initHeight"></span>px<br>
		</div>
		<div id="coord">
			<div id="xx"><label>X</label><input id="coordx"></div>
			<div id="yy"><label>Y</label><input id="coordy"></div>
			<div id="ww"><label>W</label><input id="coordw"></div>
			<div id="preserve"></div>
			<div id="hh"><label>H</label><input id="coordh"></div>
		</div>
		<div id="buttonRes"><input type="button" value="Appliquer"></div>
		<div id="buttonCrop"><input type="button" value="Appliquer"></div>

	</div>
</div>
<script>
/* Construct selection */

function Select(x, y, w, h){
	this.x = x; 
	this.y = y;
	this.w = w; 
	this.h = h;
	this.px = x; 
	this.py = y;
	this.handlesize = 4; 
	this.handlesizeh = 6; 
	this.handleEvent = [false, false, false, false]; 
	this.handle = [this.handlesize, this.handlesize, this.handlesize, this.handlesize]; 
	this.dragEvent = [false, false, false, false]; 
	this.dragEventAll = false; 
}

function pictureEditor(completename){ 
	var canvas, ctx;
	var mousex, mousey = 1;
	var selection;
	var imgw;
	var imgh;
	var ratio;
	var originx;
	var originy;
	this.snapshot = new Array();
	var history = 0;
	var dataURL;
	var extension;
	var completename = completename;
	var finalname;
	var datapic;
	var croptarget = document.getElementById("crop");
	var undotarget = document.getElementById("undo");
	var redotarget = document.getElementById("redo");
	var canvas = document.getElementById('panel');
	var ctx = canvas.getContext('2d');
	var idPreserve = document.getElementById("preserve");
	var initWidth;
	var initHeight;
	var image = new Image();
	var extension;
	var pair;
	var deg;
	var originx;
	finalname = completename;
	extension = finalname.substr((~-finalname.lastIndexOf(".") >>> 0) + 2);
	if(extension == 'jpg') extension == 'jpeg';
	finalname = finalname.replace('<?php echo BASE_PATH ?>','');

this.draw = function(){
	ctx.strokeStyle = 'white';
	ctx.lineWidth = 1;
	if (ctx.setLineDash) ctx.setLineDash([5,5]);
	else ctx.mozDash= [2,4];
	ctx.strokeRect(selection.x, selection.y, selection.w, selection.h);
	if (selection.w > 0 && selection.h > 0) 
		ctx.drawImage(image, selection.x, selection.y, selection.w, selection.h, selection.x, selection.y, selection.w, selection.h);   
	ctx.fillStyle = '#fff';
	ctx.fillRect(selection.x - selection.handle[0], selection.y - selection.handle[0], selection.handle[0] * 2, selection.handle[0] * 2);
	ctx.fillRect(selection.x + selection.w - selection.handle[1], selection.y - selection.handle[1], selection.handle[1] * 2, selection.handle[1] * 2);
	ctx.fillRect(selection.x + selection.w - selection.handle[2], selection.y + selection.h - selection.handle[2], selection.handle[2] * 2, selection.handle[2] * 2);
	ctx.fillRect(selection.x - selection.handle[3], selection.y + selection.h - selection.handle[3], selection.handle[3] * 2, selection.handle[3] * 2);
}


/* CROP SCENE */
this.drawSceneCrop = function () { 
	ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
	ctx.drawImage(image, 0, 0, ctx.canvas.width, ctx.canvas.height);
	ctx.fillStyle = 'rgba(51,51,51,0.5)';
	ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);
	this.draw();
}

/* reDraw called on tab change */
this.reDraw = function (){
	this.clearCrop();
	this.clearResize();
	this.initValues();
	ctx.clearRect(0, 0, initWidth, initHeight);
	document.getElementById('editpictures').style.display = 'block';
	canvas.width = initWidth;
	canvas.height= initHeight;
	ctx.drawImage(image, 0, 0, initWidth, initHeight);
}

/* SCENE FOR OTHERS */
this.drawScene = function () {
	ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); 
	ctx.drawImage(image, 0, 0, ctx.canvas.width, ctx.canvas.height);
}

/* loading source image */
this.loadPicture = function(dataURL) {
	document.getElementById('editpictures').style.display = 'block'; 
	imgw = image.width;
	imgh = image.height;
	canvas.width = imgw;
	canvas.height = imgh;
	ratio = imgw / imgh;
	completename = finalname;

	this.initValues();
	this.drawScene(imgw,imgh);
	dataURL = canvas.toDataURL("image/"+extension);
	datapic = dataURL;
	if(history == 0){ 
		dataURL = canvas.toDataURL("image/"+extension);
		this.snapshot.push(dataURL);
	}else if (undotarget.classList.contains('active') || redotarget.classList.contains('active')) {
		dataURL = this.snapshot[this.snapshot.length];
		undotarget.classList.remove('active');
		redotarget.classList.remove('active');
	}
 }

 /* Init values & HTML */
 this.initValues= function(){
	 document.getElementById('coordw').value = imgw;
	 document.getElementById('coordh').value = imgh;
	 document.getElementById('initWidth').textContent = imgw;
	 document.getElementById('initHeight').textContent = imgh;
	 initWidth = imgw; 
	 initHeight = imgh;
 }

 this.initCrop = function(){
	 this.clearResize();

	 /* CSS */
	 document.getElementById('results').classList.add('crop');
	 document.getElementById('results').classList.remove('resize');
	 /* New select */
	 selection = new Select(imgw/4, imgh/4, imgw/2, imgh/2);

	 /* Init forms */
	 document.getElementById('initWidth').textContent = imgw;
	 document.getElementById('initHeight').textContent = imgh;
	 $this = this;
	 /* Ratio */
	 ratio = initWidth / initHeight;
	 /* Changes on input */
	 $('.crop input').on('keyup',function() {
		 selection.x = parseInt(document.getElementById('coordx').value);
		 selection.y = parseInt(document.getElementById('coordy').value);
		 selection.w = parseInt(document.getElementById('coordw').value);
		 selection.h = parseInt(document.getElementById('coordh').value);
		 if(idPreserve.classList.contains('active')) {
			 if(this.id == 'coordw') {
				 selection.h = selection.w / ratio;
				 document.getElementById('coordh').value = selection.h;
			 } else if(this.id == 'coordh'){
				 selection.w = selection.h * ratio;
				 document.getElementById('coordw').value = selection.w;
			 }
		 }
		 $this.drawSceneCrop();
	 });

	 document.getElementById('coordx').value = selection.x;
	 document.getElementById('coordy').value = selection.y;
	 document.getElementById('coordw').value = selection.w; 
	 document.getElementById('coordh').value = selection.h; 

	 /* Draw Crop Scene */
	 this.drawSceneCrop();

	 /* MOUSEDOWN */
	$('#panel').on('mousedown',function(e) { 
		var canvOffset = $(canvas).offset();
		mousex = Math.floor(e.pageX - canvOffset.left);
		mousey = Math.floor(e.pageY - canvOffset.top);

		selection.px = mousex - selection.x;
		selection.py = mousey - selection.y;
		if (mousex > selection.x + selection.handlesizeh && mousex < selection.x+selection.w - selection.handlesizeh &&
			mousey > selection.y + selection.handlesizeh && mousey < selection.y+selection.h - selection.handlesizeh) {
			selection.dragEventAll = true;
		}
		for (i = 0; i < 4; i++) {
			selection.handleEvent[i] = false;
			selection.handle[i] = selection.handlesize;
		}
		if (mousex > selection.x - selection.handlesizeh && mousex < selection.x + selection.handlesizeh &&
			mousey > selection.y - selection.handlesizeh && mousey < selection.y + selection.handlesizeh) {
			selection.px = mousex - selection.x;
			selection.py = mousey - selection.y;
			 selection.handleEvent[0] = true;
			selection.handle[0] = selection.handlesizeh;
		}
		if (mousex > selection.x + selection.w-selection.handlesizeh && mousex < selection.x + selection.w + selection.handlesizeh &&
			mousey > selection.y - selection.handlesizeh && mousey < selection.y + selection.handlesizeh) {
			selection.px = mousex - selection.x - selection.w;
			selection.py = mousey - selection.y;
			selection.handleEvent[1] = true;
			selection.handle[1] = selection.handlesizeh;
		}
		if (mousex > selection.x + selection.w-selection.handlesizeh && mousex < selection.x + selection.w + selection.handlesizeh &&
			mousey > selection.y + selection.h-selection.handlesizeh && mousey < selection.y + selection.h + selection.handlesizeh) {
			selection.px = mousex - selection.x - selection.w;
			selection.py = mousey - selection.y - selection.h;
			selection.handleEvent[2] = true;

			selection.handle[2] = selection.handlesizeh;
		}
		if (mousex > selection.x - selection.handlesizeh && mousex < selection.x + selection.handlesizeh &&
			mousey > selection.y + selection.h-selection.handlesizeh && mousey < selection.y + selection.h + selection.handlesizeh) {
			selection.px = mousex - selection.x;
			selection.py = mousey - selection.y - selection.h;
			selection.handleEvent[3] = true;
			selection.handle[3] = selection.handlesizeh;
		}

		/* MOUSEUP */ 
		$(document).on('mouseup',function(e) { 
			selection.dragEventAll = false;
			for (i = 0; i < 4; i++) {
				selection.dragEvent[i] = false;
			}
			selection.px = 0;
			selection.py = 0;
			$(document).off('mouseup');
			$('#panel').off('mousemove');
		});

		/* MOUSEMOVE */

		$('#panel').on('mousemove',function(e) {
			mousex = Math.floor(e.pageX - canvOffset.left);
			mousey = Math.floor(e.pageY - canvOffset.top);

			if (selection.dragEventAll) {
				selection.x = mousex - selection.px;
				selection.y = mousey - selection.py;
			}
			if(selection.x < 0 ) selection.x = 0; else if (selection.x + selection.w > initWidth) selection.x = initWidth - selection.w;
			if(selection.y < 0 ) selection.y = 0;else if (selection.y + selection.h > initHeight) selection.y = initHeight - selection.h;


			var newW, newH;
			if (selection.handleEvent[0]) {
				var newX = mousex - selection.px;
				var newY = mousey - selection.py;
				newW = selection.w + selection.x - newX;
				newH = selection.h + selection.y - newY;
			}
			if (selection.handleEvent[1]) {
				var newX = selection.x;
				var newY = mousey - selection.py;
				newW = mousex - selection.px - newX;
				newH = selection.h + selection.y - newY;
			}
			if (selection.handleEvent[2]) {
				var newX = selection.x;
				var newY = selection.y;
				newW = mousex - selection.px - newX;
				newH = mousey - selection.py - newY;
			}
			if (selection.handleEvent[3]) {
				var newX = mousex - selection.px;
				var newY = selection.y;
				newW = selection.w + selection.x - newX;
				newH = mousey - selection.py - newY;
			}
			if (newW > selection.handlesizeh * 2 && newH > selection.handlesizeh * 2) {
				selection.w = newW;
				selection.h = newH;
				selection.x = newX;
				selection.y = newY;
			}
			document.getElementById('coordx').value = selection.x;
			document.getElementById('coordy').value = selection.y;
			document.getElementById('coordw').value = selection.w; 
			document.getElementById('coordh').value = selection.h;
			if(idPreserve.classList.contains('active')) {
				selection.w = selection.h * ratio; 
			}
			$this.drawSceneCrop();
		}); 
	});
 }

	this.clearCrop = function (){
		$('#coordw').off('change');
		$('#coordh').off('change');
		$('#panel').off('mousedown');
		$('.crop input').off();
		$('.current').removeClass('current');
		this.drawScene();
		document.getElementById('results').classList.remove('crop');
	}

	this.clearResize = function (){
		$('.resize #coordw,.resize #coordh').off('keyup');
		document.getElementById('results').classList.remove('resize');
		$('.current').removeClass('current');
	}

	this.initResize = function(){
		this.clearCrop();
		document.getElementById('results').classList.remove('crop');
		document.getElementById('results').classList.add('resize');
		imgw = document.getElementById('initWidth').innerHTML;
		imgh = document.getElementById('initHeight').innerHTML;
		document.getElementById('coordw').value = document.getElementById('initWidth').innerHTML;
		document.getElementById('coordh').value = document.getElementById('initHeight').innerHTML;  

		/* Preserve ratio */
			$('.resize #coordw,.resize #coordh').on('keyup',function() {
				if(idPreserve.classList.contains('active')) {
					if(this.id == 'coordw') {
						imgh = this.value / ratio;
						document.getElementById('coordh').value = imgh;
					} else if(this.id == 'coordh'){
						imgw = this.value * ratio;
						document.getElementById('coordw').value = imgw;
					}
				}
			});
		}

/* EVENTS */

image.addEventListener("load", this.loadPicture.bind(this), false);
image.src = completename;

this.undo = function(){
	if (history > 0) {
		history--;
		dataURL = this.snapshot[history];
		image.src = dataURL;
	}
}

this.redo = function() {
	if (history <= this.snapshot.length -2) {
		history++;
		dataURL = this.snapshot[history];
		image.src = dataURL;
	}
}

this.savecanv = function(dataURL) {
	dataURL = canvas.toDataURL("image/"+extension);
	image.src = dataURL;
	history++;
	if (history < this.snapshot.length) { this.snapshot.length = history; }
	this.snapshot.push(dataURL);
	$('#tabs li.active.pict').addClass("unsaved"); 
}

this.getCrop = function() {
	this.clearCrop();
	selection.x = document.getElementById('coordx').value;
	selection.y = document.getElementById('coordy').value;
	canvas.width = selection.w;
	canvas.height = selection.h;
	ctx.drawImage(image, selection.x, selection.y, selection.w, selection.h, 0, 0, selection.w, selection.h);
	this.savecanv();
}

this.resize = function() {
	this.clearResize();
	canvas.width = document.getElementById('coordw').value;
	canvas.height = document.getElementById('coordh').value;
	ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
	this.savecanv();
}

this.fliph = function() {
	/* FLIP Horiz*/
	this.initValues();
	canvas.width = document.getElementById('coordw').value;
	canvas.height = document.getElementById('coordh').value;
	ctx.translate(imgw, 0);
	ctx.scale(-1, 1);
	ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
	this.savecanv();
}

this.flipv = function () {
	/* FLIP Horiz*/
	this.initValues();
	canvas.width = document.getElementById('coordw').value;
	canvas.height = document.getElementById('coordh').value;
	ctx.translate(0, imgh);
	ctx.scale(1, -1);
	ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
	this.savecanv();
}

this.rotate = function(pair){ 
	this.initValues();
	if(pair == false){ 
		deg = 90;
		originx = canvas.height;
		originy = 0;
	}
	else {
		deg = -90;
		originx = 0 ;
		originy = canvas.width;
	}
	var initHeight = canvas.height;
	var initWidth = canvas.width;
	canvas.height = initWidth;
	canvas.width = initHeight;
	ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
	ctx.translate(originx, originy);
	ctx.rotate(deg*Math.PI/180);
	ctx.drawImage(image, 0, 0,initWidth,initHeight );
	this.savecanv();
	}

	/* UPLOAD */
	$(".savePic").on("click", function(ev) { 
		datapic = datapic.replace(/^data:image\/(png|jpg|gif|jpeg);base64,/, "");
		var filepath = '<?php echo PROFILE_PATH ?>' + finalname ;
		$.post("<?php echo BASE_PATH; ?>admin/savePicture", { file: filepath, code : datapic },
			function(data) {
			$('#tabs li.active.pict').removeClass("unsaved");
		});
	});
	}

	/* ON CLICK EVENTS */
	$('#savepict')
	.on("click","#crop", function(ev) {   
		var $this = getActiveTab();
		$this.initCrop();
		this.classList.add('current');
	})
	.on("click","#resize", function(ev) {
		var $this = getActiveTab();
		$this.initResize();
		this.classList.add('current');
	})
	.on("click","#fliph", function(ev) {
		var $this = getActiveTab();
		$this.clearCrop();
		$this.clearResize();
		$this.fliph();
	})
	.on("click","#flipv", function(ev) {
		var $this = getActiveTab();
		$this.clearCrop();
		$this.clearResize();
		$this.flipv();
	})
	.on("click","#undo", function(ev) {
		var $this = getActiveTab();
		$this.clearCrop();
		$this.clearResize();
		$this.undo();
	})
	.on("click","#redo", function(ev) {
		var $this = getActiveTab();
		$this.clearCrop();
		$this.clearResize();
		$this.redo();
	})
	.on("click","#rotateright", function(ev) {
		var $this = getActiveTab();
		$this.clearCrop();
		$this.clearResize();
		$this.rotate(false); 
	})
	.on("click","#rotateleft", function(ev) {
		var $this = getActiveTab();
		$this.clearCrop();
		$this.clearResize();
		$this.rotate(true);
	}); 

	$('#results')
	.on("click","#buttonCrop", function(ev) {
		var $this = getActiveTab();
		$this.getCrop();
	})
	.on("click","#buttonRes", function(ev) {
		var $this = getActiveTab();
		$this.resize();
	});
	/* Get PictureEditor Object of active picture */
	function getActiveTab(){
	 return pictures["tab-"+$('#tabs li.active.pict').attr('id')];
	}

	function act() {
		if (!this.classList.contains('active')) {
			this.classList.add('active');
			trigger(document.getElementById('coordw'),'input');
		} else {
			this.classList.remove('active');
		}
	}

	var undotarget = document.getElementById("undo");
	var redotarget = document.getElementById("redo");
	var preserve = document.getElementById("preserve");

	undotarget.addEventListener("click", canvEvent, false);
	redotarget.addEventListener("click", canvEvent, false);
	preserve.addEventListener("click", act, false);

	function canvEvent() {
		this.classList.add('active');
	}
	function trigger(el, event){
		ev = document.createEvent('Event');
		ev.initEvent(event, true, false);
		el.dispatchEvent(ev);
	}

</script>