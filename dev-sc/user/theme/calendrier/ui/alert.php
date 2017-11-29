<?php

	if($alert['view']){
		echo "<div class=\"alert alert-block ".$alert['class']." fade in\">";
		if($alert['title'] != '') echo "<h4 class=\"alert-heading\">".$alert['title']."</h4>";
		echo "<p>".$alert['text']."</p>";
		echo "</div>";
	}

?>