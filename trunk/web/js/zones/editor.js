/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

$(function() {
  $("a.add").click(function() {
    var records = $("table#records");
    var newRow = records.find("tr:last").clone();
    var rowId = parseInt($("input[name='recordCount']").val());
    
    newRow.find("input").each(function() {
      // clear field values
      $(this).val("");
      
      // give this row a unique name
      $(this).attr("name", $(this).attr("name").replace(/\d+/, rowId));
    });
    
    newRow.find("input.action").val("insert");
    
    newRow.hide();
    records.append(newRow);
    newRow.fadeIn();
    
    initRowEvents();
    updateRowCount();
  });
  
  initRowEvents();
  updateRowCount();
});

updateRowCount = function() {
  // put row count in post values (less 1 to exclude the header row).
  $("input[name='recordCount']").val($("table#records tr").length - 1);
}

initRowEvents = function() {
  $("a.delete").click(function() {
    if ($("table#records tr:visible").length == 2) {
      window.alert("There must be at least 1 record for the zone.");
      return;
    }
    
    if (!window.confirm("Are you sure you want to delete this record?")) {
      return;
    }
    
    var row = $(this).parent().parent();
    row.fadeOut();
    
    // tells server to delete the record.
    row.find("input.action").val("delete");
  });
  
  $("input.aux").each(updateRow);
  $("input.type").keyup(updateRow);
}

updateRow = function() {
  var row = $(this).parent().parent();
  var aux = row.find("input.aux");
  
  // show/hide the aux field depending on type (not sure about this).
  if (row.find("input.type").val().toLowerCase() != "mx") {
    aux.hide();
  }
  else {
    aux.show();
  }
}
