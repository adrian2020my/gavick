<?php

/**
 * @package		K2
 * @author		GavickPro http://gavick.com
 */

// no direct access
defined('_JEXEC') or die;

$header_display = $this->item->params->get('latestItemTitle') || ($this->item->params->get('latestItemCategory') || ($this->item->params->get('latestItemCommentsAnchor') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1'))));

$content_display = $this->item->params->get('latestItemIntroText') || ($this->item->params->get('latestItemTags') && count($this->item->tags)) || ($this->params->get('latestItemVideo') && !empty($this->item->video)) || $this->item->params->get('latestItemReadMore');

?>

<article class="itemView latestView3Cols">
	<?php echo $this->item->event->BeforeDisplay; ?>
	<?php echo $this->item->event->K2BeforeDisplay; ?>
  	
  	<?php if($this->item->params->get('latestItemImage') && !empty($this->item->image)): ?>
  	<div class="itemImageBlock<?php if(!($header_display || $content_display)) : ?> nomargin<?php endif; ?>">
  	   	<a class="itemImage" href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">
  			<img src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:<?php echo $this->item->imageWidth; ?>px;height:auto;" />
  		</a>
  	</div>
  	<?php endif; ?>
  	
  	<?php if($header_display): ?>
	<header>
		<?php if($this->item->params->get('latestItemTitle')): ?>
		<h2>
			<?php if ($this->item->params->get('latestItemTitleLinked')): ?>
				<a href="<?php echo $this->item->link; ?>"><?php echo $this->item->title; ?></a>
			<?php else: ?>
				<?php echo $this->item->title; ?>
			<?php endif; ?>
		</h2>
		<?php endif; ?>
		
		<?php if($this->item->params->get('latestItemCategory') || ($this->item->params->get('latestItemCommentsAnchor') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1')))): ?>
		<ul>
			<?php if($this->item->params->get('latestItemDateCreated')): ?>
	  		<li><time datetime="<?php echo JHtml::_('date', $this->item->created, JText::_(DATE_W3C)); ?>"><?php echo JHTML::_('date', $this->item->created, JText::_('l, j F Y')); ?></time></li>
	  		<?php endif; ?>

	  		<?php if($this->item->params->get('latestItemCategory')): ?>
	  		<li>
	  			<span><?php echo JText::_('K2_PUBLISHED_IN'); ?></span>
	  			<a href="<?php echo $this->item->category->link; ?>"><?php echo $this->item->category->name; ?></a>
	  		</li>
	  		<?php endif; ?>
	  	
	  		<?php if($this->item->params->get('latestItemCommentsAnchor') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1'))): ?>
	  		<li>
		  		<?php if(!empty($this->item->event->K2CommentsCounter)): ?>
		  			<!-- K2 Plugins: K2CommentsCounter -->
		  			<?php echo $this->item->event->K2CommentsCounter; ?>
		  		<?php else: ?>
		  			<?php if($this->item->numOfComments > 0): ?>
		  			<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
		  				<?php echo $this->item->numOfComments; ?> <?php echo ($this->item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
		  			</a>
		  			<?php else: ?>
		  			<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
		  				<?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?>
		  			</a>
		  			<?php endif; ?>
		  		<?php endif; ?>
	  		</li>
	  		<?php endif; ?>
	  	</ul>
	  	<?php endif; ?>
  	</header>
  	<?php endif; ?>

  	<?php echo $this->item->event->AfterDisplayTitle; ?>
  	<?php echo $this->item->event->K2AfterDisplayTitle; ?>

	<?php if($content_display): ?>
  	<div class="itemBody">
		<?php echo $this->item->event->BeforeDisplayContent; ?>
	  	<?php echo $this->item->event->K2BeforeDisplayContent; ?>

	  	<?php if($this->item->params->get('latestItemIntroText')): ?>
	  	<div class="itemIntroText">
	  		<?php echo $this->item->introtext; ?>
	  	</div>
	  	<?php endif; ?>

	  	<?php echo $this->item->event->AfterDisplayContent; ?>
	  	<?php echo $this->item->event->K2AfterDisplayContent; ?>
	  
	  	<?php if($this->item->params->get('latestItemTags') && count($this->item->tags)): ?>
	  	<div class="itemLinks">
	  		  <ul class="itemTags">
	  		    <?php foreach ($this->item->tags as $tag): ?>
	  		    <li><a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a></li>
	  		    <?php endforeach; ?>
	  		  </ul>
	  	</div>
	  	<?php endif; ?>
	  	
	  	<?php if($this->params->get('latestItemVideo') && !empty($this->item->video)): ?>
	  	<div class="itemVideoBlock">
	  		<h3><?php echo JText::_('K2_RELATED_VIDEO'); ?></h3>
	  		<span class="itemVideo<?php if($this->item->videoType=='embedded'): ?> embedded<?php endif; ?>"><?php echo $this->item->video; ?></span>
	  	</div>
	  	<?php endif; ?>
	  
	  	<?php if ($this->item->params->get('latestItemReadMore')): ?>
	  	<a class="button" href="<?php echo $this->item->link; ?>">
	  		<?php echo JText::_('K2_READ_MORE'); ?>
	  	</a>
	  	<?php endif; ?>
  	</div>
	<?php endif; ?>

	<?php echo $this->item->event->AfterDisplay; ?>
  	<?php echo $this->item->event->K2AfterDisplay; ?>
</article>