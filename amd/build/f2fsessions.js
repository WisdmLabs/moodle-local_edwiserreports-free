define(["jquery","core/modal_factory","core/modal_events","core/fragment","core/templates","report_elucidsitereport/variables"],function(s,e,a,n,o,t){return{init:function(e){var a="#wdm-f2fsessions-individual";s(document).ready(function(){s.ajax({url:t.requestUrl,type:t.requestType,dataType:t.requestDataType,data:{action:"get_f2fsession_data_ajax",sesskey:s(a).data("sesskey")}}).done(function(e){o.render("report_elucidsitereport/f2fsessions",e.data).then(function(e,s){o.replaceNode(a,e,s)}).fail(function(e){console.log(e)}).always(function(){s("#wdm-f2fsessions-individual .table").show(),s("#wdm-f2fsessions-individual .loader").hide()})}).fail(function(e){console.log(e)})})}}});