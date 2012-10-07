{extends file="main.tpl"}
{block name=title}Welcome page{/block}
{block name=captures}
	{capture name='currentPage'}index{/capture}
{/block}
{block name=addMenus}
<li><a href="/license">License</a></li>
{/block}
{block name=addMoreMenusWrapper}{/block}

{block name=body}
<div class="hero-unit">
  <h1 style="padding-bottom:30px">Welcome to play4php demo project</h1>
  <p>
	Hello {$firstname} {$lastname}, glad to see you can make it. <br/>
	Current time is {nocache}{$smarty.now|date_format:"%A, %d. %B %Y %H:%M:%S"}{/nocache}
  </p>
</div>
{/block}