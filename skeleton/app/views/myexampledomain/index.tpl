{extends file="main.tpl"}
{block name=title}MyExampleDomain index page{/block}
{block name=captures}
	{capture name='currentPage'}index{/capture}
{/block}
{block name=addMoreMenusWrapper}{/block}

{block name=body}
<div class="hero-unit">
  <h1 style="padding-bottom:30px">Multiple domains hosting</h1>
  <p>Welcome to myexampledomain index page. You can host multiple domains with a single application! How cool is that!</p>
</div>
{/block}