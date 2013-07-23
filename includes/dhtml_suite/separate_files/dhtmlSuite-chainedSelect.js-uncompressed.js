if(!window.DHTMLSuite)var DHTMLSuite=new Object();/************************************************************************************************************
*	DHTML chained selects script
*
*	Created:		Jun, 02th, 2007
*	@module coder: Batur Orkun (batur@bilkent.edu.tr, http:
//www.xdhtml.com)
*
*	@class Purpose of class: This script uses Ajax to popuplate a select box based on other select box you choose.
*
*	Css files used by this script:	no css file
*
*	Demos of this class:	demo-chained-selects.html
*
* 	Update log:
*
************************************************************************************************************/

/**
* @constructor
* @class Purpose of class:	Display a tooltip on screen with content from an external file(AJAX)(<a href="../../demos/demo-dyn-tooltip-1.html" target="_blank">Demo</a>)
* @version 0.1
* @author	Alf Magne Kalleland & Batur Orkun 
* (www.dhtmlgoodies.com)
*/

DHTMLSuite.chainedSelect=function(){

	var targetSels;
	var sourceSels;
	var urls;

	var waitMessage;
	var layoutCss;
	var objectIndex;

	this.targetSels=new Array();
	this.sourceSels=new Array();
	this.urls	= new Array();

	try{
	if(!standardObjectsCreated)DHTMLSuite.createStandardObjects();
	
// This line starts all the init methods
	}catch(e){
	alert('You need to include the dhtmlSuite-common.js file');
	}

	this.objectIndex=DHTMLSuite.variableStorage.arrayDSObjects.length;;
	DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex]=this;
}

DHTMLSuite.chainedSelect.prototype={
	
// {{{ init()
	/**
	 *	Initializes the script
	*
	 *
	*@public
	 */
	init:function()
  	{
	var ind=this.objectIndex;
  	for(index=0; index<this.sourceSels.length; index++){
  	sourceObj=document.getElementById(this.sourceSels[index]);
  	eval('sourceObj.onchange=function(){ DHTMLSuite.variableStorage.arrayDSObjects['+ind +'].__callURL('+index+')}'); 
  	
//sourceObj.onchange=function(){ eval(onchangeJS)};
  	}
  	 
	}
	
// }}}
	,

	
// {{{ addChain((sourceSelId, targetSelId, URLToCall)
	/**
	*	add chain
	*
	*	@param String sourceSelId-selected select box object id
	*	@param String targetSelId-target (loading)select box id
	*	@param String URLToCall	- cgi url address to load options
	 *
	*@public
	 */
	addChain:function(sourceSelId, targetSelId, URLToCall){
	var index=this.sourceSels.length;
	this.sourceSels[index]=sourceSelId;
	this.targetSels[index]=targetSelId;
	
	if (URLToCall.indexOf('?')==-1)
		this.urls[index]=URLToCall+'?';
	else
	  	this.urls[index]=URLToCall+'&';
	}
	
// }}}
	,

	
// {{{ __callURL(index)
	/**
	 *	ajax function for call cgi
	 *
	 *	@param number index-chained select box number
	 *
	*@private
	 */
	__callURL:function(index){
	var ind 	= this.objectIndex;
	var sourceObj 	= document.getElementById(this.sourceSels[index]);
	var url 	= this.urls[index];
	
	var ajaxIndex 	= DHTMLSuite.variableStorage.ajaxObjects.length;
	  
	try{
		DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex]=new sack();
	}catch(e){	
// Unable to create ajax object-send alert message and return from sort method.
		alert('Unable to create ajax object. Please make sure that the sack js file is included on your page');
		return;
	}
	
	DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex].requestFile=url+'dg_key='+sourceObj.value;
	DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex].onCompletion=function(){ DHTMLSuite.variableStorage.arrayDSObjects[ind].__loadOptions(index,DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex].response)};
	DHTMLSuite.variableStorage.ajaxObjects[ajaxIndex].runAJAX();	  
// Execute AJAX function 

	}

	
// }}}
	,
	
// {{{ __loadOptions(index,optionsData)
	/**
	 *	add options to target select box 
	 *
	 *	@param number index-loading select box number
	 *	@param string optionsData-seperated data including options 
	 *			(text1;va1 | text2;val2 | text3;val3)
	 *
	*@private
	 */
	__loadOptions:function(index, optionsData)
	{
	var targetObj 	= document.getElementById(this.targetSels[index]);
	targetObj.options.length=0;
	targetObj.options[targetObj.options.length]=new Option('select a item',''); 
	var options_array=optionsData.split("|");
	for(i=0; i<options_array.length;i++){
		var option_array=options_array[i].split(";");
		if(option_array.length>1)targetObj.options[targetObj.options.length]=new Option(option_array[0].trim(), option_array[1].trim());
	}

	}
}
