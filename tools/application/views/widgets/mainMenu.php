<ul class="nav nav-light">
<?php if(is_array($menuLinks)){ foreach($menuLinks as $linkInfo) { ?>
  <a href="<?php echo $linkInfo['link']; ?>" class="nav-link<?php if($linkInfo['isActivated']){ echo ' active'; } ?>"><?php echo $linkInfo['text']; ?></a>
<?php }} ?>
</ul>