define(["jquery","core/notification","core/fragment","core/modal_factory","core/modal_events","core/templates","report_elucidsitereport/variables","report_elucidsitereport/select2","report_elucidsitereport/jquery.dataTables","report_elucidsitereport/dataTables.bootstrap4"],function(i,o,a,n,r,d,l){var s=null,e="#scheduletab",c="button.dropdown-toggle",u=e+" input#esr-sendduration",t=e+" .dropdown:not(.duration-dropdown)",f=t+" button.dropdown-toggle",m=e+" .dropdown.daily-dropdown button.dropdown-toggle",p=e+" .dropdown.weekly-dropdown button.dropdown-toggle",h=e+" .dropdown.monthly-dropdown button.dropdown-toggle",g=e+" input#esr-sendtime",b='<div class="w-full text-center"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>',v='<div class="alert alert-danger"><b>ERROR:</b> Error while scheduling email<div>',y='<div class="alert alert-success"><b>Success:</b> Email scheduled successfully<div>',w='<div class="alert alert-danger"><b>ERROR:</b> Name and Recepient Fields can not be empty<div>',k='<div class="alert alert-danger"><b>ERROR:</b> Invalid email adderesses space not allowed<div>',_='[data-plugin="tabs"] .nav-link, [data-plugin="tabs"] .tab-pane',x='[aria-controls="scheduletab"], #scheduletab';function R(e,a){var t=a.getRoot().find("#esr-shceduled-emails");return i(document).on("click",'[aria-controls="listemailstab"]',function(){i(window).resize()}),t.DataTable({ajax:{url:l.requestUrl,type:l.requestType,data:{action:"get_scheduled_emails_ajax",sesskey:i(e).data("sesskey"),data:JSON.stringify({blockname:i(e).attr("data-blockname"),href:i(e).attr("href"),region:i(e).attr("data-region")})}},scrollY:"300px",scrollCollapse:!0,oLanguage:{sEmptyTable:"There is no scheduled emails"},order:[[1,"asc"]],columns:[{data:"esrtoggle",orderable:!1},{data:"esrname",orderable:!0},{data:"esrcomponent",orderable:!0},{data:"esrnextrun",orderable:!0},{data:"esrfrequency",orderable:!0},{data:"esrmanage",orderable:!1}],responsive:!0,bInfo:!1,lengthChange:!1,paging:!1})}function S(e){var a="#wdm-elucidsitereport > div";e<780?i(a).addClass("col-lg-12"):i(a).removeClass("col-lg-12"),i(document).find(".table.dataTable").DataTable().draw()}i(document).ready(function(){S(i("#page-admin-report-elucidsitereport-index .page-content").width()),i(window).on("resize",function(){S(l.pluginPage.width())}),i(document).on("click",'.export-dropdown a[data-action="email"]',function(e){e.preventDefault();var t=this;n.create({type:n.types.SAVE_CANCEL,title:"Email Dialog Box",body:a.loadFragment("report_elucidsitereport","email_dialog",i(t).data("contextid"),{blockname:i(t).data("blockname")})},i(this)).done(function(e){var a=e.getRoot();a.on(r.hidden,function(){e.destroy()}),a.on(r.save,function(){!function(e,a){i.ajax({url:e.href,type:"POST",data:a.find("form").serialize()}).done(function(e){(e=i.parseJSON(e)).error?o.addNotification({message:e.errormsg,type:"error"}):o.addNotification({message:"Email has been sent",type:"info"})}).fail(function(){o.addNotification({message:"Failed to send the email",type:"error"})})}(t,a)}),e.setSaveButtonText("Send"),e.show()})}),i(document).on("click",'.export-dropdown a[data-action="emailscheduled"]',function(e){e.preventDefault();var t=this,a=l.getScheduledEmailFormContext();n.create({title:"Schedule Emails",body:d.render("report_elucidsitereport/email_schedule_tabs",a)},i(this)).done(function(e){var a=e.getRoot();e.modal.addClass("modal-lg"),a.on(r.bodyRendered,function(){a.find("#esr-blockname").val(i(t).data("blockname")),a.find("#esr-region").val(i(t).data("region")),s=R(t,e)}),a.on(r.hidden,function(){e.destroy()}),function(e,a,t){a.on("click","#scheduletab .dropdown a.dropdown-item",function(){!function(e){var a=i(e).data("value"),t=i(e).text(),o=i(e).closest(".dropdown").find(c);o.text(t),o.data("value",a)}(this)}),a.on("click","#scheduletab .dropdown.duration-dropdown a.dropdown-item",function(){!function(e,a){var t=i(e).data("value");i(e).text();a.find(u).val(t),i(f).hide();var o=null;switch(t){case 1:o=i(p);break;case 2:o=i(h);break;default:o=i(m)}o.show();var n=o.data("value");i(g).val(n)}(this,a)}),a.on("click","#scheduletab .dropdown:not(.duration-dropdown) a.dropdown-item",function(){a.find(g).val(i(this).data("value"))}),a.on("click","#listemailstab .esr-email-sched-setting",function(){!function(e,a){var t=i(e).data("id"),o=i(e).data("blockname"),n=i(e).data("region");i.ajax({url:l.requestUrl,type:l.requestType,sesskey:i(e).data("sesskey"),data:{action:"get_scheduled_email_detail_ajax",sesskey:i(e).data("sesskey"),data:JSON.stringify({id:t,blockname:o,region:n})}}).done(function(e){e.error?console.log(e):(function(e,a,n){var r=null,d=null;i.each(e.data,function(e,a){if("object"==typeof a)n.find("#esr-blockname").val(a.blockname),n.find("#esr-region").val(a.region);else if("esrduration"===e){var t='[aria-labelledby="durationcount"] .dropdown-item[data-value="'+a+'"]';r=a,n.find(t).click()}else if("esrtime"===e)d=a;else if("esremailenable"===e){var o=i('input[name="'+e+'"]');a?o.prop("checked",!0):o.prop("checked",!1)}else i('[name="'+e+'"]').val(a)});var t='.dropdown-item[data-value="'+d+'"]',o=null;switch(r){case"1":o=i(".weekly-dropdown");break;case"2":o=i(".monthly-dropdown");break;default:o=i(".daily-dropdown")}o.find(t).click()}(e,0,a),a.find(_).removeClass("active show"),a.find(x).addClass("active show"))}).fail(function(e){console.log(e)})}(this,a)}),a.on("click","#listemailstab .esr-email-sched-delete",function(){!function(a,e,t){var o=i(a).data("id"),n=i(a).data("blockname"),r=i(a).data("region");i.ajax({url:l.requestUrl,type:l.requestType,sesskey:i(a).data("sesskey"),data:{action:"delete_scheduled_email_ajax",sesskey:i(a).data("sesskey"),data:JSON.stringify({id:o,blockname:n,region:r})}}).done(function(e){e.error||(s&&s.destroy(),s=R(a,t),errorBox.html(y))})}(this,0,t)}),a.on("change","#listemailstab [id^='esr-toggle-']",function(){!function(a,e,t){var o=i(a).data("id"),n=i(a).data("blockname"),r=i(a).data("region");i.ajax({url:l.requestUrl,type:l.requestType,sesskey:i(a).data("sesskey"),data:{action:"change_scheduled_email_status_ajax",sesskey:i(a).data("sesskey"),data:JSON.stringify({id:o,blockname:n,region:r})}}).done(function(e){e.error||(s&&s.destroy(),s=R(a,t),errorBox.html(y))})}(this,0,t)}),function(o,n,r){n.on("click",'[data-action="save"]',function(){var a=n.find(".esr-form-error");if(a.html(b).show(),function(e,a){var t=e.find('[name="esrname"]').val(),o=e.find('[name="esrrecepient"]').val();if(""==t||""==o)return a.html(w).show(),!1;return!!/^(\s?[^\s,]+@[^\s,]+\.[^\s,]+\s?,)*(\s?[^\s,]+@[^\s,]+\.[^\s,]+)$/g.test(o)||(a.html(k).show(),!1)}(n.find("form"),a)){var e=l.getUrlParams(o.href,"filter"),t=l.getUrlParams(o.href,"cohortid");i.ajax({url:M.cfg.wwwroot+"/report/elucidsitereport/download.php?format=emailscheduled&filter="+e+"&cohortid="+t,type:"POST",data:n.find("form").serialize()}).done(function(e){(e=i.parseJSON(e)).error?(a.html(v),console.log(e.error)):(s&&s.destroy(),s=R(o,r),a.html(y))}).fail(function(e){a.html(v),console.log(e)}).always(function(){a.delay(3e3).fadeOut("slow")})}}),n.on("click",'[data-action="cancel"]',function(){n.find("[name^=esr]").val(""),n.find("#esr-id").val(-1)})}(e,a,t)}(t,a,e),e.show()})})})});