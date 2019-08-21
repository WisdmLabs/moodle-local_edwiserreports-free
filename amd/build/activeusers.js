define(["jquery","core/modal_factory","core/modal_events","core/fragment","core/templates","report_elucidsitereport/variables","report_elucidsitereport/jquery.dataTables","report_elucidsitereport/dataTables.bootstrap4","report_elucidsitereport/flatpickr","report_elucidsitereport/common"],function($,ModalFactory,ModalEvents,Fragment,Templates,V){function init(CONTEXTID){var PageId="#wdm-activeusers-individual",ActiveUsersTable=PageId+" .table",loader=PageId+" .loader",ModalTrigger=ActiveUsersTable+" a",dropdownToggle="#filter-dropdown.dropdown-toggle",dropdownMenu=".dropdown-menu[aria-labelledby='filter-dropdown']",dropdownItem=dropdownMenu+" .dropdown-item",flatpickrCalender="#flatpickrCalender",dropdownButton="button#filter-dropdown",filter="weekly",cohortId=0,dropdownInput="#wdm-userfilter input.form-control.input",sesskey=null,DataTable=null,cohortFilterBtn="#cohortfilter",cohortFilterItem=cohortFilterBtn+" ~ .dropdown-menu .dropdown-item";function createModalOfUsersList(){$(document).on("click",ModalTrigger,function(){var title,action=$(this).data("action"),filter=$(this).data("filter"),ModalRoot=null;"activeusers"==action?title="Active Users on ":"enrolments"==action?title="Enroled Users on ":"completions"==action&&(title="Completed Users on ");var titleDate=new Date(eval(1e3*filter));title+=V.formatDate(titleDate,"d MMMM yyyy"),ModalFactory.create({body:Fragment.loadFragment("report_elucidsitereport","userslist",CONTEXTID,{page:"activeusers",filter:filter,cohortid:cohortId,action:action})}).then(function(e){ModalRoot=e.getRoot(),e.setTitle(title),e.show(),ModalRoot.on(ModalEvents.hidden,function(){e.destroy()})})})}function createDropdownCalendar(){$(flatpickrCalender).flatpickr({mode:"range",altInput:!0,altFormat:"d/m/Y",dateFormat:"Y-m-d",maxDate:"today",appendTo:document.getElementById("activeUser-calendar"),onOpen:function(e){$(dropdownMenu).addClass("withcalendar")},onClose:function(){$(dropdownMenu).removeClass("withcalendar"),$(dropdownMenu).removeClass("show"),selectedCustomDate()}})}function selectedCustomDate(){filter=$(flatpickrCalender).val();var e=$(dropdownInput).val();if(!filter.includes("to"))return!1;$(dropdownButton).html(e),$(flatpickrCalender).val(""),createActiveUsersTable(filter,cohortId)}function createActiveUsersTable(e,t){sesskey=$(PageId).data("sesskey"),DataTable&&(DataTable.destroy(),$(ActiveUsersTable).hide(),$(loader).show()),$.ajax({url:V.requestUrl,data:{action:"get_activeusers_graph_data_ajax",sesskey:sesskey,data:JSON.stringify({filter:e,cohortid:t})}}).done(function(o){var r=[];$.each(o.labels,function(e,t){r[e]={date:t,filter:parseInt(new Date(t).getTime()/1e3),activeusers:o.data.activeUsers[e],courseenrolment:o.data.enrolments[e],coursecompletion:o.data.completionRate[e]}});var e={activeusers:r,sesskey:sesskey};Templates.render("report_elucidsitereport/activeusers",e).then(function(e,t){Templates.replaceNode(PageId,e,t)}).fail(function(e){console.log(e)}).always(function(){DataTable=$(ActiveUsersTable).DataTable({responsive:!0,order:[[0,"desc"]],columnDefs:[{targets:0,className:"text-left"},{targets:"_all",className:"text-center"}],info:!1,bLengthChange:!1}),$(ActiveUsersTable).show(),$(loader).hide()})}).fail(function(e){console.log(e)})}$(document).ready(function(){$(dropdownToggle).on("click",function(){$(dropdownMenu).addClass("show")}),$(dropdownInput).ready(function(){var e=$(dropdownInput).attr("placeholder");$(dropdownInput).val(e)}),$(document).click(function(e){$(e.target).hasClass("dropdown-menu")||$(e.target).parents(".dropdown-menu").length||$(dropdownMenu).removeClass("show")}),$(cohortFilterItem).on("click",function(){cohortId=$(this).data("cohortid"),$(cohortFilterBtn).html($(this).text()),createActiveUsersTable(filter,cohortId)}),$(dropdownItem+":not(.custom)").on("click",function(){filter=$(this).attr("value"),$(dropdownMenu).removeClass("show"),$(dropdownButton).html($(this).text()),createActiveUsersTable(filter,cohortId),$(flatpickrCalender).val("Custom"),$(dropdownInput).val("Custom")}),createActiveUsersTable(),createModalOfUsersList(),createDropdownCalendar()})}return{init:init}});