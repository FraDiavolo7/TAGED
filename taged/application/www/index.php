<?php

include '../src/define.php';

Page::handle ( Form::getData ( TagedPage::PAGE_SELECTOR, TagedPage::PAGE_DEFAULT ), $_REQUEST );

