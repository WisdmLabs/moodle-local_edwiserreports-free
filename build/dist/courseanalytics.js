define(["jquery","report_elucidsitereport/variables","report_elucidsitereport/jquery.dataTables","report_elucidsitereport/dataTables.bootstrap4","report_elucidsitereport/common"],function(p,h){return{init:function(e){var o=p("#wdm-courseanalytics-individual"),n=o.find(".recent-visits .table"),r=o.find(".recent-enrolment .table"),s=o.find(".recent-completion .table"),i=p(".loader"),l=null,c=null,d=null,t=0,a="#cohortfilter";function u(e,t){var a=o.data("sesskey");p.ajax({url:h.requestUrl,type:h.requestType,dataType:h.requestDataType,data:{action:"get_courseanalytics_data_ajax",sesskey:a,data:JSON.stringify({courseid:e,cohortid:t})}}).done(function(e){l=f(n,l,e.data.recentvisits),c=f(r,c,e.data.recentenrolments),d=f(s,d,e.data.recentcompletions)}).fail(function(e){console.log(e)}).always(function(){p(window).resize(),o.fadeIn("slow")})}function f(e,t,a){var r="No users has Enrolled in this course";return e==s?r="No users has completed this course":e==n&&(r="No users has visited this course"),null!=t&&t.destroy(),i.hide(),e.fadeIn("slow"),o.fadeIn("slow"),e.DataTable({data:a,responsive:!0,oLanguage:{sEmptyTable:r},columnDefs:[{className:"text-left",targets:0},{className:"text-center",targets:"_all"}],order:[[1,"desc"]],scrollY:350,scrollX:!0,paging:!1,bInfo:!1,searching:!1,lengthChange:!1})}p(document).ready(function(){var e=h.getUrlParameter("courseid");p("#cohortfilter ~ .dropdown-menu .dropdown-item").on("click",function(){t=p(this).data("cohortid"),p(a).html(p(this).text()),u(e,t)}),u(e,t)})}}});