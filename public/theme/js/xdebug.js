/* 
 * Manage the xdebug module for the igestis debugin (server must have the xdebug installed
 */

function xdebugShowPannel(debugSectionToShow) {
    //xdebugHidePannel();
    $("#debug-pannel").slideUp(200, function() {
        $("#debug-pannel > div").hide();
        debugSectionToShow.show();
        $(this).slideDown(300);
    });
}

function xdebugHidePannel() {
    $("#debug-pannel > div").hide();
    $("#debug-pannel").slideUp(200);
}

function xdebugShowErrors() {
    xdebugShowPannel($("#debug-errors-list"));
    //$("#debug-errors-list").hide().show(100);
}

function xdebugShowLogs() {
    xdebugShowPannel($("#debug-logs-list"));
    //$("#debug-logs-list").hide().show(100);
}

function xdebugShowAll() {
    xdebugShowPannel($("#debug-all-list"));
    //$("#debug-all-list").hide().show(100);
}

function xdebugShowDumps() {
    xdebugShowPannel($("#debug-dumps-list"));
    //$("#debug-dumps-list").hide().show(100);
}

function xdebugShowOldDumps() {
    xdebugShowPannel($("#debug-old-dumps-list"));
    //$("#debug-dumps-list").hide().show(100);
}

function xdebugShowDoctrine() {
    xdebugShowPannel($("#debug-doctrine-list"));
    //$("#debug-dumps-list").hide().show(100);
}



$(function() {
   
});
