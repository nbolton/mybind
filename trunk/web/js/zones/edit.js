/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

$(function() {
  
  $("a#add").click(function() {
    var records = $("table#records");
    var newRow = records.find("tr:last").clone();
    newRow.find("input").val("");
    records.append(newRow);
  });
  
  $("a.delete").click(function() {
    if (!window.confirm("Are you sure you want to delete this record?")) {
      return;
    }
    var row = $(this).parent().parent();
    row.find("input.delete").val("true");
    row.fadeOut();
  });
  
  $("input.aux").each(updateRow);
  $("input.type").keyup(updateRow);
  
});

updateRow = function() {
  var row = $(this).parent().parent();
  var aux = row.find("input.aux");
  if (row.find("input.type").val().toLowerCase() != "mx") {
    aux.hide();
  }
  else {
    aux.show();
  }
}