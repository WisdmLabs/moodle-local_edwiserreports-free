define(["jquery","report_elucidsitereport/variables","report_elucidsitereport/jquery.dataTables","report_elucidsitereport/dataTables.bootstrap4"],function(l,d){return{init:function(e){var s=l("#wdm-courseanalytics-individual"),r=s.find(".table"),i=s.find(".loader"),o=d.requestUrl+"?action=get_courseanalytics_data_ajax";l(document).ready(function(){var e=s.data("sesskey"),a=d.getUrlParameter("courseid"),t=JSON.stringify({courseid:a});o+="&sesskey="+e,o+="&data="+t,l(r).show(),r.DataTable({ajax:o,oLanguage:{sEmptyTable:"No users are enrolled as student"},columns:[{data:"username"},{data:"useremail"},{data:"visitscount"},{data:"lastvists"},{data:"enrolledon"},{data:"completion"}],columnDefs:[{className:"text-left",targets:0},{className:"text-left",targets:1},{className:"text-center",targets:"_all"}],initComplete:function(){l(i).hide()}})})}}});