{**
 * Urbit for Pretashop
 * 
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<div class="tabs">
	<div>		
		<ul class="clearfix">
		{foreach $our_items as $key => $item}
			{*combine class begins*}
			{assign var=currentClass value=$item->getSlug()}
			{if $item->getClass()}
				{assign var=currentClass value=$currentClass|cat:" "}
				{assign var=currentClass value=$currentClass|cat:$item->getClass()}
			{/if}
			{if $parents}
				{assign var=currentClass value=$currentClass|cat:" "}
				{assign var=currentClass value=$currentClass|cat:$parents}
			{/if}
			{*combine class ends*}
			{if $item->getLink()}{* if ajax*}
				<li><a href="{$item->getLink()|escape:'htmlall':'UTF-8'}" class="{$currentClass|escape:'htmlall':'UTF-8'}" >{$item->getName()|escape:'htmlall':'UTF-8'}</a></li>
			{else}
				<li><a href="#tabs-{$item->getSlug()|escape:'htmlall':'UTF-8'}" class="{$currentClass|escape:'htmlall':'UTF-8'}">{$item->getName()|escape:'htmlall':'UTF-8'}</a></li>
			{/if}
		{/foreach}
		</ul>
		
	</div>
	<div class="main-content">
		{foreach $our_items as $key => $item}
			{if $item->getChildrenCount() > 0} {* if sub-menus are available*}
				{*combine parents class begins*}
				{if $parents ==''}
					{assign var=currentParents value=$parents|cat:"parent"}
				{else}
					{assign var=currentParents value=$parents}
				{/if}
				{assign var=currentParents value=$currentParents|cat:"__"}
				{assign var=currentParents value=$currentParents|cat:$item->getSlug()}
				{*combine parents class ends*}
				<div class="sub-menu-tab" id="tabs-{$item->getSlug()|escape:'htmlall':'UTF-8'}">
					{include file="./menu.tpl" our_items=$item->getChildren() parents=$currentParents}
				</div>			
			{else} 
				{if !$item->getLink()}
					<div id="tabs-{$item->getSlug()|escape:'htmlall':'UTF-8'}">{include file="./content-{$item->getSlug()}.tpl"}</div>
				{else}
					{* nothing to do, because we are loading ajax tabs here*}
				{/if}
			{/if}
		{/foreach}
	</div>    
</div>

	