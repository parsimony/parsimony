<?php
if($this->getConfig('module')){
    $entity = \app::getModule($this->getConfig('module'))->getEntity($this->getConfig('entity'));
    $entity->prepareFieldsForDisplay();
    include(PROFILE_PATH .$this->getConfig('pathOfView'));
}else {
    echo t('Please configure this block');
}
?>