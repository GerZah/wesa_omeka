<?php $provideRelationComments = get_option('item_relations_provide_relation_comments'); ?>
<div id="item-relations-display-item-relations">
    <h2><?php echo __('Item Relations'); ?></h2>
    <?php
  		if (!$allRelations) {
  			echo "<p>" . __('This item has no relations.') . "</p>";
  		} # if
  		else {
        echo "<table><tbody>";
        $colspan = ($provideRelationComments ? 4 : 3);
  			$lastVocab = -1;
  			foreach ($allRelations as $relation) {
  				if ($lastVocab != $relation["vocabulary_id"]) {
  					echo "<tr><th colspan='$colspan'>"
  					."<span title='".$relation["vocabulary_desc"]."'>"
  					.$relation["vocabulary"]
  					."</span><th></tr>";
  					$lastVocab = $relation["vocabulary_id"];
  				}
          echo "<tr>";
          echo "<td>" .
  				      ( $relation['subject_item_id']==$thisItemId ? __('This Item')
  								: "<a href='".url('items/show/' . $relation['subject_item_id'])."'>".
  										$relation['subject_item_title'] . "</a>"
  							) .
                "</td>";
  				echo "<td><strong>" . $relation['relation_text'] . "</strong></td>";
          echo "<td>" .
        				( $relation['object_item_id']==$thisItemId ? __('This Item')
  								: "<a href='".url('items/show/' . $relation['object_item_id'])."'>".
  										$relation['object_item_title'] . "</a>"
  							).
                "</td>";
  				if ( ($provideRelationComments) and ($relation['relation_comment']) ) {
  					echo "<td>(".$relation['relation_comment'].")</td>";
  				}
          echo "</tr>";
  			} # foreach
  		} # else
      echo "</table>";
  	?>
</div>
