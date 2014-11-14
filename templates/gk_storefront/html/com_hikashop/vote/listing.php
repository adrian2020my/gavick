<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$current_url = hikashop_currentURL();
$set = JRequest::getString('sort_comment','');
$config = JFactory::getConfig();
if(HIKASHOP_J30){
	$sef = $config->get('sef');
}else{
	$sef = $config->getValue('config.sef');
}

if(!empty($set)){
	if($sef){
		$current_url = preg_replace('/\/sort_comment-'.$set.'/','',$current_url);
	}else{
		$current_url = preg_replace('/&sort_comment='.$set.'/','',$current_url);
	}
}
$row = & $this->rows;
$elt = & $this->elts;
$pagination = & $this->pagination;
$no_comment = 1;

$hikashop_vote_con_req_list = $row->hikashop_vote_con_req_list;
$useful_rating = $row->useful_rating;
$comment_enabled = $row->comment_enabled;
$useful_style = $row->useful_style;
$show_comment_date = $row->show_comment_date;

if ($comment_enabled == 1) {
	$hikashop_vote_user_id = hikashop_loadUser();
	if (($hikashop_vote_con_req_list == 1 && $hikashop_vote_user_id != "") || $hikashop_vote_con_req_list == 0) {
		?>
		<div class="hikashop_listing_comment ui-corner-top"><?php echo JText::_('HIKASHOP_LISTING_COMMENT');?>
		<?php if($row->vote_comment_sort_frontend){ ?>
			<span style="float: right;" class="hikashop_sort_listing_comment">
				<?php
				if($sef)
					echo '<select name="sort_comment" onchange="var url=\''.$current_url.'\'+\'/sort_comment-\'+this.value;  document.location.href=\''.JRoute::_('\'+url+\'').'\'">';
				else
					echo '<select name="sort_comment" onchange="var url=\''.$current_url.'\'+\'&sort_comment=\'+this.value;  document.location.href=\''.JRoute::_('\'+url+\'').'\'">';
				?>
				<option <?php if($set == 'date')echo "selected"; ?> value="date"><?php echo JText::_('HIKASHOP_COMMENT_ORDER_DATE');?></option>
				<option <?php if($set == 'helpful')echo "selected"; ?> value="helpful"><?php echo JText::_('HIKASHOP_COMMENT_ORDER_HELPFUL');?></option>
				</select>
			</span>
		<?php } ?>
		</div>
		
		<div class="list-reviews">
		<?php
		for ($i = 1; $i <= count($elt); $i++) {
			if (!empty ($elt[$i]->vote_comment)) {
		?>
			<div class="normal">
				<div class="hika_comment_listing_name">
					<?php
					if ($elt[$i]->vote_pseudo == '0') {
					?>
						<h3><?php echo $elt[$i]->username; ?></h3>
					<?php
					} else {
					?>
						<h3><?php echo $elt[$i]->vote_pseudo; ?></h3>
					<?php
					}
					?>
					
					<?php if($show_comment_date) : ?>
					<span class="date">
						<?php
							$class = hikashop_get('class.vote');
							$vote = $class->get($elt[$i]->vote_id);
							echo hikashop_getDate($vote->vote_date);
						?>
					</span>
					<?php endif; ?>
				</div>
				<div class="hika_comment_listing_stars">
					<?php
						$nb_star_vote = $elt[$i]->vote_rating;
						JRequest::setVar("nb_star",$nb_star_vote);
						$nb_star_config = $row->vote_star_number;
						JRequest::setVar("nb_max_star",$nb_star_config);
						if($nb_star_vote != 0){
							for($k=0; $k < $nb_star_vote; $k++ ){
								?><span class="hika_comment_listing_full_stars" ></span><?php
							}
							$nb_star_empty = $nb_star_config - $nb_star_vote;
							if($nb_star_empty != 0){
								for($j=0; $j < $nb_star_empty; $j++ ){
									?><span class="hika_comment_listing_empty_stars" ></span><?php
								}
							}
						}
					?>
				</div>

				<p id="<?php echo $i; ?>" class="hika_comment_listing_content">
					<?php echo $elt[$i]->vote_comment; ?>
				</p>
				
				<div class="hika_comment_listing_notification" id="<?php echo $elt[$i]->vote_id; ?>" >
					<?php
					if($elt[$i]->total_vote_useful != 0){
						if($elt[$i]->vote_useful == 0){
							$hika_useful[$i] = $elt[$i]->total_vote_useful / 2;
						}
						else if($elt[$i]->total_vote_useful == $elt[$i]->vote_useful){
							$hika_useful[$i] = $elt[$i]->vote_useful;
						}
						else if($elt[$i]->total_vote_useful == -$elt[$i]->vote_useful){
							$hika_useful[$i] = 0;
						}
						else{
							$hika_useful[$i] = ($elt[$i]->total_vote_useful + $elt[$i]->vote_useful)/2;
						}
						$hika_useless[$i] = $elt[$i]->total_vote_useful - $hika_useful[$i];
						if($useful_style == "helpful"){
							echo JText::sprintf('HIKA_FIND_IT_HELPFUL',$hika_useful[$i],$elt[$i]->total_vote_useful);
						}
					}
					else{
						$hika_useless[$i] = 0;
						$hika_useful[$i]  = 0;
						if($useful_style == "helpful"){
							if ($useful_rating == 1) {
								echo JText::_('HIKASHOP_NO_USEFUL');
							}
						}
					}
					?>
				</div>
				<?php
				if ($useful_rating == 1) {
					?>
					<div class="hika_comment_listing_helpful_rating">
					<?php
					if($row->hide == 0 && $elt[$i]->already_vote == 0 && $elt[$i]->vote_user_id != $hikashop_vote_user_id && $elt[$i]->vote_user_id != hikashop_getIP()){
				?>
						<div class="hika_comment_listing_useful" title="Useful" onclick="hikashop_vote_useful(<?php echo $elt[$i]->vote_id;?>,1);"></div>
						<div class="hika_comment_listing_useless" title="Useless" onclick="hikashop_vote_useful(<?php echo $elt[$i]->vote_id;?>,2);"></div>
				<?php
					} else{
				?>
						<div class="hika_comment_listing_useful_p hide"></div>
						<div class="hika_comment_listing_useful locked hide"></div>
						<div class="hika_comment_listing_useless_p hide"></div>
						<div class="hika_comment_listing_useless locked hide"></div>
				<?php
					}
					?>
					</div>
					<?php
				}
				?>
				
				<?php if (!empty ($elt[$i]->purchased)) : ?>
				<div class="hika_comment_listing_bottom">
					<span class="hikashop_vote_listing_useful_bought">
						<?php echo JText::_('HIKASHOP_VOTE_BOUGHT_COMMENT'); ?>
					</span>
				</div>
				<?php endif; ?>
			</div>
		<?php
			$no_comment = 0;
			}
		}
		?>
		</div>
		<?php 
		$later = '';
		if($no_comment == 1){
			?>
				<div class="ui-corner-all hika_comment_listing">
					<div class="hika_comment_listing_empty">
						<?php echo JText::_('HIKASHOP_NO_COMMENT_YET'); ?>
					</div>
				</div>
			<?php
		}
		else{
			$this->pagination->form = '; document.hikashop_comment_form';
			$later = '<div class="pagination">'.$this->pagination->getListFooter().$this->pagination->getResultsCounter().'</div>';
			echo $later;
		}
	}
}
?>
