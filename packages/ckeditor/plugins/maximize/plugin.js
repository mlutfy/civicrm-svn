﻿/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function(){function a(f){if(!f||f.type!=CKEDITOR.NODE_ELEMENT||f.getName()!='form')return[];var g=[],h=['style','className'];for(var i=0;i<h.length;i++){var j=h[i],k=f.$.elements.namedItem(j);if(k){var l=new CKEDITOR.dom.element(k);g.push([l,l.nextSibling]);l.remove();}}return g;};function b(f,g){if(!f||f.type!=CKEDITOR.NODE_ELEMENT||f.getName()!='form')return;if(g.length>0)for(var h=g.length-1;h>=0;h--){var i=g[h][0],j=g[h][1];if(j)i.insertBefore(j);else i.appendTo(f);}};function c(f,g){var h=a(f),i={},j=f.$;if(!g){i['class']=j.className||'';j.className='';}i.inline=j.style.cssText||'';if(!g)j.style.cssText='position: static; overflow: visible';b(h);return i;};function d(f,g){var h=a(f),i=f.$;if('class' in g)i.className=g['class'];if('inline' in g)i.style.cssText=g.inline;b(h);};function e(f,g){return function(){var h=f.getViewPaneSize();g.resize(h.width,h.height,null,true);};};CKEDITOR.plugins.add('maximize',{init:function(f){var g=f.lang,h=CKEDITOR.document,i=h.getWindow(),j,k,l,m=e(i,f),n=CKEDITOR.TRISTATE_OFF;f.addCommand('maximize',{modes:{wysiwyg:1,source:1},exec:function(){var x=this;var o=f.container.getChild([0,0]),p=f.getThemeSpace('contents');if(f.mode=='wysiwyg'){j=f.getSelection().getRanges();k=i.getScrollPosition();}else{var q=f.textarea.$;j=!CKEDITOR.env.ie&&[q.selectionStart,q.selectionEnd];k=[q.scrollLeft,q.scrollTop];}if(x.state==CKEDITOR.TRISTATE_OFF){i.on('resize',m);l=i.getScrollPosition();var r=f.container;while(r=r.getParent()){r.setCustomData('maximize_saved_styles',c(r));r.setStyle('z-index',f.config.baseFloatZIndex-1);}p.setCustomData('maximize_saved_styles',c(p,true));o.setCustomData('maximize_saved_styles',c(o,true));if(CKEDITOR.env.ie)h.$.documentElement.style.overflow=h.getBody().$.style.overflow='hidden';else h.getBody().setStyles({overflow:'hidden',width:'0px',height:'0px'});i.$.scrollTo(0,0);var s=i.getViewPaneSize();o.setStyle('position','absolute');o.$.offsetLeft;o.setStyles({'z-index':f.config.baseFloatZIndex-1,left:'0px',top:'0px'});f.resize(s.width,s.height,null,true);var t=o.getDocumentPosition();o.setStyles({left:-1*t.x+'px',top:-1*t.y+'px'});o.addClass('cke_maximized');}else if(x.state==CKEDITOR.TRISTATE_ON){i.removeListener('resize',m);var u=[p,o];for(var v=0;v<u.length;v++){d(u[v],u[v].getCustomData('maximize_saved_styles'));u[v].removeCustomData('maximize_saved_styles');}r=f.container;while(r=r.getParent()){d(r,r.getCustomData('maximize_saved_styles'));r.removeCustomData('maximize_saved_styles');}i.$.scrollTo(l.x,l.y);
o.removeClass('cke_maximized');f.fire('resize');}x.toggleState();if(f.mode=='wysiwyg'){f.getSelection().selectRanges(j);var w=f.getSelection().getStartElement();if(w)w.scrollIntoView(true);else i.$.scrollTo(k.x,k.y);}else{if(j){q.selectionStart=j[0];q.selectionEnd=j[1];}q.scrollLeft=k[0];q.scrollTop=k[1];}j=k=null;n=x.state;},canUndo:false});f.ui.addButton('Maximize',{label:g.maximize,command:'maximize'});f.on('mode',function(){f.getCommand('maximize').setState(n);},null,null,100);}});})();
