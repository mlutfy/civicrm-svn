﻿/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function(){function a(h){return h.type==CKEDITOR.NODE_TEXT&&h.getLength()>0;};function b(h){var i=CKEDITOR.dtd;return h.isBlockBoundary(CKEDITOR.tools.extend({},i.$empty,i.$nonEditable));};var c=function(){var h=this;return{textNode:h.textNode,offset:h.offset,character:h.textNode?h.textNode.getText().charAt(h.offset):null,hitMatchBoundary:h._.matchBoundary};},d=['find','replace'],e=[['txtFindFind','txtFindReplace'],['txtFindCaseChk','txtReplaceCaseChk'],['txtFindWordChk','txtReplaceWordChk'],['txtFindCyclic','txtReplaceCyclic']];function f(h){var i,j,k,l;i=h==='find'?1:0;j=1-i;var m,n=e.length;for(m=0;m<n;m++){k=this.getContentElement(d[i],e[m][i]);l=this.getContentElement(d[j],e[m][j]);l.setValue(k.getValue());}};var g=function(h,i){var j=new CKEDITOR.style(h.config.find_highlight),k=function(w,x){var y=new CKEDITOR.dom.walker(w);y[x?'guard':'evaluator']=a;y.breakOnFalse=true;this._={matchWord:x,walker:y,matchBoundary:false};};k.prototype={next:function(){return this.move();},back:function(){return this.move(true);},move:function(w){var y=this;var x=y.textNode;if(x===null)return c.call(y);y._.matchBoundary=false;if(x&&w&&y.offset>0){y.offset--;return c.call(y);}else if(x&&y.offset<x.getLength()-1){y.offset++;return c.call(y);}else{x=null;while(!x){x=y._.walker[w?'previous':'next'].call(y._.walker);if(y._.matchWord&&!x||y._.walker._.end)break;if(!x&&b(y._.walker.current))y._.matchBoundary=true;}y.textNode=x;if(x)y.offset=w?x.getLength()-1:0;else y.offset=0;}return c.call(y);}};var l=function(w,x){this._={walker:w,cursors:[],rangeLength:x,highlightRange:null,isMatched:false};};l.prototype={toDomRange:function(){var w=this._.cursors;if(w.length<1)return null;var x=w[0],y=w[w.length-1],z=new CKEDITOR.dom.range(h.document);z.setStart(x.textNode,x.offset);z.setEnd(y.textNode,y.offset+1);return z;},updateFromDomRange:function(w){var z=this;var x,y=new k(w);z._.cursors=[];do{x=y.next();if(x.character)z._.cursors.push(x);}while(x.character)z._.rangeLength=z._.cursors.length;},setMatched:function(){this._.isMatched=true;},clearMatched:function(){this._.isMatched=false;},isMatched:function(){return this._.isMatched;},highlight:function(){var y=this;if(y._.cursors.length<1)return;if(y._.highlightRange)y.removeHighlight();var w=y.toDomRange();j.applyToRange(w);y._.highlightRange=w;var x=w.startContainer;if(x.type!=CKEDITOR.NODE_ELEMENT)x=x.getParent();x.scrollIntoView();y.updateFromDomRange(w);},removeHighlight:function(){var w=this;if(!w._.highlightRange)return;j.removeFromRange(w._.highlightRange);
w.updateFromDomRange(w._.highlightRange);w._.highlightRange=null;},moveBack:function(){var y=this;var w=y._.walker.back(),x=y._.cursors;if(w.hitMatchBoundary)y._.cursors=x=[];x.unshift(w);if(x.length>y._.rangeLength)x.pop();return w;},moveNext:function(){var y=this;var w=y._.walker.next(),x=y._.cursors;if(w.hitMatchBoundary)y._.cursors=x=[];x.push(w);if(x.length>y._.rangeLength)x.shift();return w;},getEndCharacter:function(){var w=this._.cursors;if(w.length<1)return null;return w[w.length-1].character;},getNextCharacterRange:function(w){var x,y,z=this._.cursors;if(x=z[z.length-1])y=new k(m(x));else y=this._.walker;return new l(y,w);},getCursors:function(){return this._.cursors;}};function m(w,x){var y=new CKEDITOR.dom.range();y.setStart(w.textNode,x?w.offset:w.offset+1);y.setEndAt(h.document.getBody(),CKEDITOR.POSITION_BEFORE_END);return y;};function n(w){var x=new CKEDITOR.dom.range();x.setStartAt(h.document.getBody(),CKEDITOR.POSITION_AFTER_START);x.setEnd(w.textNode,w.offset);return x;};var o=0,p=1,q=2,r=function(w,x){var y=[-1];if(x)w=w.toLowerCase();for(var z=0;z<w.length;z++){y.push(y[z]+1);while(y[z+1]>0&&w.charAt(z)!=w.charAt(y[z+1]-1))y[z+1]=y[y[z+1]-1]+1;}this._={overlap:y,state:0,ignoreCase:!!x,pattern:w};};r.prototype={feedCharacter:function(w){var x=this;if(x._.ignoreCase)w=w.toLowerCase();for(;;){if(w==x._.pattern.charAt(x._.state)){x._.state++;if(x._.state==x._.pattern.length){x._.state=0;return q;}return p;}else if(!x._.state)return o;else x._.state=x._.overlap[x._.state];}return null;},reset:function(){this._.state=0;}};var s=/[.,"'?!;: \u0085\u00a0\u1680\u280e\u2028\u2029\u202f\u205f\u3000]/,t=function(w){if(!w)return true;var x=w.charCodeAt(0);return x>=9&&x<=13||x>=8192&&x<=8202||s.test(w);},u={searchRange:null,matchRange:null,find:function(w,x,y,z,A,B){var K=this;if(!K.matchRange)K.matchRange=new l(new k(K.searchRange),w.length);else{K.matchRange.removeHighlight();K.matchRange=K.matchRange.getNextCharacterRange(w.length);}var C=new r(w,!x),D=o,E='%';while(E!==null){K.matchRange.moveNext();while(E=K.matchRange.getEndCharacter()){D=C.feedCharacter(E);if(D==q)break;if(K.matchRange.moveNext().hitMatchBoundary)C.reset();}if(D==q){if(y){var F=K.matchRange.getCursors(),G=F[F.length-1],H=F[0],I=new k(n(H),true),J=new k(m(G),true);if(!(t(I.back().character)&&t(J.next().character)))continue;}K.matchRange.setMatched();if(A!==false)K.matchRange.highlight();return true;}}K.matchRange.clearMatched();K.matchRange.removeHighlight();if(z&&!B){K.searchRange=v(true);
K.matchRange=null;return arguments.callee.apply(K,Array.prototype.slice.call(arguments).concat([true]));}return false;},replaceCounter:0,replace:function(w,x,y,z,A,B,C){var H=this;var D=false;if(H.matchRange&&H.matchRange.isMatched()&&!H.matchRange._.isReplaced){H.matchRange.removeHighlight();var E=H.matchRange.toDomRange(),F=h.document.createText(y);if(!C){var G=h.getSelection();G.selectRanges([E]);h.fire('saveSnapshot');}E.deleteContents();E.insertNode(F);if(!C){G.selectRanges([E]);h.fire('saveSnapshot');}H.matchRange.updateFromDomRange(E);if(!C)H.matchRange.highlight();H.matchRange._.isReplaced=true;H.replaceCounter++;D=true;}else D=H.find(x,z,A,B,!C);return D;}};function v(w){var x,y=h.getSelection(),z=h.document.getBody();if(y&&!w){x=y.getRanges()[0].clone();x.collapse(true);}else{x=new CKEDITOR.dom.range();x.setStartAt(z,CKEDITOR.POSITION_AFTER_START);}x.setEndAt(z,CKEDITOR.POSITION_BEFORE_END);return x;};return{title:h.lang.findAndReplace.title,resizable:CKEDITOR.DIALOG_RESIZE_NONE,minWidth:350,minHeight:165,buttons:[CKEDITOR.dialog.cancelButton],contents:[{id:'find',label:h.lang.findAndReplace.find,title:h.lang.findAndReplace.find,accessKey:'',elements:[{type:'hbox',widths:['230px','90px'],children:[{type:'text',id:'txtFindFind',label:h.lang.findAndReplace.findWhat,isChanged:false,labelLayout:'horizontal',accessKey:'F'},{type:'button',align:'left',style:'width:100%',label:h.lang.findAndReplace.find,onClick:function(){var w=this.getDialog();if(!u.find(w.getValueOf('find','txtFindFind'),w.getValueOf('find','txtFindCaseChk'),w.getValueOf('find','txtFindWordChk'),w.getValueOf('find','txtFindCyclic')))alert(h.lang.findAndReplace.notFoundMsg);}}]},{type:'vbox',padding:0,children:[{type:'checkbox',id:'txtFindCaseChk',isChanged:false,style:'margin-top:28px',label:h.lang.findAndReplace.matchCase},{type:'checkbox',id:'txtFindWordChk',isChanged:false,label:h.lang.findAndReplace.matchWord},{type:'checkbox',id:'txtFindCyclic',isChanged:false,'default':true,label:h.lang.findAndReplace.matchCyclic}]}]},{id:'replace',label:h.lang.findAndReplace.replace,accessKey:'M',elements:[{type:'hbox',widths:['230px','90px'],children:[{type:'text',id:'txtFindReplace',label:h.lang.findAndReplace.findWhat,isChanged:false,labelLayout:'horizontal',accessKey:'F'},{type:'button',align:'left',style:'width:100%',label:h.lang.findAndReplace.replace,onClick:function(){var w=this.getDialog();if(!u.replace(w,w.getValueOf('replace','txtFindReplace'),w.getValueOf('replace','txtReplace'),w.getValueOf('replace','txtReplaceCaseChk'),w.getValueOf('replace','txtReplaceWordChk'),w.getValueOf('replace','txtReplaceCyclic')))alert(h.lang.findAndReplace.notFoundMsg);
}}]},{type:'hbox',widths:['230px','90px'],children:[{type:'text',id:'txtReplace',label:h.lang.findAndReplace.replaceWith,isChanged:false,labelLayout:'horizontal',accessKey:'R'},{type:'button',align:'left',style:'width:100%',label:h.lang.findAndReplace.replaceAll,isChanged:false,onClick:function(){var w=this.getDialog(),x;u.replaceCounter=0;u.searchRange=v(true);if(u.matchRange){u.matchRange.removeHighlight();u.matchRange=null;}h.fire('saveSnapshot');while(u.replace(w,w.getValueOf('replace','txtFindReplace'),w.getValueOf('replace','txtReplace'),w.getValueOf('replace','txtReplaceCaseChk'),w.getValueOf('replace','txtReplaceWordChk'),false,true)){}if(u.replaceCounter){alert(h.lang.findAndReplace.replaceSuccessMsg.replace(/%1/,u.replaceCounter));h.fire('saveSnapshot');}else alert(h.lang.findAndReplace.notFoundMsg);}}]},{type:'vbox',padding:0,children:[{type:'checkbox',id:'txtReplaceCaseChk',isChanged:false,label:h.lang.findAndReplace.matchCase},{type:'checkbox',id:'txtReplaceWordChk',isChanged:false,label:h.lang.findAndReplace.matchWord},{type:'checkbox',id:'txtReplaceCyclic',isChanged:false,'default':true,label:h.lang.findAndReplace.matchCyclic}]}]}],onLoad:function(){var w=this,x,y,z=false;this.on('hide',function(){z=false;});this.on('show',function(){z=true;});this.selectPage=CKEDITOR.tools.override(this.selectPage,function(A){return function(B){A.call(w,B);var C=w._.tabs[B],D,E,F;E=B==='find'?'txtFindFind':'txtFindReplace';F=B==='find'?'txtFindWordChk':'txtReplaceWordChk';x=w.getContentElement(B,E);y=w.getContentElement(B,F);if(!C.initialized){D=CKEDITOR.document.getById(x._.inputId);C.initialized=true;}if(z)f.call(this,B);};});},onShow:function(){u.searchRange=v();this.selectPage(i);},onHide:function(){if(u.matchRange&&u.matchRange.isMatched()){u.matchRange.removeHighlight();h.focus();h.getSelection().selectRanges([u.matchRange.toDomRange()]);}delete u.matchRange;},onFocus:function(){if(i=='replace')return this.getContentElement('replace','txtFindReplace');else return this.getContentElement('find','txtFindFind');}};};CKEDITOR.dialog.add('find',function(h){return g(h,'find');});CKEDITOR.dialog.add('replace',function(h){return g(h,'replace');});})();
