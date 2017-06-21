/**
 * @author Andrei-Robert Rusu
 * @version 1.3
 * @type {{elementInformationMAP: {tagContainer: {element: string, class: string}, tagElement: {element: string, class: string}, tagElementRemove: {element: string, class: string, content: string}}, skeletonStructure: {default: {entryInformationListHTMLSkeleton: string, entryInformationSingleContainerIdentifier: string, entryInformationHTMLSkeleton: string, responseMessageSkeleton: string}}, entryInformationListHTMLSkeleton: string, entryInformationSingleContainerIdentifier: string, entryInformationHTMLSkeleton: string, responseMessageSkeleton: string, triggerInformationMAP: {searchTriggerIdentifier: string, searchTriggerEvent: string, searchTriggerMinimumLength: number, searchTriggerCSSSettings: {width: string}}, namespace: string, containerObject: {}, tagContainerObject: {}, requestURL: string, requestMethod: string, requestSearchedTerm: string, requestSelectedTerms: string, requestExtraParams: {}, closeModalOnSelect: number, clearInputOnSelect: number, maxTags: boolean, defaultValues: Array, tagInputType: string, tagInputName: string, closeOnUnFocus: number, skeleton: string, _currentAJAXRequestObject: boolean, Init: Init, _handleSettings: _handleSettings, _handleDefaultValues: _handleDefaultValues, prependTagContainer: prependTagContainer, assignFilterTriggers: assignFilterTriggers, fetchFilteredResult: fetchFilteredResult, buildTagListDisplay: buildTagListDisplay, buildTagDisplay: buildTagDisplay, addTag: addTag, removeTag: removeTag, lockFormSubmit: lockFormSubmit, unlockFormSubmit: unlockFormSubmit, setWindowResizeEvent: setWindowResizeEvent, ModalHelper: {Controller: {}, container: boolean, displayUnder: number, currentElements: boolean, modalIDPrefix: string, modalID: string, Init: Init, DisplayMessage: DisplayMessage, Display: Display, setContainer: setContainer, arrangeContainer: arrangeContainer, Close: Close}, KeyNavigationHelper: {Controller: {}, Init: Init, keyUp: keyUp, keyDown: keyDown, scrollToElement: scrollToElement, getCurrentPointedElementAndHandleUniversal: getCurrentPointedElementAndHandleUniversal, getCurrentPointedElement: getCurrentPointedElement}}}
 */
var jQueryMTSelect={elementInformationMAP:{tagContainer:{element:"span","class":"mt-tag-container"},tagElement:{element:"span","class":"mt-tag-element"},tagElementRemove:{element:"a","class":"none",content:"X"}},skeletonStructure:{"default":{entryInformationListHTMLSkeleton:'<div class="mt_search_list_container">'+"{entries_information_list}"+"</div>",entryInformationSingleContainerIdentifier:".mt_entry_container",entryInformationHTMLSkeleton:'<div class="mt_entry_container addTag" data-tag-id="{id}" data-tag-name="{name}">'+'<div class="left">'+'<img src="{picture_path}"/>'+"</div>"+'<div class="right">'+'<p class="name">{name|boldSearch}</p>'+'<p class="description">{description|boldSearch}</p>'+"</div>"+"</div>",responseMessageSkeleton:'<div class="mt_search_message">{message}</div>'}},entryInformationListHTMLSkeleton:"",entryInformationSingleContainerIdentifier:".",entryInformationHTMLSkeleton:"",responseMessageSkeleton:"",triggerInformationMAP:{searchTriggerIdentifier:":input[data-mt-filter-control]",searchTriggerEvent:"keyup focus",searchTriggerMinimumLength:3,searchTriggerCSSSettings:{width:"auto"}},namespace:"mt_search",containerObject:{},tagContainerObject:{},requestURL:"",requestMethod:"POST",requestSearchedTerm:"mt_filter",requestSelectedTerms:"mt_selected",requestExtraParams:{},closeModalOnSelect:1,clearInputOnSelect:1,maxTags:false,defaultValues:[],tagInputType:"hidden",tagInputName:"tag",closeOnUnFocus:1,skeleton:"default",_currentAJAXRequestObject:false,Init:function(e,t){this._handleSettings(t);this.containerObject=e;this.ModalHelper=this.ModalHelper.Init(this);this.KeyNavigationHelper=this.KeyNavigationHelper.Init(this);this.prependTagContainer();this.assignFilterTriggers();this._handleDefaultValues();this.setWindowResizeEvent()},_handleSettings:function(e){var t=this;this.requestURL=typeof e.request_url!="undefined"?e.request_url:"";this.requestMethod=typeof e.request_method!="undefined"?e.request_method:this.requestMethod;this.requestSearchedTerm=typeof e.request_tag_name!="undefined"?e.request_tag_name:this.requestSearchedTerm;this.requestSelectedTerms=typeof e.request_selected_tags_name!="undefined"?e.request_selected_tags_name:this.requestSelectedTerms;this.closeModalOnSelect=typeof e.close_on_select!="undefined"?parseInt(e.close_on_select):this.closeModalOnSelect;this.closeOnUnFocus=typeof e.close_on_unfocus!="undefined"?parseInt(e.close_on_unfocus):this.closeOnUnFocus;this.clearInputOnSelect=typeof e.clear_on_select!="undefined"?parseInt(e.clear_on_select):this.clearInputOnSelect;this.tagInputName=typeof e.tag_input_name!="undefined"?e.tag_input_name:this.tagInputName;this.tagInputType=typeof e.tag_input_type!="undefined"?e.tag_input_type:this.tagInputType;this.maxTags=typeof e.max_tags!="undefined"?e.max_tags:this.maxTags;this.skeleton=typeof e.skeleton!="undefined"?e.skeleton:this.skeleton;this.namespace=typeof e.namespace!="undefined"?e.namespace:this.namespace;if(typeof e.default_values!="undefined")this.defaultValues=typeof e.default_values=="string"?jQuery.parseJSON(e.default_values):e.default_values;jQuery.each(e,function(e,n){if(e.indexOf("custom_param_")==0){var r=e.replace("custom_param_","");t.requestExtraParams[r]=n}});jQuery.each(this.skeletonStructure[this.skeleton],function(e,n){t[e]=n})},_handleDefaultValues:function(){if(this.defaultValues!=false){var e=this;jQuery.each(this.defaultValues,function(t,n){e.addTag(t,n)})}},prependTagContainer:function(){this.containerObject.prepend("<"+this.elementInformationMAP.tagContainer.element+" "+'class="'+this.elementInformationMAP.tagContainer.class+'">'+"</"+this.elementInformationMAP.tagContainer.element+">");this.tagContainerObject=this.containerObject.find("> "+this.elementInformationMAP.tagContainer.element+"."+this.elementInformationMAP.tagContainer.class.replace(" ","."))},assignFilterTriggers:function(){var e=this,t=this.containerObject.find(this.triggerInformationMAP.searchTriggerIdentifier);jQuery.each(this.triggerInformationMAP.searchTriggerCSSSettings,function(e,n){t.css(e,n)});t.attr("autocomplete","off");t.val("");t.bind(this.triggerInformationMAP.searchTriggerEvent+"."+this.namespace,function(n){if(jQuery(this).val().length>=e.triggerInformationMAP.searchTriggerMinimumLength){if(n.which==38){e.KeyNavigationHelper.keyUp()}else if(n.which==40){e.KeyNavigationHelper.keyDown()}else if(n.which==13){if(e.KeyNavigationHelper.getCurrentPointedElement()!=false)e.KeyNavigationHelper.getCurrentPointedElement().click();return false}else if(n.which==27){t.val("");e.ModalHelper.Close()}else{e.fetchFilteredResult(jQuery(this).val())}}else{e.ModalHelper.Close()}});if(this.closeOnUnFocus)t.focusout(function(){setTimeout(function(){e.ModalHelper.Close()},500)})},fetchFilteredResult:function(e){var t=this;if(this._currentAJAXRequestObject!=false)this._currentAJAXRequestObject.abort();var n=[];this.tagContainerObject.find('input[type="'+this.tagInputType+'"][data-tag-id]').each(function(){n[n.length]=jQuery(this).val()});var r=this.requestExtraParams;r[this.requestSearchedTerm]=e;r[this.requestSelectedTerms]=n;this._currentAJAXRequestObject=jQuery.ajax({type:this.requestMethod,url:this.requestURL,context:document.body,dataType:"json",data:r}).done(function(n){t.ModalHelper.Close();if(n.status=="empty"){if(typeof n.message!=="undefined")t.ModalHelper.DisplayMessage(t.responseMessageSkeleton.replace("{message}",n.message),t.containerObject.find(t.triggerInformationMAP.searchTriggerIdentifier).filter(":first"));return}var r=t.buildTagListDisplay(n.results,e),i=t.containerObject.find(t.triggerInformationMAP.searchTriggerIdentifier).filter(":first");t.ModalHelper.Display(r,i);t._currentAJAXRequestObject=false})},buildTagListDisplay:function(e,t){var n="",r=this;jQuery.each(e,function(e,i){n+=r.buildTagDisplay(i,t)});return this.entryInformationListHTMLSkeleton.replace("{entries_information_list}",n)},buildTagDisplay:function(e,t){var n=this.entryInformationHTMLSkeleton;jQuery.each(e,function(e,r){n=n.replace("{"+e+"|boldSearch}",r.replace(t,"<strong>"+t+"</strong>"));n=n.replace(new RegExp("{"+e+"}","g"),r)});return n},addTag:function(e,t){if(this.tagContainerObject.find('[data-tag-id="'+e+'"]').length>0)return;var n=this;this.tagContainerObject.append('<input type="'+this.tagInputType+'" '+'name="'+this.tagInputName+"["+(this.tagContainerObject.find("[data-tag-id]").length>0?parseInt(this.tagContainerObject.find("[data-tag-id]:last").attr("data-tag-id"),10)+1:1)+']" '+'value="'+e+'"'+'data-tag-id="'+e+'"'+"/>");this.tagContainerObject.append("<"+this.elementInformationMAP.tagElement.element+" "+'class="'+this.elementInformationMAP.tagElement.class+'" '+'data-tag-id="'+e+'" '+">"+t+("<"+this.elementInformationMAP.tagElementRemove.element+" "+'class="'+this.elementInformationMAP.tagElement.class+'" '+'data-tag-remove-id="'+e+'" '+">"+this.elementInformationMAP.tagElementRemove.content+"</"+this.elementInformationMAP.tagElementRemove.element+">")+"</"+this.elementInformationMAP.tagElement.element+">");this.tagContainerObject.find('[data-tag-remove-id="'+e+'"]').bind("click."+this.namespace,function(){n.removeTag(jQuery(this).attr("data-tag-remove-id"))});if(this.tagContainerObject.find('input[type="'+this.tagInputType+'"][data-tag-id]').length>=this.maxTags)this.containerObject.find(this.triggerInformationMAP.searchTriggerIdentifier).fadeOut("slow")},removeTag:function(e){var t=this;this.tagContainerObject.find('[data-tag-id="'+e+'"]').fadeOut("fast",function(){jQuery(this).unbind(t.namespace);jQuery(this).remove()});this.containerObject.find(this.triggerInformationMAP.searchTriggerIdentifier+":hidden").fadeIn("slow",function(){jQuery(this).focus()})},lockFormSubmit:function(){if(this.containerObject.is("form"))this.containerObject.attr("onkeypress","return event.keyCode != 13");else this.containerObject.parents("form:first").attr("onkeypress","return event.keyCode != 13")},unlockFormSubmit:function(){if(this.containerObject.is("form"))this.containerObject.attr("onkeypress","");else this.containerObject.parents("form:first").attr("onkeypress","")},setWindowResizeEvent:function(){var e=this;jQuery(window).bind("resize orientationchange",function(){e.ModalHelper.arrangeContainer()})},ModalHelper:{Controller:{},container:false,displayUnder:0,currentElements:false,modalIDPrefix:"jquery-mt-select-",modalID:"",Init:function(e){this.Controller=e;this.modalID=this.modalIDPrefix+this.Controller.namespace;return jQuery.extend(1,{},this)},DisplayMessage:function(e,t){var n=this;this.displayUnder=t;this.setContainer(e);this.arrangeContainer();this.Controller.lockFormSubmit()},Display:function(e,t){var n=this;this.displayUnder=t;this.setContainer(e);this.arrangeContainer();this.Controller.lockFormSubmit();this.container.find(".addTag").unbind("click").bind("click."+this.Controller.namespace,function(){n.Controller.addTag(jQuery(this).attr("data-tag-id"),jQuery(this).attr("data-tag-name"));if(n.Controller.clearInputOnSelect==1){var e=n.Controller.containerObject.find(n.Controller.triggerInformationMAP.searchTriggerIdentifier);e.val("");e.first().focus()}if(n.Controller.closeModalOnSelect==1){n.Close()}else{jQuery(this).remove();n.arrangeContainer()}})},setContainer:function(e){jQuery("body").append('<div id="'+this.modalID+'" class="modal-helper">'+e+"</div>");this.container=jQuery("#"+this.modalID);this.currentElements=this.container.find(this.Controller.entryInformationSingleContainerIdentifier)},arrangeContainer:function(){if(this.container==false)return;this.container.css("position","absolute");this.container.css("top",this.displayUnder.offset().top+this.displayUnder.height()+parseInt(this.displayUnder.css("padding-top"),10)+parseInt(this.displayUnder.css("padding-bottom"),10));this.container.css("left",this.displayUnder.offset().left)},Close:function(){if(this.container!=false){this.currentElements=false;this.Controller.unlockFormSubmit();this.container.find(".addTag").unbind("click."+this.Controller.namespace);this.container.remove()}this.container=false}},KeyNavigationHelper:{Controller:{},Init:function(e){this.Controller=e;return jQuery.extend(1,{},this)},keyUp:function(){if(this.Controller.ModalHelper.currentElements==false)return;var e=this.getCurrentPointedElementAndHandleUniversal();e=typeof e=="undefined"||e==false||e.prev().length==0?this.Controller.ModalHelper.currentElements.filter(":last"):e.prev();e.removeClass("inactive").addClass("active");this.scrollToElement(e)},keyDown:function(){if(this.Controller.ModalHelper.currentElements==false)return;var e=this.getCurrentPointedElementAndHandleUniversal();e=typeof e=="undefined"||e==false||e.next().length==0?this.Controller.ModalHelper.currentElements.filter(":first"):e.next();e.removeClass("inactive").addClass("active");this.scrollToElement(e)},scrollToElement:function(e){this.Controller.ModalHelper.container.find("> *:first").animate({scrollTop:e.position().top},200)},getCurrentPointedElementAndHandleUniversal:function(){var e=this.getCurrentPointedElement(),t=this;this.Controller.ModalHelper.currentElements.removeClass("active").addClass("inactive");this.Controller.ModalHelper.currentElements.unbind("hover").bind("hover",function(){t.Controller.ModalHelper.currentElements.removeClass("active inactive")});return e},getCurrentPointedElement:function(){var e=this.Controller.ModalHelper.currentElements;return e.filter(".active").length>0?e.filter(".active:first"):false}}};jQuery(document).ready(function(){var e={},t=0;jQuery(".component-mt-select").each(function(){if(jQuery(this).hasClass("dispatched"))return;jQuery(this).addClass("dispatched");e[t]=jQuery.extend(1,{},jQueryMTSelect);var n={},r={};jQuery.each(jQuery(this)[0].attributes,function(e,t){n[t.name]=t.value});jQuery.each(n,function(e,t){if(e.indexOf("data-mt-")==0){var n=e.replace("data-mt-","");n=n.replace(/-/g,"_");r[n]=t}});r.namespace="mt_select_"+t;e[t].Init(jQuery(this),r);t++})})