<?php /**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package blog/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */ ?>
<style>
	#content {
		height: 100%;
		width: 100%;
		background: #ECECEC;
		font-family: 'Segoe UI',Tahoma,Helvetica,sans-serif;
		min-height: 600px;
		min-width: 600px;
	}
	label{display: block}
	.pubstatus input {
		padding: 5px;
		cursor: pointer;
		background: #44C5EC;
		color : #fff;
		border: none;
		margin: 4px 0;
		display: block;
		text-align: center;
		width:57px;
	}
	label{display: block;
		  margin: 10px 0 2px;
		  text-transform: capitalize;}
	li{list-style: none;position: relative;
	   padding: 5px 15px;}
	h4{margin: 5px 0;}
	.pendingcomment a{white-space: nowrap;
					  width: 150px;
					  overflow: hidden;text-transform: capitalize;
					  text-overflow: ellipsis;text-decoration: none;color: #777}
	textarea{width: 450px;height:250px;margin : 10px}
	.commentcl div{margin : 10px 0;line-height: 18px;}
	.adminzone .save{
		z-index: 999;
		position: relative;
		margin: 5px 20px;
		float: right;
		text-shadow: 0 1px rgba(0, 0, 0, 0.35);
		border-radius: 5px;
		border: 1px solid rgb(7, 130, 214);
		text-decoration: none;
		color: white;
		line-height: 25px;
		cursor: pointer;
		font-size: 15px;
		padding: 2px 10px;
		background: webkit-gradient(top, #44C5EC, #259BDB);
		background: -webkit-linear-gradient(top, #44C5EC, #259BDB);
		background: -moz-linear-gradient(top, #44C5EC, #259BDB);
	}
</style>
<?php $this->initConfig(); ?>
<div class="adminzone">
	<div id="conf_box_title"><?php echo t('Blog settings') ?></div>
	<div class="adminzonemenu"></div>
	<div class="adminzonecontent">
		<form action="" method="POST" target="formResult"> 
			<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
			<input type="hidden" name="action" value="saveConfig">
			<div class="commentcl">
				<h2><?php echo t('Comments settings', false); ?></h2>
				<div>
					<input type="hidden" name="config[blog][allowComments]" value="0" />
					<input type="checkbox" class="onOff" name="config[blog][allowComments]" <?php if ($this->getConfig('allowComments') === '1') echo ' checked="checked"'; ?> value="1" />
					<?php echo t('Allow people to post comments', FALSE) . ' '; ?> 
				</div>
				<div>
					<?php echo t('This block shows at most', FALSE) . ' '; ?> 
					<input type="number" style="width:40px;"  name="config[blog][items]" value="<?php echo $this->getConfig('items'); ?>" />
					<?php echo ' ' . t('comments', FALSE); ?> (<?php echo t('By default all', FALSE); ?>)
				</div>  
				<div>
					<input type="hidden" name="config[blog][loggedin]" value="0" />
					<input type="checkbox" class="onOff" name="config[blog][loggedin]" <?php if ($this->getConfig('loggedin') === '1') echo ' checked="checked"'; ?> value="1"/>
					<?php echo t('The user must be logged in to comment', FALSE) . ' '; ?> 
				</div>
				<div>
					<input type="hidden" name="config[blog][fillNameMail]" value="0" />
					<input name="config[blog][fillNameMail]" class="onOff" type="checkbox" <?php if ($this->getConfig('fillNameMail') === '1') echo ' checked="checked"'; ?> value="1"/>
					<?php echo t('Comment author must fill out name and e-mail', false); ?>
				</div>
				<div>
					<?php echo t('Automatically close comments on articles older than ', false); ?>
					<input type="number" name="config[blog][closeAfterDays]" min="1" max="99" value="<?php echo $this->getConfig('closeAfterDays') ?>" />
					<?php echo ' ' . t(' days', FALSE); ?>
				</div>
				<div style="border-bottom: 1px solid #CCC;box-shadow: 0px 2px 1px #fff;padding-bottom: 20px;">
					<?php echo t('Comments should be displayed with the ', false); ?>
					<select name="config[blog][commentOrder]">
						<option value="asc" <?php if ($this->getConfig('commentOrder') === '1') echo ' selected="selected"'; ?>><?php echo t('older', FALSE); ?></option>
						<option value="desc"><?php echo t('newer', FALSE); ?></option>
					</select>
					<?php echo ' ' . t('comments at the top of each page', FALSE); ?>
				</div>

				<h2><?php echo t('E-mail me whenever', false); ?></h2>
				<div>
					<input type="hidden" name="config[blog][mailForAnyPost]" value="0" />
					<input name="config[blog][mailForAnyPost]" class="onOff" type="checkbox" <?php if ($this->getConfig('mailForAnyPost') === '1') echo ' checked="checked"'; ?> value="1"/>
					<?php echo t('Anyone posts a comment', false); ?>
				</div>

				<div style="border-bottom: 1px solid #CCC;box-shadow: 0px 2px 1px #fff;padding-bottom: 20px;">
					<input type="hidden" name="config[blog][heldModeration]" value="0" />
					<input name="config[blog][heldModeration]" class="onOff" type="checkbox"<?php if ($this->getConfig('heldModeration') === '1') echo ' checked="checked"'; ?> value="1"/>
					<?php echo t('A comment is held for moderation', false); ?>
				</div>

				<h2><?php echo t('Before a comment appears', false); ?></h2>
				<div>
					<input type="hidden" name="config[blog][alwaysApprove]" value="0" />
					<input name="config[blog][alwaysApprove]" class="onOff" type="checkbox" <?php if ($this->getConfig('alwaysApprove') === '1') echo ' checked="checked"'; ?> value="1"/>
					<?php echo t('An administrator must always approve the comment', false); ?>
				</div>
				<div style="border-bottom: 1px solid #CCC;box-shadow: 0px 2px 1px #fff;padding-bottom: 20px;">
					<input type="hidden" name="config[blog][previousComment]" value="0" />
					<input name="config[blog][previousComment]" class="onOff" type="checkbox" <?php if ($this->getConfig('previousComment') === '1')echo ' checked="checked"'; ?> value="1"/>
					<?php echo t('Comment author must have a previously approved comment', false); ?>
				</div>
				<div>
					<h2><?php echo t('Comment Moderation', false); ?></h2>
					<?php echo t('Hold a comment in the queue if it contains', FALSE); ?>
					<input type="number" name="config[blog][linkspam]" min="1" max="99" value="<?php echo $this->getConfig('linkspam') ?>"/>
					<?php echo t('or more links. (When a large number of hyperlinks it\'s spam)', false); ?>
				</div>
				<div style="border-bottom: 1px solid #CCC;box-shadow: 0px 2px 1px #fff;padding-bottom: 20px;">
				  <?php echo t('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be held in the moderation queue. One word or IP per line. It will match inside words, so “mony” will match “Parsimony”.',false); ?>
				  <br><textarea name="config[blog][moderationWord]"><?php echo $this->getConfig('moderationWord') ?></textarea>
				</div>
				<div style="margin: 20px 0">
				  <h2><?php echo t('Comment Blacklist',false); ?></h2>
				  <?php echo t('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be marked as spam. One word or IP per line. It will match inside words, so “mony” will match “Parsimony”.',false); ?>
				  <br><textarea name="config[blog][trashWord]"><?php echo $this->getConfig('trashWord') ?></textarea>
				</div>			
				<input type="hidden" name="file" value="<?php echo 'profiles/www/modules/blog/config.php'; ?> "/>
				<input type="hidden" name="module" value="blog"/>
				<input class="none" id="save_configs" type="submit"/>
		</form> 
	</div>
</div>  
	<div class="adminzonefooter"> 
		<div>
			<div id="save_page" class="save ellipsis" onclick="$('form').trigger('submit');
			return false;"><?php echo t('Save', false); ?></div>
		</div>
	</div>  

</div>
