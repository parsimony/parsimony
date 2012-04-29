<?php
if($this->getConfig('module')){
    $entity = \app::getModule($this->getConfig('module'))->getEntity($this->getConfig('entity'));
    if(isset($_POST['add'])){
        if($entity->insertInto($_POST)){
            echo '<div class="notify positive">'.t($this->getConfig('success')).'</div>';
        }else{
            echo '<div class="notify positive">'.t($this->getConfig('fail')).'</div>';
        }
    }

    include(PROFILE_PATH .$this->getConfig('pathOfView'));
}else {
    echo t('Please configure this block');
}
?>