define(["jquery","core/modal_factory","core/modal_events","core/fragment","core/templates","report_elucidsitereport/variables","report_elucidsitereport/jquery.dataTables","report_elucidsitereport/dataTables.bootstrap4"],function(c,l,u,p,f,h){return{init:function(s){var o="#wdm-courseprogress-individual",r=o+" .table",a=o+" .loader",i=null,e="#cohortfilter",t=0,n=null;function d(e){n=c(o).data("sesskey"),c.ajax({url:h.requestUrl,data:{action:"get_courseprogress_graph_data_ajax",sesskey:n,data:JSON.stringify({courseid:"all",cohortid:e})}}).done(function(e){var t={courseprogress:e,sesskey:n};f.render("report_elucidsitereport/courseprogress",t).then(function(e,t){f.replaceNode(o,e,t)}).fail(function(e){console.log(e)}).always(function(){i=c(r).DataTable({order:[[0,"desc"]],bLengthChange:!1,pageLength:50,initComplete:function(){c("#wdm-courseprogress-individual .dataTables_wrapper .row:first-child > div:first-child .dropdown").show()},columnDefs:[{targets:0,className:"text-left"},{targets:"_all",className:"text-center"}]}),c(r).show(),c(a).hide()})}).fail(function(e){console.log(e)})}c(document).ready(function(){d(t),c(document).on("click","#cohortfilter ~ .dropdown-menu .dropdown-item",function(){i&&(i.destroy(),c(r).hide(),c(a).show()),t=c(this).data("cohortid"),c(e).html(c(this).text()),d(t)}),c(document).on("click","#wdm-courseprogress-individual .table a",function(){var e=c(this).data("minvalue"),t=c(this).data("maxvalue"),o=c(this).data("courseid"),r=c(this).data("coursename"),a=null;l.create({body:p.loadFragment("report_elucidsitereport","userslist",s,{page:"courseprogress",courseid:o,minval:e,maxval:t})}).then(function(e){a=e.getRoot(),e.setTitle(r),e.show(),a.on(u.hidden,function(){e.destroy()})})})})}}});