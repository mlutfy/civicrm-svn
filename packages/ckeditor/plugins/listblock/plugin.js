﻿/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.plugins.add('listblock',{requires:['panel'],onLoad:function(){CKEDITOR.ui.panel.prototype.addListBlock=function(a,b){return this.addBlock(a,new CKEDITOR.ui.listBlock(this.getHolderElement(),b));};CKEDITOR.ui.listBlock=CKEDITOR.tools.createClass({base:CKEDITOR.ui.panel.block,$:function(a,b){var d=this;d.base(a);d.multiSelect=!!b;var c=d.keys;c[40]='next';c[9]='next';c[38]='prev';c[CKEDITOR.SHIFT+9]='prev';c[32]='click';d._.pendingHtml=[];d._.items={};d._.groups={};},_:{close:function(){if(this._.started){this._.pendingHtml.push('</ul>');delete this._.started;}},getClick:function(){if(!this._.click)this._.click=CKEDITOR.tools.addFunction(function(a){var c=this;var b=true;if(c.multiSelect)b=c.toggle(a);else c.mark(a);if(c.onClick)c.onClick(a,b);},this);return this._.click;}},proto:{add:function(a,b,c){var f=this;var d=f._.pendingHtml,e='cke_'+CKEDITOR.tools.getNextNumber();if(!f._.started){d.push('<ul class=cke_panel_list>');f._.started=1;}f._.items[a]=e;d.push('<li id=',e,' class=cke_panel_listItem><a _cke_focus=1 hidefocus=true title="',c||a,'" href="javascript:void(\'',a,'\')" onclick="CKEDITOR.tools.callFunction(',f._.getClick(),",'",a,"'); return false;\">",b||a,'</a></li>');},startGroup:function(a){this._.close();var b='cke_'+CKEDITOR.tools.getNextNumber();this._.groups[a]=b;this._.pendingHtml.push('<h1 id=',b,' class=cke_panel_grouptitle>',a,'</h1>');},commit:function(){var a=this;a._.close();a.element.appendHtml(a._.pendingHtml.join(''));a._.pendingHtml=[];},toggle:function(a){var b=this.isMarked(a);if(b)this.unmark(a);else this.mark(a);return!b;},hideGroup:function(a){var b=this.element.getDocument().getById(this._.groups[a]),c=b&&b.getNext();if(b){b.setStyle('display','none');if(c&&c.getName()=='ul')c.setStyle('display','none');}},hideItem:function(a){this.element.getDocument().getById(this._.items[a]).setStyle('display','none');},showAll:function(){var a=this._.items,b=this._.groups,c=this.element.getDocument();for(var d in a)c.getById(a[d]).setStyle('display','');for(var e in b){var f=c.getById(b[e]),g=f.getNext();f.setStyle('display','');if(g&&g.getName()=='ul')g.setStyle('display','');}},mark:function(a){var b=this;if(!b.multiSelect)b.unmarkAll();b.element.getDocument().getById(b._.items[a]).addClass('cke_selected');},unmark:function(a){this.element.getDocument().getById(this._.items[a]).removeClass('cke_selected');},unmarkAll:function(){var a=this._.items,b=this.element.getDocument();for(var c in a)b.getById(a[c]).removeClass('cke_selected');
},isMarked:function(a){return this.element.getDocument().getById(this._.items[a]).hasClass('cke_selected');},focus:function(a){this._.focusIndex=-1;if(a){var b=this.element.getDocument().getById(this._.items[a]).getFirst(),c=this.element.getElementsByTag('a'),d,e=-1;while(d=c.getItem(++e))if(d.equals(b)){this._.focusIndex=e;break;}setTimeout(function(){b.focus();},0);}}}});}});
