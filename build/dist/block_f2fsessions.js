define(["jquery","core/templates","report_elucidsitereport/defaultconfig"],function(n,a,t){var e=t.getPanel("#f2fsessionsblock"),s=t.getPanel("#f2fsessionsblock","body");return{init:function(){n.ajax({url:t.requestUrl,type:t.requestType,dataType:t.requestDataType,data:{action:"get_f2fsession_data_ajax",sesskey:n(e).data("sesskey"),data:JSON.stringify({})}}).done(function(e){a.render(t.getTemplate("f2fsessiontable"),e.data).then(function(e,t){n(s).empty(),a.appendNodeContents(s,e,t)}).fail(function(e){console.log(e)})}).fail(function(e){console.log(e)})}}});