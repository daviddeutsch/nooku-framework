<textarea style="display: none" id="image_details">
[% var ratio= 160 / (width > height ? width : height);%]
<ul>
	<li>
		<img src="/[%=baseurl%]/[%=path%]" width="[%=ratio*width%]"
			height="[%=ratio*height%]" alt="[%=name%]" border="0" />
	</li>
	<li>
		[%=width%] x  [%=height%] | [%=new Files.Filesize(size).humanize()%]
	</li>
</ul>
</textarea>

<textarea style="display: none" id="image_container">
<ul>

</ul>
</textarea>

<textarea style="display: none"  id="image_parent">
<li class="files-node">
	<a class="navigate" href="#">
		..
	</a>
</li>
</textarea>

<textarea style="display: none"  id="image_image">
<li class="files-node files-image">
	<a class="navigate" href="#">
		[%= name %]
	</a>
</li>
</textarea>

<textarea style="display: none"  id="ercan">
<h1> [%= title %] </h1>
</textarea>