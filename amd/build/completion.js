define(["jquery","report_elucidsitereport/variables","report_elucidsitereport/common"],function(s,d){return{init:function(t){var r=s("#wdm-completion-individual"),l=r.find(".table"),n=r.find(".loader"),i=null,e=0;function a(t,e){i&&(i.destroy(),l.hide(),n.show());var a={action:"get_completion_data_ajax",sesskey:r.data("sesskey"),data:JSON.stringify({courseid:t,cohortid:e})},o=d.generateUrl(d.requestUrl,a);l.show(),i=l.DataTable({ajax:o,dom:"<'pull-left'f><t><p>",oLanguage:{sEmptyTable:"No users are enrolled as student"},columns:[{data:"username"},{data:"enrolledon"},{data:"enrolltype"},{data:"noofvisits"},{data:"completion"},{data:"compleiontime"},{data:"grade"},{data:"lastaccess"}],columnDefs:[{className:"text-left",targets:0},{className:"text-left",targets:1},{className:"text-center",targets:"_all"}],initComplete:function(){s(n).hide()}})}s(document).ready(function(){var t=d.getUrlParameter("courseid");a(t,e),s(d.cohortFilterItem).on("click",function(){e=s(this).data("cohortid"),s(d.cohortFilterBtn).html(s(this).text()),d.changeExportUrl(e,d.exportUrlLink,d.cohortReplaceFlag),a(t,e)})})}}});