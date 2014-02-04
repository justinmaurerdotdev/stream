<div class="wrap">

	<h2><?php $rule->exists() ? esc_html_e( 'Edit Notification Rule', 'stream-notifications' ) : esc_html_e( 'Add New Notification Rule', 'stream-notifications' ); ?>
		<?php if ( $rule->exists() ) : ?>
			<?php
			$new_link = add_query_arg(
				array(
					'page' => WP_Stream_Notifications::NOTIFICATIONS_PAGE_SLUG,
					'view' => 'rule',
				),
				admin_url( WP_Stream_Admin::ADMIN_PARENT_PAGE )
			);
			?>
			<a href="<?php echo esc_url( $new_link ) ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'stream-notifications' ) ?></a>
		<?php endif; ?>
	</h2>

	<form action="" method="post" id="rule-form">

		<?php
		wp_nonce_field( 'stream-notifications-form' );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		?>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-<?php echo esc_attr( 1 == get_current_screen()->get_columns() ? 1 : 2 ); ?>">
				<div id="post-body-content">

					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" name="summary" size="30" value="<?php echo esc_attr( $rule->summary ) ?>" id="title" autocomplete="off" keyev="true" placeholder="<?php esc_attr_e( 'Rule title', 'stream-notifications' ) ?>" autofocus="focus">
						</div>
					</div><!-- /titlediv -->
				</div><!-- /post-body-content -->

				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( get_current_screen()->id, 'side', $rule ); ?>
				</div><!-- postbox-container-1 -->

				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( get_current_screen()->id, 'normal', $rule ); ?>
				</div><!-- postbox-container-2 -->
			</div><!-- /postbody -->
		</div><!-- /poststuff -->
	</form>
</div><!-- /wrap -->

	<?php if ( $rule->triggers ) { ?>
		<script>
			var notification_rule = {
				triggers : <?php echo json_encode( $rule->triggers ) ?>,
				groups   : <?php echo json_encode( $rule->groups ) ?>,
				alerts   : <?php echo json_encode( $rule->alerts ) ?>,
			}
		</script>
	<?php } ?>

<script type="text/template" id="trigger-template-row">
<div class="trigger" rel="<%- vars.index %>">
	<div class="form-row">
		<input type="hidden" name="triggers[<%- vars.index %>][group]" value="<%- vars.group %>" />
		<div class="field relation">
			<select name="triggers[<%- vars.index %>][relation]" class="trigger-relation">
				<option value="and"><?php esc_html_e( 'AND', 'stream-notifications' ) ?></option>
				<option value="or"><?php esc_html_e( 'OR', 'stream-notifications' ) ?></option>
			</select>
		</div>
		<div class="field type">
			<select name="triggers[<%- vars.index %>][type]" class="trigger-type" rel="<%- vars.index %>" placeholder="Choose Rule">
				<option></option>
				<% _.each( vars.types, function( type, name ){ %>
				<option value="<%- name %>"><%- type.title %></option>
				<% }); %>
			</select>
		</div>
		<a href="#" class="delete-trigger">Delete</a>
	</div>
</div>
</script>

<script type="text/template" id="trigger-template-group">
<div class="group" rel="<%- vars.index %>">
	<div class="group-meta">
		<input type="hidden" name="groups[<%- vars.index %>][group]" value="<%- vars.parent %>" />
		<div class="field relation">
			<select name="groups[<%- vars.index %>][relation]" class="group-relation">
				<option value="and"><?php esc_html_e( 'AND', 'stream-notifications' ) ?></option>
				<option value="or"><?php esc_html_e( 'OR', 'stream-notifications' ) ?></option>
			</select>
		</div>
		<a href="#add-trigger" class="add-trigger button button-secondary" data-group="<%- vars.index %>">+ Add Trigger</a>
		<a href="#add-trigger-group" class="add-trigger-group button button-primary" data-group="<%- vars.index %>">+ Add Group</a>
		<a href="#" class="delete-group">Delete Group</a>
	</div>
</div>
</script>

<script type="text/template" id="trigger-template-options">
<div class="trigger-options">
	<div class="field operator">
		<select name="triggers[<%- vars.index %>][operator]" class="trigger-operator">
			<% _.each( vars.operators, function( list, name ){ %>
			<option value="<%- name %>"><%- list %></option>
			<% }); %>
		</select>
	</div>
	<div class="field value">
		<% if ( ['select', 'ajax'].indexOf( vars.type ) != -1 ){ %>
		<select name="triggers[<%- vars.index %>][value]" class="trigger-value" data-ajax="<% ( vars.ajax ) %>" <% if ( vars.multiple ){ %>multiple="multiple"<% } %>>
			<option></option>
			<% if ( vars.options ) { %>
				<% _.each( vars.options, function( list, name ){ %>
				<option value="<%- name %>"><%- list %></option>
				<% }); %>
			<% } %>
		</select>
		<% } else { %>
		<input type="text" name="triggers[<%- vars.index %>][value]" class="trigger-value <% if ( vars.tags ){ %>tags<% } %> <% if ( vars.ajax ){ %>ajax<% } %>">
		<% } // endif%>
	</div>
</div>
</script>

<script type="text/template" id="alert-template-row">
<div class="alert" rel="<%- vars.index %>">
	<div class="form-row">
		<div class="type">
			<span class="circle"><%- vars.index + 1 %></span>
			<select name="alerts[<%- vars.index %>][type]" class="alert-type" rel="<%- vars.index %>" placeholder="Choose Type">
				<option></option>
				<% _.each( vars.adapters, function( type, name ){ %>
				<option value="<%- name %>"><%- type.title %></option>
				<% }); %>
			</select>
			<a href="#" class="delete-alert alignright">Delete</a>
			<div class="clear"></div>
		</div>
	</div>
</div>
</script>

<script type="text/template" id="alert-template-options">
<table class="alert-options form-table">
	<% for ( field_name in vars.fields ) { var field = vars.fields[field_name]; %>
		<tr>
			<th class="label">
				<label><%- field.title %></label>
				<% if ( field.hint ) { %>
					<p class="description"><%- field.hint %></p>
				<% } %>
			</th>
			<td>
				<div class="field value">
					<% if ( ['select'].indexOf( field.type ) != -1 ){ %>
						<select name="alerts[<%- vars.index %>][<%- field_name %>]" class="alert-value widefat" data-ajax="<% ( field.ajax ) %>" <% if ( field.multiple ){ %>multiple="multiple"<% } %>>
							<option></option>
							<% if ( vars.fields[field] ) { %>
								<% _.each( vars.fields[field], function( list, name ){ %>
								<option value="<%- name %>"><%- list %></option>
								<% }); %>
							<% } %>
						</select>
					<% } else if ( ['textarea'].indexOf( field.type ) != -1 ) { %>
						<textarea name="alerts[<%- vars.index %>][<%- field_name %>]" class="alert-value large-text code" rows="10" cols="80"></textarea>
					<% } else { %>
						<input type="text" name="alerts[<%- vars.index %>][<%- field_name %>]" class="alert-value widefat <% if ( field.tags ){ %>tags<% } %> <% if ( field.ajax ){ %>ajax<% } %>" <% if ( field.ajax && field.key ){ %>data-ajax-key="<%- field.key %>"<% } %> >
					<% } %>
				</div>
			</td>
		</tr>
	<% } %>
	<% if ( typeof vars.hints != 'undefined' ) { %>
		<tr>
			<th></th>
			<td><%= vars.hints %></td>
		</tr>
	<% } %>
</table>

</script>

<style>
	#triggers .field, .trigger-type, .trigger-options, .trigger-value { float: left; }
	.trigger .form-row {
		clear: both;
		overflow: hidden;
		margin-bottom: 10px;
		background: #eee;
		padding: 10px;
	}
	#triggers .inside {
		padding-bottom: 3px;
	}
	#triggers .inside,
	#alerts .inside {
		margin-top: 12px;
	}
	#alerts .inside {
		padding: 0;
	}
	.group {
		background: rgba(0, 0, 0, 0.08);
		padding: 20px 20px 12px;
		margin-bottom: 10px;
		min-height: 16px;
		clear: both;
	}
	.inside > .group {
		margin: 0;
		min-height: 0;
		background: none;
		padding: 0;

		-webkit-box-shadow: none;
			    box-shadow: none;
	}
	.group .form-row {
		background: rgba(0, 0, 0, 0.03);
	}
	.group,
	.group .form-row {
		margin-left: 90px;

		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.15);
			    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.15);
	}
	.add-trigger,
	.add-trigger-group {
		margin-right: 4px !important;
		margin-bottom: 10px !important;
	}
	.group .delete-trigger,
	.alert .delete-alert {
		line-height: 29px;
	}
	.group .delete-trigger,
	.group .delete-group,
	.alert .delete-alert {
		color: #a00;
		text-decoration: none;
	}
	.group .delete-trigger:hover,
	.group .delete-group:hover,
	.alert .delete-alert:hover {
		color: #f00;
		text-decoration: none;
	}
	.inside > .group,
	.inside > .group > .group,
	.inside > .alert {
		margin-left: 0;
	}
	.inside > .group > .trigger > .form-row {
		margin-top: 10px;
	}
	.inside > .group > .trigger.first > .form-row {
		margin-top: 0;
	}
	.inside > .group > .trigger > .form-row,
	.inside > .alert > .form-row {
		margin-left: 0;
	}
	.group-meta {
		float: left;
		margin-top: -10px;
		margin-left: -10px;
		padding: 0 0 12px;
	}
	.group-meta a {
		font-size: 10px;
	}
	.group-meta a.delete-group {
		line-height: 28px;
	}
	.group .trigger:first-of-type .field.relation,
	.trigger.first .field.relation {
		display: none;
	}
	.trigger.first .field.type {
		margin-left: 99px;
	}
	.delete-trigger,
	.delete-alert {
		float: right;
	}
	.select2-container {
		margin-right: 6px;
	}
	.select2-results li {
		margin-bottom: 2px;
	}
	.select2-container .select2-choice > .select2-chosen {
		font-size: 13px;
	}
	.select2-container.trigger-type {
		width: 180px !important;
	}
	.select2-container.trigger-operator {
		width: 140px !important;
	}
	.select2-choices img.avatar,
	.select2-results img.avatar {
		vertical-align: middle;
		margin-right: 5px;
		margin-bottom: 2px;

		-webkit-border-radius: 3px;
		   -moz-border-radius: 3px;
		        border-radius: 3px;
	}
	.select2-search-choice-close {
		-webkit-transition: none;
		   -moz-transition: none;
		     -o-transition: all 0 none;
		        transition: none;
	}
	#alerts .add-alert {
		margin: 0 0 12px 12px;
	}
	#alerts .select2-container {
		max-width: 300px;
	}
	.select2-container.alert-type {
		min-width: 150px;
	}
	.alert .form-row .type {
		padding: 12px;
		border: 1px solid #ddd;
		box-shadow: inset #f5f5f5 0 1px 0 0;
		background: #eaeaea;
	}
	.alert .form-row .type .circle {
		display: inline-block;
		height: 22px;
		width: 22px;
		font-size: 12px;
		line-height: 23px;
		border-radius: 12px;
		text-align: center;
		background: #aaa;
		color: #fff;
		margin-right: 10px;
		vertical-align: middle;
	}
	table.alert-options {
		margin-top: 0;
	}
	table.alert-options tbody tr th.label {
		width: 24%;
		vertical-align: top;
		background: #f9f9f9;
		border-right: 1px solid #e1e1e1;
		border-top: 1px solid #f0f0f0;
	}
	table.alert-options tbody tr th.label label {
		display: block;
		font-size: 12px;
		font-weight: bold;
		padding: 0;
		margin: 0 0 3px;
		color: #333;
	}
	table.alert-options tbody tr th .description {
		display: block;
		font-size: 12px;
		line-height: 16px;
		font-weight: normal;
		font-style: normal;
		color: #888;
	}
	table.alert-options tbody tr th,
	table.alert-options tbody tr td {
		padding: 13px 15px;

	}
	table.alert-options tbody tr td {
		border-top: 1px solid #f5f5f5;
	}
	#data-tags .inside {
		margin: 0 !important;
		padding: 0 !important;
	}
	#data-tags header {
		padding: 10px;
		padding-left: 25px;
		background-color: rgba(0, 0, 0, 0.03);
		cursor: pointer;
	}
	#data-tags dl {
		padding-bottom: 10px;
	}
	#data-tags header:before {
		font: normal 20px/1 'dashicons';
		display: inline-block;
		content: '\f140';
		position: absolute;
		left: 0;
	}
	#data-tags header.ui-state-active:before {
		content: '\f142';
	}
</style>
