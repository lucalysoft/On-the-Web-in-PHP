{*
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.

 * SuiteCRM is an extension to SugarCRM Community Edition developed by Salesagility Ltd.
 * Copyright (C) 2011 - 2014 Salesagility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for  technical reasons, the Appropriate Legal Notices must
 * display the words  "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 ********************************************************************************/

*}

<script type="text/javascript">

	global_langPrefix = "{$langprefix}";
	global_edit = true;
	global_view = "{$view}";
	global_style = "{$style}";
	global_t_step = {$t_step};
	global_current_user_id = "{$current_user_id}";
	global_current_user_name = "{$current_user_name}";
	global_time_format = "{$time_format}";
	global_enable_repeat = "{$enable_repeat}";
	global_items_draggable = "{$items_draggable}";
	global_items_resizable = "{$items_resizable}";
	global_cells_per_day = {$cells_per_day};
	global_dashlet = "{$dashlet}";
	global_grid_start_ts = {$grid_start_ts};
	global_basic_min_height = {$basic_min_height};
	global_timeslots = 30;
	global_start_week_day = "{$start_weekday}";
    global_datetime_format = "{$datetime_user_format}";
	global_year = "{$year}";
	global_month = "{$month}";
	global_day = "{$day}";
	global_start_time = "{$day_start_time}";
	global_end_time = "{$day_end_time}";
	global_colorList = {$activityColors};
	calendar_items = {$a_str};

	{literal}
	views = {
		sharedMonth: {
			type: 'month',
			duration: { months: 1 },
			buttonText: 'Shared Month'
		},
		sharedWeek: {
			type: 'agenda',
			duration: {days: 7},
			buttonText: 'Shared Week'
		}
	};
	{/literal}

	{literal}
	YAHOO.util.Event.onDOMReady(function(){
		dom_loaded = true;
	});

	function check_cal_loaded(){
		return (typeof cal_loaded != 'undefined' && cal_loaded == true && typeof dom_loaded != 'undefined' && dom_loaded == true);
	}
	{/literal}



	SUGAR.util.doWhen(check_cal_loaded, function(){literal}{{/literal}

		CAL.view = "{$view}";
		CAL.style = "{$style}";
		CAL.t_step = {$t_step};
		CAL.current_user_id = global_current_user_id;
		CAL.current_user_name = global_current_user_name;
		CAL.time_format = "{$time_format}";
		CAL.enable_repeat = "{$enable_repeat}";
		CAL.items_draggable = "{$items_draggable}";
		CAL.items_resizable = "{$items_resizable}";
		CAL.cells_per_day = {$cells_per_day};
		CAL.current_params = {literal}{}{/literal};
		CAL.dashlet = "{$dashlet}";
		CAL.grid_start_ts = {$grid_start_ts};
		CAL.scroll_slot = {$scroll_slot};

		CAL.basic.min_height = {$basic_min_height};


		CAL.lbl_create_new = "{$MOD.LBL_CREATE_NEW_RECORD}";
		CAL.lbl_edit = "{$MOD.LBL_EDIT_RECORD}";
		CAL.lbl_saving = "{$MOD.LBL_SAVING}";
		CAL.lbl_loading = "{$MOD.LBL_LOADING}";
		CAL.lbl_sending = "{$MOD.LBL_SENDING_INVITES}";
		CAL.lbl_confirm_remove = "{$MOD.LBL_CONFIRM_REMOVE}";
		CAL.lbl_confirm_remove_all_recurring = "{$MOD.LBL_CONFIRM_REMOVE_ALL_RECURRING}";

		CAL.lbl_error_saving = "{$MOD.LBL_ERROR_SAVING}";
		CAL.lbl_error_loading = "{$MOD.LBL_ERROR_LOADING}";
		CAL.lbl_repeat_limit_error = "{$MOD.LBL_REPEAT_LIMIT_ERROR}";

		CAL.year = {$year};
		CAL.month = {$month};
		CAL.day = {$day};

		CAL.print = {$isPrint};



		CAL.field_list = new Array();
		CAL.field_disabled_list = new Array();

		CAL.act_types = [];
		CAL.act_types['Meetings'] = 'meeting';
		CAL.act_types['Calls'] = 'call';
		CAL.act_types['Tasks'] = 'task';

		{literal}

		CAL.init_edit_dialog({
			width: "{/literal}{$editview_width}{literal}",
			height: "{/literal}{$editview_height}{literal}"
		});

		YAHOO.util.Event.on("btn-save","click",function(){
			if(!CAL.check_forms())
				return false;
			CAL.dialog_save();
		});
		YAHOO.util.Event.on("btn-send-invites","click",function(){
			if(!CAL.check_forms())
				return false;
			CAL.get("send_invites").value = "1";
			CAL.dialog_save();
		});
		YAHOO.util.Event.on("btn-delete","click",function(){
			if(CAL.get("record").value != "")
				if(confirm(CAL.lbl_confirm_remove))
					CAL.dialog_remove();
		});
		YAHOO.util.Event.on("btn-cancel","click",function(){
			document.schedulerwidget.reset();
            if(document.getElementById('empty-search-message')) {
                document.getElementById('empty-search-message').style.display = 'none';
            }
            CAL.editDialog.cancel();
		});
		YAHOO.util.Event.on("btn-full-form","click",function(){
			CAL.full_form();
		});
		CAL.select_tab("cal-tab-1");

		YAHOO.util.Event.on(CAL.get("btn-cancel-settings"), 'click', function(){
			CAL.settingsDialog.cancel();
		});

		YAHOO.util.Event.on(CAL.get("btn-save-settings"), 'click', function(){
			CAL.get("form_settings").submit();
		});

		{/literal}

		cal_loaded = null;
	});
</script>
			
<div id="cal-edit" style="display: none;">
	
	<div class="hd"><span id="title-cal-edit"></span></div>
	<div class="bd" id="edit-dialog-content">
		<div id="cal-tabs" class="yui-navset yui-navset-top yui-content" style="height: auto; padding: 0 2px;">
			<ul class="yui-nav">
				<li id="tab_general"><a tabname="cal-tab-1" id="cal-tab-1-link"><em>{$MOD.LBL_GENERAL_TAB}</em></a></li>
				<li id="tab_invitees"><a tabname="cal-tab-2" id="cal-tab-2-link"><em>{$MOD.LBL_PARTICIPANTS_TAB}</em></a></li>
				{if $enable_repeat}
				<li id="tab_repeat"><a tabname="cal-tab-3" id="cal-tab-3-link"><em>{$MOD.LBL_REPEAT_TAB}</em></a></li>
				{/if}
			</ul>
			<div id="cal-tab-1" class="yui-content">
				{include file=$form}
			</div>				
			<div id="cal-tab-2" class="yui-content">
				<div class="h3Row" id="scheduler"></div>
			</div>
			{if $enable_repeat}
			<div id="cal-tab-3" class="yui-content">
				{include file=$repeat}
			</div>
			{/if}
		</div>
	</div>	
	<div id="cal-edit-buttons" class="ft">
		<button id="btn-save" class="button" type="button">{$MOD.LBL_SAVE_BUTTON}</button>
		<button id="btn-cancel" class="button" type="button">{$MOD.LBL_CANCEL_BUTTON}</button>
		<button id="btn-delete" class="button" type="button">{$MOD.LBL_DELETE_BUTTON}</button>
		<button id="btn-send-invites" class="button" type="button">{$MOD.LBL_SEND_INVITES}</button>
		<button id="btn-full-form" class="button" type="button">{$APP.LBL_FULL_FORM_BUTTON_LABEL}</button>
	</div>
</div>

{if $settings}
{include file=$settings}
{/if}
	
<script type="text/javascript">	
{$GRjavascript}
</script>
	
<script type="text/javascript">	
{literal}
YAHOO.util.Event.onDOMReady(function(){	
	var schedulerLoader = new YAHOO.util.YUILoader({
		require : ["jsclass_scheduler"],
        skin: { base: 'blank', defaultSkin: '' },
		onSuccess: function(){
			var root_div = document.getElementById('scheduler');
			var sugarContainer_instance = new SugarContainer(document.getElementById('scheduler'));
			sugarContainer_instance.start(SugarWidgetScheduler);
		}
	});
	schedulerLoader.addModule({
		name :"jsclass_scheduler",
		type : "js",
		fullpath: "modules/Meetings/jsclass_scheduler.js",
		varName: "global_rpcClient",
		requires: []
	});
	schedulerLoader.insert();
});	
{/literal}	
</script>
	
<script type="text/javascript" src="include/javascript/jsclass_base.js"></script>
<script type="text/javascript" src="include/javascript/jsclass_async.js"></script>	
	
<style type="text/css">
{literal}
	.schedulerDiv h3{
		display: none;
	}
	.schedulerDiv{
		width: auto !important;
	}
{/literal}
</style>	



<link type="text/css" href="{sugar_getjspath file="modules/Calendar/fullcalendar-2.3.2/fullcalendar.css"}" rel="stylesheet" />
<link type="text/css" href="{sugar_getjspath file="modules/Calendar/fullcalendar-2.3.2/fullcalendar.print.css"}" media='print' rel="stylesheet" />

<script src='{sugar_getjspath file="include/javascript/qtip/jquery.qtip.min.js"}'></script>
<script src='{sugar_getjspath file="modules/Calendar/fullcalendar-2.3.2/lib/moment.min.js"}'></script>
<script src='{sugar_getjspath file="modules/Calendar/fullcalendar-2.3.2/fullcalendar.min.js"}'></script>
<script src='{sugar_getjspath file="modules/Calendar/fullcalendar-2.3.2/lang-all.js"}'></script>

<div class='monthCalBody'><h5 class='calSharedUser' id='user_name1'></h5></div>
<div id='calendar1'></div>
{sugar_getscript file="modules/Calendar/Cal.js"}
