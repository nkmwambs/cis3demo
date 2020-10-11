<?php $this->load->view("header",array('page_title'=>$page_title));?>
<div class="container" style="width:550px;float:left;margin-top: 20px">
  <div class="alert alert-success"> <?php echo $message; ?></div>
</div>
<?php $this->load->view("footer");?>