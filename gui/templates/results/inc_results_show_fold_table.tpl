{* 
TestLink Open Source Project - http://testlink.sourceforge.net/ 
*}

{assign var="args_title" value=$args_title|default:""}
{assign var="args_first_column_header" value=$args_first_column_header|default:"first column"}
{assign var="args_show_percentage" value=$args_show_percentage|default:true}

{if $args_column_definition != ""}

<script type="text/javascript" src="{$basehref}third_party/jquery/{$smarty.const.TL_JQUERY}" language="javascript"></script>
<script>
  $(document).ready(function() {
    $('.firstLevel').click(function(){
        $(this).nextUntil('tr.firstLevel').slideToggle(10);
    });
  });
</script>

<h2>{$args_title|escape}</h2>
<table class="simple_tableruler sortable" style="text-align: center; margin-left: 0px;">
	<tr>
		<th>{$args_first_column_header|escape}</th>
		<th>{lang_get s='trep_total'}</th>
    {foreach item=the_column from=$args_column_definition}
        <th>{$the_column.qty}</th>
        {if $args_show_percentage}
        <th>{$the_column.percentage}</th>
        {/if}
    {/foreach}
    <th>{lang_get s='trep_comp_perc'}</th>
    </tr>
 {foreach from=$args_column_data|@sortby:"topsuite,-level,name" item=res}
    {if $res.level}
        <tr class="firstLevel">
        {assign var="bg" value="background-color:#c0d0e0"}
        <td style="font-weight:bold;text-align:left;{$bg}">{$res.$args_first_column_key|escape}</td>
    {else}
        <tr>
        {assign var="bg" value=""}
        <td style="text-align:left;{$bg}">{$res.$args_first_column_key|escape}</td>
    {/if}
    <td style="{$bg}">{$res.total_tc}</td>
    {foreach item=the_column from=$res.details}
      <td style="{$bg}">{$the_column.qty}</td>
      {if $args_show_percentage}
        <td style="{$bg}">{$the_column.percentage}</td>
      {/if}
    {/foreach}
    <td style="{$bg}">{$res.percentage_completed}</td>
    </tr>
 {/foreach}
</table>
{/if}