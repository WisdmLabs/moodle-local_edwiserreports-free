define(["jquery","report_elucidsitereport/variables","report_elucidsitereport/jquery.dataTables","report_elucidsitereport/dataTables.bootstrap4"],function(n,d){return{init:function(e){var r=n("#wdm-completion-individual"),i=r.find(".table"),l=r.find(".loader"),s=null,t=0,a="#cohortfilter";function o(e,t){s&&(s.destroy(),i.hide(),l.show());var a={action:"get_completion_data_ajax",sesskey:r.data("sesskey"),data:JSON.stringify({courseid:e,cohortid:t})},o=d.generateUrl(d.requestUrl,a);i.show(),s=i.DataTable({ajax:o,dom:"<'pull-left'f><t><p>",oLanguage:{sEmptyTable:"No users are enrolled as student"},columns:[{data:"username"},{data:"enrolledon"},{data:"enrolltype"},{data:"noofvisits"},{data:"completion"},{data:"compleiontime"},{data:"grade"},{data:"lastaccess"}],columnDefs:[{className:"text-left",targets:0},{className:"text-left",targets:1},{className:"text-center",targets:"_all"}],initComplete:function(){n(l).hide()}})}n(document).ready(function(){var e=d.getUrlParameter("courseid");o(e,t),n("#cohortfilter ~ .dropdown-menu .dropdown-item").on("click",function(){t=n(this).data("cohortid"),n(a).html(n(this).text()),o(e,t)})})}}});