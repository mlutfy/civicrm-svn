if(!dojo._hasResource["dijit.form._Spinner"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form._Spinner"] = true;
dojo.provide("dijit.form._Spinner");

dojo.require("dijit.form.ValidationTextBox");

dojo.declare(
	"dijit.form._Spinner",
	dijit.form.RangeBoundTextBox,
	{

		// summary: Mixin for validation widgets with a spinner
		// description: This class basically (conceptually) extends dijit.form.ValidationTextBox.
		//	It modifies the template to have up/down arrows, and provides related handling code.

		// defaultTimeout: Number
		//	  number of milliseconds before a held key or button becomes typematic
		defaultTimeout: 500,

		// timeoutChangeRate: Number
		//	  fraction of time used to change the typematic timer between events
		//	  1.0 means that each typematic event fires at defaultTimeout intervals
		//	  < 1.0 means that each typematic event fires at an increasing faster rate
		timeoutChangeRate: 0.90,

		// smallDelta: Number
		//	  adjust the value by this much when spinning using the arrow keys/buttons
		smallDelta: 1,
		// largeDelta: Number
		//	  adjust the value by this much when spinning using the PgUp/Dn keys
		largeDelta: 10,

		templateString:"<div class=\"dijit dijitReset dijitInlineTable dijitLeft\"\n\tid=\"widget_${id}\"\n\tdojoAttachEvent=\"onmouseenter:_onMouse,onmouseleave:_onMouse\" waiRole=\"presentation\"\n\t><div class=\"dijitInputLayoutContainer\"\n\t\t><div class=\"dijitReset dijitValidationIcon\">&nbsp;</div\n\t\t><div class=\"dijitReset dijitValidationIconText\">&Chi;</div\n\t\t><div class=\"dijitReset dijitInputField\"\n\t\t\t><input class='dijitReset' dojoAttachPoint=\"textbox,focusNode\" type=\"${type}\" dojoAttachEvent=\"onfocus,onkeyup,onkeypress:_onKeyPress\"\n\t\t\t\twaiRole=\"spinbutton\" autocomplete=\"off\" name=\"${name}\"\n\t\t/></div\n\t\t><div class=\"dijitReset dijitLeft dijitButtonNode dijitArrowButton dijitUpArrowButton\"\n\t\t\tdojoAttachPoint=\"upArrowNode\"\n\t\t\tdojoAttachEvent=\"onmouseenter:_onMouse,onmouseleave:_onMouse\"\n\t\t\tstateModifier=\"UpArrow\"\n\t\t\t><div class=\"dijitArrowButtonInner\">&nbsp;</div\n\t\t\t><div class=\"dijitArrowButtonChar\">&#9650;</div\n\t\t></div\n\t\t><div class=\"dijitReset dijitLeft dijitButtonNode dijitArrowButton dijitDownArrowButton\"\n\t\t\tdojoAttachPoint=\"downArrowNode\"\n\t\t\tdojoAttachEvent=\"onmouseenter:_onMouse,onmouseleave:_onMouse\"\n\t\t\tstateModifier=\"DownArrow\"\n\t\t\t><div class=\"dijitArrowButtonInner\">&nbsp;</div\n\t\t\t><div class=\"dijitArrowButtonChar\">&#9660;</div\n\t\t></div\n\t></div\n></div>\n",
		baseClass: "dijitSpinner",

		adjust: function(/* Object */ val, /*Number*/ delta){
			// summary: user replaceable function used to adjust a primitive value(Number/Date/...) by the delta amount specified
			// the val is adjusted in a way that makes sense to the object type
			return val;
		},

		_arrowState: function(/*Node*/ node, /*Boolean*/ pressed){
			this._active = pressed;
			this.stateModifier = node.getAttribute("stateModifier") || "";
			this._setStateClass();
		},

		_arrowPressed: function(/*Node*/ nodePressed, /*Number*/ direction){
			if(this.disabled){ return; }
			this._arrowState(nodePressed, true);
			this.setValue(this.adjust(this.getValue(), direction*this.smallDelta), false);
		},

		_arrowReleased: function(/*Node*/ node){
			this._wheelTimer = null;
			if(this.disabled){ return; }
			dijit.focus(this.textbox);
			this._arrowState(node, false);
		},

		_typematicCallback: function(/*Number*/ count, /*DOMNode*/ node, /*Event*/ evt){
			if(node == this.textbox){ node = (evt.keyCode == dojo.keys.UP_ARROW) ? this.upArrowNode : this.downArrowNode; }
			if(count == -1){ this._arrowReleased(node); }
			else{ this._arrowPressed(node, (node == this.upArrowNode) ? 1 : -1); }
		},

		_wheelTimer: null,
		_mouseWheeled: function(/*Event*/ evt){
			dojo.stopEvent(evt);
			var scrollAmount = 0;
			if(typeof evt.wheelDelta == 'number'){ // IE
				scrollAmount = evt.wheelDelta;
			}else if(typeof evt.detail == 'number'){ // Mozilla+Firefox
				scrollAmount = -evt.detail;
			}
			if(scrollAmount > 0){
				var node = this.upArrowNode;
				var dir = +1;
			}else if(scrollAmount < 0){
				var node = this.downArrowNode;
				var dir = -1;
			}else{ return; }
			this._arrowPressed(node, dir);
			if(this._wheelTimer != null){
				clearTimeout(this._wheelTimer);
			}
			var _this = this;
			this._wheelTimer = setTimeout(function(){_this._arrowReleased(node);}, 50);
		},

		postCreate: function(){
			this.inherited('postCreate', arguments);

			// extra listeners
			this.connect(this.textbox, dojo.isIE ? "onmousewheel" : 'DOMMouseScroll', "_mouseWheeled");
			this._connects.push(dijit.typematic.addListener(this.upArrowNode, this.textbox, {keyCode:dojo.keys.UP_ARROW,ctrlKey:false,altKey:false,shiftKey:false}, this, "_typematicCallback", this.timeoutChangeRate, this.defaultTimeout));
			this._connects.push(dijit.typematic.addListener(this.downArrowNode, this.textbox, {keyCode:dojo.keys.DOWN_ARROW,ctrlKey:false,altKey:false,shiftKey:false}, this, "_typematicCallback", this.timeoutChangeRate, this.defaultTimeout));
		}
});

}
