/**
 * Créer un Take a Tour.
 * Le paramètre steps doit un array contenant des objets de la forme suivante :
 *  {
 *      targetName: // (optionnel) Nom de l'élément à viser, attribué avec l'attribut data-tat-name
 *      content:    // (optionnel) Contenu HTML à afficher en milieu de page
 *  }
 * @param String id
 * @param Array steps
 */

TakeATour = function(id, steps) {
        this.started = false;
		 
	
        // Bloc de contenu du Take a Tour
        this.block = document.createElement('div');
        document.body.appendChild(this.block);
        this.block.id = id;
        this.block.className = "tat hidden";
        this.block.innerHTML =
              '<div class="overlay-blocks">'
                + '<div class="top"></div>'
                + '<div class="right"></div>'
                + '<div class="bottom"></div>'
                + '<div class="left"></div>'
                + '<div class="center"></div>'
            + '</div>'
            + '<svg class="arrow">'
                + '<defs>'
                    + '<marker id="markerArrow" markerWidth="8" markerHeight="8" refx="2" refy="6" orient="auto">'
                        + '<path d="M 0,4 6,6 0,8 1,6 z" />'
                    + '</marker>'
                    + '<marker id="markerCircle" markerWidth="4" markerHeight="4" refx="2" refy="2">'
                        + '<circle cx="2" cy="2" r="2" />'
                    + '</marker>'
                + '</defs>'
                + '<path class="p" d="M 00 10 Q 150 50 100 100 Q 50 150 200 200" marker-start="url(#markerCircle)" marker-end="url(#markerArrow)" />'
            + '</svg>'
            + '<div class="content hidden"></div>'
            + '<div class="buttons">'
                + '<span class="previous">Prev</span>'
                + '<span class="quit" onclick="ParsimonyAdmin.setCookie(\'takeATour\',\'\');">Quit</span>'
                + '<span class="next">Next</span>'
            + '</div>';

        // Array contenant les noms des éléments "étapes"
        this.steps = steps || [];

        // Numéro de l'étape actuelle (ou false)
        this.currentStep = false;

        // Overlay
        this.overlayBlocks = {top: null, right: null, bottom: null, left: null, center: null};

        // Marge intérieure de l'overlay
        this.padding = 4;

        // Largeur de la bordure
        this.borderWidth = 2;

        // Création des blocs
        for (var b in this.overlayBlocks)
            this.overlayBlocks[b] = this.querySelector("." + b);
        
        // Création du bloc d'affichage de contenu
        this.contentBlock = this.querySelector(".content");

        // Pour le scope
        var _this = this;

        // Bouton quitter
        this.quitButton = this.querySelector(".buttons .quit");
        this.quitButton.addEventListener('click', function(){ _this.stop() });

        // Bouton précédent
        this.previousButton = this.querySelector(".buttons .previous");
        this.previousButton.addEventListener('click', function(){ _this.previousStep() });

        // Bouton suivant
        this.nextButton = this.querySelector(".buttons .next");
        this.nextButton.addEventListener('click', function(){ _this.nextStep() ; 
		
    if (!this.currentStep || this.currentStep >= this.steps.length){
        this.currentStep = 0;
		document.querySelector('.previous').style.opacity = '1';
	}
		});
}
 

/**
 * Raccourci pour récupérer un élément (ou plusieurs) à l'intérieur du Take a Tour
 * @param String selector
 * @return HTMLElement|Array
 */

TakeATour.prototype.querySelector = function(selector) {
    var elems = document.querySelector("#" + this.block.id + " " + selector);
    if (elems.length == 1)
        return elems[0];
    else
        return elems;
}




/**
 * Démarrer le Take a Tour
 */

TakeATour.prototype.start = function() {
    this.started = true;
	
    this.switchToStep(0);

    // Suppression de la classe hidden
    this.block.classList.remove("hidden");
}

/**
 * Stopper le Take a Tour
 */
TakeATour.prototype.stop = function() {
    this.started = false;
    // Ajout de la classe hidden
    this.block.classList.add("hidden")
}

/**
 * Passer à l'étape suivante.
 */
TakeATour.prototype.nextStep = function() {

    // Etape suivante
    if (typeof this.currentStep == "number")
        this.currentStep++;

    // Ou retour à la première étape
    if (!this.currentStep || this.currentStep >= this.steps.length)
        this.currentStep = 0;

    this.switchToStep(this.currentStep);
}

/**
 * Passer à l'étape précédente.
 */
TakeATour.prototype.previousStep = function() {

    // Etape suivante
    if (typeof this.currentStep == "number")
        this.currentStep--;

    // Ou retour à la première étape
    if (!this.currentStep || this.currentStep < 0)
        this.currentStep = this.steps.length - 1;

    this.switchToStep(this.currentStep);
}

/**
 * Passer directement à une étape.
 */
TakeATour.prototype.switchToStep = function(stepNum) {
    this.currentStep = stepNum;
    var step = this.steps[stepNum];

    this.hideArrow();
    this.hideContent();
    
    // Callback
    if (typeof step.callback == "function")
        step.callback();
    
    // Fenêtre de description
    if (step.content)
        this.showContent(step.content);
    
    var target = this.getCurrentTarget();
    
    if (target)
    {
        // Application du focus
        this.focusOn(target);
        
        // Afficher la flèche si nécessaire
        if (step.content)
            this.showArrow(target);
    }
    else
        // Ecran complètement noir
        this.focusOn();
}

/**
 * Appliquer le focus sur un élément
 * @param target
 */
TakeATour.prototype.focusOn = function(target) {
    // Raccourci vers la liste des blocs
    var ob = this.overlayBlocks;

    // Marge intérieure
    var p = 0;

    // Position de l'élément HTML
    var box;

    if (typeof target == "object") {
        p = this.padding + this.borderWidth;

        // Position de l'élément HTML
        box = target.getBoundingClientRect();

        // Affichage du bloc central
        ob.center.style.opacity = 1;
    }
    else {
        // Disparition du bloc central
        ob.center.style.opacity = 0;

        // Position
        box = {
            top: document.body.clientHeight / 2,
            bottom: document.body.clientHeight / 2,
            right: document.body.clientWidth / 2,
            left: document.body.clientWidth / 2,
            width: 0,
            height: 0
        }
    }

    // Bloc du bas
    ob.bottom.style.top = box.bottom + p + "px";
    ob.bottom.style.width = box.width + p * 2 + "px";
    ob.bottom.style.left = box.left - p + "px";

    // Bloc du haut
    if (box.top - p >= 0)
        ob.top.style.height = box.top - p + "px";
    else
        ob.top.style.height = "0";
    
    
    ob.top.style.width = box.width + p * 2 + "px";
    ob.top.style.left = box.left - p + "px";
    
    // Blocs droite/gauche
    ob.left.style.width = box.left - p + "px";
    ob.right.style.left = box.right + p + "px";

    // Bloc du haut
    if (box.left - p >= 0)
        ob.left.style.width = box.left - p + "px";
    else
        ob.left.style.width = "0";
    
    
    // Bloc central
    ob.center.style.top = box.top - p - this.borderWidth + "px";
    ob.center.style.width = box.width + p * 2 + this.borderWidth + this.borderWidth / 2 + "px";
    ob.center.style.height = box.height + p * 2 + this.borderWidth * 2 - this.borderWidth / 2 + "px";
    ob.center.style.left = box.left - p - this.borderWidth + "px";
    
    
}

/**
 * Afficher du contenu
 */
TakeATour.prototype.showContent = function(content) {
    this.contentBlock.innerHTML = content;
    this.contentBlock.classList.remove("hidden");
}

/**
 * Masquer le contenu
 */
TakeATour.prototype.hideContent = function() {
    this.contentBlock.classList.add("hidden");
}

/**
 * Afficher la flèche
 */
TakeATour.prototype.showArrow = function(target) {
    var arrowBlock = this.querySelector("svg.arrow");
    arrowBlock.className.baseVal = arrowBlock.className.baseVal.replace(/ ?hidden/, '');
    
    var path = this.querySelector("svg.arrow > path");
    var box2 = target.getBoundingClientRect();
    var box1 = this.contentBlock.getBoundingClientRect();
    
    var x1 = box1.left + box1.width/ 2, y1 = box1.top;
    var x2 = box2.left + box2.width/ 2, y2 = box2.bottom + 20;
    
    var cx1 = box1.left + box1.width/ 2, cy1 = box1.top + box1.height / 2
    var cx2 = box2.left + box2.width/ 2, cy2 = box2.top + box2.height / 2
    
    if (cx1 < cx2) {
        x1 = box1.left + box1.width / 2;
        x2 = box2.left + box2.width / 2;
    }
    
    if (cx2 < box1.left && cy2 < box1.top)
    {
        x2 = box2.right + 25;
        x1 = box1.left;
        
        y1 = box1.top;
        y2 = box2.bottom + 25;
    }
    else if (cy2 > box1.top && cy2 < box1.bottom)
    {
        y1 = box1.top + box1.height / 2;
        y2 = box2.top + box2.height / 2;
        
        if (cx2 < box1.left) {
            x2 = box2.right + 25;
            x1 = box1.left;
        }
        else {
            x2 = box2.left - 25;
            x1 = box1.right;
        }
    }
    else if (cx2 < box1.left)
    {
        x2 = box2.right + 25;
        x1 = box1.left;
        
        y2 = box2.top + box2.height / 2;
    }
    else if (cx2 > box1.right)
    {
        x2 = box2.left - 25;
        x1 = box1.right;
        
        y2 = box2.top + box2.height / 2;
    }
    
    // Longueur du segment
    var distance = Math.sqrt(Math.pow(x2-x1, 2) + Math.pow(y2-y1, 2));
    
    // Calcul de l'angle
    var angle;
    var lx = x2 - x1;
    if (distance > 0)
    {
        lx = lx / distance
        if (y2 > y1)
            angle = Math.asin(lx)
        else
            angle = Math.PI - Math.asin(lx)
    }
    
    // Point central du segment
    var mx = (x2+x1) / 2, my = (y2+y1) / 2;
    
    // Léger décalage de l'angle
    angle += Math.PI / 12;
    
    // Division de la longueur
    distance /= 4;
    
    // Points de la courbe
    var d = "M " + x1 + "," + y1 + " ";
    d += "Q " + (mx + Math.sin(angle - Math.PI) * distance) + "," + (my + Math.cos(angle - Math.PI) * distance) + " ";
    d += mx + "," + my + " ";
    d += "Q " + (mx + Math.sin(angle) * distance) + "," + (my + Math.cos(angle) * distance) + " ";
    d += x2 + "," + y2;
    
    path.setAttribute('d', d);
}

/**
 * Masquer la flèche
 */
TakeATour.prototype.hideArrow = function() {
    this.querySelector("svg.arrow").classList.add("hidden");
}

/**
 * Retourne l'élément actuellement visé
 * @return HTMLElement|false
 */
TakeATour.prototype.getCurrentTarget = function()
{
    var step = this.steps[this.currentStep];
    
    if (step && step.target)
        return this.steps[this.currentStep].target;
    else if (step && step.targetName)
        return document.querySelector('[data-tat-name="' + step.targetName.replace('"', '\\"') + '"]');
    else
        return false;
}
	