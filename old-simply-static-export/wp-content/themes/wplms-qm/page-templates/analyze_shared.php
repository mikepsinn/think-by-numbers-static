<section id="section-configure">
	<header class="card-header">
		<h3 class="heading">
			<span>Resolution</span>
		</h3>
	</header>
	<div class="accordion-content closed" id="accordion-date-content">
		<div class="inner">
			<div id="accordion-content-rangepickers">							
				<input type="radio" value="Hour" id="radio3" name="radio" /><label for="radio3">Hour</label>
				<input type="radio" value="Day" id="radio4" name="radio" checked='checked' /><label for="radio4">Day</label>
				<input type="radio" value="Week" id="radio5" name="radio" /><label for="radio5">Week</label>
				<input type="radio" value="Month" id="radio6" name="radio" /><label for="radio6">Month</label>
			</div>
		</div>
	</div>

	<header class="card-header">
		<h3 class="heading">
			<span>Variables</span>
		</h3>
	</header>
	<div class="accordion-content closed" id="accordion-input-content">
		<div class="inner">
			<ul id="addVariableMenu">
			  <li>
				<a>Add a Variable</a>
				<ul id="addVariableMenuCategories" style="z-index: 999999">
				</ul>
			  </li>
			</ul>
			<ul id="selectedVariables">
			</ul>
			<div class="no-message no-vars">You have no variables yet. A good place to start importing variables is the <a href="#">CONNECT</a> page.</div>
			<div class="no-message no-shared-vars">This user hasn't shared any variables.</div>
		</div>
	</div>
</section>

<section id="section-analyze">
	<div style="width: 1px; overflow: hidden;"></div>	<!-- Dirty hack for <768px -->
	<div id="timeline-graph">
		<header class="card-header">
			<h3 class="heading">
				<span>Timeline</span>
				<div id="gauge-timeline-settingsicon" data-dropdown="#dropdown-timeline-settings" class="gear-icon"></div>
			</h3>
		</header>
		<div class="graph-content" id="graph-timeline">
		</div>
	</div>
</section>

<!-- Menu for timeline settings -->
<div id="dropdown-timeline-settings" class="dropdown dropdown-tip dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><label><input name="tl-enable-markers" type="checkbox" /> Show markers</label></li>
		<li><label><input name="tl-smooth-graph" type="checkbox" /> Smoothen graph</label></li>
		<li><label><input name="tl-enable-horizontal-guides" type="checkbox" /> Show horizontal guides</label></li>
		<li class="dropdown-divider"></li>
		<li><a id="shareTimeline">Share graph</a></li>
	</ul>
</div>