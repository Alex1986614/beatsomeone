<?php $this->managelayout->add_css('/dist/wishlist.css'); ?>
<?php $this->managelayout->add_js('/dist/wishlist.js'); ?>

<?php
$this->managelayout->add_script('window.vm.$children[0].$data.isLogin = ("' . $this->member->is_member() . '" != "");');
?>