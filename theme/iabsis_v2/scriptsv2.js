/*****************************************************************************
 * @description Collection of method usable by the igestis environnement
 * @version 1.0
 * @author Gilles Hemmerlé (Iabsis) <gilles.h@iabsis.com>
 *****************************************************************************/

/**
 * Activate the autohide buttons in the tables (for big screens)
 */

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
    return {
        "iStart":         oSettings._iDisplayStart,
        "iEnd":           oSettings.fnDisplayEnd(),
        "iLength":        oSettings._iDisplayLength,
        "iTotal":         oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage":          oSettings._iDisplayLength === -1 ?
            0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
        "iTotalPages":    oSettings._iDisplayLength === -1 ?
            0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
    };
};
 
/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function( oSettings, nPaging, fnDraw ) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function ( e ) {
                e.preventDefault();
                if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
                    fnDraw( oSettings );
                }
            };
 
            $(nPaging).addClass('pagination').append(
                '<ul>'+
                    '<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
                    '<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
                '</ul>'
            );
            var els = $('a', nPaging);
            $(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
            $(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
        },
 
        "fnUpdate": function ( oSettings, fnDraw ) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);
 
            if ( oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }
            else if ( oPaging.iPage <= iHalf ) {
                iStart = 1;
                iEnd = iListLength;
            } else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }
 
            for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
                // Remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(:last)').remove();
 
                // Add the new list items and their event handlers
                for ( j=iStart ; j<=iEnd ; j++ ) {
                    sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
                    $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                        .insertBefore( $('li:last', an[i])[0] )
                        .bind('click', function (e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                            fnDraw( oSettings );
                        } );
                }
 
                // Add / remove disabled classes from the static elements
                if ( oPaging.iPage === 0 ) {
                    $('li:first', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                }
 
                if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
} );

$.fn.equals = function(compareTo) {
  if (!compareTo || this.length !== compareTo.length) {
    return false;
  }
  for (var i = 0; i < this.length; ++i) {
    if (this[i] !== compareTo[i]) {
      return false;
    }
  }
  return true;
};

var igestisInitTableHover = function() {
    // Table.
    $('tbody > tr').hover(function(){
    $(this).find('.a-visible-line-on-over').addClass('opacity-1');
    },function(){
    $(this).find('.a-visible-line-on-over').removeClass('opacity-1');
    });
};

$(function() {
    $('.modal').append("<iframe class='iframe-hack-ie'>");
    $('body').on('show', '.modal', function () {
        setTimeout(function() {
            $('.modal-backdrop').append("<iframe class='iframe-hack-ie'>");
        }, 30);        
    });
    /*$('.modal').on('shown', function () {
        $(".modal-backdrop").append("<iframe class='iframe-hack-ie'>");
    });*/
    // Used to customize lists.
	$("select.select2").select2({
         allowClear: true
	});
    $(".taglist").each(function() { $(this).select2({ tags: $(this).data('availableTags'), maximumSelectionSize: $(this).data('maxSelectableTags') }); });
    igestisInitTableHover();
    
});

/**
 * Activate popover message for all concerned fields
 */
/* Disable at it causing too many issues.
var popoverOpened = null;
$(function() { 
    $('span.btn').popover();
    $('span.btn').unbind("click");
    $('span.btn').bind("click", function(e) {
        e.stopPropagation();
        if($(this).equals(popoverOpened)) return;
        if(popoverOpened !== null) {
            popoverOpened.popover("hide");            
        }
        $(this).popover('show');
        popoverOpened = $(this);
        e.preventDefault();
    });
    
    $(document).click(function(e) {
        if(popoverOpened !== null) {
            popoverOpened.popover("hide");   
            popoverOpened = null;
        }        
    });
});
*/

var modalWaitingMessage = function(content) {
    var $modal = $("#igestis-waiting-msg");
    if($modal.length === 0) {
        var $modal = 
            '<div id="igestis-waiting-msg" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
            '<div class="modal-header">' +                  
              '<h3 id="myModalLabel">Please wait ...</h3>' +
            '</div>' +
            '<div class="modal-body">' +
              '<div style="text-align:center"><i class="icon-spinner icon-spin icon-2x"></i><br>' + content + '</div>' +
            '</div>' +
            '</div>';
        $("body").append($modal);
        $modal = $("#igestis-waiting-msg");
    }
    
    $modal.modal('show');
    return $modal;
};

var igestisWizz = function(msg, type, target, preserveWizzs) {
    var wizz_type = "";
    
    switch(type) {
        case "error" :  case "WIZZ_ERROR" :
            wizz_type = "alert-error";
            break;
        case "success" : case "WIZZ_SUCCESS" :
            wizz_type = "alert-success";
            break;
    }
    
    var $msg = $(
          '<div class="alert fade in ' + wizz_type + '">' +
            '<button type="button" class="close" data-dismiss="alert">×</button>' +
             msg +
          '</div>'
    );
    
    if(target !== undefined && target !== null) {
        if(!preserveWizzs) $(target + " > .alert").remove();        
        $(target).prepend($msg);
    }
    else {
        if(!preserveWizzs) $("#general-container > .alert").remove();        
        $("#general-container").prepend($msg);
    }
    
    
};

var igestisParseJsonAjaxResult = function (jsonData) {
    if(!jsonData) {
        igestisWizz("An error occurred during the ajax form validation", "error");
    }
    else if(jsonData.error) {              
        igestisWizz(jsonData.error, "error");
    }

    if(jsonData.redirection) {
        window.location.href= jsonData.redirection;
    }

    if(jsonData.IgestisAjaxWizz !== undefined && $.isArray(jsonData.IgestisAjaxWizz)) {
        for(i = 0; i < jsonData.IgestisAjaxWizz.length; i++) {
            igestisWizz(jsonData.IgestisAjaxWizz[i].label, jsonData.IgestisAjaxWizz[i].type, jsonData.IgestisAjaxWizz[i].target);
        }
    }    

    if(jsonData.IgestisAjaxReplace !== undefined && $.isArray(jsonData.IgestisAjaxReplace)) {
        for(i = 0; i < jsonData.IgestisAjaxReplace.length; i++) {
            var $field = $("#" + jsonData.IgestisAjaxReplace[i].id);
            if($field.is("textarea") || $field.is("input")) {
                $field.val(jsonData.IgestisAjaxReplace[i].value);
            }
            else $field.html(jsonData.IgestisAjaxReplace[i].value);
        }
    }        
    
    if(jsonData.IgestisAjaxResultScript !== undefined && $.isArray(jsonData.IgestisAjaxResultScript)) {
        for(i = 0; i < jsonData.IgestisAjaxResultScript.length; i++) {
            try {
              eval(jsonData.IgestisAjaxResultScript[i]);
            } catch(err) {
              bootbox.alert(err);
            }                  
        }
    }
};

$(function() {
   var id = 0;
   $(".ajax-emulation-validation").each(function() {
      var iframeName = "ajax-emulation-validation-iframe-" + (++id);
      var $iframe = $('<iframe id="' + iframeName + '" name="' + iframeName + '" class="ajax-emulation-validation-iframe"></iframe>');      
      $(this).attr("target", $iframe.attr("id"));
      $(this).append($iframe);
      
      $iframe.on("load", function() { 
          var $modal = $("#igestis-waiting-msg");
          if($modal.length !== 0)  {
              $modal.modal("hide");
          }
          
          // Manage the result 
          var jsonData = $.parseJSON($(this).contents().find("body").text());
          if(jsonData !== null) igestisParseJsonAjaxResult(jsonData);
      });
      
   });
});



/**
 * Translations for Jquery Validator
 */
jQuery.extend(jQuery.validator.messages, {
    required: translations.required,
    remote: translations.remote,
    email: translations.email,
    url: translations.url,
    date: translations.date,
    dateISO: translations.dateISO,
    number: translations.number,
    digits: translations.digits,
    creditcard: translations.creditcard,
    equalTo: translations.equalTo,
    accept: translations.accept,
    maxlength: jQuery.validator.format(translations.maxlength),
    minlength: jQuery.validator.format(translations.minlength),
    rangelength: jQuery.validator.format(translations.rangelength),
    range: jQuery.validator.format(translations.range),
    max: jQuery.validator.format(translations.max),
    min: jQuery.validator.format(translations.min)
});

/**
 * RegEx Method to check a custom format in a form.
 */
$.validator.addMethod(
    "regex",
    function(value, element, regexp) {
        var re = new RegExp(regexp);
        
        return this.optional(element) || re.test(value);
    },
    translations.regex
);

/**
 * RegEx Method to check an existing login.
 */
$.validator.addMethod(
    "LoginNotExists",
    function(value, element, param) {
        var result = null;
        $.ajax({
          type: "GET",
          url: "index.php",
          data: { Page: "core_ajax", Action: "login_exists", Login : value },
          async:false,
          success:function(data) {
              result = data;
          }
        });
        
        
        if(value !== param) {
            result = JSON.parse(result);
        }
        
        return !result.exists;
    },
    translations.loginexists
);


/**
 * Unique function to be sure that the field is not repeated in the same form.
 */
$.validator.addMethod(
    "unique",
    function (value, element) {
    	
    	if (value === "") return true;
        var parentForm = $(element).closest('form');
        var timeRepeated = 0;
        $(parentForm.find('input:text')).each(function () {
            if ($(this).val() === value) {
                timeRepeated++;
            }
        });
        if (timeRepeated === 1 || timeRepeated === 0) {
            return true;
        }
        else { 
            return false;
        }
    },
    translations.mustbeunique
);



/**
 * Manage the Ajax forms population
 */
function IgestisPopulateAjaxForms() {
    this.sUrl = "";
    this.oModal = null;
    this.aForceList = [];
    this.oInitValidatorFunction = null;
    this.callbackFunction = null;

    this.setValidator = function(oFunction) {
        this.oInitValidatorFunction = oFunction;
    };

    this.force = function (object, value) {
        this.aForceList.push(new Array(object, value));
    };
    
    this.setCallback = function(callback) {
        this.callbackFunction = callback;
    };

    this.start = function (url, modal) {
        this.sUrl = url;
        this.oModal = modal;

        $.ajax({
            url: this.sUrl,
            context: this,
            processData:true, //résultat sous forme d'objet
            dataType:'json', //type html
            success: function(result) {
                if (result === null) {
                    bootbox.alert("No data received");   
                }
                else if(result.error) {
                    alert(result.error);
                }
                else if(result.successful) {

                    this.oModal.html(result.successful);
                    this.oModal.modal("show");
                    this.oInitValidatorFunction();
                    if(this.callbackFunction) {
                        this.callbackFunction();
                    }
                    $("select.select2").select2({
                                placeholder: translations.chooseavalue,
                                allowClear: true
                    });
                    $(".taglist").each(function() { $(this).select2({ tags: $(this).data('availableTags') }); });
                    avtiveRadioButtons();
                    activateBootstrapDatepicker();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                bootbox.alert(jqXHR.responseText);   
            }
        });
    };
}


/**
 * Initialise a table with the datatable plugin
 *
 * @param {string} jquerySelector The jquery selector string
 * @param {string} order Which column sort by default
 * @param {string} orderType Which type of order ("asc"|"desc")
 */
function IgestisInitTable(jquerySelector, order, orderType) {

    // Default value for the selector is "#table_data"
    if(jquerySelector === null || jquerySelector === undefined) {
        jquerySelector = "#table_data";
    }
    
    $.extend( $.fn.dataTableExt.oStdClasses, {
        "sWrapper": "dataTables_wrapper form-inline"
    } );
    
    $.extend( $.fn.dataTableExt.oStdClasses, {
        "sSortAsc": "header headerSortDown",
        "sSortDesc": "header headerSortUp",
        "sSortable": "header"
    } );
    
    var oDatatableOptions = {
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span5'i><'span7'p>>",
        "sPaginationType": "bootstrap",
        "aLengthMenu": [[10, 25, 40, -1], [10, 25, 40, translations.all]],
        "oLanguage": {
            "sLengthMenu": translations.recordsperpage,
            "oPaginate": {
                "sFirst": translations.first,
                "sLast": translations.last,
                "sNext": translations.next,
                "sPrevious": translations.previous
            },
            "sEmptyTable": translations.tableempty,
            "sInfo": translations.showingxtoyofzentires,
            "sInfoEmpty": translations.infoempty,
            "sSearch ": translations.search,
            "sZeroRecords ": translations.zerorecords,
            "sInfoFiltered ": " " + translations.infofiltered
        }
    };
    
    // Launch init
    var oTable = $(jquerySelector).dataTable(oDatatableOptions);
    
    if(order !== undefined) {
        if(orderType !== 'asc' && orderType !== 'desc') orderType = 'asc';
        oTable.fnSort( [ [order,orderType] ] ); 
    }

    $(jquerySelector + ' tbody > tr').hover(function(){
        $(this).find('a-visible-line-on-over').addClass('opacity-1');
    },function(){
        $(this).find('a-visible-line-on-over').removeClass('opacity-1');
    });

    igestisInitInputEraser($(jquerySelector).parent().find("input:text, input:password"));
    return oTable;
}



(function($) {
    /**
     * Extend jquery with the possibility to add a cross on each input fields to reset content
     * @param params
     * @return {*}
     */
    $.fn.inputEraser = function(params) {
        params = $.extend( {rightPadding:20}, params);
        var rightPadding = params.rightPadding;

        this.each(function() {
            var $t = $(this);
            var button = null;
            $t.keyup(function() {
                if($t.val() !== "") {
                    if(!$t.hasClass("hasContent")) {
                        var previousRightPadding = $t.css("paddingRight");
                        $t.css("paddingRight", rightPadding);
                        $t.width(parseInt($t.width()) - parseInt(rightPadding) + parseInt(previousRightPadding));
                        $t.addClass("hasContent");

                        button = $('<div><a href="javascript:;"><i class="icon-remove"></i></a></div>');
                        $t.after(button);

                        var objectOffset = $t.offset();

                        button
                            .css("position", "absolute")
                            .css("margin-top", -1 * (parseInt($t.outerHeight() / 2) + parseInt(button.height()/2)))
                            .css("margin-left", parseInt($t.outerWidth()) - parseInt(rightPadding/2) - parseInt(button.width()/2))
                            .css("z-index", 200);

                        button.hide();
                        button.click(function(e) {
                            // Click on the button, remove input field content and launch the event keyup
                            $t.val('').removeClass("hasContent").keyup();
                            // Stop event propagation to avoid a infinite loop
                            e.stopPropagation();
                            // Hide and then remove the delete button
                            $(this).fadeOut(350, function() {
                                $(this).remove();
                                button = null;
                            });
                        });

                        // Add button to page content
                        button.fadeTo(350, 0.5);
                    }
                }
                else {
                    $t.removeClass("hasContent");
                    $t.css("paddingRight", "auto");
                    if(button !== null)  {
                        button.fadeOut(100, function() {
                            $(this).remove(); 
                        });
                    }
                    
                }
            });
            
            $t.keyup();
        });

        // Returning element
        return this;
    };
})(jQuery);


(function($) {
    /**
     * Extend jquery with the possibility to ask before changing page after a form modification
     * Place the "allowToQuit" class to the forms you do not want to manage
     * @param params
     * @return {*}
     */
    $.fn.IgestisConfirmBeforeQuit = function(params) {
        //params = $.extend( {}, params);

        var hasChanged = false;
        this.each(function() {
            var $t = $(this);
            if($t.hasClass("allow-to-quit")) return;

            var changeField = function() {
                hasChanged = true;
            };

            $t.find("input, select, textarea").change(changeField).keyup(changeField);
            $t.submit(function() {
                window.onbeforeunload = null;
            });

            $(window).bind("beforeunload",function(event) {
                if(hasChanged) return "You have unsaved changes";
            });

        });

        // Returning element
        return this;
    };
})(jQuery);

$(function() {
    $("form").IgestisConfirmBeforeQuit();
});



/**
 * Activate the erase cross for all the fields passed on the argument
 * @param oFields Jquery collection ( ie : $("input:text") )
 * @return The jquery collection
 */
function igestisInitInputEraser(oFields) {
    return oFields.inputEraser();
}

/**
 * Replace the modal defaults values
 * @type {Object}
 */
$.fn.modal.defaults = {
    backdrop: "static"
    , keyboard: false
    , show: true
};


$('div[data-toggle=buttons-radio]').each(function() {
    $(this).find("a.btn").click(function() {
       var input = $(this).parents("div.btn-toolbar").find("input");
       if($(this).data("value") === "1") {
           $(this).addClass("active btn-primary");
           $(this).parents("div").find("a.btn").not($(this)).removeClass("active btn-primary");
           $(input[0]).attr("checked", true);
       }
       else {
           $(this).removeClass("active btn-primary");
           $(this).parents("div").find("a.btn").not($(this)).addClass("active btn-primary");
           $(input[0]).attr("checked", false);
       }
    });
});

function activateBootstrapDatepicker() {
    $('div.datepicker').datepicker();
}

function activeRadioButtons() {
    $('div[data-toggle=buttons-radio]').each(function() {
        $(this).find("a.btn").unbind("click");
        $(this).find("a.btn").bind("click", function(e) {            
            e.stopPropagation();
            if($(this).hasClass("disabled")) return;
            
            $(this).parents("div[data-toggle=buttons-radio]").find("a.btn").each(function() {
               $(this).removeClass("btn-success active");
            });
            $(this).addClass("btn-success active");
            //$(this).parents("div.controls").find("input").attr("checked", ($(this).data("value") === 1));
            $(this).parents("div.controls").find("input").val($(this).data("value"));
            $(this).parents("div.controls").find("input").trigger("change");
        });    
    });
};

/**
 * Function to avoid bug in the different module after function name correction
 * @returns {undefined}
 * @deprecated Use the correct function name activeRadioButtons()
 */
function avtiveRadioButtons() {
    activeRadioButtons();
}

$(function() {
    avtiveRadioButtons();
    activateBootstrapDatepicker();
});

// Upload system initialisation
$(function() {
   $('a[data-upload-url]').each(function() {
      var $inputFile = $('<input data-url="' + $(this).data('uploadUrl') + '" type="file" multiple="multiple" name="files[]">').uniqueId().hide();
      var $progressBar = $('<div id="progress" style=" width: 140px; display:none" class="progress progress-striped active">' +
                            '<div class="bar" style="width: 0%;"></div>' + 
                          '</div>');
      $(this).after($inputFile); 
      $(this).before($progressBar);
      $(this).unbind("click").bind("click", function() {
          $("#" + $inputFile.attr('id')).trigger("click");
      });
      
      var callback = $(this).data('uploadCallback');
      
      // Attach the uploader progressbar manager to the file field
      $($inputFile).fileupload({
        dataType: 'json',
        fail: function(e, data) {
        },
        done: function (e, data) {
            var message = $.parseJSON(data.jqXHR.responseText);
            if(message.files) {
                for(i = 0; i < message.files.length; i++) {
                    if(message.files[i].error) {
                        igestisWizz(message.files[i].name + " : " + message.files[i].error, "error", null, true);
                    }
                }
            }
            
            
            if(callback) {
                var splitted = callback.split('.');
                var callbackfunction = null;
                for(i = 0; i < splitted.length; i++) {
                    callbackfunction = callbackfunction ? callbackfunction[splitted[i]] : window[splitted[i]];
                }
                callbackfunction.call(window, e, data);
            }
        },
        start: function(e, data) {
            $progressBar.fadeIn();
        },
        always: function (e, data) {
            $progressBar.fadeOut();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });
   });
});

var igestisLockPage = function(msg) {
    igestisWizz(msg);
    $("#global-page-content").find("input, select, textarea, .btn, .add-on").not(".cancel").attr("disabled", true).addClass("disabled");
    $("#global-page-content").find(".btn, .add-on").unbind("click").bind("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $("#global-page-content").find(".select2").select2("disable");
};